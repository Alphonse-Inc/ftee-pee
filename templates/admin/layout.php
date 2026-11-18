<?php
// Admin shell. Not indexed.
$business = setting('business_name', 'FTEE-PEE NIG LTD');
$title = isset($title) ? $title : 'Admin';
$nav = isset($nav) ? $nav : '';
$newEnquiries = (int) db()->value('SELECT COUNT(*) FROM enquiries WHERE status = ?', ['new']);
$menu = [
    'dashboard' => ['Dashboard', '/admin'],
    'enquiries' => ['Enquiries', '/admin/enquiries'],
    'settings' => ['Settings', '/admin/settings'],
    'services' => ['Services', '/admin/services'],
    'packages' => ['Packages', '/admin/packages'],
    'branches' => ['Branches', '/admin/branches'],
    'gallery' => ['Gallery', '/admin/gallery'],
    'faqs' => ['FAQs', '/admin/faqs'],
    'posts' => ['Blog', '/admin/posts'],
    'pages' => ['Pages', '/admin/pages'],
    'testimonials' => ['Testimonials', '/admin/testimonials'],
    'users' => ['Users', '/admin/users'],
];
$user = Auth::user();
$success = flash('success');
$errorFlash = flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | <?= e($business) ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="<?= e(asset('img/favicon.svg', false)) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
</head>
<body class="admin-body">
<a class="skip-link" href="#main">Skip to content</a>
<div class="admin-shell">
    <aside class="admin-side">
        <a class="admin-brand" href="<?= e(url('/admin')) ?>"><?= e($business) ?></a>
        <nav aria-label="Admin">
            <?php foreach ($menu as $key => $item): ?>
                <a href="<?= e(url($item[1])) ?>"<?= $nav === $key ? ' aria-current="page"' : '' ?>>
                    <?= e($item[0]) ?>
                    <?php if ($key === 'enquiries' && $newEnquiries > 0): ?>
                        <span class="badge badge-new"><?= (int) $newEnquiries ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-top">
            <p><?= e($user ? $user['name'] : '') ?></p>
            <div class="cluster">
                <a href="<?= e(url('/')) ?>">View site</a>
                <form method="post" action="<?= e(url('/admin/logout')) ?>">
                    <?= Csrf::field() ?>
                    <button type="submit">Sign out</button>
                </form>
            </div>
        </header>
        <main id="main" class="admin-content">
            <h1><?= e($title) ?></h1>
            <?php if ($success): ?><div class="flash flash-ok" role="status"><?= e($success) ?></div><?php endif; ?>
            <?php if ($errorFlash): ?><div class="flash flash-error" role="alert"><?= e($errorFlash) ?></div><?php endif; ?>
            <?php if (!empty($errors) && is_array($errors)): ?>
                <div class="flash flash-error" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php require $contentTemplate; ?>
        </main>
    </div>
</div>
<script src="<?= e(asset('js/site.js')) ?>"></script>
</body>
</html>
