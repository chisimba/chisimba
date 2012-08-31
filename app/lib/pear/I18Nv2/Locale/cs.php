<?php
/**
* $Id$
* @author <mishal(@)centrum.cz>
*/

$this->dateFormats = array(
    I18Nv2_DATETIME_SHORT     =>  '%d.%m.%y',
    I18Nv2_DATETIME_DEFAULT   =>  '%d.%m.%Y',
    I18Nv2_DATETIME_MEDIUM    =>  '%d %b %Y',
    I18Nv2_DATETIME_LONG      =>  '%d %B %Y',
    I18Nv2_DATETIME_FULL      =>  '%A, %d %B %Y'
);
$this->timeFormats = array(
    I18Nv2_DATETIME_SHORT     =>  '%H:%M',
    I18Nv2_DATETIME_DEFAULT   =>  '%H:%M:%S',
    I18Nv2_DATETIME_MEDIUM    =>  '%H:%M:%S',
    I18Nv2_DATETIME_LONG      =>  '%H:%M:%S %Z',
    I18Nv2_DATETIME_FULL      =>  '%H:%M hod. %Z'
);

$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][0] = 'Kè';
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][1] = '2';
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][2] = ',';
$this->currencyFormats[I18Nv2_CURRENCY_LOCAL][3] = '.';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][0] = 'CZK';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][1] = '2';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][2] = '.';
$this->currencyFormats[I18Nv2_CURRENCY_INTERNATIONAL][3] = ',';

?>
