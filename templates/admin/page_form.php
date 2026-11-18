<?php
// Create or edit a text page.
$action = $id ? url('/admin/pages/' . (int) $id . '/edit') : url('/admin/pages/new');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="title">Title (required)</label>
        <input id="title" name="title" required value="<?= e(field_value($posted, $row, 'title')) ?>">
    </div>
    <?php if (empty($core)): ?>
        <div class="field">
            <label for="slug">Web address</label>
            <input id="slug" name="slug" value="<?= e(field_value($posted, $row, 'slug')) ?>" placeholder="team">
            <p class="fine">Leave blank to build it from the title. About, privacy, and terms keep their addresses.</p>
        </div>
    <?php else: ?>
        <p class="fine">This address stays /<?= e($row['slug']) ?>.</p>
    <?php endif; ?>
    <div class="field">
        <label for="body">Page text (required)</label>
        <textarea id="body" name="body" rows="16" required><?= e(field_value($posted, $row, 'body')) ?></textarea>
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
    <button class="btn btn-primary" type="submit">Save page</button>
</form>
<?php if ($id && empty($core)): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/pages/' . (int) $id . '/delete')) ?>" data-confirm="Delete this page?">
        <?= Csrf::field() ?>
        <button type="submit">Delete page</button>
    </form>
<?php endif; ?>
