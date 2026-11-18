<?php
// A single blog article.
?>
<article class="section">
    <div class="wrap prose-page">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <a href="<?= e(url('/blog')) ?>">Blog</a>
            <span aria-hidden="true">/</span>
            <span><?= e($post['title']) ?></span>
        </nav>
        <p class="meta"><?= e(format_day($post['published_at'] ? $post['published_at'] : $post['created_at'])) ?> · <?= e(setting('business_name')) ?></p>
        <h1><?= e($post['title']) ?></h1>
        <div class="prose"><?= format_text($post['body']) ?></div>
        <p class="closing-inline"><a class="btn btn-primary" href="<?= e(url('/contact')) ?>">Request a quote</a></p>
    </div>
</article>
