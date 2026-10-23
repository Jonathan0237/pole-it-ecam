<?php
/** @var array $demande */
$debut = DateTimeImmutable::createFromFormat('Y-m-d', $demande['date_debut']);
$rows = [
    'Prénom'        => $demande['prenom'],
    'Nom'           => $demande['nom'],
    'Âge'           => $demande['age'] . ' ans',
    'E-mail'        => $demande['email'],
    'Téléphone'     => $demande['telephone'],
    'Ville'         => $demande['ville'] . ', ' . $demande['pays'],
    'Domaine'       => $domaines[$demande['domaine']] ?? $demande['domaine'],
    'Début souhaité'=> $debut ? $debut->format('d/m/Y') : $demande['date_debut'],
    'Durée'         => $demande['duree'] . ' mois',
];
?>
<section class="section">
    <div class="container narrow">
        <div class="alert alert-success no-print" role="status">Votre demande a bien été enregistrée. Conservez votre fiche ci-dessous.</div>

        <article class="fiche panel">
            <header class="fiche-head">
                <div class="brand">
                    <span class="brand-mark" aria-hidden="true"><i class="fa-solid fa-microchip"></i></span>
                    <span class="brand-text">Pôle IT<small>ECAM-EPMI</small></span>
                </div>
                <div class="fiche-ref">
                    <span>Référence</span>
                    <strong><?= e($demande['reference']) ?></strong>
                </div>
            </header>
            <h1 class="fiche-title">Fiche de demande de stage</h1>
            <dl class="fiche-list">
                <?php foreach ($rows as $label => $value): ?>
                    <div><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd></div>
                <?php endforeach; ?>
            </dl>
            <p class="muted small">Demande enregistrée le <?= e(date('d/m/Y à H:i', strtotime($demande['created_at'] . ' UTC'))) ?>.</p>
        </article>

        <div class="fiche-actions no-print">
            <button type="button" class="btn btn-primary" data-print><i class="fa-solid fa-file-pdf" aria-hidden="true"></i> Télécharger en PDF</button>
            <a class="btn btn-ghost-dark" href="/stage.php">Nouvelle demande</a>
        </div>
    </div>
</section>
