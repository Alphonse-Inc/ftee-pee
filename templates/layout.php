<?php
// Public page shell: header, footer, SEO tags, and JSON-LD.
$business = setting('business_name', 'FTEE-PEE NIG LTD');
$tagline = setting('tagline', '');
$pageTitle = (isset($seoTitle) && $seoTitle !== '') ? $seoTitle : setting('seo_title', $business);
$documentTitle = $pageTitle;
if (stripos($documentTitle, 'FTEE-PEE') === false) {
    $documentTitle .= ' | ' . $business;
}
$pageDescription = (isset($seoDescription) && $seoDescription !== '') ? $seoDescription : setting('seo_description', '');
$robots = isset($robots) ? $robots : 'index, follow';
$ogType = isset($ogType) ? $ogType : 'website';
$bodyClass = isset($bodyClass) ? $bodyClass : '';
$extraLd = (isset($jsonLd) && is_array($jsonLd)) ? $jsonLd : [];
$graph = [
    '@context' => 'https://schema.org',
    '@graph' => array_merge([local_business_node()], $extraLd),
];
$canonical = canonical_url(isset($canonicalPath) ? $canonicalPath : null);
if (setting('logo_path') !== '') {
    $ogImage = absolute_url(media_url(setting('logo_path')));
} else {
    $ogImage = absolute_url(asset('img/og.png', false));
}
$phone = setting('phone');
$phoneAlt = setting('phone_alt');
$email = setting('email');
$whatsapp = whatsapp_href(setting('whatsapp'));
$office = head_office();
$success = flash('success');
$errorFlash = flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($documentTitle) ?></title>
    <?php if ($pageDescription !== ''): ?>
        <meta name="description" content="<?= e($pageDescription) ?>">
    <?php endif; ?>
    <meta name="robots" content="<?= e($robots) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:title" content="<?= e($documentTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:type" content="<?= e($ogType) ?>">
    <meta property="og:site_name" content="<?= e($business) ?>">
    <meta property="og:locale" content="en_NG">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#1b1714">
    <link rel="icon" href="<?= e(asset('img/favicon.svg', false)) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
    <?= json_ld_script($graph) ?>
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main">Skip to content</a>
<div class="topbar">
    <div class="wrap topbar-inner">
        <p><?= e(setting('home_kicker', 'Aguda, Surulere')) ?></p>
        <a href="<?= e(url('/contact')) ?>">Request a quote</a>
    </div>
</div>
<header class="site-header">
    <div class="wrap header-bar">
        <a class="wordmark" href="<?= e(url('/')) ?>">
            <?php if (setting('logo_path') !== ''): ?>
                <img class="logo-img" src="<?= e(media_url(setting('logo_path'))) ?>" alt="<?= e($business) ?>" width="160" height="48">
            <?php else: ?>
                <?php require app_root() . '/templates/partials/mark.php'; ?>
                <span class="wordmark-text">
                    <span class="wordmark-name"><?= e($business) ?></span>
                    <?php if ($tagline !== ''): ?>
                        <span class="wordmark-sub"><?= e($tagline) ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </a>
        <input class="nav-check" type="checkbox" id="nav-check">
        <label class="nav-toggle" for="nav-check">Menu</label>
        <nav class="site-nav" aria-label="Primary">
            <a href="<?= e(url('/services')) ?>"<?= nav_current('/services') ?>>Services</a>
            <a href="<?= e(url('/about')) ?>"<?= nav_current('/about') ?>>About</a>
            <a href="<?= e(url('/branches')) ?>"<?= nav_current('/branches') ?>>Branches</a>
            <a href="<?= e(url('/gallery')) ?>"<?= nav_current('/gallery') ?>>Gallery</a>
            <a href="<?= e(url('/blog')) ?>"<?= nav_current('/blog') ?>>Blog</a>
            <a class="btn btn-primary nav-cta" href="<?= e(url('/contact')) ?>">Request a quote</a>
        </nav>
    </div>
</header>
<main id="main">
    <?php if ($success): ?>
        <div class="wrap"><div class="flash flash-ok" role="status"><?= e($success) ?></div></div>
    <?php endif; ?>
    <?php if ($errorFlash): ?>
        <div class="wrap"><div class="flash flash-error" role="alert"><?= e($errorFlash) ?></div></div>
    <?php endif; ?>
    <?php require $contentTemplate; ?>
</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div>
            <p class="footer-name"><?= e($business) ?></p>
            <p><?= e(setting('footer_blurb')) ?></p>
        </div>
        <div>
            <h2>Visit</h2>
            <?php if ($office): ?>
                <?php foreach (branch_lines($office) as $line): ?>
                    <p><?= e($line) ?></p>
                <?php endforeach; ?>
                <?php $map = branch_map_url($office); ?>
                <?php if ($map !== ''): ?>
                    <p><a href="<?= e($map) ?>" target="_blank" rel="noopener noreferrer">Open in Google Maps</a></p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($phone !== ''): ?><p><a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>"><?= e($phone) ?></a></p><?php endif; ?>
            <?php if ($phoneAlt !== ''): ?><p><a href="tel:<?= e(preg_replace('/\s+/', '', $phoneAlt)) ?>"><?= e($phoneAlt) ?></a></p><?php endif; ?>
            <?php if ($email !== ''): ?><p><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p><?php endif; ?>
        </div>
        <div>
            <h2>Services</h2>
            <ul>
                <?php foreach (published_services() as $service): ?>
                    <li><a href="<?= e(url('/services/' . $service['slug'])) ?>"><?= e($service['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <h2>Pages</h2>
            <ul>
                <li><a href="<?= e(url('/faq')) ?>">FAQ</a></li>
                <li><a href="<?= e(url('/contact')) ?>">Contact</a></li>
                <li><a href="<?= e(url('/privacy')) ?>">Privacy</a></li>
                <li><a href="<?= e(url('/terms')) ?>">Terms</a></li>
            </ul>
        </div>
    </div>
    <div class="wrap footer-base">
        <p>&copy; <?= e(date('Y')) ?> <?= e($business) ?>. Lagos, Nigeria.</p>
        <?php
        $socials = [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'x_url' => 'X',
            'linkedin' => 'LinkedIn',
        ];
        ?>
        <ul class="socials">
            <?php foreach ($socials as $key => $label): ?>
                <?php if (setting($key) !== '' && valid_optional_url(setting($key))): ?>
                    <li><a href="<?= e(setting($key)) ?>" target="_blank" rel="noopener noreferrer"><?= e($label) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</footer>
<?php if ($whatsapp !== ''): ?>
    <a class="whatsapp" href="<?= e($whatsapp) ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
<?php endif; ?>
<div class="mobile-cta">
    <a class="btn btn-primary" href="<?= e(url('/contact')) ?>">Request a quote</a>
</div>
<script src="<?= e(asset('js/site.js')) ?>"></script>
</body>
</html>
