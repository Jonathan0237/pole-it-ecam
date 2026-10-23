<?php

declare(strict_types=1);

/*
 * Remplit la base avec des données fictives pour la démo : php bin/seed.php [--force]
 * Sans --force, ne fait rien si la base contient déjà des messages.
 */

use PoleIt\Database;
use PoleIt\Forms;
use PoleIt\Repository;

require dirname(__DIR__) . '/src/bootstrap.php';

$messages = new Repository('messages');
if ($messages->count() > 0 && !in_array('--force', $argv, true)) {
    echo "Base déjà remplie, rien à faire (utiliser --force pour ajouter des données).\n";
    exit(0);
}

$pdo = Database::connection();
$at = static fn (int $daysAgo, int $hour): string => date('Y-m-d H:i:s', strtotime("-{$daysAgo} days {$hour}:15"));

$people = [
    ['Léa', 'Martin'], ['Yanis', 'Benali'], ['Chloé', 'Nguyen'], ['Moussa', 'Traoré'],
    ['Inès', 'Lefèvre'], ['Hugo', 'Moreau'], ['Aïcha', 'Diop'], ['Lucas', 'Bernard'],
];
$mail = static fn (array $p): string => strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $p[0] . '.' . $p[1])) . '@exemple.fr';

$sujets = [
    ['information', "Bonjour,\nQuelles sont les dates des prochaines journées portes ouvertes ?\nMerci."],
    ['admission', "Bonjour,\nJe suis en BUT informatique, puis-je intégrer le cycle ingénieur par apprentissage ?"],
    ['stage', "Bonjour,\nNotre entreprise recherche deux stagiaires en développement web pour le printemps."],
    ['autre', "Bravo pour le site, la navigation est claire et le formulaire de stage très pratique !"],
];
foreach ($sujets as $i => [$sujet, $texte]) {
    $p = $people[$i];
    $id = $messages->insert(['nom' => "{$p[0]} {$p[1]}", 'email' => $mail($p), 'sujet' => $sujet, 'message' => $texte]);
    $pdo->prepare('UPDATE messages SET created_at = ?, lu = ? WHERE id = ?')->execute([$at(6 - $i, 9 + $i), $i < 2 ? 1 : 0, $id]);
}

$admissions = new Repository('admissions');
$programmes = array_keys(Forms::PROGRAMMES);
$niveaux = array_keys(Forms::NIVEAUX);
foreach (array_slice($people, 2, 4) as $i => $p) {
    $id = $admissions->insert([
        'prenom' => $p[0], 'nom' => $p[1], 'email' => $mail($p), 'telephone' => '06' . str_pad((string) (12345678 + $i * 1111), 8, '0'),
        'adresse' => (10 + $i) . ' avenue de la Gare', 'code_postal' => '9500' . $i, 'ville' => 'Cergy',
        'niveau' => $niveaux[$i % count($niveaux)], 'programme' => $programmes[$i % count($programmes)],
        'motivation' => "Passionné(e) par le numérique, je souhaite rejoindre le Pôle IT pour me former au développement et aux systèmes embarqués.",
    ]);
    $pdo->prepare('UPDATE admissions SET created_at = ? WHERE id = ?')->execute([$at(5 - $i, 14), $id]);
}

$stages = new Repository('stages');
$domaines = array_keys(Forms::DOMAINES);
foreach (array_slice($people, 4, 4) as $i => $p) {
    $id = $stages->insert([
        'prenom' => $p[0], 'nom' => $p[1], 'age' => 20 + $i, 'email' => $mail($p), 'telephone' => '07' . str_pad((string) (23456789 + $i * 2222), 8, '0'),
        'ville' => ['Cergy', 'Paris', 'Pontoise', 'Douala'][$i], 'pays' => $i === 3 ? 'Cameroun' : 'France',
        'domaine' => $domaines[$i], 'date_debut' => date('Y-m-d', strtotime('+' . (2 + $i) . ' months')), 'duree' => [2, 4, 6, 6][$i],
        'reference' => Forms::reference(),
    ]);
    $pdo->prepare('UPDATE stages SET created_at = ? WHERE id = ?')->execute([$at(4 - $i, 11), $id]);
}

echo "Données de démo ajoutées.\n";
