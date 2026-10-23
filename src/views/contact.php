<?php

use PoleIt\Csrf;
use PoleIt\FormGuard;
use PoleIt\Forms;

?>
<section class="section">
    <div class="container contact-grid">
        <div class="panel reveal">
            <h2 class="section-title">Écrivez-<span>nous</span></h2>
            <p class="muted">Ce site est un projet de développement web réalisé en groupe. Pour toute question, laissez-nous un message : il est enregistré en base et consultable dans l'espace admin.</p>

            <form method="post" action="/contact.php" class="form" novalidate data-validate>
                <?= Csrf::field() ?>
                <?= FormGuard::honeypotField() ?>
                <div class="grid-2">
                    <?= field('nom', ['label' => 'Nom complet', 'required' => true, 'attrs' => 'autocomplete="name" maxlength="80"'], $old, $errors) ?>
                    <?= field('email', ['label' => 'E-mail', 'type' => 'email', 'required' => true, 'attrs' => 'autocomplete="email" maxlength="160"'], $old, $errors) ?>
                </div>
                <?= field('sujet', ['label' => 'Sujet', 'type' => 'select', 'required' => true, 'options' => Forms::SUJETS], $old, $errors) ?>
                <?= field('message', ['label' => 'Message', 'type' => 'textarea', 'required' => true, 'attrs' => 'minlength="10" maxlength="3000"', 'hint' => '10 à 3 000 caractères.'], $old, $errors) ?>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Envoyer</button>
            </form>
        </div>

        <aside class="contact-aside reveal">
            <img class="rounded shadow" src="/assets/img/equipe.jpg" alt="Deux étudiants travaillent sur un ordinateur" loading="lazy" width="474" height="376">
            <ul class="contact-list">
                <li><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>ECAM-EPMI, Cergy (Val-d'Oise)</span></li>
                <li><i class="fa-brands fa-github" aria-hidden="true"></i><a href="https://github.com/Jonathan0237">github.com/Jonathan0237</a></li>
                <li><i class="fa-brands fa-linkedin" aria-hidden="true"></i><a href="https://www.linkedin.com/in/jonathan-simo-511322264">Jonathan Simo Kuate</a></li>
            </ul>
        </aside>
    </div>
</section>
