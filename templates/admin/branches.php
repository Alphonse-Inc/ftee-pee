<?php
// Branch list.
?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/branches/new')) ?>">New branch</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Place</th><th>Role</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/branches/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['name']) ?></a></td>
                    <td><?= e(trim($row['area'] . ' ' . $row['city'])) ?></td>
                    <td><?= (int) $row['is_head_office'] === 1 ? 'Head office' : 'Branch' ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
