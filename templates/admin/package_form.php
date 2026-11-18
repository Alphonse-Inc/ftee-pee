<?php
// Create or edit a package. Sample stays on until the admin clears it.
$action = $id ? url('/admin/packages/' . (int) $id . '/edit') : url('/admin/packages/new');
$selectedService = (string) field_value($posted, $row, 'service_id', $preselect ? (string) $preselect : '');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="service_id">Service (required)</label>
        <select id="service_id" name="service_id" required>
            <option value="">Select</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= (int) $service['id'] ?>"<?= $selectedService === (string) $service['id'] ? ' selected' : '' ?>><?= e($service['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="name">Name (required)</label>
        <input id="name" name="name" required value="<?= e(field_value($posted, $row, 'name')) ?>">
    </div>
    <div class="field">
        <label for="summary">Summary</label>
        <textarea id="summary" name="summary" rows="3"><?= e(field_value($posted, $row, 'summary')) ?></textarea>
    </div>
    <div class="field">
        <label for="price_label">Price label</label>
        <input id="price_label" name="price_label" value="<?= e(field_value($posted, $row, 'price_label')) ?>" placeholder="From ₦18,000 for 100 cards">
    </div>
    <div class="field">
        <label for="price_amount">Naira amount (digits only, optional)</label>
        <input id="price_amount" name="price_amount" inputmode="numeric" value="<?= e(field_value($posted, $row, 'price_amount')) ?>">
        <p class="fine">Used for structured data only after Sample is unchecked. Leave blank for “price on request”.</p>
    </div>
    <div class="field">
        <label for="price_note">Price note</label>
        <input id="price_note" name="price_note" value="<?= e(field_value($posted, $row, 'price_note')) ?>">
    </div>
    <div class="field">
        <label for="features">Included items, one per line</label>
        <textarea id="features" name="features" rows="6"><?= e(field_value($posted, $row, 'features')) ?></textarea>
    </div>
    <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e(field_value($posted, $row, 'sort_order', '0')) ?>">
    </div>
    <label class="check"><input type="checkbox" name="is_sample" value="1"<?= field_checked($posted, $row, 'is_sample', true) ? ' checked' : '' ?>> Sample price. Leave this on until the figure is your own.</label>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', true) ? ' checked' : '' ?>> Published</label>
    <button class="btn btn-primary" type="submit">Save package</button>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/packages/' . (int) $id . '/delete')) ?>" data-confirm="Delete this package?">
        <?= Csrf::field() ?>
        <button type="submit">Delete package</button>
    </form>
<?php endif; ?>
