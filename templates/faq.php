<?php
// All published questions, grouped by service.
?>
<section class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <span>FAQ</span>
        </nav>
        <h1>Questions</h1>
        <p class="lede"><?= e(setting('faq_intro')) ?></p>
        <?php if (!$grouped): ?>
            <p>Questions will appear here when they are published.</p>
        <?php endif; ?>
        <?php foreach ($grouped as $label => $faqs): ?>
            <h2><?= e($label) ?></h2>
            <div class="faq-list">
                <?php foreach ($faqs as $faq): ?>
                    <details class="faq">
                        <summary><?= e($faq['question']) ?></summary>
                        <div class="faq-body"><?= format_text($faq['answer']) ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
