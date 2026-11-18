<?php
// Service list.
?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/services/new')) ?>">New service</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Address</th><th>Order</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/services/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['name']) ?></a></td>
                    <td>/services/<?= e($row['slug']) ?></td>
                    <td><?= (int) $row['sort_order'] ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
