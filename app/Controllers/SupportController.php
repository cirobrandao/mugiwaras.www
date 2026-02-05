<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\RateLimiter;
use App\Core\Audit;
use App\Models\SupportMessage;
use App\Models\SupportReply;
use App\Core\Auth;

final class SupportController extends Controller
{
    public function form(): void
    {
        $user = Auth::user();
        if (Auth::isSupportStaff($user)) {
            Response::redirect(base_path('/admin/support'));
        }

        if ($user) {
            $messages = SupportMessage::byUser((int)$user['id']);
            $hasOpen = false;
            $needsAttention = [];
            foreach ($messages as $m) {
                if (($m['status'] ?? 'open') !== 'closed') {
                    $hasOpen = true;
                }
                $id = (int)($m['id'] ?? 0);
                if ($id > 0 && ($m['status'] ?? 'open') !== 'closed') {
                    if (SupportReply::lastReplyIsAdmin($id) && !SupportReply::lastReplyIsUser($id)) {
                        $needsAttention[$id] = true;
                    }
                }
            }
            echo $this->view('support/index', [
                'csrf' => Csrf::token(),
                'messages' => $messages,
                'user' => $user,
                'hasOpen' => $hasOpen,
                'needsAttention' => $needsAttention,
            ]);
            return;
        }

        echo $this->view('support/form', ['csrf' => Csrf::token()]);
    }

    public function show(Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user || Auth::isSupportStaff($user)) {
            Response::redirect(base_path('/support'));
        }
        $ticket = SupportMessage::find((int)$id);
        if (!$ticket || (int)$ticket['user_id'] !== (int)$user['id']) {
            Response::redirect(base_path('/support'));
        }
            $replies = SupportReply::bySupportId((int)$ticket['id']);
            $canReply = SupportReply::hasAdminReply((int)$ticket['id']);
        echo $this->view('support/show', [
            'csrf' => Csrf::token(),
            'ticket' => $ticket,
            'replies' => $replies,
            'user' => $user,
                'canReply' => $canReply,
        ]);
    }

    public function submit(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/support'));
        }
        $rate = new RateLimiter();
        $allowed = $rate->hit('support:' . $request->ip(), (int)config('security.rate_limit.support'), (int)config('security.rate_limit.window'));
        if (!$allowed) {
            echo $this->view('support/form', ['error' => 'Muitas mensagens. Tente mais tarde.', 'csrf' => Csrf::token()]);
            return;
        }

        $email = trim((string)($request->post['email'] ?? ''));
        $subject = trim((string)($request->post['subject'] ?? ''));
        $message = trim((string)($request->post['message'] ?? ''));
        $user = Auth::user();
        $whatsappOptIn = 0;
        $whatsappNumber = '';

        if ($user) {
            $email = (string)$user['email'];
        }

        if ($email === '' || $subject === '' || $message === '') {
            echo $this->view('support/form', ['error' => 'Preencha todos os campos.', 'csrf' => Csrf::token()]);
            return;
        }

        [$attachmentPath, $attachmentName] = $this->handleAttachment($request);

        $token = $user ? null : bin2hex(random_bytes(16));
        $result = SupportMessage::create([
            'uid' => $_SESSION['user_id'] ?? null,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip' => $request->ip(),
            'ap' => $attachmentPath,
            'an' => $attachmentName,
            'status' => 'open',
            'token' => $token,
            'wopt' => $whatsappOptIn,
            'wnum' => $whatsappNumber,
        ]);
        Audit::log('support_message', $_SESSION['user_id'] ?? null, ['ip' => $request->ip()]);
        if ($token) {
            $stored = !empty($result['tokenStored']);
            if (!$stored && !empty($result['id'])) {
                $stored = SupportMessage::setPublicToken((int)$result['id'], $token);
            }
            Response::redirect(base_path('/support/track/' . $token . '?sent=1'));
        }
        Response::redirect(base_path('/support?sent=1'));
    }

    public function track(Request $request, string $token): void
    {
        $ticket = SupportMessage::findByToken($token);
        if (!$ticket) {
            Response::redirect(base_path('/support'));
        }
        $replies = SupportReply::bySupportId((int)$ticket['id']);
        echo $this->view('support/track', [
            'csrf' => Csrf::token(),
            'ticket' => $ticket,
            'replies' => $replies,
            'token' => $token,
                'canReply' => SupportReply::hasAdminReply((int)$ticket['id']),
        ]);
    }

    public function replyGuest(Request $request, string $token): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/support/track/' . $token));
        }
        $ticket = SupportMessage::findByToken($token);
        if (!$ticket) {
            Response::redirect(base_path('/support'));
        }
            if (!SupportReply::hasAdminReply((int)$ticket['id'])) {
                Response::redirect(base_path('/support/track/' . $token . '?error=wait'));
            }
        $message = trim((string)($request->post['message'] ?? ''));
        if ($message === '') {
            Response::redirect(base_path('/support/track/' . $token . '?error=message'));
        }
        [$attachmentPath, $attachmentName] = $this->handleAttachment($request);
        SupportReply::create([
            'sid' => (int)$ticket['id'],
            'uid' => null,
            'aid' => null,
            'msg' => $message,
            'ap' => $attachmentPath,
            'an' => $attachmentName,
        ]);
        SupportMessage::setStatus((int)$ticket['id'], 'open');
        Response::redirect(base_path('/support/track/' . $token));
    }

    public function reply(Request $request, string $id): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/support/' . (int)$id));
        }
        $user = Auth::user();
        if (!$user || Auth::isSupportStaff($user)) {
            Response::redirect(base_path('/support'));
        }
        $ticket = SupportMessage::find((int)$id);
        if (!$ticket || (int)$ticket['user_id'] !== (int)$user['id']) {
            Response::redirect(base_path('/support'));
        }
            if (!SupportReply::hasAdminReply((int)$ticket['id'])) {
                Response::redirect(base_path('/support/' . (int)$id . '?error=wait'));
            }
        $message = trim((string)($request->post['message'] ?? ''));
        if ($message === '') {
            Response::redirect(base_path('/support/' . (int)$id . '?error=message'));
        }
        [$attachmentPath, $attachmentName] = $this->handleAttachment($request);
        SupportReply::create([
            'sid' => (int)$id,
            'uid' => (int)$user['id'],
            'aid' => null,
            'msg' => $message,
            'ap' => $attachmentPath,
            'an' => $attachmentName,
        ]);
        SupportMessage::setStatus((int)$id, 'open');
        Response::redirect(base_path('/support/' . (int)$id));
    }

    private function handleAttachment(Request $request): array
    {
        $file = $request->files['attachment'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return [null, null];
        }
        $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','pdf'];
        if (!in_array($ext, $allowed, true)) {
            return [null, null];
        }

        $dir = $this->publicUploadsRoot() . '/support';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = 'support_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $dir . '/' . $filename;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            return [null, null];
        }

        return ['uploads/support/' . $filename, (string)($file['name'] ?? '')];
    }

    private function publicUploadsRoot(): string
    {
        return dirname(__DIR__, 2) . '/public/uploads';
    }
}
