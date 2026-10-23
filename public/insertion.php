<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

render('insertion', [
    'page'        => 'insertion',
    'title'       => 'Insertion professionnelle',
    'description' => 'Employabilité, premier salaire et débouchés des ingénieurs du Pôle IT.',
    'hero'        => ['image' => 'amphi', 'small' => true, 'kicker' => 'Insertion', 'title' => 'Des diplômés <span>recherchés</span>'],
]);
