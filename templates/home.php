<?php
// Homepage: services, process, head office, and a short FAQ.
$services = published_services();
?>
<section class="hero">
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="kicker"><?= e(setting('home_kicker')) ?></p>
            <h1 class="hero-title"><?= nl2br(e(setting('home_title')), false) ?></h1>
            <p class="hero-lead"><?= e(setting('home_lead')) ?></p>
            <div class="cluster">
                <a class="btn btn-primary" href="<?= e(url('/contact')) ?>">Request a quote</a>
                <a class="btn btn-ghost" href="<?= e(url('/services')) ?>">See the services</a>
            </div>
        </div>
        <ol class="hero-rail">
            <?php foreach ($services as $index => $service): ?>
                <li>
                    <a class="rail-link" href="<?= e(url('/services/' . $service['slug'])) ?>">
                        <span class="rail-num"><?= e(sprintf('%02d', $index + 1)) ?></span>
                        <span class="rail-name"><?= e($service['name']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="ink-band">
    <div class="wrap">
        <h2><?= e(setting('home_steps_title', 'How a job moves')) ?></h2>
        <ol class="steps">
            <?php for ($step = 1; $step <= 3; $step++): ?>
                <li>
                    <span><?= e(sprintf('%02d', $step)) ?></span>
                    <h3><?= e(setting('step_' . $step . '_title')) ?></h3>
                    <p><?= e(setting('step_' . $step . '_body')) ?></p>
                </li>
            <?php endfor; ?>
        </ol>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <div class="section-head">
            <p class="kicker">Four desks</p>
            <h2><?= e(setting('home_services_title', 'What we take on')) ?></h2>
        </div>
        <div class="service-list">
            <?php foreach ($services as $index => $service): ?>
                <article class="service-row">
                    <p class="service-num"><?= e(sprintf('%02d', $index + 1)) ?></p>
                    <div>
                        <h3><a href="<?= e(url('/services/' . $service['slug'])) ?>"><?= e($service['name']) ?></a></h3>
                        <p><?= e($service['summary']) ?></p>
                    </div>
                    <a class="text-link" href="<?= e(url('/services/' . $service['slug'])) ?>">View service</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($office)): ?>
<section class="section section-tight">
    <div class="wrap split">
        <div>
            <p class="kicker">Head office</p>
            <h2><?= e(setting('home_office_title', 'Find us in Aguda')) ?></h2>
            <div class="docket">
                <p class="docket-label">Location</p>
                <?php foreach (branch_lines($office) as $line): ?>
                    <p class="docket-line"><?= e($line) ?></p>
                <?php endforeach; ?>
                <?php if (!empty($office['hours_text'])): ?>
                    <p class="docket-label">Hours</p>
                    <p class="docket-line"><?= e($office['hours_text']) ?></p>
                <?php endif; ?>
                <?php $map = branch_map_url($office); ?>
                <?php if ($map !== ''): ?>
                    <p><a class="text-link" href="<?= e($map) ?>" target="_blank" rel="noopener noreferrer">Map search for this Plus Code</a></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="office-note">
            <?php if (!empty($office['notes'])): ?>
                <div class="prose"><?= format_text($office['notes']) ?></div>
            <?php endif; ?>
            <a class="btn btn-primary" href="<?= e(url('/contact?branch=' . (int) $office['id'])) ?>">Request a quote</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($faqs)): ?>
<section class="section">
    <div class="wrap">
        <div class="section-head">
            <p class="kicker">Before you write</p>
            <h2><?= e(setting('home_faq_title', 'Questions people ask')) ?></h2>
        </div>
        <div class="faq-list">
            <?php foreach ($faqs as $faq): ?>
                <details class="faq">
                    <summary><?= e($faq['question']) ?></summary>
                    <div class="faq-body"><?= format_text($faq['answer']) ?></div>
                </details>
            <?php endforeach; ?>
        </div>
        <p class="more-link"><a href="<?= e(url('/faq')) ?>">All questions</a></p>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($testimonials)): ?>
<section class="section">
    <div class="wrap">
        <h2>What customers have agreed we may quote</h2>
        <div class="quote-list">
            <?php foreach ($testimonials as $quote): ?>
                <blockquote>
                    <p><?= e($quote['quote']) ?></p>
                    <footer><?= e($quote['author_name']) ?><?php if ($quote['author_detail'] !== ''): ?>, <?= e($quote['author_detail']) ?><?php endif; ?></footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="closing">
    <div class="wrap closing-inner">
        <h2><?= e(setting('home_closing')) ?></h2>
        <a class="btn btn-primary" href="<?= e(url('/contact')) ?>">Request a quote</a>
    </div>
</section>
