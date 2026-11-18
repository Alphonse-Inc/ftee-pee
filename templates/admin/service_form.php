<?php
// Create or edit a service.
$action = $id ? url('/admin/services/' . (int) $id . '/edit') : url('/admin/services/new');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="name">Name (required)</label>
        <input id="name" name="name" required value="<?= e(field_value($posted, $row, 'name')) ?>">
    </div>
    <div class="field">
        <label for="summary">Summary (required)</label>
        <textarea id="summary" name="summary" rows="3" required><?= e(field_value($posted, $row, 'summary')) ?></textarea>
    </div>
    <div class="field">
        <label for="icon_key">Icon</label>
        <select id="icon_key" name="icon_key">
            <?php foreach (icon_keys() as $key => $label): ?>
                <option value="<?= e($key) ?>"<?= (string) field_value($posted, $row, 'icon_key', 'print') === (string) $key ? ' selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="body">Page text</label>
        <textarea id="body" name="body" rows="10"><?= e(field_value($posted, $row, 'body')) ?></textarea>
        <p class="fine">Blank line between paragraphs. The text is shown as plain text, not HTML.</p>
    </div>
    <div class="field">
        <label for="process">Process</label>
        <textarea id="process" name="process" rows="6"><?= e(field_value($posted, $row, 'process')) ?></textarea>
    </div>
    <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e(field_value($posted, $row, 'sort_order', '0')) ?>">
    </div>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', true) ? ' checked' : '' ?>> Published</label>
    <div class="field">
        <label for="seo_title">SEO title</label>
        <input id="seo_title" name="seo_title" value="<?= e(field_value($posted, $row, 'seo_title')) ?>">
    </div>
    <div class="field">
        <label for="seo_description">Meta description</label>
        <textarea id="seo_description" name="seo_description" rows="3"><?= e(field_value($posted, $row, 'seo_description')) ?></textarea>
    </div>
    <div class="cluster">
        <button class="btn btn-primary" type="submit">Save service</button>
        <?php if ($id): ?><a href="<?= e(url('/admin/packages?service=' . (int) $id)) ?>">Packages for this service</a><?php endif; ?>
    </div>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/services/' . (int) $id . '/delete')) ?>" data-confirm="Delete this service and its packages?">
        <?= Csrf::field() ?>
        <button type="submit">Delete service</button>
    </form>
<?php endif; ?>
