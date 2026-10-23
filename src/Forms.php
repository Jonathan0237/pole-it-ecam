<?php

declare(strict_types=1);

namespace PoleIt;

/**
 * Règles de validation et normalisation des trois formulaires publics.
 * Chaque méthode renvoie [données nettoyées, erreurs par champ].
 */
final class Forms
{
    public const NIVEAUX = [
        'bac'     => 'Baccalauréat (entrée en 1re année)',
        'bac+2'   => 'Bac +2 (BTS, BUT, CPGE)',
        'bac+3'   => 'Bac +3 (licence)',
        'bac+4'   => 'Bac +4 ou plus',
    ];

    public const PROGRAMMES = [
        'cycle-prepa'   => 'Cycle préparatoire intégré',
        'ingenieur-fi'  => 'Cycle ingénieur, formation initiale',
        'ingenieur-app' => 'Cycle ingénieur par apprentissage',
    ];

    public const DOMAINES = [
        'dev'      => 'Développement logiciel et web',
        'reseaux'  => 'Réseaux et cybersécurité',
        'data'     => 'Données et intelligence artificielle',
        'embarque' => 'Systèmes embarqués et IoT',
    ];

    public const SUJETS = [
        'information' => 'Demande d\'information',
        'admission'   => 'Question sur l\'admission',
        'stage'       => 'Question sur les stages',
        'autre'       => 'Autre',
    ];

    public static function contact(array $input): array
    {
        $data = self::pick($input, ['nom', 'email', 'sujet', 'message']);
        $v = (new Validator($data))
            ->required('nom', 'Nom')->length('nom', 'Nom', 2, 80)->name('nom', 'Nom')
            ->required('email', 'E-mail')->email('email', 'E-mail')->length('email', 'E-mail', 5, 160)
            ->required('sujet', 'Sujet')->in('sujet', 'Sujet', array_keys(self::SUJETS))
            ->required('message', 'Message')->length('message', 'Message', 10, 3000);

        $data['email'] = mb_strtolower($data['email']);
        return [$data, $v->errors()];
    }

    /**
     * Le formulaire d'admission est découpé en 3 étapes côté navigateur ;
     * $step permet de renvoyer l'utilisateur sur l'étape fautive.
     *
     * @return array{0: array, 1: array<string,string>, 2: int}
     */
    public static function admission(array $input): array
    {
        $data = self::pick($input, ['prenom', 'nom', 'email', 'telephone', 'adresse', 'code_postal', 'ville', 'niveau', 'programme', 'motivation', 'rgpd']);
        $v = (new Validator($data))
            ->required('prenom', 'Prénom')->length('prenom', 'Prénom', 2, 60)->name('prenom', 'Prénom')
            ->required('nom', 'Nom')->length('nom', 'Nom', 2, 60)->name('nom', 'Nom')
            ->required('email', 'E-mail')->email('email', 'E-mail')
            ->required('telephone', 'Téléphone')->phone('telephone', 'Téléphone')
            ->required('adresse', 'Adresse')->length('adresse', 'Adresse', 5, 160)
            ->required('code_postal', 'Code postal')->postalCode('code_postal', 'Code postal')
            ->required('ville', 'Ville')->length('ville', 'Ville', 2, 80)
            ->required('niveau', 'Niveau actuel')->in('niveau', 'Niveau actuel', array_keys(self::NIVEAUX))
            ->required('programme', 'Programme visé')->in('programme', 'Programme visé', array_keys(self::PROGRAMMES))
            ->required('motivation', 'Motivation')->length('motivation', 'Motivation', 30, 2000)
            ->accepted('rgpd', 'Vous devez accepter le traitement de vos données.');

        $errors = $v->errors();
        $steps = [
            1 => ['prenom', 'nom', 'email', 'telephone'],
            2 => ['adresse', 'code_postal', 'ville'],
            3 => ['niveau', 'programme', 'motivation', 'rgpd'],
        ];
        $step = 1;
        foreach ($steps as $n => $fields) {
            if (array_intersect($fields, array_keys($errors))) {
                $step = $n;
                break;
            }
        }

        $data['email'] = mb_strtolower($data['email']);
        $data['telephone'] = self::normalizePhone($data['telephone']);
        unset($data['rgpd']);
        return [$data, $errors, $step];
    }

    public static function stage(array $input, ?\DateTimeImmutable $today = null): array
    {
        $today ??= new \DateTimeImmutable('today');
        $data = self::pick($input, ['prenom', 'nom', 'age', 'email', 'telephone', 'ville', 'pays', 'domaine', 'date_debut', 'duree']);
        $v = (new Validator($data))
            ->required('prenom', 'Prénom')->length('prenom', 'Prénom', 2, 60)->name('prenom', 'Prénom')
            ->required('nom', 'Nom')->length('nom', 'Nom', 2, 60)->name('nom', 'Nom')
            ->required('age', 'Âge')->integer('age', 'Âge', 16, 99)
            ->required('email', 'E-mail')->email('email', 'E-mail')
            ->required('telephone', 'Téléphone')->phone('telephone', 'Téléphone')
            ->required('ville', 'Ville')->length('ville', 'Ville', 2, 80)
            ->required('pays', 'Pays')->length('pays', 'Pays', 2, 60)
            ->required('domaine', 'Domaine')->in('domaine', 'Domaine', array_keys(self::DOMAINES))
            ->required('date_debut', 'Date de début')->date('date_debut', 'Date de début')
            ->required('duree', 'Durée')->integer('duree', 'Durée', 1, 12);

        $errors = $v->errors();
        if (!isset($errors['date_debut']) && $data['date_debut'] !== '' && $data['date_debut'] < $today->format('Y-m-d')) {
            $errors['date_debut'] = 'La date de début ne peut pas être dans le passé.';
        }

        $data['email'] = mb_strtolower($data['email']);
        $data['telephone'] = self::normalizePhone($data['telephone']);
        $data['age'] = (int) $data['age'];
        $data['duree'] = (int) $data['duree'];
        return [$data, $errors];
    }

    /** Référence lisible et non devinable, ex. STG-2026-7K3QX9 */
    public static function reference(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $suffix = '';
        for ($i = 0; $i < 6; $i++) {
            $suffix .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return 'STG-' . date('Y') . '-' . $suffix;
    }

    private static function normalizePhone(string $phone): string
    {
        return preg_replace('/[\s.-]/', '', $phone) ?? $phone;
    }

    private static function pick(array $input, array $keys): array
    {
        $out = [];
        foreach ($keys as $key) {
            $value = $input[$key] ?? '';
            $out[$key] = is_string($value) ? trim($value) : '';
        }
        return $out;
    }
}
