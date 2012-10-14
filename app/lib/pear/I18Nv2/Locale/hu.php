<?php
/**
* $Id$
*/

$this->dateFormats = array(
    I18Nv2_DATETIME_SHORT     =>  '%Y-%m-%d',
    I18Nv2_DATETIME_DEFAULT   =>  '%Y. %b. %d.',
    I18Nv2_DATETIME_MEDIUM    =>  '%Y. %b. %d.',
    I18Nv2_DATETIME_LONG      =>  '%Y. %B %d.',
    I18Nv2_DATETIME_FULL      =>  '%Y. %B %d., %A'
);
$this->timeFormats = array(
    I18Nv2_DATETIME_SHORT     =>  '%H:%M',
    I18Nv2_DATETIME_DEFAULT   =>  '%H:%M:%S',
    I18Nv2_DATETIME_MEDIUM    =>  '%H:%M:%S',
    I18Nv2_DATETIME_LONG      =>  '%H:%M:%S %Z',
    I18Nv2_DATETIME_FULL      =>  'idõ: %H:%M %Z'
);
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][0] = 'Ft';
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][1] = '2';
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][2] = ',';
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][3] = '.';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][0] = 'HUF';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][1] = '2';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][2] = '.';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][3] = ',';
?>
