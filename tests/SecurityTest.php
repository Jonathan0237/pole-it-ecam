<?php

declare(strict_types=1);

use PoleIt\Csrf;
use PoleIt\FormGuard;

test('CSRF : jeton stable dans la session et vérifié', function () {
    $token = Csrf::token();
    assert_same($token, Csrf::token());
    assert_true(Csrf::check($token));
    assert_true(!Csrf::check('mauvais'));
    assert_true(!Csrf::check(null));
});

test('FormGuard : honeypot rempli refusé', function () {
    $_POST = ['_csrf' => Csrf::token(), FormGuard::HONEYPOT => 'http://spam'];
    assert_same('Envoi refusé.', FormGuard::check('contact'));
});

test('FormGuard : envois trop rapprochés refusés', function () {
    $_POST = ['_csrf' => Csrf::token()];
    assert_same(null, FormGuard::check('contact'));
    FormGuard::markSubmitted('contact');
    assert_true(FormGuard::check('contact') !== null);
    assert_same(null, FormGuard::check('stage'), 'la limite est propre à chaque formulaire');
});

test('Échappement HTML', function () {
    assert_same('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', e('<script>alert("x")</script>'));
});

test('Champ de formulaire : valeur échappée et erreur reliée', function () {
    $html = field('nom', ['label' => 'Nom', 'required' => true], ['nom' => '"><img src=x>'], ['nom' => 'Erreur']);
    assert_true(!str_contains($html, '<img'), 'XSS dans la valeur');
    assert_true(str_contains($html, 'aria-describedby="f-nom-err"'));
    assert_true(str_contains($html, 'aria-invalid="true"'));
});
