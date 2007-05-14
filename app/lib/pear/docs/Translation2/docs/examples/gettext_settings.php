<?php
require_once 'settings.php';

$driver = 'gettext';

$options = array(
    'prefetch'          => false,
    'langs_avail_file'  => 'gettext_langs.ini',
    'domains_path_file' => 'gettext_domains.ini',
    'default_domain'    => 'calendar',
    //'file_type'         => 'po',
);
?>
