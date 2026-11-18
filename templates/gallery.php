<?php
// Gallery. Placeholder visuals are labeled so they are not read as client work.
?>
<section class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <span>Gallery</span>
        </nav>
        <h1>Gallery</h1>
        <p class="lede"><?= e(setting('gallery_intro')) ?></p>
        <?php if (!empty($showPlaceholderNote)): ?>
            <p class="note">Items marked Placeholder are layout visuals drawn for this website. They are not photographs of customer work.</p>
        <?php endif; ?>
        <?php if (!$items): ?>
            <p>Photos will appear here when the admin adds them.</p>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($items as $item): ?>
                    <figure>
                        <img src="<?= e(media_url($item['image_path'])) ?>" alt="<?= e($item['alt_text'] !== '' ? $item['alt_text'] : $item['title']) ?>" width="<?= (int) $item['image_width'] ?>" height="<?= (int) $item['image_height'] ?>" loading="lazy" decoding="async">
                        <figcaption>
                            <?php if ((int) $item['is_placeholder'] === 1): ?>
                                <span class="sample-flag">Placeholder</span>
                            <?php endif; ?>
                            <strong><?= e($item['title']) ?></strong>
                            <?php if ($item['caption'] !== ''): ?>
                                <span><?= e($item['caption']) ?></span>
                            <?php endif; ?>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
