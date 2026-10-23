<?php

declare(strict_types=1);

use PoleIt\Auth;
use PoleIt\Forms;
use PoleIt\Repository;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

Auth::require();

$tab = (string) ($_GET['t'] ?? '');
if (!isset(Repository::TABLES[$tab])) {
    redirect('/admin/');
}

$repo = new Repository($tab);
$item = $repo->find((int) ($_GET['id'] ?? 0));
if ($item === null) {
    flash('Élément introuvable.', 'error');
    redirect('/admin/?t=' . $tab);
}
$repo->markRead((int) $item['id']);

$labels = [
    'niveau'    => Forms::NIVEAUX,
    'programme' => Forms::PROGRAMMES,
    'domaine'   => Forms::DOMAINES,
    'sujet'     => Forms::SUJETS,
];

render('admin/detail', ['page' => 'admin', 'title' => 'Détail', 'tab' => $tab, 'item' => $item, 'labels' => $labels]);
