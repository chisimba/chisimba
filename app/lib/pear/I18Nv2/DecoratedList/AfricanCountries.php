<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: DecoartedList :: AfricanCountries                  |
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
 * I18Nv2::DecoratedList::AfricanCountries
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/DecoratedList/Filter.php';

/**
 * I18Nv2_DecoratedList_AfricanCountries
 * 
 * Use only for decorating I18Nv2_Country.
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 */
class I18Nv2_DecoratedList_AfricanCountries extends I18Nv2_DecoratedList_Filter
{
    /**
     * Keys for African countries
     * 
     * @var array
     */
    var $elements = array(
        'DZ','AO','BJ','BW','BF','BI','CM','CV','CF','TD','KM','CG','CD','DJ',
        'EG','GQ','ER','ET','GA','GM','GH','GN','GW','CI','KE','LS','LR','LY',
        'MG','MW','ML','MR','MU','MA','MZ','NA','NE','NG','RW','ST','SN','SC',
        'SL','SO','ZA','SD','SZ','TZ','TG','TN','UG','ZM','ZW'
    );
}
?>
