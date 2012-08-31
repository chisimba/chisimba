<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: AreaCode                                           |
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
 * I18Nv2::AreaCode
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

require_once 'I18Nv2/CommonList.php';

/**
 * I18Nv2_AreaCode
 * 
 * List of two letter country code to international area code mapping.
 * 
 * @author      Michael Wallner <mike@php.net> 
 * @version     $Revision$
 * @access      public 
 * @package     I18Nv2
 */
class I18Nv2_AreaCode extends I18Nv2_CommonList
{
    /**
     * Codes
     * 
     * @access  protected
     * @var     array
     */
    var $codes = array(
        'AF' => 93,
        'AL' => 355,
        'DZ' => 213,
        'AS' => 684,
        'AD' => 376,
        'AO' => 244,
        'AQ' => 672,
        'AR' => 54,
        'AM' => 374,
        'AW' => 297,
        'AC' => 247,
        'AU' => 61,
        'AT' => 43,
        'AZ' => 994,
        'BH' => 973,
        'BD' => 880,
        'BY' => 375,
        'BE' => 32,
        'BZ' => 501,
        'BJ' => 229,
        'BT' => 975,
        'GW' => 245,
        'BO' => 591,
        'BA' => 387,
        'BW' => 267,
        'BR' => 55,
        'BN' => 673,
        'BG' => 359,
        'BF' => 226,
        'BI' => 257,
        'KH' => 855,
        'CM' => 237,
        'CV' => 238,
        'CF' => 236,
        'TD' => 235,
        'CL' => 56,
        'CN' => 86,
        'CO' => 57,
        'KM' => 2690,
        'CG' => 242,
        'CK' => 682,
        'CR' => 506,
        'HR' => 385,
        'CU' => 53,
        'CY' => 357,
        'CZ' => 420,
        'DK' => 45,
        'DG' => 246,
        'DJ' => 253,
        'EC' => 593,
        'EG' => 20,
        'SV' => 503,
        'GQ' => 240,
        'ER' => 291,
        'EE' => 372,
        'ET' => 251,
        'FO' => 298,
        'FK' => 500,
        'FJ' => 679,
        'FI' => 358,
        'FR' => 33,
        'GF' => 594,
        'PF' => 689,
        'GA' => 241,
        'GM' => 220,
        'GE' => 995,
        'DE' => 49,
        'GH' => 233,
        'GI' => 350,
        'GR' => 30,
        'GL' => 299,
        'GP' => 590,
        'GT' => 502,
        'GN' => 224,
        'GY' => 592,
        'HT' => 509,
        'HN' => 504,
        'HK' => 852,
        'HU' => 36,
        'IS' => 354,
        'IN' => 91,
        'ID' => 62,
        'QB' => 871,
        'QE' => 873,
        'QD' => 872,
        'QC' => 874,
        'IR' => 98,
        'IQ' => 964,
        'IE' => 353,
        'IM' => 881,
        'IL' => 972,
        'IT' => 39,
        'IC' => 225,
        'JP' => 81,
        'JO' => 962,
        'KE' => 254,
        'KI' => 686,
        'KP' => 850,
        'KR' => 82,
        'KW' => 965,
        'KG' => 9962,
        'LA' => 856,
        'LV' => 371,
        'LB' => 961,
        'LS' => 266,
        'LR' => 231,
        'LY' => 218,
        'LI' => 423,
        'LT' => 370,
        'LU' => 352,
        'MO' => 853,
        'MK' => 389,
        'MG' => 261,
        'MW' => 265,
        'MY' => 60,
        'MV' => 960,
        'ML' => 223,
        'MT' => 356,
        'MH' => 692,
        'MQ' => 596,
        'MR' => 222,
        'MU' => 230,
        'MX' => 52,
        'FM' => 691,
        'MD' => 373,
        'MC' => 377,
        'MN' => 976,
        'MA' => 212,
        'MZ' => 258,
        'MM' => 95,
        'NA' => 264,
        'NR' => 674,
        'NP' => 977,
        'NL' => 31,
        'AN' => 599,
        'NC' => 687,
        'NZ' => 64,
        'NI' => 505,
        'NE' => 227,
        'NG' => 234,
        'NU' => 683,
        'NO' => 47,
        'OM' => 968,
        'PK' => 92,
        'PW' => 680,
        'PA' => 507,
        'PG' => 675,
        'PY' => 595,
        'PE' => 51,
        'PH' => 63,
        'PL' => 48,
        'PT' => 351,
        'QA' => 974,
        'RE' => 262,
        'RO' => 40,
        'RU' => 7,
        'RW' => 250,
        'SH' => 290,
        'SM' => 378,
        'ST' => 239,
        'SA' => 966,
        'SN' => 221,
        'SC' => 248,
        'SL' => 232,
        'SG' => 65,
        'SK' => 421,
        'SI' => 386,
        'SB' => 677,
        'SO' => 252,
        'ZA' => 27,
        'ES' => 34,
        'LK' => 94,
        'PM' => 508,
        'SD' => 249,
        'SR' => 597,
        'SZ' => 268,
        'SE' => 46,
        'CH' => 41,
        'SY' => 963,
        'TW' => 886,
        'TJ' => 992364,
        'TZ' => 255,
        'TH' => 66,
        'TG' => 228,
        'TK' => 690,
        'TO' => 676,
        'TN' => 216,
        'TR' => 90,
        'TM' => 993,
        'TV' => 688,
        'UG' => 256,
        'UA' => 380,
        'AE' => 971,
        'GB' => 44,
        'UR' => 598,
        'UZ' => 998,
        'VU' => 678,
        'VE' => 58,
        'VN' => 84,
        'WF' => 681,
        'WS' => 685,
        'YD' => 967,
        'YU' => 381,
        'ZR' => 243,
        'ZM' => 260,
        'ZW' => 263
    );

    /**
     * Load Language
     * 
     * Does nothing.
     * 
     * @access  public
     * @return  bool true
     * @param   string  $lang
     */
    function loadLanguage($lang)
    {
        return true;
    }
    
    /**
     * Change Key Case
     * 
     * @access  protected
     * @return  string
     * @param   string  $key
     */
    function changeKeyCase($key)
    {
        return strToUpper($key);
    }
    
    /**
     * Merge Country
     * 
     * Merge this list with an I18Nv2_Country list to a new I18Nv2_CommonList,
     * where the international area codes map to the full country name.
     * 
     * @access  public
     * @return  object  I18Nv2_CommonList
     * @param   object  $country I18Nv2_Country
     */
    function &mergeCountry(&$country)
    {
        $list = &new I18Nv2_CommonList(
            $country->getLanguage(), 
            $encoding = $country->getEncoding()
        );
        
        $country->setEncoding('UTF-8');
        $ctys = $country->getAllCodes();
        $acds = $this->getAllCodes();
        $country->setEncoding($encoding);
        
        $uniq = array_intersect(array_keys($acds), array_keys($ctys));
        
        foreach ($uniq as $code) {
            $list->codes[$acds[$code]] = $ctys[$code];
        }
        
        return $list;
    }
    
}
?>
