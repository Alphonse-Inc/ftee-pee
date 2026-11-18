<?php
// Branch list. The map link is a Google search URL, not a paid Maps embed.
?>
<section class="section">
    <div class="wrap">
        <nav class="crumbs" aria-label="Breadcrumb">
            <a href="<?= e(url('/')) ?>">Home</a>
            <span aria-hidden="true">/</span>
            <span>Branches</span>
        </nav>
        <h1>Branches</h1>
        <p class="lede"><?= e(setting('branches_intro')) ?></p>
        <div class="branch-list">
            <?php foreach ($branches as $branch): ?>
                <article class="docket branch-card">
                    <?php if ((int) $branch['is_head_office'] === 1): ?>
                        <p class="sample-flag">Head office</p>
                    <?php endif; ?>
                    <h2><?= e($branch['name']) ?></h2>
                    <?php foreach (branch_lines($branch) as $line): ?>
                        <p class="docket-line"><?= e($line) ?></p>
                    <?php endforeach; ?>
                    <?php if ($branch['hours_text'] !== ''): ?>
                        <p class="docket-label">Hours</p>
                        <p><?= e($branch['hours_text']) ?></p>
                    <?php endif; ?>
                    <?php
                    $phone = $branch['phone'] !== '' ? $branch['phone'] : setting('phone');
                    $email = $branch['email'] !== '' ? $branch['email'] : setting('email');
                    ?>
                    <?php if ($phone !== ''): ?>
                        <p><a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>"><?= e($phone) ?></a></p>
                    <?php endif; ?>
                    <?php if ($email !== ''): ?>
                        <p><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p>
                    <?php endif; ?>
                    <?php if ($branch['notes'] !== ''): ?>
                        <div class="prose"><?= format_text($branch['notes']) ?></div>
                    <?php endif; ?>
                    <?php $map = branch_map_url($branch); ?>
                    <div class="cluster">
                        <?php if ($map !== ''): ?>
                            <a class="btn btn-ghost" href="<?= e($map) ?>" target="_blank" rel="noopener noreferrer">Open in Google Maps</a>
                        <?php endif; ?>
                        <a class="btn btn-primary" href="<?= e(url('/contact?branch=' . (int) $branch['id'])) ?>">Request a quote</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
