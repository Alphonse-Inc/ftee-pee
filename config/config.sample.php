<?php
// Example configuration. The installer writes the real file at config/config.php.
// That generated file is the one the deployer edits if the site moves.
// base_url is the folder path only: empty string at the domain root, or /folder
// for a subdirectory. It must match RewriteBase in the project .htaccess.
// The public domain (https://example.com) is Admin → Settings → Site URL, not here.

return [
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => 'cpaneluser_fteepee',
    'db_user' => 'cpaneluser_fteepee',
    'db_pass' => 'change-me',
    'base_url' => '',
];
