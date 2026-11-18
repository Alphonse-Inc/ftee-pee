<?php // Testimonials. Sample rows stay off the public site. ?>
<p class="note">Sample quotes never appear on the public site, even if Published is checked. Uncheck Sample only for a real customer who agreed to be quoted.</p>
<p><a class="btn btn-primary" href="<?= e(url('/admin/testimonials/new')) ?>">New testimonial</a></p>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Sample</th><th>Published</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/testimonials/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['author_name']) ?></a></td>
                    <td><?= (int) $row['is_sample'] === 1 ? 'Sample' : 'Real' ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
