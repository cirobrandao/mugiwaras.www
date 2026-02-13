<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Auth;
use App\Core\Database;
use App\Core\Validation;
use App\Core\Audit;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\Category;
use App\Models\Package;
use App\Models\Payment;
use App\Models\LoginHistory;
use App\Models\Voucher;

final class UsersController extends Controller
{
    public function index(): void
    {
        echo $this->view('admin/users', $this->baseIndexData());
    }

    public function update(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $tier = (string)($request->post['access_tier'] ?? 'user');

        $current = Auth::user();
        $target = User::findById($id);
        if (!$current || !$target) {
            Response::redirect(base_path('/admin/users'));
        }

        if ($target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }

        $username = mb_strtolower(trim((string)($request->post['username'] ?? (string)$target['username'])));
        $email = mb_strtolower(trim((string)($request->post['email'] ?? (string)$target['email'])));
        $phone = Validation::normalizePhone(trim((string)($request->post['phone'] ?? (string)$target['phone'])));
        $phoneCountry = preg_replace('/\D+/', '', trim((string)($request->post['phone_country'] ?? (string)$target['phone_country']))) ?? '';
        $birthDate = trim((string)($request->post['birth_date'] ?? (string)$target['birth_date']));
        $observations = trim((string)($request->post['observations'] ?? (string)($target['observations'] ?? '')));
        $phoneWhatsApp = (int)($request->post['phone_has_whatsapp'] ?? (int)($target['phone_has_whatsapp'] ?? 0));

        if ($username === '' || $email === '' || $phone === '' || $phoneCountry === '' || $birthDate === '') {
            Response::redirect(base_path('/admin/users'));
        }

        User::updateProfile($id, [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'phone_country' => $phoneCountry,
            'phone_has_whatsapp' => $phoneWhatsApp,
            'birth_date' => $birthDate,
            'observations' => $observations,
            'access_tier' => $tier,
        ]);
        Response::redirect(base_path('/admin/users'));
    }

    public function restrict(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin' || $target['role'] === 'admin') {
            Response::redirect(base_path('/admin/users'));
        }

        $isRestricted = ($target['access_tier'] ?? '') === 'restrito';
        $newTier = $isRestricted ? 'user' : 'restrito';

        User::updateRoleFlags($id, 'user', 0, 0, 0);
        User::setAccessTier($id, $newTier);
        Response::redirect(base_path('/admin/users'));
    }

    public function team(): void
    {
        $currentUser = Auth::user();
        echo $this->view('admin/team', [
            'teamMembers' => User::teamMembers(),
            'userPool' => User::nonStaff(),
            'csrf' => Csrf::token(),
            'currentUser' => $currentUser,
        ]);
    }

    public function teamUpdate(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/team'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $role = (string)($request->post['role'] ?? '');

        $current = Auth::user();
        $target = User::findById($id);
        if (!$current || !$target) {
            Response::redirect(base_path('/admin/team'));
        }

        if ($target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/team'));
        }

        $currentRole = (string)($current['role'] ?? 'user');
        if ($role === '') {
            $role = (string)($target['role'] ?? 'user');
        }

        $supportAgent = !empty($request->post['support_agent']) ? 1 : 0;
        $uploaderAgent = !empty($request->post['uploader_agent']) ? 1 : 0;
        $moderatorAgent = !empty($request->post['moderator_agent']) ? 1 : 0;

        $isSuper = $currentRole === 'superadmin';
        $isAdmin = $currentRole === 'admin' || $isSuper;
        $isModerator = ($currentRole === 'equipe' && !empty($current['moderator_agent']));

        if ($role === '') {
            $role = (string)($target['role'] ?? 'user');
        }
        if ($role === 'superadmin') {
            Response::redirect(base_path('/admin/team'));
        }

        if ($isSuper) {
            // superadmin pode gerir admin e equipe
        } elseif ($isAdmin) {
            if ($role === 'admin') {
                Response::redirect(base_path('/admin/team'));
            }
            if (($target['role'] ?? '') === 'admin') {
                Response::redirect(base_path('/admin/team'));
            }
        } elseif ($isModerator) {
            if ($role !== 'equipe') {
                Response::redirect(base_path('/admin/team'));
            }
            if (in_array(($target['role'] ?? ''), ['admin','superadmin'], true)) {
                Response::redirect(base_path('/admin/team'));
            }
        } else {
            Response::redirect(base_path('/admin/team'));
        }

        if (!$isAdmin) {
            $supportAgent = (int)($target['support_agent'] ?? 0);
            $moderatorAgent = (int)($target['moderator_agent'] ?? 0);
        }
        if (!$isAdmin && !$isModerator) {
            $uploaderAgent = (int)($target['uploader_agent'] ?? 0);
        }

        if ($role === 'admin' || $role === 'superadmin') {
            $supportAgent = 0;
            $uploaderAgent = 0;
            $moderatorAgent = 0;
        }

        if ($role === 'equipe' && ($supportAgent > 0 || $uploaderAgent > 0 || $moderatorAgent > 0) === false) {
            $role = 'equipe';
        }

        User::updateRoleFlags($id, $role, $supportAgent > 0 ? 1 : 0, $uploaderAgent > 0 ? 1 : 0, $moderatorAgent > 0 ? 1 : 0);
        Response::redirect(base_path('/admin/team'));
    }

    public function lock(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }
        $until = (string)($request->post['lock_until'] ?? '2099-12-31 00:00:00');
        User::setLockUntil($id, $until);
        Response::redirect(base_path('/admin/users'));
    }

    public function unlock(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }
        User::setLockUntil($id, null);
        Response::redirect(base_path('/admin/users'));
    }

    public function teamToggle(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id <= 0) {
            Response::redirect(base_path('/admin/users'));
        }
        $target = User::findById($id);
        if (!$target) {
            Response::redirect(base_path('/admin/users'));
        }
        if (in_array((string)($target['role'] ?? ''), ['superadmin', 'admin'], true)) {
            Response::redirect(base_path('/admin/users'));
        }
        $isTeam = (string)($target['role'] ?? '') === 'equipe'
            || !empty($target['support_agent'])
            || !empty($target['uploader_agent'])
            || !empty($target['moderator_agent']);

        if ($isTeam) {
            User::updateRoleFlags($id, 'user', 0, 0, 0);
            Audit::log('team_remove', (int)$id, ['by' => (int)($request->session['user_id'] ?? 0)]);
        } else {
            User::updateRoleFlags($id, 'equipe', 0, 0, 0);
            Audit::log('team_add', (int)$id, ['by' => (int)($request->session['user_id'] ?? 0)]);
        }
        Response::redirect(base_path('/admin/users'));
    }

    public function reset(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $target = User::findById($id);
        if (!$target || $target['role'] === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }
        $token = bin2hex(random_bytes(24));
        PasswordReset::create($id, $token, 60);
        Response::redirect(base_path('/admin/users?reset=' . $token . '&uid=' . $id));
    }

    public function assignPackage(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users'));
        }
        $userId = (int)($request->post['id'] ?? 0);
        $packageId = (int)($request->post['package_id'] ?? 0);
        $assignOnly = !empty($request->post['assign_only']);
        $months = (int)($request->post['months'] ?? 1);
        if ($assignOnly) {
            $months = 0;
        } else {
            if ($months < 1) {
                $months = 1;
            } elseif ($months > 12) {
                $months = 12;
            }
        }

        $target = User::findById($userId);
        $package = Package::find($packageId);
        if (!$target || !$package) {
            Response::redirect(base_path('/admin/users'));
        }
        if (($target['role'] ?? '') === 'superadmin') {
            Response::redirect(base_path('/admin/users'));
        }

        if (($target['access_tier'] ?? '') === 'restrito') {
            User::setAccessTier($userId, 'user');
        }

        Payment::create([
            'uid' => $userId,
            'pid' => $packageId,
            'status' => 'approved',
            'months' => $months,
        ]);

        if (!$assignOnly) {
            if ((int)($package['bonus_credits'] ?? 0) > 0) {
                User::addCredits($userId, (int)$package['bonus_credits']);
            }
            if ((int)($package['subscription_days'] ?? 0) > 0) {
                $days = (int)$package['subscription_days'] * $months;
                User::extendSubscription($userId, $days);
            }
        }

        Audit::log('user_package_assign', $_SESSION['user_id'] ?? null, [
            'user_id' => $userId,
            'package_id' => $packageId,
            'months' => $months,
            'assign_only' => $assignOnly ? 1 : 0,
        ]);
        Response::redirect(base_path('/admin/users'));
    }

    public function importPage(): void
    {
        echo $this->view('admin/users_import', $this->baseImportData());
    }

    public function importPreview(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users/import'));
        }

        $json = trim((string)($request->post['import_json'] ?? ''));
        $data = $this->baseImportData();
        $data['importJson'] = $json;
        $errors = [];
        $rows = $this->parseImportJson($json, $errors);
        if ($errors) {
            $data['importErrors'] = $errors;
            echo $this->view('admin/users_import', $data);
            return;
        }

        $preview = [];
        $summary = [
            'total' => count($rows),
            'valid' => 0,
            'errors' => 0,
            'warnings' => 0,
            'matched' => 0,
            'create' => 0,
            'update' => 0,
        ];
        $currentUser = $data['currentUser'] ?? null;

        $seenEmails = [];
        $seenUsernames = [];
        foreach ($rows as $index => $row) {
            if (!is_array($row)) {
                $preview[] = [
                    'index' => $index,
                    'mapped' => [],
                    'errors' => ['Registro invalido.'],
                    'warnings' => [],
                    'match' => null,
                    'default_action' => 'skip',
                ];
                $summary['errors']++;
                continue;
            }

            $mapped = $this->mapImportRow($row, $request->ip());
            $emailKey = strtolower(trim((string)($mapped['email'] ?? '')));
            if ($emailKey !== '') {
                if (!empty($seenEmails[$emailKey])) {
                    $mapped['errors'][] = 'Email repetido no arquivo.';
                } else {
                    $seenEmails[$emailKey] = true;
                }
            }
            $usernameKey = strtolower(trim((string)($mapped['username'] ?? '')));
            if ($usernameKey !== '') {
                if (!empty($seenUsernames[$usernameKey])) {
                    $mapped['errors'][] = 'Username repetido no arquivo.';
                } else {
                    $seenUsernames[$usernameKey] = true;
                }
            }
            $match = null;
            if (!empty($mapped['email'])) {
                $match = User::findByEmail((string)$mapped['email']);
            }
            if (!$match && !empty($mapped['username'])) {
                $match = User::findByUsername((string)$mapped['username']);
            }

            if ($match) {
                $summary['matched']++;
                if (($match['role'] ?? '') === 'superadmin' && !Auth::isSuperadmin($currentUser)) {
                    $mapped['errors'][] = 'Usuario superadmin nao pode ser alterado.';
                }
            }

            if (($mapped['role'] ?? '') === 'superadmin' && !Auth::isSuperadmin($currentUser)) {
                $mapped['errors'][] = 'Somente superadmin pode importar superadmin.';
            }

            $hasErrors = !empty($mapped['errors']);
            if ($hasErrors) {
                $summary['errors']++;
            } else {
                $summary['valid']++;
            }
            if (!empty($mapped['warnings'])) {
                $summary['warnings']++;
            }

            $defaultAction = $match ? 'update' : 'create';
            if ($hasErrors) {
                $defaultAction = 'skip';
            }
            if ($defaultAction === 'create') {
                $summary['create']++;
            }
            if ($defaultAction === 'update') {
                $summary['update']++;
            }

            $preview[] = [
                'index' => $index,
                'mapped' => $mapped,
                'errors' => $mapped['errors'],
                'warnings' => $mapped['warnings'],
                'match' => $match,
                'default_action' => $defaultAction,
            ];
        }

        $data['importPreview'] = $preview;
        $data['importSummary'] = $summary;
        echo $this->view('admin/users_import', $data);
    }

    public function importApply(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/users/import'));
        }

        $json = trim((string)($request->post['import_json'] ?? ''));
        $data = $this->baseImportData();
        $data['importJson'] = $json;
        $errors = [];
        $rows = $this->parseImportJson($json, $errors);
        if ($errors) {
            $data['importErrors'] = $errors;
            echo $this->view('admin/users_import', $data);
            return;
        }

        $selected = $request->post['import_rows'] ?? [];
        $actions = $request->post['import_action'] ?? [];
        $selectedSet = [];
        foreach ($selected as $idx) {
            $selectedSet[(int)$idx] = true;
        }

        $result = [
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'details' => [],
        ];
        $currentUser = $data['currentUser'] ?? null;
        $pdo = Database::connection();
        $defaultPackageId = 0;
        $defaultPackages = Package::all();
        if (!empty($defaultPackages)) {
            $defaultPackageId = (int)($defaultPackages[0]['id'] ?? 0);
        }

        $seenEmails = [];
        $seenUsernames = [];
        foreach ($rows as $index => $row) {
            if (!isset($selectedSet[(int)$index])) {
                continue;
            }
            $action = (string)($actions[$index] ?? 'skip');
            if ($action === 'skip') {
                $result['skipped']++;
                continue;
            }
            if (!is_array($row)) {
                $result['failed']++;
                $result['details'][] = ['index' => $index, 'error' => 'Registro invalido.'];
                continue;
            }

            $mapped = $this->mapImportRow($row, $request->ip());
            $emailKey = strtolower(trim((string)($mapped['email'] ?? '')));
            if ($emailKey !== '') {
                if (!empty($seenEmails[$emailKey])) {
                    $result['failed']++;
                    $result['details'][] = ['index' => $index, 'error' => 'Email repetido no arquivo.'];
                    continue;
                }
                $seenEmails[$emailKey] = true;
            }
            $usernameKey = strtolower(trim((string)($mapped['username'] ?? '')));
            if ($usernameKey !== '') {
                if (!empty($seenUsernames[$usernameKey])) {
                    $result['failed']++;
                    $result['details'][] = ['index' => $index, 'error' => 'Username repetido no arquivo.'];
                    continue;
                }
                $seenUsernames[$usernameKey] = true;
            }
            if (!empty($mapped['errors'])) {
                $result['failed']++;
                $result['details'][] = ['index' => $index, 'error' => implode(' ', $mapped['errors'])];
                continue;
            }

            if (($mapped['role'] ?? '') === 'superadmin' && !Auth::isSuperadmin($currentUser)) {
                $result['failed']++;
                $result['details'][] = ['index' => $index, 'error' => 'Somente superadmin pode importar superadmin.'];
                continue;
            }

            $match = null;
            if (!empty($mapped['email'])) {
                $match = User::findByEmail((string)$mapped['email']);
            }
            if (!$match && !empty($mapped['username'])) {
                $match = User::findByUsername((string)$mapped['username']);
            }

            if ($action === 'update' && !$match) {
                $result['failed']++;
                $result['details'][] = ['index' => $index, 'error' => 'Usuario nao encontrado para atualizar.'];
                continue;
            }
            if ($action === 'create' && $match) {
                $result['failed']++;
                $result['details'][] = ['index' => $index, 'error' => 'Usuario ja existe.'];
                continue;
            }

            if ($match) {
                $matchId = (int)($match['id'] ?? 0);
                $byEmail = User::findByEmail((string)$mapped['email']);
                if ($byEmail && (int)$byEmail['id'] !== $matchId) {
                    $result['failed']++;
                    $result['details'][] = ['index' => $index, 'error' => 'Email ja em uso por outro usuario.'];
                    continue;
                }
                $byUser = User::findByUsername((string)$mapped['username']);
                if ($byUser && (int)$byUser['id'] !== $matchId) {
                    $result['failed']++;
                    $result['details'][] = ['index' => $index, 'error' => 'Username ja em uso por outro usuario.'];
                    continue;
                }
                if (($match['role'] ?? '') === 'superadmin' && !Auth::isSuperadmin($currentUser)) {
                    $result['failed']++;
                    $result['details'][] = ['index' => $index, 'error' => 'Usuario superadmin nao pode ser alterado.'];
                    continue;
                }
            }

            try {
                if ($action === 'create') {
                    $referralCode = $this->generateReferralCode($pdo);
                    $stmt = $pdo->prepare('INSERT INTO users (username,email,phone,phone_country,phone_has_whatsapp,birth_date,observations,password_hash,access_tier,role,referral_code,referrer_id,ip_cadastro,ip_ultimo_acesso,ip_penultimo_acesso,data_registro,data_ultimo_login,subscription_expires_at,lock_until) VALUES (:username,:email,:phone,:phone_country,:phone_has_whatsapp,:birth_date,:observations,:password_hash,:access_tier,:role,:referral_code,:referrer_id,:ip_cadastro,:ip_ultimo_acesso,:ip_penultimo_acesso,:data_registro,:data_ultimo_login,:subscription_expires_at,:lock_until)');
                    $stmt->execute([
                        'username' => $mapped['username'],
                        'email' => $mapped['email'],
                        'phone' => $mapped['phone'],
                        'phone_country' => $mapped['phone_country'],
                        'phone_has_whatsapp' => $mapped['phone_has_whatsapp'],
                        'birth_date' => $mapped['birth_date'],
                        'observations' => $mapped['observations'],
                        'password_hash' => $mapped['password_hash'],
                        'access_tier' => $mapped['access_tier'],
                        'role' => $mapped['role'],
                        'referral_code' => $referralCode,
                        'referrer_id' => null,
                        'ip_cadastro' => $mapped['ip_cadastro'],
                        'ip_ultimo_acesso' => $mapped['ip_ultimo_acesso'],
                        'ip_penultimo_acesso' => $mapped['ip_penultimo_acesso'],
                        'data_registro' => $mapped['data_registro'] ?? date('Y-m-d H:i:s'),
                        'data_ultimo_login' => $mapped['data_ultimo_login'],
                        'subscription_expires_at' => $mapped['subscription_expires_at'],
                        'lock_until' => $mapped['lock_until'],
                    ]);
                } else {
                    $matchId = (int)($match['id'] ?? 0);
                    $updateSql = 'UPDATE users SET username = :username, email = :email, phone = :phone, phone_country = :phone_country, phone_has_whatsapp = :phone_has_whatsapp, birth_date = :birth_date, observations = :observations, password_hash = :password_hash, access_tier = :access_tier, role = :role, ip_ultimo_acesso = :ip_ultimo_acesso, ip_penultimo_acesso = :ip_penultimo_acesso, data_ultimo_login = :data_ultimo_login, subscription_expires_at = :subscription_expires_at';
                    if (!empty($mapped['lock_until_set'])) {
                        $updateSql .= ', lock_until = :lock_until';
                    }
                    $updateSql .= ' WHERE id = :id';

                    $params = [
                        'username' => $mapped['username'],
                        'email' => $mapped['email'],
                        'phone' => $mapped['phone'],
                        'phone_country' => $mapped['phone_country'],
                        'phone_has_whatsapp' => $mapped['phone_has_whatsapp'],
                        'birth_date' => $mapped['birth_date'],
                        'observations' => $mapped['observations'],
                        'password_hash' => $mapped['password_hash'],
                        'access_tier' => $mapped['access_tier'],
                        'role' => $mapped['role'],
                        'ip_ultimo_acesso' => $mapped['ip_ultimo_acesso'],
                        'ip_penultimo_acesso' => $mapped['ip_penultimo_acesso'],
                        'data_ultimo_login' => $mapped['data_ultimo_login'],
                        'subscription_expires_at' => $mapped['subscription_expires_at'],
                        'id' => $matchId,
                    ];
                    if (!empty($mapped['lock_until_set'])) {
                        $params['lock_until'] = $mapped['lock_until'];
                    }
                    $stmt = $pdo->prepare($updateSql);
                    $stmt->execute($params);
                }
                $importedUserId = $action === 'create' ? (int)$pdo->lastInsertId() : (int)($match['id'] ?? 0);
                if ($defaultPackageId > 0 && !empty($mapped['subscription_expires_at'])) {
                    $expTs = strtotime((string)$mapped['subscription_expires_at']);
                    if ($expTs !== false && $expTs > time()) {
                        $existing = Payment::latestApprovedByUser($importedUserId);
                        if (!$existing) {
                            Payment::create([
                                'uid' => $importedUserId,
                                'pid' => $defaultPackageId,
                                'status' => 'approved',
                                'months' => 0,
                            ]);
                        }
                    }
                }
                $result['imported']++;
            } catch (\Throwable $e) {
                $result['failed']++;
                $result['details'][] = ['index' => $index, 'error' => 'Falha ao importar.'];
            }
        }

        Audit::log('user_import', (int)($currentUser['id'] ?? 0), [
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'failed' => $result['failed'],
        ]);

        $data['importResult'] = $result;
        echo $this->view('admin/users_import', $data);
    }

    private function baseIndexData(): array
    {
        $currentUser = Auth::user();
        $filters = [
            'q' => trim((string)($_GET['q'] ?? '')),
            'tier' => trim((string)($_GET['tier'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
            'package' => trim((string)($_GET['package'] ?? '')),
        ];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = (int)($_GET['perPage'] ?? 25);
        if ($perPage < 10) {
            $perPage = 10;
        }
        if ($perPage > 200) {
            $perPage = 200;
        }
        $total = User::countFiltered($filters);
        $pages = (int)max(1, ceil($total / $perPage));
        if ($page > $pages) {
            $page = $pages;
        }
        $users = User::pagedFiltered($page, $perPage, $filters);
        $userIds = array_map(static fn ($u) => (int)($u['id'] ?? 0), $users);
        $latestPayments = Payment::latestApprovedByUsers($userIds);
        $paymentRows = Payment::historyByUsers($userIds);
        $voucherRows = Voucher::redemptionHistoryByUsers($userIds);
        $paymentHistory = [];
        foreach ($paymentRows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($paymentHistory[$uid])) {
                $paymentHistory[$uid] = [];
            }
            $paymentHistory[$uid][] = [
                'history_type' => 'payment',
                'history_date' => (string)($row['created_at'] ?? ''),
                'payment' => $row,
            ];
        }
        foreach ($voucherRows as $row) {
            $uid = (int)($row['user_id'] ?? 0);
            if ($uid <= 0) {
                continue;
            }
            if (!isset($paymentHistory[$uid])) {
                $paymentHistory[$uid] = [];
            }
            $paymentHistory[$uid][] = [
                'history_type' => 'voucher',
                'history_date' => (string)($row['redeemed_at'] ?? ''),
                'voucher' => $row,
            ];
        }
        foreach ($paymentHistory as &$historyItems) {
            usort($historyItems, static function (array $a, array $b): int {
                $aDate = strtotime((string)($a['history_date'] ?? '')) ?: 0;
                $bDate = strtotime((string)($b['history_date'] ?? '')) ?: 0;
                return $bDate <=> $aDate;
            });
        }
        unset($historyItems);
        $loginHistory = LoginHistory::forUsers($userIds, 10);
        $packages = Package::all();
        $packageIds = array_map(static fn ($p) => (int)($p['id'] ?? 0), $packages);
        $packageCategories = Package::categoriesMap($packageIds);
        $packageMap = [];
        foreach ($packages as $pkg) {
            $pid = (int)($pkg['id'] ?? 0);
            if ($pid > 0) {
                $packageMap[$pid] = $pkg;
            }
        }
        $categories = Category::all();
        $resetToken = $_GET['reset'] ?? null;
        $resetUserId = $_GET['uid'] ?? null;
        $resetUserName = null;
        if (!empty($resetUserId)) {
            $ru = User::findById((int)$resetUserId);
            if ($ru) {
                $resetUserName = (string)($ru['username'] ?? null);
            }
        }

        return [
            'users' => $users,
            'csrf' => Csrf::token(),
            'resetToken' => $resetToken,
            'resetUserId' => $resetUserId,
            'resetUserName' => $resetUserName,
            'currentUser' => $currentUser,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'pages' => $pages,
            'latestPayments' => $latestPayments,
            'paymentHistory' => $paymentHistory,
            'loginHistory' => $loginHistory,
            'packages' => $packages,
            'packageCategories' => $packageCategories,
            'packageMap' => $packageMap,
            'categories' => $categories,
            'filters' => $filters,
        ];
    }

    private function baseImportData(): array
    {
        return [
            'csrf' => Csrf::token(),
            'currentUser' => Auth::user(),
        ];
    }

    private function parseImportJson(string $json, array &$errors): array
    {
        if ($json === '') {
            $errors[] = 'JSON vazio.';
            return [];
        }
        $payload = json_decode($json, true);
        if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = 'JSON invalido: ' . json_last_error_msg();
            return [];
        }
        if ($payload === null) {
            $errors[] = 'JSON invalido.';
            return [];
        }
        if ($this->isAssoc($payload)) {
            return [$payload];
        }
        return $payload;
    }

    private function mapImportRow(array $row, string $defaultIp): array
    {
        $errors = [];
        $warnings = [];

        $username = trim((string)($row['username'] ?? ''));
        if ($username === '') {
            $errors[] = 'Username vazio.';
        }

        $email = trim((string)($row['email'] ?? ''));
        if ($email === '') {
            $errors[] = 'Email vazio.';
        } elseif (!Validation::email($email)) {
            $errors[] = 'Email invalido.';
        }

        $phoneRaw = trim((string)($row['phone'] ?? ''));
        $phone = $this->normalizePhone($phoneRaw, $warnings);
        if ($phone === '') {
            $warnings[] = 'Telefone vazio, importado sem numero.';
        }

        $phoneCountry = preg_replace('/\D+/', '', (string)($row['phone_country'] ?? '')) ?? '';
        if ($phoneCountry === '') {
            $phoneCountry = '55';
        }
        $phoneHasWhatsApp = isset($row['phone_has_whatsapp']) ? (int)$row['phone_has_whatsapp'] : 1;

        $birthRaw = trim((string)($row['birthdate'] ?? ($row['birth_date'] ?? '')));
        $birthDate = $this->normalizeBirthDate($birthRaw);
        if ($birthDate === null) {
            $birthDate = '0000-00-00';
            $warnings[] = 'Nascimento ausente, definido como 0000-00-00.';
        }

        $passwordHash = trim((string)($row['password_hash'] ?? ''));
        if ($passwordHash === '') {
            $errors[] = 'password_hash vazio.';
        }

        $observations = trim((string)($row['admin_note'] ?? ''));

        $accessTier = trim((string)($row['access_tier'] ?? ''));
        $roleRaw = strtolower(trim((string)($row['role'] ?? '')));
        $role = 'user';
        if ($roleRaw !== '') {
            if ($roleRaw === 'user') {
                // role user permitido
            } elseif ($roleRaw === 'vitalicio') {
                if ($accessTier === '') {
                    $accessTier = 'vitalicio';
                }
                $warnings[] = 'Role vitalicio convertido para tier.';
            } elseif (in_array($roleRaw, ['admin','superadmin','equipe'], true)) {
                $errors[] = 'Role nao permitido.';
            } else {
                $warnings[] = 'Role ignorado.';
            }
        }

        if ($accessTier === '') {
            $accessTier = 'user';
        }
        if (!in_array($accessTier, ['user','trial','assinante','restrito','vitalicio'], true)) {
            $warnings[] = 'access_tier invalido, usando user.';
            $accessTier = 'user';
        }
        if ($accessTier === 'vitalicio') {
            // permitido
        }

        $lockUntil = null;
        $lockUntilSet = false;
        if (array_key_exists('status', $row)) {
            $lockUntilSet = true;
            $statusRaw = strtolower(trim((string)($row['status'] ?? 'active')));
            if ($statusRaw !== '' && $statusRaw !== 'active') {
                $lockUntil = '2099-12-31 00:00:00';
                $warnings[] = 'Status nao ativo: usuario sera bloqueado.';
            }
        }

        $registeredAtRaw = (string)($row['registered_at'] ?? '');
        $registeredAt = $this->normalizeDateTime($registeredAtRaw);
        if (trim($registeredAtRaw) !== '' && $registeredAt === null) {
            $warnings[] = 'registered_at invalido.';
        }

        $lastLoginAt = null;

        $accessExpiresRaw = (string)($row['access_expires_at'] ?? '');
        $accessExpiresAt = $this->normalizeDateTime($accessExpiresRaw);
        if (trim($accessExpiresRaw) !== '' && $accessExpiresAt === null) {
            $warnings[] = 'access_expires_at invalido.';
        }

        if ($accessTier === 'vitalicio' && $accessExpiresAt !== null) {
            $warnings[] = 'Access expires ignorado para vitalicio.';
            $accessExpiresAt = null;
        }

        $ipCadastro = $defaultIp;
        $ipUltimo = null;
        $ipPenultimo = null;

        return [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'phone_country' => $phoneCountry,
            'phone_has_whatsapp' => $phoneHasWhatsApp,
            'birth_date' => $birthDate,
            'observations' => $observations,
            'password_hash' => $passwordHash,
            'access_tier' => $accessTier,
            'role' => $role,
            'data_registro' => $registeredAt,
            'data_ultimo_login' => $lastLoginAt,
            'subscription_expires_at' => $accessExpiresAt,
            'ip_cadastro' => $ipCadastro,
            'ip_ultimo_acesso' => $ipUltimo,
            'ip_penultimo_acesso' => $ipPenultimo,
            'lock_until' => $lockUntil,
            'lock_until_set' => $lockUntilSet,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    private function normalizeDateTime(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        $ts = strtotime($value);
        if ($ts === false) {
            return null;
        }
        return date('Y-m-d H:i:s', $ts);
    }

    private function normalizeBirthDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        if (!Validation::birthDate($value)) {
            return null;
        }
        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            $dt = \DateTimeImmutable::createFromFormat($fmt, $value);
            if ($dt && $dt->format($fmt) === $value) {
                return $dt->format('Y-m-d');
            }
        }
        try {
            $dt = new \DateTimeImmutable($value);
            return $dt->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function normalizePhone(string $value, array &$warnings): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';
        if ($digits === '') {
            return '';
        }
        if (strlen($digits) >= 12 && str_starts_with($digits, '55')) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 10) {
            $digits = substr($digits, 0, 2) . '9' . substr($digits, 2);
            $warnings[] = 'Telefone ajustado para formato com 9.';
        }
        if (strlen($digits) !== 11) {
            return '';
        }
        return $digits;
    }

    private function generateReferralCode(\PDO $pdo): string
    {
        for ($i = 0; $i < 5; $i++) {
            $code = bin2hex(random_bytes(4));
            $stmt = $pdo->prepare('SELECT id FROM users WHERE referral_code = :c');
            $stmt->execute(['c' => $code]);
            if (!$stmt->fetch()) {
                return $code;
            }
        }
        return bin2hex(random_bytes(6));
    }

    private function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
