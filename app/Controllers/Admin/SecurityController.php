<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\EmailBlocklist;
use App\Models\UsernameBlocklist;

final class SecurityController extends Controller
{
    public function emailBlocklist(): void
    {
        $emails = EmailBlocklist::all();
        $testEmail = trim((string)($_GET['test_email'] ?? ''));
        $testResult = null;
        if ($testEmail !== '') {
            $testResult = EmailBlocklist::isBlocked($testEmail);
        }
        echo $this->view('admin/security', [
            'emails' => $emails,
            'testEmail' => $testEmail,
            'testResult' => $testResult,
            'csrf' => Csrf::token(),
        ]);
    }

    public function userBlocklist(): void
    {
        $userBlocks = UsernameBlocklist::all();
        echo $this->view('admin/security_user_blocklist', [
            'userBlocks' => $userBlocks,
            'csrf' => Csrf::token(),
        ]);
    }

    public function emailAdd(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/security/email-blocklist'));
        }
        $domain = (string)($request->post['domain'] ?? '');
        $status = EmailBlocklist::add($domain);
        Response::redirect(base_path('/admin/security/email-blocklist?status=' . $status));
    }

    public function emailRemove(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/security/email-blocklist'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            EmailBlocklist::remove($id);
        }
        Response::redirect(base_path('/admin/security/email-blocklist?status=removed'));
    }

    public function userBlockAdd(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/security/user-blocklist'));
        }
        $identifier = trim((string)($request->post['identifier'] ?? ''));
        $status = UsernameBlocklist::add($identifier);
        Response::redirect(base_path('/admin/security/user-blocklist?status=' . $status));
    }

    public function userBlockRemove(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/security/user-blocklist'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            UsernameBlocklist::remove($id);
        }
        Response::redirect(base_path('/admin/security/user-blocklist?status=removed'));
    }
}
