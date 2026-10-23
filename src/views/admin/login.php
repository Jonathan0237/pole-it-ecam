<?php use PoleIt\Csrf; ?>
<section class="section">
    <div class="container login-wrap">
        <form method="post" action="/admin/login.php" class="form panel">
            <h1 class="section-title">Espace <span>admin</span></h1>
            <?php if ($error): ?><div class="alert alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
            <?= Csrf::field() ?>
            <div class="field">
                <label for="utilisateur">Identifiant</label>
                <input id="utilisateur" name="utilisateur" autocomplete="username" required>
            </div>
            <div class="field">
                <label for="mot_de_passe">Mot de passe</label>
                <input id="mot_de_passe" name="mot_de_passe" type="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Se connecter</button>
        </form>
    </div>
</section>
