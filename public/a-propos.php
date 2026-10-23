<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

render('about', [
    'page'        => 'a-propos',
    'title'       => 'À propos',
    'description' => 'Engagements, histoire et projets du Pôle IT de l\'ECAM-EPMI.',
    'hero'        => ['image' => 'projet', 'small' => true, 'kicker' => 'À propos', 'title' => 'Une école <span>tournée vers l\'entreprise</span>'],
]);
