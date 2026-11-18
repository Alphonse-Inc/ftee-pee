<?php
// Short public message, used when a form token is no longer valid.
?>
<section class="section">
    <div class="wrap prose-page">
        <h1><?= e(isset($heading) ? $heading : 'Notice') ?></h1>
        <p><?= e(isset($message) ? $message : '') ?></p>
        <p><a class="btn btn-primary" href="<?= e(url('/')) ?>">Back to the home page</a></p>
    </div>
</section>
