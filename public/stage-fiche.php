<?php

declare(strict_types=1);

use PoleIt\Forms;
use PoleIt\Repository;

require dirname(__DIR__) . '/src/bootstrap.php';

$ref = (string) ($_GET['ref'] ?? '');
$allowed = in_array($ref, $_SESSION['stage_refs'] ?? [], true) || \PoleIt\Auth::check();
$demande = $allowed ? (new Repository('stages'))->findBy('reference', $ref) : null;

if ($demande === null) {
    http_response_code(404);
    render('404', ['title' => 'Fiche introuvable']);
    exit;
}

render('stage-fiche', [
    'page'     => 'stage',
    'title'    => 'Fiche de demande de stage ' . $ref,
    'demande'  => $demande,
    'domaines' => Forms::DOMAINES,
]);
