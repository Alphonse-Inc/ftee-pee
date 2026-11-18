<?php
// Sign-in page, outside the admin navigation.
$business = setting('business_name', 'FTEE-PEE NIG LTD');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | <?= e($business) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= e(asset('img/favicon.svg', false)) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
</head>
<body class="admin-body guest-body">
<main id="main" class="guest-card">
    <p class="kicker"><?= e($business) ?></p>
    <h1>Admin sign in</h1>
    <?php require $contentTemplate; ?>
</main>
</body>
</html>
