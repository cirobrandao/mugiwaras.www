<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\RateLimiter;
use App\Core\Validation;
use App\Core\Audit;
use App\Core\Logger;
use App\Models\EmailBlocklist;
use App\Models\UsernameBlocklist;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\Setting;

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        echo $this->view('auth/login', ['csrf' => Csrf::token()]);
    }

    public function login(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/'));
        }

        $rate = new RateLimiter();
        $allowed = $rate->hit('login:' . $request->ip(), (int)config('security.rate_limit.login'), (int)config('security.rate_limit.window'));
        if (!$allowed) {
            echo $this->view('auth/login', ['error' => 'Muitas tentativas. Tente mais tarde.', 'csrf' => Csrf::token()]);
            return;
        }

        $username = trim((string)($request->post['username'] ?? ''));
        $password = (string)($request->post['password'] ?? '');
        $remember = isset($request->post['remember']);

        if (!Auth::attempt($username, $password, $remember, $request)) {
            Audit::log('login_fail', null, ['username' => $username, 'ip' => $request->ip()]);
            echo $this->view('auth/login', ['error' => 'Credenciais inválidas.', 'csrf' => Csrf::token()]);
            return;
        }

        Audit::log('login_success', (int)$_SESSION['user_id'], ['ip' => $request->ip()]);
        $user = Auth::user();
        if ($user && Auth::needsProfileUpdate($user)) {
            Response::redirect(base_path('/user/editar?force=1'));
        }
        Response::redirect(base_path('/dashboard'));
    }

    public function registerForm(): void
    {
        $defaultTerms = "Ao criar uma conta, você declara ter lido e aceito os termos de uso do serviço, incluindo regras de acesso, limites e políticas de privacidade.";
        $accepted = !empty($_SESSION['accepted_terms']);
        echo $this->view('auth/register', [
            'csrf' => Csrf::token(),
            'terms' => Setting::get('terms_of_use', $defaultTerms),
            'stage' => $accepted ? 'form' : 'terms',
        ]);
    }

    public function acceptTerms(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/register'));
        }
        if (!isset($request->post['accept_terms'])) {
            Response::redirect(base_path('/register?error=terms'));
        }
        $_SESSION['accepted_terms'] = true;
        Response::redirect(base_path('/register'));
    }

    public function register(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/register'));
        }

        $birthDate = trim((string)($request->post['birth_date'] ?? ''));
        if ($birthDate === '') {
            $day = (int)($request->post['birth_day'] ?? 0);
            $month = (int)($request->post['birth_month'] ?? 0);
            $year = (int)($request->post['birth_year'] ?? 0);
            if ($day > 0 && $month > 0 && $year > 0) {
                $birthDate = sprintf('%02d-%02d-%04d', $day, $month, $year);
            }
        }

        $data = [
            'username' => trim((string)($request->post['username'] ?? '')),
            'email' => trim((string)($request->post['email'] ?? '')),
            'phone' => trim((string)($request->post['phone'] ?? '')),
            'phone_country' => trim((string)($request->post['phone_country'] ?? '')),
            'phone_has_whatsapp' => isset($request->post['no_whatsapp']) ? 0 : 1,
            'birth_date' => $birthDate,
            'password' => (string)($request->post['password'] ?? ''),
            'password_confirm' => (string)($request->post['password_confirm'] ?? ''),
            'referral' => trim((string)($request->post['referral'] ?? '')),
            'accept_terms' => isset($request->post['accept_terms']),
        ];

        $errors = [];
        if (empty($_SESSION['accepted_terms'])) {
            $errors[] = 'Você precisa ler e aceitar os termos antes de se cadastrar.';
        }
        if (!Validation::username($data['username'])) {
            $errors[] = 'Usuário inválido.';
        }
        if (!Validation::email($data['email'])) {
            $errors[] = 'Email inválido.';
        }
        if (EmailBlocklist::isBlocked($data['email'])) {
            $errors[] = 'Provedor de email bloqueado.';
        }
        if (UsernameBlocklist::isBlocked($data['username'])) {
            $errors[] = 'Nome de usuario bloqueado.';
        }
        if (!Validation::phone($data['phone'])) {
            $errors[] = 'Telefone inválido.';
        }
        if (!Validation::birthDate($data['birth_date'])) {
            $errors[] = 'Data de nascimento inválida.';
        }
        if (!Validation::password($data['password'])) {
            $errors[] = 'Senha inválida.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Senhas não conferem.';
        }
        if (User::findByUsername($data['username'])) {
            $errors[] = 'Usuário já cadastrado.';
        }
        if (User::findByEmail($data['email'])) {
            $errors[] = 'Email já cadastrado.';
        }
        if (!$data['accept_terms']) {
            $errors[] = 'Você precisa aceitar os termos de uso.';
        }

        if ($errors) {
            $defaultTerms = "Ao criar uma conta, você declara ter lido e aceito os termos de uso do serviço, incluindo regras de acesso, limites e políticas de privacidade.";
            echo $this->view('auth/register', [
                'errors' => $errors,
                'csrf' => Csrf::token(),
                'terms' => Setting::get('terms_of_use', $defaultTerms),
                'stage' => 'form',
            ]);
            return;
        }

        $pdo = \App\Core\Database::connection();
        $pdo->beginTransaction();
        try {
            $role = User::countSuperadmins() === 0 ? 'superadmin' : 'user';
            $referrerId = null;
            if ($data['referral'] !== '') {
                $stmt = $pdo->prepare('SELECT id FROM users WHERE referral_code = :c');
                $stmt->execute(['c' => $data['referral']]);
                $referrerId = $stmt->fetch()['id'] ?? null;
            }
            $referralCode = bin2hex(random_bytes(4));
            $userId = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'phone_country' => $data['phone_country'],
                'phone_has_whatsapp' => $data['phone_has_whatsapp'],
                'birth_date' => $data['birth_date'],
                'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
                'access_tier' => 'user',
                'role' => $role,
                'referral_code' => $referralCode,
                'referrer_id' => $referrerId,
                'ip_cadastro' => $request->ip(),
                'ip_ultimo_acesso' => $request->ip(),
                'ip_penultimo_acesso' => null,
            ]);
            $pdo->commit();
            Audit::log('register', $userId, ['ip' => $request->ip()]);
            unset($_SESSION['accepted_terms']);
            Response::redirect(base_path('/?registered=1'));
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Logger::error('register_failed', ['error' => $e->getMessage()]);
            echo $this->view('auth/register', ['errors' => ['Erro ao registrar.'], 'csrf' => Csrf::token()]);
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Response::redirect(base_path('/'));
    }

    public function resetForm(): void
    {
        $token = (string)($_GET['token'] ?? '');
        echo $this->view('auth/reset', ['token' => $token, 'csrf' => Csrf::token()]);
    }

    public function resetSubmit(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/'));
        }
        $token = (string)($request->post['token'] ?? '');
        $password = (string)($request->post['password'] ?? '');
        $confirm = (string)($request->post['password_confirm'] ?? '');

        if (!\App\Core\Validation::password($password) || $password !== $confirm) {
            echo $this->view('auth/reset', ['token' => $token, 'error' => 'Senha inválida.', 'csrf' => Csrf::token()]);
            return;
        }

        $userId = PasswordReset::validate($token);
        if (!$userId) {
            echo $this->view('auth/reset', ['token' => $token, 'error' => 'Token inválido ou expirado.', 'csrf' => Csrf::token()]);
            return;
        }

        $stmt = \App\Core\Database::connection()->prepare('UPDATE users SET password_hash = :ph WHERE id = :id');
        $stmt->execute(['ph' => password_hash($password, PASSWORD_DEFAULT), 'id' => $userId]);
        PasswordReset::consume($token);
        Response::redirect(base_path('/?reset=1'));
    }
}
