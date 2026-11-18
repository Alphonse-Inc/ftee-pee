<?php
// Enquiry inbox. Shows the latest 100 for the selected status.
$filters = ['' => 'All', 'new' => 'New', 'read' => 'Read', 'closed' => 'Closed'];
?>
<p class="fine"><?= (int) $total ?> saved in total. This list shows the latest 100 for the filter.</p>
<p class="filter-links">
    <?php foreach ($filters as $key => $label): ?>
        <a href="<?= e(url('/admin/enquiries' . ($key === '' ? '' : '?status=' . $key))) ?>"<?= $status === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
</p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>When</th><th>Name</th><th>Service</th><th>Status</th><th>Email</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= e(format_stamp($row['created_at'])) ?></td>
                    <td><a href="<?= e(url('/admin/enquiries/' . (int) $row['id'])) ?>"><?= e($row['name']) ?></a></td>
                    <td><?= e($row['service_name']) ?></td>
                    <td><span class="badge badge-<?= e($row['status']) ?>"><?= e($row['status']) ?></span></td>
                    <td><?= e(mail_status_label($row['mail_status'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
