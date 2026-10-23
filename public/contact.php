<?php

declare(strict_types=1);

use PoleIt\FormGuard;
use PoleIt\Forms;
use PoleIt\Repository;

require dirname(__DIR__) . '/src/bootstrap.php';

$old = [];
$errors = [];

if (is_post()) {
    $guardError = FormGuard::check('contact');
    [$old, $errors] = Forms::contact($_POST);

    if ($guardError !== null) {
        flash($guardError, 'error');
    } elseif ($errors === []) {
        (new Repository('messages'))->insert($old);
        FormGuard::markSubmitted('contact');
        flash('Merci ' . $old['nom'] . ', votre message a bien été envoyé. Nous vous répondrons rapidement.');
        redirect('/contact.php');
    }
}

render('contact', [
    'page'        => 'contact',
    'title'       => 'Contact',
    'description' => 'Contacter l\'équipe du Pôle IT.',
    'hero'        => ['image' => 'cours', 'small' => true, 'kicker' => 'Contact', 'title' => 'Parlons de <span>votre projet</span>'],
    'old'         => $old,
    'errors'      => $errors,
]);
