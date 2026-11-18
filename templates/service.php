<?php
// One service line, with sample packages, process, and FAQ.
$hasSample = false;
foreach ($packages as $package) {
    if ((int) $package['is_sample'] === 1) {
        $hasSample = true;
        break;
    }
}
?>
<article class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?= e(url('/services')) ?>">Services</a>
            <span aria-hidden="true">/</span>
            <span><?= e($service['name']) ?></span>
        </nav>
        <div class="service-hero">
            <?php service_icon($service['icon_key']); ?>
            <h1><?= e($service['name']) ?></h1>
        </div>
        <p class="lede"><?= e($service['summary']) ?></p>
        <div class="prose"><?= format_text($service['body']) ?></div>

        <?php if ($packages): ?>
            <h2>Packages</h2>
            <?php if ($hasSample): ?>
                <p class="note">Prices marked Sample are starting points taken from publicly listed Lagos ranges, so you can see the shape of a package. They are not a confirmed FTEE-PEE tariff. Request a quote and we will price the job in front of us.</p>
            <?php endif; ?>
            <div class="package-grid">
                <?php foreach ($packages as $package): ?>
                    <article class="package">
                        <?php if ((int) $package['is_sample'] === 1): ?>
                            <p class="sample-flag">Sample price</p>
                        <?php endif; ?>
                        <h3><?= e($package['name']) ?></h3>
                        <p class="price"><?= e(package_price($package)) ?></p>
                        <?php if ($package['summary'] !== ''): ?>
                            <p><?= e($package['summary']) ?></p>
                        <?php endif; ?>
                        <?php $features = line_items($package['features']); ?>
                        <?php if ($features): ?>
                            <ul>
                                <?php foreach ($features as $feature): ?>
                                    <li><?= e($feature) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if ($package['price_note'] !== ''): ?>
                            <p class="price-note"><?= e($package['price_note']) ?></p>
                        <?php endif; ?>
                        <a class="text-link" href="<?= e(url('/contact?service=' . $service['slug'])) ?>">Request a quote</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (trim($service['process']) !== ''): ?>
            <h2>How it goes</h2>
            <div class="prose"><?= format_text($service['process']) ?></div>
        <?php endif; ?>

        <?php if ($faqs): ?>
            <h2>Questions</h2>
            <div class="faq-list">
                <?php foreach ($faqs as $faq): ?>
                    <details class="faq">
                        <summary><?= e($faq['question']) ?></summary>
                        <div class="faq-body"><?= format_text($faq['answer']) ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="closing-inline"><a class="btn btn-primary" href="<?= e(url('/contact?service=' . $service['slug'])) ?>">Request a quote</a></p>
    </div>
</article>
