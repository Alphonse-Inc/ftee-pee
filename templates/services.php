<?php
// Services index.
?>
<section class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <span>Services</span>
        </nav>
        <p class="kicker">Aguda, Surulere</p>
        <h1>Services</h1>
        <p class="lede"><?= e(setting('services_intro')) ?></p>
        <div class="service-list">
            <?php foreach ($services as $index => $service): ?>
                <article class="service-row">
                    <p class="service-num"><?= e(sprintf('%02d', $index + 1)) ?></p>
                    <div>
                        <?php service_icon($service['icon_key']); ?>
                        <h2><a href="<?= e(url('/services/' . $service['slug'])) ?>"><?= e($service['name']) ?></a></h2>
                        <p><?= e($service['summary']) ?></p>
                    </div>
                    <a class="text-link" href="<?= e(url('/services/' . $service['slug'])) ?>">Open</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
