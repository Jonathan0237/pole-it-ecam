<?php

declare(strict_types=1);

use PoleIt\Forms;

function admission_valide(): array
{
    return [
        'prenom' => 'Awa', 'nom' => 'Diallo', 'email' => 'Awa@Example.FR', 'telephone' => '06 12 34 56 78',
        'adresse' => '1 rue de la Gare', 'code_postal' => '95000', 'ville' => 'Cergy',
        'niveau' => 'bac+2', 'programme' => 'ingenieur-app',
        'motivation' => 'Je souhaite devenir ingénieur DevOps dans le secteur de l\'énergie.', 'rgpd' => '1',
    ];
}

test('Contact : données valides normalisées', function () {
    [$data, $errors] = Forms::contact(['nom' => ' Jean ', 'email' => 'JEAN@TEST.FR', 'sujet' => 'stage', 'message' => 'Bonjour, je cherche un stage.']);
    assert_same([], $errors);
    assert_same('Jean', $data['nom']);
    assert_same('jean@test.fr', $data['email']);
});

test('Contact : sujet hors liste refusé', function () {
    [, $errors] = Forms::contact(['nom' => 'Jean', 'email' => 'j@t.fr', 'sujet' => 'spam', 'message' => 'Bonjour à tous ici']);
    assert_true(isset($errors['sujet']));
});

test('Admission : dossier complet accepté et téléphone normalisé', function () {
    [$data, $errors, $step] = Forms::admission(admission_valide());
    assert_same([], $errors);
    assert_same(1, $step);
    assert_same('0612345678', $data['telephone']);
    assert_same('awa@example.fr', $data['email']);
    assert_true(!array_key_exists('rgpd', $data), 'la case RGPD ne doit pas être stockée');
});

test('Admission : renvoie l\'étape de la première erreur', function () {
    $input = admission_valide();
    $input['code_postal'] = 'ABCDE';
    [, , $step] = Forms::admission($input);
    assert_same(2, $step);

    $input = admission_valide();
    unset($input['rgpd']);
    [, $errors, $step] = Forms::admission($input);
    assert_same(3, $step);
    assert_true(isset($errors['rgpd']));
});

test('Stage : date de début dans le passé refusée', function () {
    $input = [
        'prenom' => 'Awa', 'nom' => 'Diallo', 'age' => '21', 'email' => 'a@b.fr', 'telephone' => '0612345678',
        'ville' => 'Cergy', 'pays' => 'France', 'domaine' => 'dev', 'date_debut' => '2026-01-01', 'duree' => '6',
    ];
    [, $errors] = Forms::stage($input, new DateTimeImmutable('2026-10-23'));
    assert_true(isset($errors['date_debut']));

    $input['date_debut'] = '2027-02-01';
    [$data, $errors] = Forms::stage($input, new DateTimeImmutable('2026-10-23'));
    assert_same([], $errors);
    assert_same(6, $data['duree']);
});

test('Stage : référence au bon format et unique', function () {
    $refs = array_map(static fn () => Forms::reference(), range(1, 200));
    assert_same(200, count(array_unique($refs)));
    assert_true((bool) preg_match('/^STG-\d{4}-[A-HJ-NP-Z2-9]{6}$/', $refs[0]));
});
