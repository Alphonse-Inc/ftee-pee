<?php // Blog list. ?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/posts/new')) ?>">New article</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Date</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/posts/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['title']) ?></a></td>
                    <td><?= e(format_day($row['published_at'])) ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
