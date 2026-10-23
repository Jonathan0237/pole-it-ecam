<?php

declare(strict_types=1);

use PoleIt\Auth;
use PoleIt\Repository;

require dirname(__DIR__, 2) . '/src/bootstrap.php';

Auth::require();

$tab = (string) ($_GET['t'] ?? '');
if (!isset(Repository::TABLES[$tab])) {
    redirect('/admin/');
}

$rows = (new Repository($tab))->all();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $tab . '-' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM : Excel ouvre le fichier en UTF-8
if ($rows) {
    fputcsv($out, array_keys($rows[0]), ';', '"', '');
    foreach ($rows as $row) {
        // Neutralise les formules (injection CSV) : =, +, -, @ en début de cellule
        $row = array_map(static fn ($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v, $row);
        fputcsv($out, $row, ';', '"', '');
    }
}
fclose($out);
