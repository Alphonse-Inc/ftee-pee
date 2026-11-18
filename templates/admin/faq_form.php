<?php
// Create or edit a FAQ.
$action = $id ? url('/admin/faqs/' . (int) $id . '/edit') : url('/admin/faqs/new');
$selected = (string) field_value($posted, $row, 'service_id', '');
?>
<form class="form" method="post" action="<?= e($action) ?>">
    <?= Csrf::field() ?>
    <div class="field">
        <label for="question">Question (required)</label>
        <input id="question" name="question" required value="<?= e(field_value($posted, $row, 'question')) ?>">
    </div>
    <div class="field">
        <label for="answer">Answer (required)</label>
        <textarea id="answer" name="answer" rows="6" required><?= e(field_value($posted, $row, 'answer')) ?></textarea>
    </div>
    <div class="field">
        <label for="service_id">Service</label>
        <select id="service_id" name="service_id">
            <option value="0">General</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= (int) $service['id'] ?>"<?= $selected === (string) $service['id'] ? ' selected' : '' ?>><?= e($service['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="field">
        <label for="sort_order">Sort order</label>
        <input id="sort_order" name="sort_order" type="number" value="<?= e(field_value($posted, $row, 'sort_order', '0')) ?>">
    </div>
    <label class="check"><input type="checkbox" name="show_on_home" value="1"<?= field_checked($posted, $row, 'show_on_home', false) ? ' checked' : '' ?>> Show on the home page</label>
    <label class="check"><input type="checkbox" name="is_published" value="1"<?= field_checked($posted, $row, 'is_published', true) ? ' checked' : '' ?>> Published</label>
    <button class="btn btn-primary" type="submit">Save question</button>
</form>
<?php if ($id): ?>
    <form class="inline-delete" method="post" action="<?= e(url('/admin/faqs/' . (int) $id . '/delete')) ?>" data-confirm="Delete this question?">
        <?= Csrf::field() ?>
        <button type="submit">Delete question</button>
    </form>
<?php endif; ?>
