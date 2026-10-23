<?php

declare(strict_types=1);

use PoleIt\Auth;
use PoleIt\Csrf;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

if (is_post() && Csrf::check($_POST['_csrf'] ?? null)) {
    Auth::logout();
}
redirect('/admin/login.php');
