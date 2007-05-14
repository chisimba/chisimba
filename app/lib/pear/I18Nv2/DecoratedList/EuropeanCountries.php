<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: DecoartedList :: EuropeanCountries                 |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * I18Nv2::DecoratedList::EuropeanCountries
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/DecoratedList/Filter.php';

/**
 * I18Nv2_DecoratedList_EuropeanCountries
 * 
 * Use only for decorating I18Nv2_Country.
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 */
class I18Nv2_DecoratedList_EuropeanCountries extends I18Nv2_DecoratedList_Filter
{
    /**
     * Keys for European countries
     * 
     * @var array
     */
    var $elements = array(
        'AL','AD','AM','AT','AZ','BY','BE','BA','BG','HR','CY','CZ','DK','EE',
        'FI','FR','GE','DE','GR','HU','IS','IE','IT','LV','LI','LT','LU','MK',
        'MT','MD','MC','NL','NO','PL','PT','RO','SM','SP','SK','SI','ES','SE',
        'CH','UA','GB','VA'
   );
}
?>
