<?php
// Create or edit an article.
$action = $id ? url('/admin/posts/' . (int) $id . '/edit') : url('/admin/posts/new');
$dateValue = field_value($posted, $row, 'published_at', '');
if (!is_array($posted) && strlen((string) $dateValue) >= 10) {
    $dateValue = substr((string) $dateValue, 0, 10);
}
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="title">Title (required)</label>
        <input id="title" name="title" required value="<?= e(field_value($posted, $row, 'title')) ?>">
    </div>
    <div class="field">
        <label for="excerpt">Excerpt</label>
        <textarea id="excerpt" name="excerpt" rows="3"><?= e(field_value($posted, $row, 'excerpt')) ?></textarea>
    </div>
    <div class="field">
        <label for="body">Article (required)</label>
        <textarea id="body" name="body" rows="16" required><?= e(field_value($posted, $row, 'body')) ?></textarea>
    </div>
    <div class="field">
        <label for="published_at">Publish date</label>
        <input id="published_at" name="published_at" type="date" value="<?= e($dateValue) ?>">
    </div>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', false) ? ' checked' : '' ?>> Published</label>
    <div class="field">
        <label for="seo_title">SEO title</label>
        <input id="seo_title" name="seo_title" value="<?= e(field_value($posted, $row, 'seo_title')) ?>">
    </div>
    <div class="field">
        <label for="seo_description">Meta description</label>
        <textarea id="seo_description" name="seo_description" rows="3"><?= e(field_value($posted, $row, 'seo_description')) ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Save article</button>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/posts/' . (int) $id . '/delete')) ?>" data-confirm="Delete this article?">
        <?= Csrf::field() ?>
        <button type="submit">Delete article</button>
    </form>
<?php endif; ?>
