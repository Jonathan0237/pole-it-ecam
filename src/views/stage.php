<?php

use PoleIt\Csrf;
use PoleIt\FormGuard;
use PoleIt\Forms;

?>
<section class="section">
    <div class="container split split-top">
        <div class="reveal">
            <p class="kicker">Demande de stage</p>
            <h2 class="section-title">Un stage <span>dans votre domaine</span></h2>
            <p>Remplissez ce formulaire pour être mis en relation avec les entreprises partenaires du Pôle IT. À l'envoi, une <strong>fiche récapitulative</strong> avec un numéro de référence est générée : vous pouvez l'imprimer ou l'enregistrer en PDF.</p>
            <ul class="check-list">
                <li>Stages de 1 à 12 mois</li>
                <li>Quatre domaines : développement, réseaux, données, embarqué</li>
                <li>Réponse sous 15 jours</li>
            </ul>
            <img class="rounded shadow" src="/assets/img/robotique.jpg" alt="Deux étudiants programment un robot" loading="lazy" width="1000" height="667">
        </div>

        <form method="post" action="/stage.php" class="form panel reveal" novalidate data-validate>
            <?= Csrf::field() ?>
            <?= FormGuard::honeypotField() ?>
            <h3 class="form-title">Informations personnelles</h3>
            <div class="grid-3">
                <?= field('prenom', ['label' => 'Prénom', 'required' => true, 'attrs' => 'autocomplete="given-name"'], $old, $errors) ?>
                <?= field('nom', ['label' => 'Nom', 'required' => true, 'attrs' => 'autocomplete="family-name"'], $old, $errors) ?>
                <?= field('age', ['label' => 'Âge', 'type' => 'number', 'required' => true, 'attrs' => 'min="16" max="99"'], $old, $errors) ?>
            </div>
            <div class="grid-2">
                <?= field('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true, 'attrs' => 'autocomplete="email"'], $old, $errors) ?>
                <?= field('telephone', ['label' => 'Téléphone', 'type' => 'tel', 'required' => true, 'attrs' => 'autocomplete="tel"'], $old, $errors) ?>
                <?= field('ville', ['label' => 'Ville', 'required' => true, 'attrs' => 'autocomplete="address-level2"'], $old, $errors) ?>
                <?= field('pays', ['label' => 'Pays', 'required' => true, 'attrs' => 'autocomplete="country-name"'], $old ?: ['pays' => 'France'], $errors) ?>
            </div>

            <h3 class="form-title">Le stage recherché</h3>
            <?= field('domaine', ['label' => 'Domaine', 'type' => 'select', 'required' => true, 'options' => Forms::DOMAINES], $old, $errors) ?>
            <div class="grid-2">
                <?= field('date_debut', ['label' => 'Date de début souhaitée', 'type' => 'date', 'required' => true, 'attrs' => 'min="' . date('Y-m-d') . '"'], $old, $errors) ?>
                <?= field('duree', ['label' => 'Durée (mois)', 'type' => 'number', 'required' => true, 'attrs' => 'min="1" max="12"'], $old, $errors) ?>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i> Envoyer et générer ma fiche</button>
        </form>
    </div>
</section>
