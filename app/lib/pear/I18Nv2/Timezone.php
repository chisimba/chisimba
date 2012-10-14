<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Timezone                                           |
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
 * I18Nv2::Timezone
 * 
 * @package     I18Nv2
 * @category    Internationalization
 */

/**
 * I18Nv2_Timezone
 *
 * Map of country codes to time zone names.
 * 
 * Note that:
 * AX is not in country list
 * CS is not in country list
 * BV (Bouvet Island) is not in time zones list
 * HM (Heard Island And Mcdonald Islands) is not in time zones list
 * SP (Serbia) is not in time zones list
 * YU (Serbia And Montenegro) is not in time zones list
 * 
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * @access      public
 * @static
 */
class I18Nv2_Timezone
{
    /**
     * For Country
     * 
     * @static
     * @access  public
     * @return  array   time zones applicable for the country
     * @param   string  $country ISO country code
     */
    function forCountry($country)
    {
        $country = strToUpper($country);
        return isset($GLOBALS['_I18Nv2_Timezones'][$country]) ?
            $GLOBALS['_I18Nv2_Timezones'][$country] : array();
    }
    
    /**
     * For Continent
     * 
     * @static
     * @access  public
     * @return  array   time zones applicable for the "continent"
     * @param   string  $continent  Europe|America|Africa|Asia|Pacific|Australia|
     *                              Indian|Atlantic|Antartica
     */
    function forContinent($continent)
    {
        $continent = ucFirst(strToLower($continent));
        
        if (!count($GLOBALS['_I18Nv2_Continental_Timezones'])) {
            foreach (I18Nv2_Timezone::getAll(true) as $tz) {
                $cn = current($a = explode('/', $tz, 2));
                $GLOBALS['_I18Nv2_Continental_Timeszones'][$cn][] = $tz;
            }
        }
        
        return isset($GLOBALS['_I18Nv2_Continental_Timeszones'][$continent]) ?
            $GLOBALS['_I18Nv2_Continental_Timeszones'][$continent] : array();
    }
    
    /**
     * Exists
     * 
     * @static
     * @access  public
     * @return  bool
     * @param   string  $timezone
     */
    function exists($timezone)
    {
        return in_array($timezone, I18Nv2_Timezone::getAll(true));
    }
    
    /**
     * Get All
     * 
     * @static
     * @access  public
     * @return  array
     * @param   bool    $merged
     */
    function getAll($merged = false)
    {
        if ($merged) {
            if (!count($GLOBALS['_I18Nv2_Merged_Timezones'])) {
                $GLOBALS['_I18Nv2_Merged_Timezones'] = call_user_func_array(
                    'array_merge', $GLOBALS['_I18Nv2_Timezones']);
            }
            return $GLOBALS['_I18Nv2_Merged_Timezones'];
        }
        return $GLOBALS['_I18Nv2_Timezones'];
    }
    
}

$GLOBALS['_I18Nv2_Continental_Timezones'] = array();
$GLOBALS['_I18Nv2_Merged_Timezones'] = array();
$GLOBALS['_I18Nv2_Timezones'] = array(
    'AD' => array(
        'Europe/Andorra',
    ),
    'AE' => array(
        'Asia/Dubai',
    ),
    'AF' => array(
        'Asia/Kabul',
    ),
    'AG' => array(
        'America/Antigua',
    ),
    'AI' => array(
        'America/Anguilla',
    ),
    'AL' => array(
        'Europe/Tirane',
    ),
    'AM' => array(
        'Asia/Yerevan',
    ),
    'AN' => array(
        'America/Curacao',
    ),
    'AO' => array(
        'Africa/Luanda',
    ),
    'AQ' => array(
        'Antarctica/McMurdo',
        'Antarctica/South_Pole',
        'Antarctica/Rothera',
        'Antarctica/Palmer',
        'Antarctica/Mawson',
        'Antarctica/Davis',
        'Antarctica/Casey',
        'Antarctica/Vostok',
        'Antarctica/DumontDUrville',
        'Antarctica/Syowa',
    ),
    'AR' => array(
        'America/Argentina/Buenos_Aires',
        'America/Argentina/Cordoba',
        'America/Argentina/Jujuy',
        'America/Argentina/Tucuman',
        'America/Argentina/Catamarca',
        'America/Argentina/La_Rioja',
        'America/Argentina/San_Juan',
        'America/Argentina/Mendoza',
        'America/Argentina/ComodRivadavia',
        'America/Argentina/Rio_Gallegos',
        'America/Argentina/Ushuaia',
    ),
    'AS' => array(
        'Pacific/Pago_Pago',
    ),
    'AT' => array(
        'Europe/Vienna',
    ),
    'AU' => array(
        'Australia/Lord_Howe',
        'Australia/Hobart',
        'Australia/Melbourne',
        'Australia/Sydney',
        'Australia/Broken_Hill',
        'Australia/Brisbane',
        'Australia/Lindeman',
        'Australia/Adelaide',
        'Australia/Darwin',
        'Australia/Perth',
    ),
    'AW' => array(
        'America/Aruba',
    ),
    'AX' => array(
        'Europe/Mariehamn',
    ),
    'AZ' => array(
        'Asia/Baku',
    ),
    'BA' => array(
        'Europe/Sarajevo',
    ),
    'BB' => array(
        'America/Barbados',
    ),
    'BD' => array(
        'Asia/Dhaka',
    ),
    'BE' => array(
        'Europe/Brussels',
    ),
    'BF' => array(
        'Africa/Ouagadougou',
    ),
    'BG' => array(
        'Europe/Sofia',
    ),
    'BH' => array(
        'Asia/Bahrain',
    ),
    'BI' => array(
        'Africa/Bujumbura',
    ),
    'BJ' => array(
        'Africa/Porto-Novo',
    ),
    'BM' => array(
        'Atlantic/Bermuda',
    ),
    'BN' => array(
        'Asia/Brunei',
    ),
    'BO' => array(
        'America/La_Paz',
    ),
    'BR' => array(
        'America/Noronha',
        'America/Belem',
        'America/Fortaleza',
        'America/Recife',
        'America/Araguaina',
        'America/Maceio',
        'America/Bahia',
        'America/Sao_Paulo',
        'America/Campo_Grande',
        'America/Cuiaba',
        'America/Porto_Velho',
        'America/Boa_Vista',
        'America/Manaus',
        'America/Eirunepe',
        'America/Rio_Branco',
    ),
    'BS' => array(
        'America/Nassau',
    ),
    'BT' => array(
        'Asia/Thimphu',
    ),
    'BW' => array(
        'Africa/Gaborone',
    ),
    'BY' => array(
        'Europe/Minsk',
    ),
    'BZ' => array(
        'America/Belize',
    ),
    'CA' => array(
        'America/St_Johns',
        'America/Halifax',
        'America/Glace_Bay',
        'America/Goose_Bay',
        'America/Montreal',
        'America/Toronto',
        'America/Nipigon',
        'America/Thunder_Bay',
        'America/Pangnirtung',
        'America/Iqaluit',
        'America/Rankin_Inlet',
        'America/Winnipeg',
        'America/Rainy_River',
        'America/Cambridge_Bay',
        'America/Regina',
        'America/Swift_Current',
        'America/Edmonton',
        'America/Yellowknife',
        'America/Inuvik',
        'America/Dawson_Creek',
        'America/Vancouver',
        'America/Whitehorse',
        'America/Dawson',
    ),
    'CC' => array(
        'Indian/Cocos',
    ),
    'CD' => array(
        'Africa/Kinshasa',
        'Africa/Lubumbashi',
    ),
    'CF' => array(
        'Africa/Bangui',
    ),
    'CG' => array(
        'Africa/Brazzaville',
    ),
    'CH' => array(
        'Europe/Zurich',
    ),
    'CI' => array(
        'Africa/Abidjan',
    ),
    'CK' => array(
        'Pacific/Rarotonga',
    ),
    'CL' => array(
        'America/Santiago',
        'Pacific/Easter',
    ),
    'CM' => array(
        'Africa/Douala',
    ),
    'CN' => array(
        'Asia/Shanghai',
        'Asia/Harbin',
        'Asia/Chongqing',
        'Asia/Urumqi',
        'Asia/Kashgar',
    ),
    'CO' => array(
        'America/Bogota',
    ),
    'CR' => array(
        'America/Costa_Rica',
    ),
    'CS' => array(
        'Europe/Belgrade',
    ),
    'CU' => array(
        'America/Havana',
    ),
    'CV' => array(
        'Atlantic/Cape_Verde',
    ),
    'CX' => array(
        'Indian/Christmas',
    ),
    'CY' => array(
        'Asia/Nicosia',
    ),
    'CZ' => array(
        'Europe/Prague',
    ),
    'DE' => array(
        'Europe/Berlin',
    ),
    'DJ' => array(
        'Africa/Djibouti',
    ),
    'DK' => array(
        'Europe/Copenhagen',
    ),
    'DM' => array(
        'America/Dominica',
    ),
    'DO' => array(
        'America/Santo_Domingo',
    ),
    'DZ' => array(
        'Africa/Algiers',
    ),
    'EC' => array(
        'America/Guayaquil',
        'Pacific/Galapagos',
    ),
    'EE' => array(
        'Europe/Tallinn',
    ),
    'EG' => array(
        'Africa/Cairo',
    ),
    'EH' => array(
        'Africa/El_Aaiun',
    ),
    'ER' => array(
        'Africa/Asmera',
    ),
    'ES' => array(
        'Europe/Madrid',
        'Africa/Ceuta',
        'Atlantic/Canary',
    ),
    'ET' => array(
        'Africa/Addis_Ababa',
    ),
    'FI' => array(
        'Europe/Helsinki',
    ),
    'FJ' => array(
        'Pacific/Fiji',
    ),
    'FK' => array(
        'Atlantic/Stanley',
    ),
    'FM' => array(
        'Pacific/Yap',
        'Pacific/Truk',
        'Pacific/Ponape',
        'Pacific/Kosrae',
    ),
    'FO' => array(
        'Atlantic/Faeroe',
    ),
    'FR' => array(
        'Europe/Paris',
    ),
    'GA' => array(
        'Africa/Libreville',
    ),
    'GB' => array(
        'Europe/London',
        'Europe/Belfast',
    ),
    'GD' => array(
        'America/Grenada',
    ),
    'GE' => array(
        'Asia/Tbilisi',
    ),
    'GF' => array(
        'America/Cayenne',
    ),
    'GH' => array(
        'Africa/Accra',
    ),
    'GI' => array(
        'Europe/Gibraltar',
    ),
    'GL' => array(
        'America/Godthab',
        'America/Danmarkshavn',
        'America/Scoresbysund',
        'America/Thule',
    ),
    'GM' => array(
        'Africa/Banjul',
    ),
    'GN' => array(
        'Africa/Conakry',
    ),
    'GP' => array(
        'America/Guadeloupe',
    ),
    'GQ' => array(
        'Africa/Malabo',
    ),
    'GR' => array(
        'Europe/Athens',
    ),
    'GS' => array(
        'Atlantic/South_Georgia',
    ),
    'GT' => array(
        'America/Guatemala',
    ),
    'GU' => array(
        'Pacific/Guam',
    ),
    'GW' => array(
        'Africa/Bissau',
    ),
    'GY' => array(
        'America/Guyana',
    ),
    'HK' => array(
        'Asia/Hong_Kong',
    ),
    'HN' => array(
        'America/Tegucigalpa',
    ),
    'HR' => array(
        'Europe/Zagreb',
    ),
    'HT' => array(
        'America/Port-au-Prince',
    ),
    'HU' => array(
        'Europe/Budapest',
    ),
    'ID' => array(
        'Asia/Jakarta',
        'Asia/Pontianak',
        'Asia/Makassar',
        'Asia/Jayapura',
    ),
    'IE' => array(
        'Europe/Dublin',
    ),
    'IL' => array(
        'Asia/Jerusalem',
    ),
    'IN' => array(
        'Asia/Calcutta',
    ),
    'IO' => array(
        'Indian/Chagos',
    ),
    'IQ' => array(
        'Asia/Baghdad',
    ),
    'IR' => array(
        'Asia/Tehran',
    ),
    'IS' => array(
        'Atlantic/Reykjavik',
    ),
    'IT' => array(
        'Europe/Rome',
    ),
    'JM' => array(
        'America/Jamaica',
    ),
    'JO' => array(
        'Asia/Amman',
    ),
    'JP' => array(
        'Asia/Tokyo',
    ),
    'KE' => array(
        'Africa/Nairobi',
    ),
    'KG' => array(
        'Asia/Bishkek',
    ),
    'KH' => array(
        'Asia/Phnom_Penh',
    ),
    'KI' => array(
        'Pacific/Tarawa',
        'Pacific/Enderbury',
        'Pacific/Kiritimati',
    ),
    'KM' => array(
        'Indian/Comoro',
    ),
    'KN' => array(
        'America/St_Kitts',
    ),
    'KP' => array(
        'Asia/Pyongyang',
    ),
    'KR' => array(
        'Asia/Seoul',
    ),
    'KW' => array(
        'Asia/Kuwait',
    ),
    'KY' => array(
        'America/Cayman',
    ),
    'KZ' => array(
        'Asia/Almaty',
        'Asia/Qyzylorda',
        'Asia/Aqtobe',
        'Asia/Aqtau',
        'Asia/Oral',
    ),
    'LA' => array(
        'Asia/Vientiane',
    ),
    'LB' => array(
        'Asia/Beirut',
    ),
    'LC' => array(
        'America/St_Lucia',
    ),
    'LI' => array(
        'Europe/Vaduz',
    ),
    'LK' => array(
        'Asia/Colombo',
    ),
    'LR' => array(
        'Africa/Monrovia',
    ),
    'LS' => array(
        'Africa/Maseru',
    ),
    'LT' => array(
        'Europe/Vilnius',
    ),
    'LU' => array(
        'Europe/Luxembourg',
    ),
    'LV' => array(
        'Europe/Riga',
    ),
    'LY' => array(
        'Africa/Tripoli',
    ),
    'MA' => array(
        'Africa/Casablanca',
    ),
    'MC' => array(
        'Europe/Monaco',
    ),
    'MD' => array(
        'Europe/Chisinau',
    ),
    'MG' => array(
        'Indian/Antananarivo',
    ),
    'MH' => array(
        'Pacific/Majuro',
        'Pacific/Kwajalein',
    ),
    'MK' => array(
        'Europe/Skopje',
    ),
    'ML' => array(
        'Africa/Bamako',
        'Africa/Timbuktu',
    ),
    'MM' => array(
        'Asia/Rangoon',
    ),
    'MN' => array(
        'Asia/Ulaanbaatar',
        'Asia/Hovd',
        'Asia/Choibalsan',
    ),
    'MO' => array(
        'Asia/Macau',
    ),
    'MP' => array(
        'Pacific/Saipan',
    ),
    'MQ' => array(
        'America/Martinique',
    ),
    'MR' => array(
        'Africa/Nouakchott',
    ),
    'MS' => array(
        'America/Montserrat',
    ),
    'MT' => array(
        'Europe/Malta',
    ),
    'MU' => array(
        'Indian/Mauritius',
    ),
    'MV' => array(
        'Indian/Maldives',
    ),
    'MW' => array(
        'Africa/Blantyre',
    ),
    'MX' => array(
        'America/Mexico_City',
        'America/Cancun',
        'America/Merida',
        'America/Monterrey',
        'America/Mazatlan',
        'America/Chihuahua',
        'America/Hermosillo',
        'America/Tijuana',
    ),
    'MY' => array(
        'Asia/Kuala_Lumpur',
        'Asia/Kuching',
    ),
    'MZ' => array(
        'Africa/Maputo',
    ),
    'NA' => array(
        'Africa/Windhoek',
    ),
    'NC' => array(
        'Pacific/Noumea',
    ),
    'NE' => array(
        'Africa/Niamey',
    ),
    'NF' => array(
        'Pacific/Norfolk',
    ),
    'NG' => array(
        'Africa/Lagos',
    ),
    'NI' => array(
        'America/Managua',
    ),
    'NL' => array(
        'Europe/Amsterdam',
    ),
    'NO' => array(
        'Europe/Oslo',
    ),
    'NP' => array(
        'Asia/Katmandu',
    ),
    'NR' => array(
        'Pacific/Nauru',
    ),
    'NU' => array(
        'Pacific/Niue',
    ),
    'NZ' => array(
        'Pacific/Auckland',
        'Pacific/Chatham',
    ),
    'OM' => array(
        'Asia/Muscat',
    ),
    'PA' => array(
        'America/Panama',
    ),
    'PE' => array(
        'America/Lima',
    ),
    'PF' => array(
        'Pacific/Tahiti',
        'Pacific/Marquesas',
        'Pacific/Gambier',
    ),
    'PG' => array(
        'Pacific/Port_Moresby',
    ),
    'PH' => array(
        'Asia/Manila',
    ),
    'PK' => array(
        'Asia/Karachi',
    ),
    'PL' => array(
        'Europe/Warsaw',
    ),
    'PM' => array(
        'America/Miquelon',
    ),
    'PN' => array(
        'Pacific/Pitcairn',
    ),
    'PR' => array(
        'America/Puerto_Rico',
    ),
    'PS' => array(
        'Asia/Gaza',
    ),
    'PT' => array(
        'Europe/Lisbon',
        'Atlantic/Madeira',
        'Atlantic/Azores',
    ),
    'PW' => array(
        'Pacific/Palau',
    ),
    'PY' => array(
        'America/Asuncion',
    ),
    'QA' => array(
        'Asia/Qatar',
    ),
    'RE' => array(
        'Indian/Reunion',
    ),
    'RO' => array(
        'Europe/Bucharest',
    ),
    'RU' => array(
        'Europe/Kaliningrad',
        'Europe/Moscow',
        'Europe/Samara',
        'Asia/Yekaterinburg',
        'Asia/Omsk',
        'Asia/Novosibirsk',
        'Asia/Krasnoyarsk',
        'Asia/Irkutsk',
        'Asia/Yakutsk',
        'Asia/Vladivostok',
        'Asia/Sakhalin',
        'Asia/Magadan',
        'Asia/Kamchatka',
        'Asia/Anadyr',
    ),
    'RW' => array(
        'Africa/Kigali',
    ),
    'SA' => array(
        'Asia/Riyadh',
    ),
    'SB' => array(
        'Pacific/Guadalcanal',
    ),
    'SC' => array(
        'Indian/Mahe',
    ),
    'SD' => array(
        'Africa/Khartoum',
    ),
    'SE' => array(
        'Europe/Stockholm',
    ),
    'SG' => array(
        'Asia/Singapore',
    ),
    'SH' => array(
        'Atlantic/St_Helena',
    ),
    'SI' => array(
        'Europe/Ljubljana',
    ),
    'SJ' => array(
        'Arctic/Longyearbyen',
        'Atlantic/Jan_Mayen',
    ),
    'SK' => array(
        'Europe/Bratislava',
    ),
    'SL' => array(
        'Africa/Freetown',
    ),
    'SM' => array(
        'Europe/San_Marino',
    ),
    'SN' => array(
        'Africa/Dakar',
    ),
    'SO' => array(
        'Africa/Mogadishu',
    ),
    'SR' => array(
        'America/Paramaribo',
    ),
    'ST' => array(
        'Africa/Sao_Tome',
    ),
    'SV' => array(
        'America/El_Salvador',
    ),
    'SY' => array(
        'Asia/Damascus',
    ),
    'SZ' => array(
        'Africa/Mbabane',
    ),
    'TC' => array(
        'America/Grand_Turk',
    ),
    'TD' => array(
        'Africa/Ndjamena',
    ),
    'TF' => array(
        'Indian/Kerguelen',
    ),
    'TG' => array(
        'Africa/Lome',
    ),
    'TH' => array(
        'Asia/Bangkok',
    ),
    'TJ' => array(
        'Asia/Dushanbe',
    ),
    'TK' => array(
        'Pacific/Fakaofo',
    ),
    'TL' => array(
        'Asia/Dili',
    ),
    'TM' => array(
        'Asia/Ashgabat',
    ),
    'TN' => array(
        'Africa/Tunis',
    ),
    'TO' => array(
        'Pacific/Tongatapu',
    ),
    'TR' => array(
        'Europe/Istanbul',
    ),
    'TT' => array(
        'America/Port_of_Spain',
    ),
    'TV' => array(
        'Pacific/Funafuti',
    ),
    'TW' => array(
        'Asia/Taipei',
    ),
    'TZ' => array(
        'Africa/Dar_es_Salaam',
    ),
    'UA' => array(
        'Europe/Kiev',
        'Europe/Uzhgorod',
        'Europe/Zaporozhye',
        'Europe/Simferopol',
    ),
    'UG' => array(
        'Africa/Kampala',
    ),
    'UM' => array(
        'Pacific/Johnston',
        'Pacific/Midway',
        'Pacific/Wake',
    ),
    'US' => array(
        'America/New_York',
        'America/Detroit',
        'America/Louisville',
        'America/Kentucky/Monticello',
        'America/Indianapolis',
        'America/Indiana/Marengo',
        'America/Indiana/Knox',
        'America/Indiana/Vevay',
        'America/Chicago',
        'America/Menominee',
        'America/North_Dakota/Center',
        'America/Denver',
        'America/Boise',
        'America/Shiprock',
        'America/Phoenix',
        'America/Los_Angeles',
        'America/Anchorage',
        'America/Juneau',
        'America/Yakutat',
        'America/Nome',
        'America/Adak',
        'Pacific/Honolulu',
    ),
    'UY' => array(
        'America/Montevideo',
    ),
    'UZ' => array(
        'Asia/Samarkand',
        'Asia/Tashkent',
    ),
    'VA' => array(
        'Europe/Vatican',
    ),
    'VC' => array(
        'America/St_Vincent',
    ),
    'VE' => array(
        'America/Caracas',
    ),
    'VG' => array(
        'America/Tortola',
    ),
    'VI' => array(
        'America/St_Thomas',
    ),
    'VN' => array(
        'Asia/Saigon',
    ),
    'VU' => array(
        'Pacific/Efate',
    ),
    'WF' => array(
        'Pacific/Wallis',
    ),
    'WS' => array(
        'Pacific/Apia',
    ),
    'YE' => array(
        'Asia/Aden',
    ),
    'YT' => array(
        'Indian/Mayotte',
    ),
    'ZA' => array(
        'Africa/Johannesburg',
    ),
    'ZM' => array(
        'Africa/Lusaka',
    ),
    'ZW' => array(
        'Africa/Harare',
    ),
);

?>
