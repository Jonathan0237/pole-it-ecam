<?php

declare(strict_types=1);

use PoleIt\Auth;
use PoleIt\Csrf;
use PoleIt\Repository;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

Auth::require();

$tab = (string) ($_POST['t'] ?? '');
if (!is_post() || !Csrf::check($_POST['_csrf'] ?? null) || !isset(Repository::TABLES[$tab])) {
    http_response_code(400);
    exit('Requête invalide.');
}

(new Repository($tab))->delete((int) ($_POST['id'] ?? 0));
flash('Élément supprimé.');
redirect('/admin/?t=' . $tab);
