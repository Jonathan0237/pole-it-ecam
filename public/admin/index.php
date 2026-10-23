<?php

declare(strict_types=1);

use PoleIt\Auth;
use PoleIt\Repository;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

Auth::require();

const PER_PAGE = 10;

$tabs = ['messages' => 'Messages', 'admissions' => 'Pré-inscriptions', 'stages' => 'Demandes de stage'];
$counts = [];
foreach (array_keys($tabs) as $t) {
    $r = new Repository($t);
    $counts[$t] = ['total' => $r->count(), 'unread' => $r->countUnread(), 'latest' => $r->latestUnread()];
}

// Sans onglet choisi, on ouvre celui qui contient la demande non lue la plus récente
$latest = array_filter(array_map(static fn (array $c): ?string => $c['latest'], $counts));
arsort($latest);
$tab = array_key_exists($_GET['t'] ?? '', $tabs) ? $_GET['t'] : (array_key_first($latest) ?? 'messages');
$search = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$repo = new Repository($tab);

$total = $repo->count($search);
$pages = max(1, (int) ceil($total / PER_PAGE));
$current = min($pages, max(1, (int) ($_GET['p'] ?? 1)));

render('admin/dashboard', [
    'page'    => 'admin',
    'title'   => 'Tableau de bord',
    'tabs'    => $tabs,
    'tab'     => $tab,
    'search'  => $search,
    'rows'    => $repo->list($search, PER_PAGE, ($current - 1) * PER_PAGE),
    'total'   => $total,
    'pages'   => $pages,
    'current' => $current,
    'counts'  => $counts,
]);
