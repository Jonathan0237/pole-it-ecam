<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

http_response_code(404);
render('404', ['title' => 'Page introuvable']);
