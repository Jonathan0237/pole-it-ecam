<section class="section">
    <div class="container split">
        <div class="reveal">
            <p class="kicker">Qui sommes-nous ?</p>
            <h2 class="section-title">Une grande école d'ingénieurs <span>née de l'industrie</span></h2>
            <p>L'ECAM-EPMI est une grande école d'ingénieurs généraliste créée en 1992 à l'initiative de quatre groupes industriels : <strong>EDF, Schneider Electric, Philips et PSA</strong>.</p>
            <p>Reconnue par l'État, membre de la Conférence des grandes écoles et habilitée par la CTI à délivrer le diplôme d'ingénieur depuis 1996, elle construit son projet pédagogique en lien direct avec les entreprises. Le Pôle IT y forme des ingénieurs au numérique : développement, réseaux, données et systèmes embarqués.</p>
            <a class="link-arrow" href="/a-propos.php">Découvrir le Pôle IT <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <ul class="stats reveal" aria-label="L'école en chiffres">
            <li><strong data-count="1992">1992</strong><span>Année de création</span></li>
            <li><strong data-count="30">30</strong><span>Promotions diplômées</span></li>
            <li><strong data-count="4000" data-suffix="+">4000+</strong><span>Ingénieurs en poste</span></li>
            <li><strong data-count="80" data-suffix=" %">80 %</strong><span>Embauchés avant le diplôme</span></li>
        </ul>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Pourquoi nous rejoindre</p>
            <h2 class="section-title">Trois atouts <span>pour votre avenir</span></h2>
        </div>
        <div class="cards cards-3">
            <article class="card reveal">
                <span class="card-icon"><i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i></span>
                <h3>Une formation de qualité</h3>
                <p>Des enseignants-chercheurs et des intervenants issus de l'industrie, des projets concrets dès la première année.</p>
            </article>
            <article class="card reveal">
                <span class="card-icon"><i class="fa-solid fa-computer" aria-hidden="true"></i></span>
                <h3>Des équipements disponibles</h3>
                <p>Salles informatiques, laboratoires d'électronique et de robotique, accès en libre-service pour les projets étudiants.</p>
            </article>
            <article class="card reveal">
                <span class="card-icon"><i class="fa-solid fa-earth-europe" aria-hidden="true"></i></span>
                <h3>Une notoriété internationale</h3>
                <p>Des partenariats à l'étranger, des stages et semestres d'échange pour construire un profil international.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">La vie du campus</p>
            <h2 class="section-title">Le Pôle IT <span>en images</span></h2>
        </div>
        <div class="slider reveal" data-slider aria-roledescription="carrousel" aria-label="Photos du campus">
            <div class="slides">
                <?php
                $photos = [
                    ['conference', 'Conférence dans la salle de projection'],
                    ['cours', 'Cours en petit groupe'],
                    ['amphi', 'Amphithéâtre lors d\'une conférence'],
                    ['projet', 'Étudiants en séance de projet'],
                    ['amphi-2', 'Rentrée en amphithéâtre'],
                    ['robotique', 'Atelier de robotique'],
                    ['equipe', 'Travail en équipe sur poste informatique'],
                ];
                foreach ($photos as $i => [$file, $alt]): ?>
                    <figure class="slide <?= $i === 0 ? 'is-active' : '' ?>" role="group" aria-roledescription="diapositive" aria-label="<?= $i + 1 ?> sur <?= count($photos) ?>">
                        <img src="/assets/img/<?= $file ?>.jpg" alt="<?= e($alt) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>" width="1600" height="1000">
                        <figcaption><?= e($alt) ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
            <button class="slider-btn prev" type="button" data-prev aria-label="Photo précédente"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
            <button class="slider-btn next" type="button" data-next aria-label="Photo suivante"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
            <div class="slider-dots" data-dots></div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head reveal">
            <p class="kicker">Nos services</p>
            <h2 class="section-title">Tout votre parcours <span>au même endroit</span></h2>
        </div>
        <div class="cards cards-3">
            <a class="card card-link reveal" href="/admission.php">
                <span class="card-icon"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i></span>
                <h3>Admission</h3>
                <p>Conditions, parcours d'entrée et pré-inscription en ligne en trois étapes.</p>
                <span class="link-arrow">Candidater <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
            </a>
            <a class="card card-link reveal" href="/stage.php">
                <span class="card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span>
                <h3>Stage</h3>
                <p>Déposez votre demande de stage et téléchargez votre fiche récapitulative en PDF.</p>
                <span class="link-arrow">Déposer une demande <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
            </a>
            <a class="card card-link reveal" href="/insertion.php">
                <span class="card-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
                <h3>Insertion</h3>
                <p>Employabilité, premier salaire et poursuites d'études de nos diplômés.</p>
                <span class="link-arrow">Voir les chiffres <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
            </a>
        </div>
    </div>
</section>
