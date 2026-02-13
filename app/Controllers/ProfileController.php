<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;
use App\Core\Request;
use App\Core\Csrf;
use App\Core\Validation;
use App\Core\Database;
use App\Models\Payment;
use App\Models\Package;
use App\Models\LoginHistory;
use App\Models\ContentEvent;
use App\Models\User;
use App\Models\AvatarGallery;
use App\Models\AuditLog;

final class ProfileController extends Controller
{
    public function show(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $data = $this->profileViewData($user);
        $data['profileTitle'] = 'Meu perfil';
        $data['profileBase'] = '/perfil';
        $data['canEditProfile'] = ($user['access_tier'] ?? '') !== 'restrito';
        $data['isAdminView'] = false;
        echo $this->view('profile/show', $data);
    }

    public function showByUsername(Request $request, string $usuario): void
    {
        $viewer = Auth::user();
        if (!$viewer || !Auth::isAdmin($viewer)) {
            Response::redirect(base_path('/'));
        }
        $username = trim(rawurldecode($usuario));
        if ($username === '') {
            Response::redirect(base_path('/admin/users'));
        }
        $target = User::findByUsername($username);
        if (!$target) {
            Response::redirect(base_path('/admin/users'));
        }
        $data = $this->profileViewData($target);
        $data['profileTitle'] = 'Perfil do usuario';
        $data['profileBase'] = '/perfil/' . rawurlencode((string)($target['username'] ?? ''));
        $data['canEditProfile'] = false;
        $data['isAdminView'] = true;
        echo $this->view('profile/show', $data);
    }

    public function editForm(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if (($user['access_tier'] ?? '') === 'restrito' && !Auth::needsProfileUpdate($user)) {
            Response::redirect(base_path('/perfil'));
        }
        echo $this->view('profile/edit', $this->editViewData($user));
    }

    public function editUpdate(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/user/editar'));
        }

        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if (($user['access_tier'] ?? '') === 'restrito' && !Auth::needsProfileUpdate($user)) {
            Response::redirect(base_path('/perfil'));
        }

        $username = mb_strtolower(trim((string)($request->post['username'] ?? '')));
        $email = mb_strtolower(trim((string)($request->post['email'] ?? '')));
        $phone = Validation::normalizePhone(trim((string)($request->post['phone'] ?? '')));
        $phoneCountry = preg_replace('/\D+/', '', (string)($request->post['phone_country'] ?? '')) ?? '';
        $phoneWhatsApp = !empty($request->post['phone_has_whatsapp']) ? 1 : 0;
        $birthDate = trim((string)($request->post['birth_date'] ?? ''));

        $currentUsername = (string)($user['username'] ?? '');
        $currentEmail = (string)($user['email'] ?? '');
        $needsUpdate = Auth::needsProfileUpdate($user);

        if ($username === '' || $email === '') {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Usuario e email sao obrigatorios.'));
            return;
        }

        if ($needsUpdate && mb_strlen($username) < 5) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Usuario precisa ter no minimo 5 caracteres.'));
            return;
        }

        if ($username !== $currentUsername && !Validation::username($username)) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Usuario invalido.'));
            return;
        }

        if ($email !== $currentEmail && !Validation::email($email)) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Email invalido.'));
            return;
        }

        if ($needsUpdate && ($phone === '' || !Validation::phone($phone))) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Telefone invalido. Use o formato 11 9 9999-9999.'));
            return;
        }

        if ($phone !== '' && !Validation::phone($phone)) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Telefone invalido. Use o formato 11 9 9999-9999.'));
            return;
        }

        if ($needsUpdate && $phoneCountry === '') {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Informe o DDI do telefone.'));
            return;
        }

        if ($phone !== '' && $phoneCountry === '') {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Informe o DDI do telefone.'));
            return;
        }

        if ($phone === '') {
            $phoneCountry = '';
            $phoneWhatsApp = 0;
        }

        if ($birthDate === '') {
            $birthDate = '0000-00-00';
        }

        if ($needsUpdate && ($birthDate === '0000-00-00' || !Validation::birthDate($birthDate))) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Data de nascimento invalida.'));
            return;
        }

        if ($birthDate !== '0000-00-00' && !Validation::birthDate($birthDate)) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Data de nascimento invalida.'));
            return;
        }

        $existing = User::findByUsername($username);
        if ($existing && (int)$existing['id'] !== (int)$user['id']) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Usuario ja existe.'));
            return;
        }

        $existing = User::findByEmail($email);
        if ($existing && (int)$existing['id'] !== (int)$user['id']) {
            echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Email ja esta em uso.'));
            return;
        }

        $avatarPath = (string)($user['avatar_path'] ?? '');
        $galleryId = (int)($request->post['avatar_gallery_id'] ?? 0);
        $file = $request->files['avatar'] ?? null;
        if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Falha ao enviar avatar.'));
                return;
            }
            $dir = $this->publicUploadsRoot() . '/avatars/users';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $error = null;
            if (!$this->validateAvatarUpload($file, $dir, $error)) {
                $message = 'Formato de avatar invalido.';
                if ($error === 'size') {
                    $message = 'Avatar muito grande. Maximo 2MB.';
                } elseif ($error === 'dim') {
                    $message = 'Avatar deve ter no maximo 500x500px.';
                } elseif ($error === 'space') {
                    $message = 'Espaco insuficiente para salvar o avatar.';
                }
                echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, $message));
                return;
            }
            $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
            $filename = 'user_' . (int)$user['id'] . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $target = $dir . '/' . $filename;
            if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
                echo $this->view('profile/edit', $this->editViewData($user, (array)$request->post, 'Nao foi possivel salvar o avatar.'));
                return;
            }
            $avatarPath = 'uploads/avatars/users/' . $filename;
        } elseif ($galleryId > 0) {
            $galleryAvatar = AvatarGallery::find($galleryId);
            if ($galleryAvatar && !empty($galleryAvatar['is_active'])) {
                $avatarPath = (string)($galleryAvatar['file_path'] ?? $avatarPath);
            }
        }

        User::updateProfileSelf((int)$user['id'], [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'phone_country' => $phoneCountry,
            'phone_has_whatsapp' => $phoneWhatsApp,
            'birth_date' => $birthDate,
            'avatar_path' => $avatarPath,
        ]);

        $freshUser = User::findById((int)$user['id']) ?? $user;
        echo $this->view('profile/edit', $this->editViewData($freshUser, [], null, 'Perfil atualizado com sucesso.'));
    }

    private function editViewData(array $user, array $form = [], ?string $error = null, ?string $success = null): array
    {
        $avatars = AvatarGallery::activeAll();
        $selectedAvatarId = 0;
        $currentPath = (string)($user['avatar_path'] ?? '');
        if ($currentPath !== '') {
            foreach ($avatars as $av) {
                if ((string)($av['file_path'] ?? '') === $currentPath) {
                    $selectedAvatarId = (int)($av['id'] ?? 0);
                    break;
                }
            }
        }
        return [
            'user' => $user,
            'csrf' => Csrf::token(),
            'avatars' => $avatars,
            'selectedAvatarId' => $selectedAvatarId,
            'form' => $form,
            'error' => $error,
            'success' => $success,
        ];
    }

    private function profileViewData(array $user): array
    {
        $readPage = isset($_GET['reads_page']) ? (int)$_GET['reads_page'] : 1;
        $readPerPage = 10;

        $paymentsAll = Payment::byUserAll((int)$user['id']);
        $paymentsPreview = array_slice($paymentsAll, 0, 10);
        $paymentsMore = array_slice($paymentsAll, 10);
        $packages = Package::all();
        $packageMap = [];
        foreach ($packages as $pkg) {
            $pid = (int)($pkg['id'] ?? 0);
            if ($pid > 0) {
                $packageMap[$pid] = $pkg;
            }
        }
        $loginHistoryAll = LoginHistory::forUserAll((int)$user['id']);
        $loginHistoryPreview = array_slice($loginHistoryAll, 0, 10);
        $loginHistoryMore = array_slice($loginHistoryAll, 10);
        $loginFails = AuditLog::loginFailsForUsername((string)($user['username'] ?? ''), 10);

        $readsTotal = ContentEvent::countReadsForUser((int)$user['id']);
        $readPages = (int)max(1, (int)ceil($readsTotal / $readPerPage));
        $readPage = max(1, min($readPage, $readPages));
        $readingHistory = ContentEvent::pagedReadsForUser((int)$user['id'], $readPage, $readPerPage);

        return [
            'user' => $user,
            'payments' => $paymentsPreview,
            'paymentsMore' => $paymentsMore,
            'packageMap' => $packageMap,
            'loginHistory' => $loginHistoryPreview,
            'loginHistoryMore' => $loginHistoryMore,
            'loginFails' => $loginFails,
            'readingHistory' => $readingHistory,
            'readPage' => $readPage,
            'readPages' => $readPages,
        ];
    }

    private function publicUploadsRoot(): string
    {
        return dirname(__DIR__, 2) . '/public/uploads';
    }

    private function validateAvatarUpload(array $file, string $dir, ?string &$error): bool
    {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $maxBytes = 2 * 1024 * 1024;

        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            $error = 'type';
            return false;
        }
        $size = (int)($file['size'] ?? 0);
        if ($size <= 0) {
            $error = 'upload';
            return false;
        }
        if ($size > $maxBytes) {
            $error = 'size';
            return false;
        }
        $free = @disk_free_space($dir);
        if ($free !== false && $size > $free) {
            $error = 'space';
            return false;
        }

        $info = @getimagesize((string)($file['tmp_name'] ?? ''));
        if ($info === false) {
            $error = 'type';
            return false;
        }
        $mime = (string)($info['mime'] ?? '');
        if (!in_array($mime, $allowedMime, true)) {
            $error = 'type';
            return false;
        }
        $width = (int)($info[0] ?? 0);
        $height = (int)($info[1] ?? 0);
        if ($width <= 0 || $height <= 0 || $width > 500 || $height > 500) {
            $error = 'dim';
            return false;
        }
        return true;
    }

    public function passwordForm(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        echo $this->view('profile/password', ['user' => $user, 'csrf' => Csrf::token()]);
    }

    public function passwordUpdate(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/perfil/senha'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }

        $current = (string)($request->post['current_password'] ?? '');
        $password = (string)($request->post['password'] ?? '');
        $confirm = (string)($request->post['password_confirm'] ?? '');

        if (!password_verify($current, (string)($user['password_hash'] ?? ''))) {
            echo $this->view('profile/password', ['user' => $user, 'csrf' => Csrf::token(), 'error' => 'Senha atual incorreta.']);
            return;
        }
        if (!Validation::password($password)) {
            echo $this->view('profile/password', ['user' => $user, 'csrf' => Csrf::token(), 'error' => 'Nova senha invalida.']);
            return;
        }
        if ($password !== $confirm) {
            echo $this->view('profile/password', ['user' => $user, 'csrf' => Csrf::token(), 'error' => 'Senhas nao conferem.']);
            return;
        }

        $stmt = Database::connection()->prepare('UPDATE users SET password_hash = :ph WHERE id = :id');
        $stmt->execute(['ph' => password_hash($password, PASSWORD_DEFAULT), 'id' => (int)$user['id']]);

        echo $this->view('profile/password', ['user' => $user, 'csrf' => Csrf::token(), 'success' => 'Senha atualizada com sucesso.']);
    }
}
