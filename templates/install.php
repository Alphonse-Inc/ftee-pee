<?php
// Installer screen. Runs once, then the script refuses to run again.
$mode = isset($mode) ? $mode : 'form';
$prefix = isset($prefix) ? $prefix : '';
$css = ($prefix === '' ? '' : $prefix) . '/assets/css/site.css';
$icon = ($prefix === '' ? '' : $prefix) . '/assets/img/favicon.svg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install FTEE-PEE</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= e($icon) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e($css) ?>">
</head>
<body>
<main class="installer">
    <div class="installer-card">
        <p class="kicker">FTEE-PEE NIG LTD</p>
        <?php if ($mode === 'locked'): ?>
            <h1>Already installed</h1>
            <p>This installer has already run and will not run again. Delete install.php from the server if you want it gone completely. The lock file is config/installed.lock.</p>
            <div class="cluster">
                <a class="btn btn-primary" href="<?= e($home) ?>">View the site</a>
                <a class="btn btn-ghost" href="<?= e($admin) ?>">Admin sign in</a>
            </div>
        <?php elseif ($mode === 'done'): ?>
            <h1>Installed</h1>
            <p>The database, the starter pages, and the first admin are in place. Sign in with <?= e($email) ?>.</p>
            <p>Add the company phone, email, WhatsApp number, and opening hours under Settings before you announce the site. Sample prices are marked Sample. The wordmark stands in until you upload a logo.</p>
            <?php if (!$gd): ?>
                <p class="note">GD is not enabled. Uploads still work, but images will not be resized.</p>
            <?php endif; ?>
            <?php if (!empty($warning)): ?>
                <p class="note"><?= e($warning) ?> Set RewriteBase to <?= e($rewrite) ?> in .htaccess so it matches base_url.</p>
            <?php endif; ?>
            <p>You can delete install.php now. If you leave it, it will only show this lock.</p>
            <div class="cluster">
                <a class="btn btn-primary" href="<?= e($admin) ?>">Admin sign in</a>
                <a class="btn btn-ghost" href="<?= e($home) ?>">View the site</a>
            </div>
        <?php else: ?>
            <h1>Install</h1>
            <p>Create the MySQL database and user in cPanel first. This page builds the tables, writes config/config.php, and creates the first admin. It refuses to run twice.</p>
            <?php if (!empty($errors)): ?>
                <div class="flash flash-error" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form class="form" method="post" action="">
                <?= Csrf::field() ?>
                <fieldset>
                    <legend>Database</legend>
                    <div class="field"><label for="db_host">Host</label><input id="db_host" name="db_host" required value="<?= e($old['db_host']) ?>"></div>
                    <div class="field"><label for="db_port">Port</label><input id="db_port" name="db_port" required value="<?= e($old['db_port']) ?>"></div>
                    <div class="field"><label for="db_name">Database name</label><input id="db_name" name="db_name" required value="<?= e($old['db_name']) ?>"></div>
                    <div class="field"><label for="db_user">Database user</label><input id="db_user" name="db_user" required value="<?= e($old['db_user']) ?>"></div>
                    <div class="field"><label for="db_pass">Database password</label><input id="db_pass" name="db_pass" type="password" value=""></div>
                </fieldset>
                <fieldset>
                    <legend>Address</legend>
                    <div class="field">
                        <label for="base_url">Folder path</label>
                        <input id="base_url" name="base_url" value="<?= e($old['base_url']) ?>" placeholder="/fteepee">
                        <p class="fine">Leave empty if the site is the domain root. For a subdirectory, use /fteepee with no trailing slash. This one field is written to config/config.php as base_url and to .htaccess as RewriteBase.</p>
                    </div>
                    <div class="field">
                        <label for="site_url">Site URL</label>
                        <input id="site_url" name="site_url" value="<?= e($old['site_url']) ?>" placeholder="https://example.com">
                        <p class="fine">Origin only, no folder. Used for canonical links. You can change it later in Admin → Settings.</p>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>First admin</legend>
                    <div class="field"><label for="admin_name">Name</label><input id="admin_name" name="admin_name" required value="<?= e($old['admin_name']) ?>"></div>
                    <div class="field"><label for="admin_email">Email</label><input id="admin_email" name="admin_email" type="email" required value="<?= e($old['admin_email']) ?>"></div>
                    <div class="field"><label for="admin_password">Password</label><input id="admin_password" name="admin_password" type="password" required></div>
                    <div class="field"><label for="admin_password_confirm">Confirm password</label><input id="admin_password_confirm" name="admin_password_confirm" type="password" required></div>
                </fieldset>
                <button class="btn btn-primary" type="submit">Install</button>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
