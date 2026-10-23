<?php use PoleIt\Csrf; ?>
<div class="admin-bar">
    <h1 class="admin-title"><i class="fa-solid fa-gauge" aria-hidden="true"></i> Espace admin</h1>
    <form method="post" action="/admin/logout.php">
        <?= Csrf::field() ?>
        <button class="btn btn-ghost-dark btn-sm" type="submit"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> Déconnexion</button>
    </form>
</div>
