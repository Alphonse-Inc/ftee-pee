<?php
// Package list, optional service filter.
?>
<p><a class="btn btn-primary" href="<?= e(url('/admin/packages/new')) ?>">New package</a></p>
<form class="filter-form" method="get" action="<?= e(url('/admin/packages')) ?>">
    <label for="service">Service
        <select id="service" name="service">
            <option value="0">All services</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= (int) $service['id'] ?>"<?= (int) $serviceId === (int) $service['id'] ? ' selected' : '' ?>><?= e($service['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Filter</button>
</form>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>Package</th><th>Service</th><th>Price label</th><th>Sample</th><th>Visible</th></tr></thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><a href="<?= e(url('/admin/packages/' . (int) $row['id'] . '/edit')) ?>"><?= e($row['name']) ?></a></td>
                    <td><?= e($row['service_name']) ?></td>
                    <td><?= e($row['price_label']) ?></td>
                    <td><?= (int) $row['is_sample'] === 1 ? 'Sample' : 'Own price' ?></td>
                    <td><?= (int) $row['is_published'] === 1 ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
