<?php
// Blog index for the company's own notes.
?>
<section class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <span>Blog</span>
        </nav>
        <h1>Notes</h1>
        <p class="lede"><?= e(setting('blog_intro')) ?></p>
        <?php if (!$posts): ?>
            <p>New notes will appear here.</p>
        <?php else: ?>
            <div class="post-list">
                <?php foreach ($posts as $post): ?>
                    <article>
                        <p class="meta"><?= e(format_day($post['published_at'])) ?></p>
                        <h2><a href="<?= e(url('/blog/' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h2>
                        <p><?= e($post['excerpt']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
