<?php

declare(strict_types=1);

use PoleIt\FormGuard;
use PoleIt\Forms;
use PoleIt\Repository;

require dirname(__DIR__) . '/src/bootstrap.php';

$old = [];
$errors = [];
$step = 1;

if (is_post()) {
    $guardError = FormGuard::check('admission');
    [$old, $errors, $step] = Forms::admission($_POST);

    if ($guardError !== null) {
        flash($guardError, 'error');
    } elseif ($errors === []) {
        (new Repository('admissions'))->insert($old);
        FormGuard::markSubmitted('admission');
        flash('Merci ' . $old['prenom'] . ', votre pré-inscription est enregistrée. Un conseiller vous contactera par e-mail.');
        redirect('/admission.php#preinscription');
    }
}

render('admission', [
    'page'        => 'admission',
    'title'       => 'Admission',
    'description' => 'Conditions d\'admission et pré-inscription en ligne au Pôle IT.',
    'hero'        => ['image' => 'cours', 'small' => true, 'kicker' => 'Admission', 'title' => 'Rejoindre <span>le Pôle IT</span>'],
    'old'         => $old,
    'errors'      => $errors,
    'step'        => $step,
]);
