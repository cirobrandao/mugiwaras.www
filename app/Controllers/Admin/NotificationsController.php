<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Models\Notification;

final class NotificationsController extends Controller
{
    public function index(): void
    {
        echo $this->view('admin/notifications', [
            'items' => Notification::adminAll(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function save(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/notifications?error=csrf'));
        }

        $id = (int)($request->post['id'] ?? 0);
        $title = trim((string)($request->post['title'] ?? ''));
        $body = trim((string)($request->post['body'] ?? ''));
        $priority = mb_strtolower(trim((string)($request->post['priority'] ?? 'low')));
        $active = !empty($request->post['is_active']);
        $startsAt = $this->normalizeDateTime((string)($request->post['starts_at'] ?? ''));
        $endsAt = $this->normalizeDateTime((string)($request->post['ends_at'] ?? ''));

        if ($title === '' || $body === '') {
            Response::redirect(base_path('/admin/notifications?error=required'));
        }
        if (!in_array($priority, ['high', 'medium', 'low'], true)) {
            $priority = 'low';
        }

        if ($id > 0) {
            Notification::update($id, $title, $body, $priority, $active, $startsAt, $endsAt);
            Response::redirect(base_path('/admin/notifications?updated=1'));
        }

        Notification::create($title, $body, $priority, $active, $startsAt, $endsAt);
        Response::redirect(base_path('/admin/notifications?created=1'));
    }

    public function delete(Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/admin/notifications?error=csrf'));
        }

        $id = (int)($request->post['id'] ?? 0);
        if ($id > 0) {
            Notification::delete($id);
        }

        Response::redirect(base_path('/admin/notifications?deleted=1'));
    }

    private function normalizeDateTime(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        $value = str_replace('T', ' ', $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/', $value) === 1) {
            return $value . ':00';
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $value) === 1) {
            return $value;
        }
        return null;
    }
}
