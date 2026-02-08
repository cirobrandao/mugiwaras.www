<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AvatarGallery;

final class AvatarGalleryController extends Controller
{
    public function index(): void
    {
        echo $this->view('avatar_gallery/index', [
            'avatars' => AvatarGallery::activeAll(),
        ]);
    }
}
