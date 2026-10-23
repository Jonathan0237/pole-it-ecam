<?php

declare(strict_types=1);

use PoleIt\Repository;

function message(string $nom, string $email): array
{
    return ['nom' => $nom, 'email' => $email, 'sujet' => 'autre', 'message' => 'Un message de test.'];
}

test('Repository : insertion, lecture et suppression', function () {
    $repo = new Repository('messages');
    $id = $repo->insert(message('Jean Dupont', 'jean@test.fr'));
    assert_same('Jean Dupont', $repo->find($id)['nom']);
    assert_same(1, $repo->countUnread());
    $repo->markRead($id);
    assert_same(0, $repo->countUnread());
    $repo->delete($id);
    assert_same(null, $repo->find($id));
});

test('Repository : recherche et pagination', function () {
    $repo = new Repository('messages');
    foreach (range(1, 25) as $i) {
        $repo->insert(message("Personne {$i}", "p{$i}@test.fr"));
    }
    $repo->insert(message('Zoé Martin', 'zoe@test.fr'));
    assert_same(26, $repo->count());
    assert_same(10, count($repo->list('', 10, 0)));
    assert_same(6, count($repo->list('', 10, 20)));
    assert_same(1, $repo->count('zoé'));
});

test('Repository : les jokers SQL sont échappés dans la recherche', function () {
    $repo = new Repository('messages');
    $repo->insert(message('Jean', 'jean@test.fr'));
    assert_same(0, $repo->count('%'));
    assert_same(0, $repo->count('_'));
});

test('Repository : injection SQL sans effet', function () {
    $repo = new Repository('messages');
    $repo->insert(message("Robert'); DROP TABLE messages;--", 'r@test.fr'));
    assert_same(1, $repo->count());
});

test('Repository : table ou colonne inconnue refusée', function () {
    $thrown = false;
    try { new Repository('users'); } catch (InvalidArgumentException) { $thrown = true; }
    assert_true($thrown);

    $thrown = false;
    try { (new Repository('stages'))->findBy('1=1 OR reference', 'x'); } catch (InvalidArgumentException) { $thrown = true; }
    assert_true($thrown);
});

test('Repository : date du plus récent non lu', function () {
    $repo = new Repository('messages');
    assert_same(null, $repo->latestUnread());
    $id = $repo->insert(message('Jean', 'jean@test.fr'));
    assert_true($repo->latestUnread() !== null);
    $repo->markRead($id);
    assert_same(null, $repo->latestUnread());
});
