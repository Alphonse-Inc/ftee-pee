<?php
// Create or edit a testimonial.
$action = $id ? url('/admin/testimonials/' . (int) $id . '/edit') : url('/admin/testimonials/new');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="author_name">Name (required)</label>
        <input id="author_name" name="author_name" required value="<?= e(field_value($posted, $row, 'author_name')) ?>">
    </div>
    <div class="field">
        <label for="author_detail">Detail</label>
        <input id="author_detail" name="author_detail" value="<?= e(field_value($posted, $row, 'author_detail')) ?>" placeholder="Surulere, printing">
    </div>
    <div class="field">
        <label for="quote">Quote (required)</label>
        <textarea id="quote" name="quote" rows="5" required><?= e(field_value($posted, $row, 'quote')) ?></textarea>
    </div>
    <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e(field_value($posted, $row, 'sort_order', '0')) ?>">
    </div>
    <label class="check"><input type="checkbox" name="is_sample" value="1"<?= field_checked($posted, $row, 'is_sample', true) ? ' checked' : '' ?>> Sample. Hidden on the public site.</label>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', false) ? ' checked' : '' ?>> Published</label>
    <button class="btn btn-primary" type="submit">Save testimonial</button>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/testimonials/' . (int) $id . '/delete')) ?>" data-confirm="Delete this testimonial?">
        <?= Csrf::field() ?>
        <button type="submit">Delete testimonial</button>
    </form>
<?php endif; ?>
