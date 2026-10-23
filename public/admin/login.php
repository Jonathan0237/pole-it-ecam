<?php

declare(strict_types=1);

use PoleIt\Auth;
use PoleIt\Csrf;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

if (Auth::check()) {
    redirect('/admin/');
}

$error = null;
if (is_post()) {
    if (!Csrf::check($_POST['_csrf'] ?? null)) {
        $error = 'Session expirée, merci de réessayer.';
    } elseif (Auth::isLocked()) {
        $error = 'Trop de tentatives. Réessayez dans quelques minutes.';
    } elseif (Auth::attempt(input('utilisateur'), (string) ($_POST['mot_de_passe'] ?? ''))) {
        redirect('/admin/');
    } else {
        $error = 'Identifiants incorrects.';
    }
}

render('admin/login', ['page' => 'admin', 'title' => 'Connexion admin', 'error' => $error]);
