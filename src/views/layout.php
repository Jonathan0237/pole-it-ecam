<?php
/** @var string $content */
$page        = $page ?? '';
$title       = isset($title) ? $title . ' · Pôle IT ECAM-EPMI' : 'Pôle IT ECAM-EPMI';
$description = $description ?? "Site de présentation du Pôle IT de l'ECAM-EPMI : formation, admission, stages et insertion professionnelle.";
$flash       = flash();

$nav = [
    'accueil'  => ['/', 'Accueil', 'fa-house'],
    'a-propos' => ['/a-propos.php', 'À propos', 'fa-circle-info'],
];
$services = [
    'admission' => ['/admission.php', 'Admission', 'fa-user-graduate'],
    'stage'     => ['/stage.php', 'Stage', 'fa-briefcase'],
    'insertion' => ['/insertion.php', 'Insertion', 'fa-chart-line'],
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta name="theme-color" content="#172c80">
    <meta property="og:title" content="<?= e($title) ?>">
    <meta property="og:description" content="<?= e($description) ?>">
    <meta property="og:image" content="/assets/img/amphi-2.jpg">
    <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <script src="<?= asset('js/theme.js') ?>"></script>
    <script src="<?= asset('js/main.js') ?>" defer></script>
</head>
<body class="page-<?= e($page) ?>">
<a class="skip-link" href="#contenu">Aller au contenu</a>

<header class="site-header" data-header>
    <div class="container header-inner">
        <a class="brand" href="/" aria-label="Pôle IT ECAM-EPMI, accueil">
            <span class="brand-logo"><img src="/assets/img/logo-ecam-epmi.png" alt="ECAM-EPMI" width="157" height="26"></span>
            <span class="brand-text">Pôle IT</span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu" data-nav-toggle>
            <span class="sr-only">Ouvrir le menu</span><i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <nav class="main-nav" id="menu" aria-label="Navigation principale">
            <ul>
                <?php foreach ($nav as $key => [$href, $label, $icon]): ?>
                    <li><a href="<?= $href ?>" <?= $page === $key ? 'aria-current="page"' : '' ?>><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i><?= $label ?></a></li>
                <?php endforeach; ?>
                <li class="has-submenu">
                    <button type="button" aria-expanded="false" data-submenu-toggle <?= isset($services[$page]) ? 'class="is-current"' : '' ?>>
                        <i class="fa-solid fa-layer-group" aria-hidden="true"></i>Services<i class="fa-solid fa-chevron-down caret" aria-hidden="true"></i>
                    </button>
                    <ul class="submenu">
                        <?php foreach ($services as $key => [$href, $label, $icon]): ?>
                            <li><a href="<?= $href ?>" <?= $page === $key ? 'aria-current="page"' : '' ?>><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i><?= $label ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li><a href="/contact.php" <?= $page === 'contact' ? 'aria-current="page"' : '' ?>><i class="fa-solid fa-envelope" aria-hidden="true"></i>Contact</a></li>
            </ul>
            <button class="theme-toggle" type="button" data-theme-toggle aria-label="Changer de thème">
                <i class="fa-solid fa-moon" aria-hidden="true"></i>
            </button>
        </nav>
    </div>
</header>

<?php if (!empty($hero)): ?>
    <section class="hero <?= !empty($hero['small']) ? 'hero-small' : '' ?>">
        <?php if (!empty($hero['image'])): ?>
            <img class="hero-bg" src="/assets/img/<?= e($hero['image']) ?>.jpg" alt="" fetchpriority="high">
        <?php endif; ?>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <?php if (!empty($hero['kicker'])): ?><p class="kicker"><?= e($hero['kicker']) ?></p><?php endif; ?>
            <h1><?= $hero['title'] ?></h1>
            <?php if (!empty($hero['text'])): ?><p class="hero-text"><?= e($hero['text']) ?></p><?php endif; ?>
            <?php if (!empty($hero['actions'])): ?>
                <div class="hero-actions">
                    <?php foreach ($hero['actions'] as $i => [$href, $label]): ?>
                        <a class="btn <?= $i === 0 ? 'btn-primary' : 'btn-ghost' ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<main id="contenu">
    <?php if ($flash): ?>
        <div class="container">
            <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
        </div>
    <?php endif; ?>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand brand-footer" href="/">
                <span class="brand-logo"><img src="/assets/img/logo-ecam-epmi.png" alt="ECAM-EPMI" width="157" height="26"></span>
                <span class="brand-text">Pôle IT</span>
            </a>
            <p class="muted">Projet de développement web réalisé en 2022 à l'ECAM-EPMI, refondu en 2026 en PHP.</p>
        </div>
        <div>
            <h2 class="footer-title">Navigation</h2>
            <ul class="footer-links">
                <li><a href="/a-propos.php">À propos</a></li>
                <li><a href="/admission.php">Admission</a></li>
                <li><a href="/stage.php">Stage</a></li>
                <li><a href="/insertion.php">Insertion</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
        </div>
        <div>
            <h2 class="footer-title">Équipe projet (groupe 12)</h2>
            <ul class="footer-links">
                <li>Jonathan Simo Kuate</li>
                <li>Emeric Tchoffo</li>
                <li>Will Jerph Yemeufack</li>
                <li>Fabrice Talla Teneku</li>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <p><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Projet étudiant non officiel, sans lien commercial avec l'ECAM-EPMI. Les formulaires sont fictifs<?= DEMO_MODE ? ' : merci de ne saisir aucune donnée réelle' : '' ?>.</p>
        <p><a href="https://github.com/Jonathan0237/pole-it-ecam"><i class="fa-brands fa-github" aria-hidden="true"></i> Code source</a> · <a href="/admin/">Espace admin</a></p>
    </div>
</footer>
</body>
</html>
