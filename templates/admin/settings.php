<?php
// Business settings, head office, logo, and optional email delivery.
$base = base_url();
?>
<form class="form" method="post" action="<?= e(url('/admin/settings')) ?>" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <fieldset>
        <legend>Folder path</legend>
        <p class="fine">This install is served from <strong><?= $base === '' ? 'the domain root' : e($base) ?></strong>. That value is <code>base_url</code> in config/config.php and must match <code>RewriteBase</code> in .htaccess. Change those two lines together if you move the folder. The domain itself is the Site URL field below.</p>
    </fieldset>
    <?php foreach ($groups as $heading => $fields): ?>
        <fieldset>
            <legend><?= e($heading) ?></legend>
            <?php foreach ($fields as $field): ?>
                <?php
                $key = $field['key'];
                $type = $field['type'] === 'textarea' ? 'textarea' : $field['type'];
                ?>
                <div class="field">
                    <label for="set-<?= e($key) ?>"><?= e($field['label']) ?><?php if (!empty($field['required'])): ?> (required)<?php endif; ?></label>
                    <?php if ($type === 'textarea'): ?>
                        <textarea id="set-<?= e($key) ?>" name="<?= e($key) ?>" rows="<?= (int) (isset($field['rows']) ? $field['rows'] : 4) ?>"><?= e(isset($values[$key]) ? $values[$key] : '') ?></textarea>
                    <?php else: ?>
                        <input id="set-<?= e($key) ?>" name="<?= e($key) ?>" type="<?= e($type) ?>" value="<?= e(isset($values[$key]) ? $values[$key] : '') ?>">
                    <?php endif; ?>
                    <?php if (!empty($field['help'])): ?><p class="fine"><?= e($field['help']) ?></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if ($heading === 'Email delivery'): ?>
                <label class="check"><input type="checkbox" name="mail_enabled" value="1"<?= !empty($mailOn) ? ' checked' : '' ?>> Turn on email delivery</label>
                <p class="fine">The form always saves the enquiry first. mail() runs only when this box is on and a recipient is set. If the host blocks mail, the admin still sees the message.</p>
            <?php endif; ?>
        </fieldset>
    <?php endforeach; ?>
    <fieldset>
        <legend>Head office</legend>
        <p class="fine">Saving this form updates the head office branch. Add more branches from the Branches screen.</p>
        <?php
        $officeFields = [
            ['ho_name', 'name', 'Name', 'Head office'],
            ['ho_area', 'area', 'Area', ''],
            ['ho_address_line', 'address_line', 'Address line', ''],
            ['ho_city', 'city', 'City', ''],
            ['ho_state', 'state', 'State', ''],
            ['ho_postal_code', 'postal_code', 'Postal code', ''],
            ['ho_country', 'country', 'Country', 'Nigeria'],
            ['ho_plus_code', 'plus_code', 'Plus Code', ''],
            ['ho_phone', 'phone', 'Direct phone', ''],
            ['ho_email', 'email', 'Direct email', ''],
            ['ho_hours_text', 'hours_text', 'Opening hours', ''],
        ];
        foreach ($officeFields as $officeField):
            $current = office_field(isset($posted) ? $posted : null, isset($office) ? $office : null, $officeField[0], $officeField[1], $officeField[3]);
        ?>
            <div class="field">
                <label for="<?= e($officeField[0]) ?>"><?= e($officeField[2]) ?></label>
                <input id="<?= e($officeField[0]) ?>" name="<?= e($officeField[0]) ?>" type="text" value="<?= e($current) ?>">
            </div>
        <?php endforeach; ?>
        <div class="field">
            <label for="ho_notes">Finding the office</label>
            <textarea id="ho_notes" name="ho_notes" rows="5"><?= e(office_field(isset($posted) ? $posted : null, isset($office) ? $office : null, 'ho_notes', 'notes', '')) ?></textarea>
            <p class="fine">The seeded address uses Aguda as the area and Surulere as the address line because no street number was provided. The Plus Code is the precise location.</p>
        </div>
    </fieldset>
    <fieldset>
        <legend>Logo</legend>
        <?php if (!empty($logoPath)): ?>
            <p><img class="logo-preview" src="<?= e(media_url($logoPath)) ?>" alt="Current logo" width="160" height="48"></p>
            <label class="check"><input type="checkbox" name="remove_logo" value="1"> Remove the logo and use the wordmark</label>
        <?php else: ?>
            <p class="fine">No logo file yet. The site shows the FTEE-PEE wordmark.</p>
        <?php endif; ?>
        <div class="field">
            <label for="logo">Upload a logo (JPG, PNG, or WebP, up to 3 MB)</label>
            <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp">
        </div>
    </fieldset>
    <button class="btn btn-primary" type="submit">Save settings</button>
</form>
