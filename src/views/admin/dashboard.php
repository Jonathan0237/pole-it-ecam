<?php
$icons = ['messages' => 'fa-envelope', 'admissions' => 'fa-user-graduate', 'stages' => 'fa-briefcase'];
$query = static fn (array $p): string => '/admin/?' . http_build_query(array_filter($p + ['t' => $tab, 'q' => $search], static fn ($v) => $v !== '' && $v !== null));
?>
<section class="section admin">
    <div class="container">
        <?php require __DIR__ . '/_bar.php'; ?>

        <div class="kpis">
            <?php foreach ($tabs as $key => $label): ?>
                <a class="kpi <?= $key === $tab ? 'is-active' : '' ?>" href="/admin/?t=<?= $key ?>">
                    <i class="fa-solid <?= $icons[$key] ?>" aria-hidden="true"></i>
                    <span class="kpi-value"><?= $counts[$key]['total'] ?></span>
                    <span class="kpi-label"><?= e($label) ?></span>
                    <?php if ($counts[$key]['unread']): ?><span class="pill"><?= $counts[$key]['unread'] ?> non lu(s)</span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="panel">
            <div class="table-tools">
                <form method="get" action="/admin/" class="search" role="search">
                    <input type="hidden" name="t" value="<?= e($tab) ?>">
                    <label class="sr-only" for="q">Rechercher</label>
                    <input id="q" name="q" type="search" value="<?= e($search) ?>" placeholder="Nom, prénom ou e-mail…">
                    <button class="btn btn-primary btn-sm" type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><span class="sr-only">Rechercher</span></button>
                </form>
                <a class="btn btn-ghost-dark btn-sm" href="/admin/export.php?t=<?= e($tab) ?>"><i class="fa-solid fa-file-csv" aria-hidden="true"></i> Export CSV</a>
            </div>

            <?php if (!$rows): ?>
                <p class="empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i> Aucun élément<?= $search !== '' ? ' pour « ' . e($search) . ' »' : '' ?>.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Nom</th>
                                <th scope="col">E-mail</th>
                                <th scope="col"><?= $tab === 'messages' ? 'Sujet' : ($tab === 'admissions' ? 'Programme' : 'Référence') ?></th>
                                <th scope="col"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr class="<?= $row['lu'] ? '' : 'is-unread' ?>">
                                    <td><?= e(date('d/m/Y H:i', strtotime($row['created_at'] . ' UTC'))) ?></td>
                                    <td><?= e(trim(($row['prenom'] ?? '') . ' ' . $row['nom'])) ?></td>
                                    <td><a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a></td>
                                    <td><?= e(match ($tab) {
                                        'messages'   => \PoleIt\Forms::SUJETS[$row['sujet']] ?? $row['sujet'],
                                        'admissions' => \PoleIt\Forms::PROGRAMMES[$row['programme']] ?? $row['programme'],
                                        default      => $row['reference'],
                                    }) ?></td>
                                    <td class="actions"><a class="btn btn-sm btn-ghost-dark" href="/admin/voir.php?t=<?= e($tab) ?>&amp;id=<?= (int) $row['id'] ?>">Voir</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($pages > 1): ?>
                    <nav class="pagination" aria-label="Pagination">
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <a href="<?= e($query(['p' => $i])) ?>" <?= $i === $current ? 'aria-current="page"' : '' ?>><?= $i ?></a>
                        <?php endfor; ?>
                    </nav>
                <?php endif; ?>
                <p class="muted small"><?= $total ?> élément(s)</p>
            <?php endif; ?>
        </div>
    </div>
</section>
