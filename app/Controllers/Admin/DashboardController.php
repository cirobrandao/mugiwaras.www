<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;

final class DashboardController extends Controller
{
    public function index(): void
    {
        echo $this->view('admin/dashboard');
    }
}
