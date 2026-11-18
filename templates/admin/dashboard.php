<?php
// Recent enquiries and a short setup checklist.
?>
<div class="stat-grid">
    <article class="stat">
        <p>New enquiries</p>
        <strong><?= (int) $newCount ?></strong>
    </article>
    <article class="stat">
        <p>All enquiries</p>
        <strong><?= (int) $totalCount ?></strong>
    </article>
    <article class="stat">
        <p>Email delivery</p>
        <strong><?= !empty($mailOn) ? 'On' : 'Off' ?></strong>
    </article>
</div>
<?php if (!$mailOn): ?>
    <p class="note">Email delivery is off. Every enquiry is still saved on this dashboard. Turn mail on under Settings only if the host allows mail().</p>
<?php endif; ?>
<?php if (!empty($checks)): ?>
    <h2>Before you announce the site</h2>
    <ul class="checklist">
        <?php foreach ($checks as $check): ?>
            <li><?= e($check) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="note">Phone, email, logo, hours, and the site URL are filled in. Check that any remaining sample prices are ones you intend to show.</p>
<?php endif; ?>
<h2>Recent enquiries</h2>
<?php if (!$recent): ?>
    <p>No enquiries yet.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>When</th><th>Name</th><th>Phone</th><th>Service</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td><?= e(format_stamp($row['created_at'])) ?></td>
                        <td><a href="<?= e(url('/admin/enquiries/' . (int) $row['id'])) ?>"><?= e($row['name']) ?></a></td>
                        <td><?= e($row['phone']) ?></td>
                        <td><?= e($row['service_name']) ?></td>
                        <td><span class="badge badge-<?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
