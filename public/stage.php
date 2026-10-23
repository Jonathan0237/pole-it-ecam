<?php

declare(strict_types=1);

use PoleIt\FormGuard;
use PoleIt\Forms;
use PoleIt\Repository;

require dirname(__DIR__) . '/src/bootstrap.php';

$old = [];
$errors = [];

if (is_post()) {
    $guardError = FormGuard::check('stage');
    [$old, $errors] = Forms::stage($_POST);

    if ($guardError !== null) {
        flash($guardError, 'error');
    } elseif ($errors === []) {
        $old['reference'] = Forms::reference();
        (new Repository('stages'))->insert($old);
        FormGuard::markSubmitted('stage');
        // Seul l'auteur de la demande (même session) peut afficher sa fiche.
        $_SESSION['stage_refs'][] = $old['reference'];
        redirect('/stage-fiche.php?ref=' . urlencode($old['reference']));
    }
}

render('stage', [
    'page'        => 'stage',
    'title'       => 'Demande de stage',
    'description' => 'Déposer une demande de stage au Pôle IT et télécharger sa fiche en PDF.',
    'hero'        => ['image' => 'conference', 'small' => true, 'kicker' => 'Stage', 'title' => 'Trouver <span>votre stage</span>'],
    'old'         => $old,
    'errors'      => $errors,
]);
