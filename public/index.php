<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

render('home', [
    'page'  => 'accueil',
    'hero'  => [
        'image' => 'amphi-2',
        'kicker'  => 'ECAM-EPMI · Grande école d\'ingénieurs',
        'title'   => 'Pôle <span>IT</span>',
        'text'    => 'Former les ingénieurs du numérique : développement, réseaux, données et systèmes embarqués.',
        'actions' => [['/admission.php', 'Candidater'], ['/a-propos.php', 'Découvrir le pôle']],
    ],
]);
