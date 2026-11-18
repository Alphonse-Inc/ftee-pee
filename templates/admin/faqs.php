<?php // FAQ list. ?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/faqs/new')) ?>">New question</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Question</th><th>Group</th><th>Home</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/faqs/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['question']) ?></a></td>
                    <td><?= e($row['service_name'] ? $row['service_name'] : 'General') ?></td>
                    <td><?= (int) $row['show_on_home'] === 1 ? 'Yes' : 'No' ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
