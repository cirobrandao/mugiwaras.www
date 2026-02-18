<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Csrf;
use App\Models\SupportMessage;
use App\Models\SupportReply;

final class SupportController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        
        $messages = SupportMessage::paginated($page, $perPage);
        $totalMessages = SupportMessage::count();
        $totalPages = (int)ceil($totalMessages / $perPage);
        
        echo $this->view('admin/support', [
            'messages' => $messages,
            'csrf' => Csrf::token(),
            'page' => $page,
            'totalPages' => $totalPages,
            'totalMessages' => $totalMessages,
        ]);
    }

    public function show(Request $request, string $id): void
    {
        $ticket = SupportMessage::find((int)$id);
        if (!$ticket) {
            Response::redirect(base_path('/admin/support'));
        }
        $replies = SupportReply::bySupportId((int)$ticket['id']);
        
        echo $this->view('admin/support_show', [
            'ticket' => $ticket,
            'replies' => $replies,
            'csrf' => Csrf::token(),
        ]);
    }

    public function status(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/support'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $status = (string)($request->post['status'] ?? 'open');
        if ($id > 0) {
            if ($status === 'closed' && !SupportReply::lastReplyIsAdmin($id)) {
                Response::redirect(base_path('/admin/support/' . $id . '?error=final'));
            }
            SupportMessage::setStatus($id, $status);
        }
        Response::redirect(base_path('/admin/support'));
    }

    public function note(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/support'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $note = trim((string)($request->post['admin_note'] ?? ''));
        if ($id > 0) {
            SupportMessage::addNote($id, $note);
        }
        if ($id > 0) {
            Response::redirect(base_path('/admin/support/' . $id));
        }
        Response::redirect(base_path('/admin/support'));
    }

    public function reply(Request $request, string $id): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/support/' . (int)$id));
        }
        $ticket = SupportMessage::find((int)$id);
        if (!$ticket) {
            Response::redirect(base_path('/admin/support'));
        }
        if (SupportReply::lastReplyIsAdmin((int)$id) && !SupportReply::lastReplyIsUser((int)$id)) {
            Response::redirect(base_path('/admin/support/' . (int)$id . '?error=wait_user'));
        }
        $message = trim((string)($request->post['message'] ?? ''));
        if ($message === '') {
            Response::redirect(base_path('/admin/support/' . (int)$id . '?error=message'));
        }
        [$attachmentPath, $attachmentName] = $this->handleAttachment($request);
        SupportReply::create([
            'sid' => (int)$id,
            'uid' => null,
            'aid' => (int)($_SESSION['user_id'] ?? 0),
            'msg' => $message,
            'ap' => $attachmentPath,
            'an' => $attachmentName,
        ]);
        SupportMessage::setStatus((int)$id, 'in_progress');
        Response::redirect(base_path('/admin/support/' . (int)$id));
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
        return dirname(__DIR__, 3) . '/public/uploads';
    }
}
