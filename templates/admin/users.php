<?php // Admin accounts. ?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/users/new')) ?>">New admin</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Added</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/users/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['name']) ?></a></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e(format_day($row['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
