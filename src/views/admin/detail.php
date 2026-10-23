<?php

use PoleIt\Csrf;

$hidden = ['id', 'lu'];
$names = [
    'created_at' => 'Reçu le', 'code_postal' => 'Code postal', 'date_debut' => 'Début souhaité',
    'telephone' => 'Téléphone', 'prenom' => 'Prénom', 'reference' => 'Référence', 'age' => 'Âge', 'duree' => 'Durée (mois)',
];
?>
<section class="section admin">
    <div class="container narrow">
        <?php require __DIR__ . '/_bar.php'; ?>
        <a class="link-arrow back" href="/admin/?t=<?= e($tab) ?>"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour à la liste</a>

        <article class="panel">
            <dl class="fiche-list">
                <?php foreach ($item as $key => $value): ?>
                    <?php if (in_array($key, $hidden, true)) continue; ?>
                    <div>
                        <dt><?= e($names[$key] ?? ucfirst(str_replace('_', ' ', $key))) ?></dt>
                        <dd class="<?= in_array($key, ['message', 'motivation'], true) ? 'pre' : '' ?>"><?=
                            e($key === 'created_at' ? date('d/m/Y à H:i', strtotime($value . ' UTC')) : ($labels[$key][$value] ?? $value))
                        ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>

            <div class="detail-actions">
                <a class="btn btn-primary btn-sm" href="mailto:<?= e($item['email']) ?>"><i class="fa-solid fa-reply" aria-hidden="true"></i> Répondre</a>
                <?php if ($tab === 'stages'): ?>
                    <a class="btn btn-ghost-dark btn-sm" href="/stage-fiche.php?ref=<?= e(urlencode($item['reference'])) ?>"><i class="fa-solid fa-file-pdf" aria-hidden="true"></i> Fiche PDF</a>
                <?php endif; ?>
                <form method="post" action="/admin/supprimer.php" data-confirm="Supprimer définitivement cet élément ?">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="t" value="<?= e($tab) ?>">
                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                    <button class="btn btn-danger btn-sm" type="submit"><i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer</button>
                </form>
            </div>
        </article>
    </div>
</section>
