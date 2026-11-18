<?php
// Short admin notice.
?>
<p><?= e(isset($message) ? $message : '') ?></p>
<p><a href="<?= e(url('/admin')) ?>">Back to the dashboard</a></p>
