<?php
// Create or edit an admin. Blank password on edit keeps the current one.
$action = $id ? url('/admin/users/' . (int) $id . '/edit') : url('/admin/users/new');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="name">Name (required)</label>
        <input id="name" name="name" required value="<?= e(field_value($posted, $row, 'name')) ?>">
    </div>
    <div class="field">
        <label for="email">Email (required)</label>
        <input id="email" name="email" type="email" required autocomplete="off" value="<?= e(field_value($posted, $row, 'email')) ?>">
    </div>
    <div class="field">
        <label for="password">Password<?= $id ? '' : ' (required)' ?></label>
        <input id="password" name="password" type="password" autocomplete="new-password"<?= $id ? '' : ' required' ?>>
        <?php if ($id): ?><p class="fine">Leave blank to keep the current password.</p><?php endif; ?>
    </div>
    <div class="field">
        <label for="password_confirm">Confirm password</label>
        <input id="password_confirm" name="password_confirm" type="password" autocomplete="new-password">
    </div>
    <button class="btn btn-primary" type="submit">Save admin</button>
</form>
<?php if ($id && (int) $id !== Auth::id()): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/users/' . (int) $id . '/delete')) ?>" data-confirm="Delete this admin?">
        <?= Csrf::field() ?>
        <button type="submit">Delete admin</button>
    </form>
<?php endif; ?>
