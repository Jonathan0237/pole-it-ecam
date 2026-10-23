<?php

declare(strict_types=1);

use PoleIt\Validator;

test('Validator : champ obligatoire vide', function () {
    $v = (new Validator(['nom' => '   ']))->required('nom', 'Nom');
    assert_true($v->fails());
    assert_same('Le champ « Nom » est obligatoire.', $v->errors()['nom']);
});

test('Validator : e-mail valide et invalide', function () {
    assert_true(!(new Validator(['e' => 'a@b.fr']))->email('e', 'E')->fails());
    assert_true((new Validator(['e' => 'pas-un-mail']))->email('e', 'E')->fails());
});

test('Validator : une seule erreur conservée par champ', function () {
    $v = (new Validator(['nom' => '']))->required('nom', 'Nom')->length('nom', 'Nom', 2, 5);
    assert_same(1, count($v->errors()));
});

test('Validator : longueur en caractères multi-octets', function () {
    assert_true(!(new Validator(['n' => 'Éloïse']))->length('n', 'N', 2, 6)->fails());
});

test('Validator : noms avec accents, tirets et apostrophes', function () {
    foreach (["Jean-Luc", "N'Diaye", 'Simo Kuate', 'Zoé'] as $name) {
        assert_true(!(new Validator(['n' => $name]))->name('n', 'N')->fails(), $name);
    }
    assert_true((new Validator(['n' => '<script>']))->name('n', 'N')->fails());
});

test('Validator : code postal, téléphone, entier, date', function () {
    assert_true(!(new Validator(['c' => '95000']))->postalCode('c', 'C')->fails());
    assert_true((new Validator(['c' => '9500']))->postalCode('c', 'C')->fails());
    assert_true(!(new Validator(['t' => '06 12 34 56 78']))->phone('t', 'T')->fails());
    assert_true(!(new Validator(['t' => '+237 6 96 36 89 93']))->phone('t', 'T')->fails());
    assert_true((new Validator(['t' => '12']))->phone('t', 'T')->fails());
    assert_true((new Validator(['a' => '15']))->integer('a', 'A', 16, 99)->fails());
    assert_true((new Validator(['d' => '2027-02-30']))->date('d', 'D')->fails());
    assert_true(!(new Validator(['d' => '2027-02-28']))->date('d', 'D')->fails());
});

test('Validator : liste de valeurs autorisées', function () {
    assert_true((new Validator(['s' => 'hack']))->in('s', 'S', ['a', 'b'])->fails());
});
