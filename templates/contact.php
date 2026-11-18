<?php
// Quote and contact form. The enquiry is stored before any email is attempted.
$posted = isset($posted) && is_array($posted) ? $posted : null;
$errors = isset($errors) && is_array($errors) ? $errors : [];
$value = function ($key) use ($posted) {
    if (!$posted || !array_key_exists($key, $posted)) {
        return '';
    }
    return $posted[$key];
};
?>
<section class="section">
    <div class="wrap split">
        <div>
            <nav class="crumbs" aria-label="Breadcrumb">
                <a href="<?= e(url('/')) ?>">Home</a>
                <span aria-hidden="true">/</span>
                <span>Contact</span>
            </nav>
            <h1>Request a quote</h1>
            <p class="lede"><?= e(setting('contact_intro')) ?></p>
            <?php if (!empty($office)): ?>
                <div class="docket">
                    <p class="docket-label">Head office</p>
                    <?php foreach (branch_lines($office) as $line): ?>
                        <p class="docket-line"><?= e($line) ?></p>
                    <?php endforeach; ?>
                    <?php if (setting('phone') !== ''): ?>
                        <p><a href="tel:<?= e(preg_replace('/\s+/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
                    <?php endif; ?>
                    <?php if (setting('email') !== ''): ?>
                        <p><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <form class="form" method="post" action="<?= e(url('/contact')) ?>" novalidate>
            <?= Csrf::field() ?>
            <div class="hp" aria-hidden="true">
                <label>Company website
                    <input type="text" name="company_website" tabindex="-1" autocomplete="off" value="">
                </label>
            </div>
            <?php if (!empty($errors['form'])): ?>
                <div class="flash flash-error" role="alert"><?= e($errors['form']) ?></div>
            <?php endif; ?>
            <?php if ($errors): ?>
                <div class="flash flash-error" role="alert">
                    <p>Please fix the following:</p>
                    <ul>
                        <?php foreach ($errors as $key => $error): ?>
                            <?php if ($key === 'form') { continue; } ?>
                            <li><a href="#field-<?= e($key) ?>"><?= e($error) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="field">
                <label for="field-service_id">Service (required)</label>
                <select id="field-service_id" name="service_id" required>
                    <option value="">Select a service</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= (int) $service['id'] ?>"<?= ((string) $selectedService === (string) $service['id']) ? ' selected' : '' ?>><?= e($service['name']) ?></option>
                    <?php endforeach; ?>
                    <option value="none"<?= $selectedService === 'none' ? ' selected' : '' ?>>Not sure yet</option>
                </select>
            </div>
            <div class="field">
                <label for="field-branch_id">Branch (required)</label>
                <select id="field-branch_id" name="branch_id" required>
                    <option value="">Select a branch</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= (int) $branch['id'] ?>"<?= ((string) $selectedBranch === (string) $branch['id']) ? ' selected' : '' ?>><?= e($branch['name']) ?></option>
                    <?php endforeach; ?>
                    <option value="none"<?= $selectedBranch === 'none' ? ' selected' : '' ?>>Not sure yet</option>
                </select>
            </div>
            <div class="field">
                <label for="field-name">Name (required)</label>
                <input id="field-name" name="name" type="text" required maxlength="120" autocomplete="name" value="<?= e($value('name')) ?>">
            </div>
            <div class="field">
                <label for="field-phone">Phone (required)</label>
                <input id="field-phone" name="phone" type="tel" required inputmode="tel" maxlength="40" autocomplete="tel" placeholder="0803 123 4567" value="<?= e($value('phone')) ?>">
            </div>
            <div class="field">
                <label for="field-email">Email (required)</label>
                <input id="field-email" name="email" type="email" required maxlength="190" autocomplete="email" value="<?= e($value('email')) ?>">
            </div>
            <div class="field">
                <label for="field-message">Message (required)</label>
                <textarea id="field-message" name="message" rows="6" required maxlength="5000" placeholder="Quantity, property size, vehicle type, and when you need the work."><?= e($value('message')) ?></textarea>
            </div>
            <p class="fine">We use these details only to reply to this request. See the <a href="<?= e(url('/privacy')) ?>">privacy page</a>.</p>
            <button class="btn btn-primary" type="submit">Send the request</button>
        </form>
    </div>
</section>
