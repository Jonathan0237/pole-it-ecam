<?php

use PoleIt\Csrf;
use PoleIt\FormGuard;
use PoleIt\Forms;

?>
<section class="section">
    <div class="container split">
        <div class="reveal">
            <p class="kicker">Conditions d'admission</p>
            <h2 class="section-title">Plusieurs voies <span>pour intégrer l'école</span></h2>
            <p>L'école recrute après le baccalauréat en cycle préparatoire intégré, puis à bac +2 et bac +3 en cycle ingénieur, en formation initiale ou par apprentissage. Les règles, les coûts de scolarité et les équivalences sont détaillés sur le site officiel de l'école.</p>
            <ul class="check-list">
                <li>Étude du dossier scolaire</li>
                <li>Entretien de motivation</li>
                <li>Tests selon le profil (anglais, scientifique)</li>
            </ul>
            <a class="btn btn-ghost-dark" href="https://www.ecam-epmi.fr/" rel="noopener" target="_blank">Site officiel de l'école <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
        </div>
        <figure class="reveal">
            <img class="rounded shadow" src="/assets/img/parcours.png" alt="Schéma des parcours d'admission selon le diplôme d'origine" loading="lazy" width="628" height="909">
        </figure>
    </div>
</section>

<section class="section section-alt" id="preinscription">
    <div class="container narrow">
        <div class="section-head reveal">
            <p class="kicker">Pré-inscription</p>
            <h2 class="section-title">Votre dossier <span>en 3 étapes</span></h2>
        </div>

        <form method="post" action="/admission.php#preinscription" class="form panel stepper" novalidate data-stepper data-start-step="<?= (int) $step ?>">
            <?= Csrf::field() ?>
            <?= FormGuard::honeypotField() ?>

            <ol class="steps" aria-label="Progression">
                <li data-step-indicator="1"><span>1</span>Identité</li>
                <li data-step-indicator="2"><span>2</span>Coordonnées</li>
                <li data-step-indicator="3"><span>3</span>Projet</li>
            </ol>

            <?php if ($errors): ?>
                <div class="alert alert-error" role="alert">Le formulaire contient <?= count($errors) ?> erreur(s). Merci de les corriger.</div>
            <?php endif; ?>

            <fieldset class="step" data-step="1">
                <legend>Identité</legend>
                <div class="grid-2">
                    <?= field('prenom', ['label' => 'Prénom', 'required' => true, 'attrs' => 'autocomplete="given-name"'], $old, $errors) ?>
                    <?= field('nom', ['label' => 'Nom', 'required' => true, 'attrs' => 'autocomplete="family-name"'], $old, $errors) ?>
                    <?= field('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true, 'attrs' => 'autocomplete="email"'], $old, $errors) ?>
                    <?= field('telephone', ['label' => 'Téléphone', 'type' => 'tel', 'required' => true, 'attrs' => 'autocomplete="tel"', 'hint' => 'Ex. 06 12 34 56 78'], $old, $errors) ?>
                </div>
                <div class="step-actions"><button type="button" class="btn btn-primary" data-next-step>Suivant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button></div>
            </fieldset>

            <fieldset class="step" data-step="2">
                <legend>Coordonnées</legend>
                <?= field('adresse', ['label' => 'Adresse', 'required' => true, 'attrs' => 'autocomplete="street-address" minlength="5"'], $old, $errors) ?>
                <div class="grid-2">
                    <?= field('code_postal', ['label' => 'Code postal', 'required' => true, 'attrs' => 'autocomplete="postal-code" inputmode="numeric" pattern="\d{5}" maxlength="5"'], $old, $errors) ?>
                    <?= field('ville', ['label' => 'Ville', 'required' => true, 'attrs' => 'autocomplete="address-level2"'], $old, $errors) ?>
                </div>
                <div class="step-actions">
                    <button type="button" class="btn btn-ghost-dark" data-prev-step><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Précédent</button>
                    <button type="button" class="btn btn-primary" data-next-step>Suivant <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
            </fieldset>

            <fieldset class="step" data-step="3">
                <legend>Projet d'études</legend>
                <div class="grid-2">
                    <?= field('niveau', ['label' => 'Niveau actuel', 'type' => 'select', 'required' => true, 'options' => Forms::NIVEAUX], $old, $errors) ?>
                    <?= field('programme', ['label' => 'Programme visé', 'type' => 'select', 'required' => true, 'options' => Forms::PROGRAMMES], $old, $errors) ?>
                </div>
                <?= field('motivation', ['label' => 'Motivation', 'type' => 'textarea', 'required' => true, 'attrs' => 'minlength="30" maxlength="2000"', 'hint' => 'Quelques lignes sur votre projet (30 caractères minimum).'], $old, $errors) ?>
                <div class="field checkbox <?= isset($errors['rgpd']) ? 'has-error' : '' ?>">
                    <label><input type="checkbox" name="rgpd" value="1" required> J'accepte que mes données soient utilisées pour traiter ma demande.</label>
                    <?php if (isset($errors['rgpd'])): ?><p class="error"><?= e($errors['rgpd']) ?></p><?php endif; ?>
                </div>
                <div class="step-actions">
                    <button type="button" class="btn btn-ghost-dark" data-prev-step><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Précédent</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check" aria-hidden="true"></i> Envoyer ma pré-inscription</button>
                </div>
            </fieldset>
        </form>
    </div>
</section>
