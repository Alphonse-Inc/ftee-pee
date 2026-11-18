<?php
// Editable page: about, privacy, terms, and any extra page.
?>
<article class="section">
    <div class="wrap prose-page">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <span><?= e($page['title']) ?></span>
        </nav>
        <h1><?= e($page['title']) ?></h1>
        <p class="meta">Updated <?= e(format_day($page['updated_at'])) ?></p>
        <div class="prose"><?= format_text($page['body']) ?></div>
    </div>
</article>
