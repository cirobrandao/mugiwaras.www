<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\EmailBlocklist;
use App\Models\User;
use App\Models\UserBlocklist;

final class SecurityController extends Controller
{
    public function emailBlocklist(): void
    {
        $emails = EmailBlocklist::all();
        $userBlocks = UserBlocklist::all();
        echo $this->view('admin/security', [
            'emails' => $emails,
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
        EmailBlocklist::add($domain);
        Response::redirect(base_path('/admin/security/email-blocklist'));
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
        Response::redirect(base_path('/admin/security/email-blocklist'));
    }

    public function userBlockAdd(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/security/email-blocklist'));
        }
        $identifier = trim((string)($request->post['identifier'] ?? ''));
        $reason = trim((string)($request->post['reason'] ?? ''));

        $user = User::findByUsername($identifier) ?? User::findByEmail($identifier);
        if ($user) {
            UserBlocklist::add((int)$user['id'], $reason);
        }
        Response::redirect(base_path('/admin/security/email-blocklist'));
    }

    public function userBlockRemove(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/security/email-blocklist'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            UserBlocklist::deactivate($id);
        }
        Response::redirect(base_path('/admin/security/email-blocklist'));
    }
}
