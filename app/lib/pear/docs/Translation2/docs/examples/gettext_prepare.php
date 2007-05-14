<?php

require_once 'System.php';
require_once 'File/Gettext.php';
require_once 'I18Nv2/Locale.php';

$l = &new I18Nv2_Locale('en');
$g = &File_Gettext::factory('MO');

$g->meta = array('Content-Type' => 'text/plain; charset=iso-8859-1');

$langs = array('en', 'de', 'it');
foreach ($langs as $lang) {
    $l->setLocale($lang);
    $g->strings = array();
    foreach (range(0, 6) as $day) {
        $g->strings["day_$day"] = $l->dayName($day);
    }
    foreach (range(0, 11) as $month) {
        $g->strings[sprintf('month_%02d', $month + 1)] = $l->monthName($month);
    }
    $g->strings['nasty ampersand'] = 'lean & mean';
    System::mkdir(array('-p', $dir = 'locale/'. $lang .'/LC_MESSAGES/'));
    $g->save($dir . 'calendar.mo');
}
$g->strings = array('alone' => 'solo soletto');
$g->save('locale/it/LC_MESSAGES/alone.mo');

?>
