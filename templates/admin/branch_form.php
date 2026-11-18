<?php
// Create or edit a branch. Map URL is optional.
$action = $id ? url('/admin/branches/' . (int) $id . '/edit') : url('/admin/branches/new');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <?php
    $fields = [
        ['name', 'Name', true],
        ['area', 'Area', false],
        ['address_line', 'Address line', false],
        ['city', 'City', false],
        ['state', 'State', false],
        ['postal_code', 'Postal code', false],
        ['country', 'Country', false],
        ['plus_code', 'Plus Code', false],
        ['phone', 'Phone', false],
        ['email', 'Email', false],
        ['hours_text', 'Opening hours', false],
        ['map_url', 'Map URL', false],
    ];
    foreach ($fields as $field):
        $default = $field[0] === 'country' ? 'Nigeria' : '';
    ?>
        <div class="field">
            <label for="<?= e($field[0]) ?>"><?= e($field[1]) ?><?php if ($field[2]): ?> (required)<?php endif; ?></label>
            <input id="<?= e($field[0]) ?>" name="<?= e($field[0]) ?>" value="<?= e(field_value($posted, $row, $field[0], $default)) ?>"<?= $field[2] ? ' required' : '' ?>>
        </div>
    <?php endforeach; ?>
    <div class="field">
        <label for="notes">Finding notes</label>
        <textarea id="notes" name="notes" rows="5"><?= e(field_value($posted, $row, 'notes')) ?></textarea>
    </div>
    <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e(field_value($posted, $row, 'sort_order', '0')) ?>">
    </div>
    <label class="check"><input type="checkbox" name="is_head_office" value="1"<?= field_checked($posted, $row, 'is_head_office', false) ? ' checked' : '' ?>> Head office</label>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', true) ? ' checked' : '' ?>> Published</label>
    <p class="fine">Leave the map URL blank to build a Google Maps search from the Plus Code and address. No Maps API key is used.</p>
    <button class="btn btn-primary" type="submit">Save branch</button>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/branches/' . (int) $id . '/delete')) ?>" data-confirm="Delete this branch?">
        <?= Csrf::field() ?>
        <button type="submit">Delete branch</button>
    </form>
<?php endif; ?>
