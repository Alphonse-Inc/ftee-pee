<?php // Page list. ?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/pages/new')) ?>">New page</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Address</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/pages/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['title']) ?></a></td>
                    <td>/<?= e($row['slug']) ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
