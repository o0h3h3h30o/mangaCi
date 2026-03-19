<?= $this->extend('themes/comixx/layouts/main') ?>

<?= $this->section('content') ?>

<div class="profile-page">
    <div class="profile-banner">
        <div class="profile-avatar">
            <img src="https://ui-avatars.com/api/?name=<?= esc(urlencode($currentUser['username'])) ?>&size=120&background=6C5CE7&color=fff" alt="<?= esc($currentUser['username']) ?>">
        </div>
        <div class="profile-info">
            <h1><?= esc($currentUser['username']) ?></h1>
            <p>Cambiar Contraseña</p>
        </div>
    </div>

    <div class="profile-tab-content">
        <div class="profile-form-header">
            <a href="/profile" class="btn btn-back">&larr; Volver al Perfil</a>
            <h2>Cambiar Contraseña</h2>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/profile/change-password" method="post" class="profile-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">Nueva Contraseña</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Nueva Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
