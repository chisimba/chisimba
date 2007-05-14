<?php
require_once 'Translation2.php';
require_once 'Translation2/Admin.php';
require_once 'I18Nv2/Locale.php';

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$options = array(
    'langs_avail_file'  => 'gettext_langs.ini',
    'domains_path_file' => 'gettext_domains.ini',
    'default_domain'    => 'admin'
);

$tr = &Translation2_Admin::factory('gettext', $options, array('prefetch' => false));

$langs = $tr->getLangs('ids');
$days  = array();
$months= array();

$lc = &new I18Nv2_Locale;

foreach ($langs as $lang) {
    $lc->setLocale($lang);
    foreach (range(0,6) as $day) {
        $days[$day][$lang] = $lc->dayName($day);
    }
    foreach (range(0,11) as $month) {
        $months[$month][$lang] = $lc->monthName($month);
    }
}

$tr->storage->begin();
foreach ($langs as $lang) {
    foreach (range(0,6) as $day) {
        $tr->add('day_'. sprintf('%02d', $day), null, $days[$day]);
    }
    foreach (range(0,11) as $month) {
        $tr->add('month_'. sprintf('%02d', $month), null, $months[$month]);
    }
}
$tr->storage->commit();

foreach ($langs as $lang) {
    foreach (range(0,6) as $day) {
        echo "$lang day $day: ", $tr->get('day_'. sprintf('%02d', $day), null, $lang), "\n";
    }
    foreach (range(0,11) as $month) {
        echo "$lang month $month: ", $tr->get('month_'. sprintf('%02d', $month), null, $lang), "\n";
    }
    echo "\n";
}
?>
