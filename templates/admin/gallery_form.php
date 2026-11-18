<?php
// Upload or edit a gallery image.
$action = $id ? url('/admin/gallery/' . (int) $id . '/edit') : url('/admin/gallery/new');
?>
<form class="form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <?php if ($row && !empty($row['image_path'])): ?>
        <p><img src="<?= e(media_url($row['image_path'])) ?>" alt="" width="240" height="160"></p>
    <?php endif; ?>
    <div class="field">
        <label for="title">Title (required)</label>
        <input id="title" name="title" required value="<?= e(field_value($posted, $row, 'title')) ?>">
    </div>
    <div class="field">
        <label for="caption">Caption</label>
        <input id="caption" name="caption" value="<?= e(field_value($posted, $row, 'caption')) ?>">
    </div>
    <div class="field">
        <label for="alt_text">Alt text</label>
        <input id="alt_text" name="alt_text" value="<?= e(field_value($posted, $row, 'alt_text')) ?>">
    </div>
    <div class="field">
        <label for="image">Image<?= $id ? '' : ' (required)' ?></label>
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"<?= $id ? '' : ' required' ?>>
    </div>
    <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e(field_value($posted, $row, 'sort_order', '0')) ?>">
    </div>
    <label class="check"><input type="checkbox" name="is_placeholder" value="1"<?= field_checked($posted, $row, 'is_placeholder', false) ? ' checked' : '' ?>> Placeholder, not client work</label>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', true) ? ' checked' : '' ?>> Published</label>
    <p class="fine">A new upload is stored with a random filename. JPG, PNG, and WebP only, up to 3 MB. PHP files are not accepted.</p>
    <button class="btn btn-primary" type="submit">Save image</button>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/gallery/' . (int) $id . '/delete')) ?>" data-confirm="Delete this image?">
        <?= Csrf::field() ?>
        <button type="submit">Delete image</button>
    </form>
<?php endif; ?>
