<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;

final class ProfileController extends Controller
{
    public function show(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        echo $this->view('profile/show', ['user' => $user]);
    }

    public function editForm(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        echo $this->view('profile/edit', ['user' => $user]);
    }

    public function passwordForm(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        echo $this->view('profile/password', ['user' => $user]);
    }
}
