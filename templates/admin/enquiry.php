<?php
// One enquiry. Opening a new message marks it read.
?>
<dl class="enquiry">
    <div><dt>When</dt><dd><?= e(format_stamp($row['created_at'])) ?></dd></div>
    <div><dt>Name</dt><dd><?= e($row['name']) ?></dd></div>
    <div><dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $row['phone'])) ?>"><?= e($row['phone']) ?></a></dd></div>
    <div><dt>Email</dt><dd><a href="mailto:<?= e($row['email']) ?>?subject=<?= e(rawurlencode('Re: your FTEE-PEE enquiry')) ?>"><?= e($row['email']) ?></a></dd></div>
    <div><dt>Service</dt><dd><?= e($row['service_name']) ?></dd></div>
    <div><dt>Branch</dt><dd><?= e($row['branch_name'] !== '' ? $row['branch_name'] : 'Not specified') ?></dd></div>
    <div><dt>Email delivery</dt><dd><?= e(mail_status_label($row['mail_status'])) ?></dd></div>
    <div><dt>Network</dt><dd><?= e($row['ip_address']) ?></dd></div>
</dl>
<div class="prose enquiry-message"><?= format_text($row['message']) ?></div>
<form class="form inline-form" method="post" action="<?= e(url('/admin/enquiries/' . (int) $row['id'] . '/status')) ?>">
    <?= Csrf::field() ?>
    <label for="status">Status
        <select id="status" name="status">
            <?php foreach (['new', 'read', 'closed'] as $state): ?>
                <option value="<?= e($state) ?>"<?= $row['status'] === $state ? ' selected' : '' ?>><?= e($state) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button class="btn btn-primary" type="submit">Update status</button>
</form>
<form class="inline-delete" method="post" action="<?= e(url('/admin/enquiries/' . (int) $row['id'] . '/delete')) ?>" data-confirm="Delete this enquiry?">
    <?= Csrf::field() ?>
    <button type="submit">Delete enquiry</button>
</form>
