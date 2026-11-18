<?php
// Admin login form.
?>
<?php if (!empty($error)): ?>
    <div class="flash flash-error" role="alert"><?= e($error) ?></div>
<?php endif; ?>
<form class="form" method="post" action="<?= e(url('/admin/login')) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required autocomplete="username" value="<?= e(isset($email) ? $email : '') ?>">
    </div>
    <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password">
    </div>
    <button class="btn btn-primary" type="submit">Sign in</button>
</form>
