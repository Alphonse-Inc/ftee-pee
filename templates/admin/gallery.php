<?php
// Gallery list.
?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/gallery/new')) ?>">New image</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Kind</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/gallery/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['title']) ?></a></td>
                    <td><?= (int) $row['is_placeholder'] === 1 ? 'Placeholder' : 'Upload' ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
