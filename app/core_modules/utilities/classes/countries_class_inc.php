<?php
/* -------------------- countries class ----------------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Class for the country codes
*
* This class provides a list of country codes, 
* and allows conversion between country codes to country names
* This class can also be used to get Country Flags
*/
class countries extends object
{

    /**
    * @var array $countries List of Countries as an associative array with code as key, and name as value
    */
    private $countries;
    
    /**
    * @var array $countriesFlip A reverse of the above with name as key, and code as value
    */
    private $countriesFlip;
    
    
	/**
	* Constructor
	*/
    public function init()
    {
        $this->setupCountries();
        $this->objConfig =& $this->getObject('altconfig','config');
        $this->loadClass('dropdown', 'htmlelements');
    }
	
    /**
    * Method to setup the list of countries in an array
    */
    private function setupCountries()
    {
        $this->countries['AD']='Andorra';  
        $this->countries['AE']='United Arab Emirates';  
        $this->countries['AF']='Afghanistan';  
        $this->countries['AG']='Antigua and Barbuda';  
        $this->countries['AI']='Anguilla';  
        $this->countries['AL']='Albania';  
        $this->countries['AM']='Armenia';  
        $this->countries['AN']='Netherlands Antilles';  
        $this->countries['AO']='Angola';  
        $this->countries['AQ']='Antarctica';  
        $this->countries['AR']='Argentina';  
        $this->countries['AS']='American Samoa';  
        $this->countries['AT']='Austria';  
        $this->countries['AU']='Australia';  
        $this->countries['AW']='Aruba';  
        $this->countries['AZ']='Azerbaijan';  
        $this->countries['BA']='Bosnia and Herzegovina';  
        $this->countries['BB']='Barbados';  
        $this->countries['BD']='Bangladesh';  
        $this->countries['BE']='Belgium';  
        $this->countries['BF']='Burkina Faso';  
        $this->countries['BG']='Bulgaria';  
        $this->countries['BH']='Bahrain';  
        $this->countries['BI']='Burundi';  
        $this->countries['BJ']='Benin';  
        $this->countries['BM']='Bermuda';  
        $this->countries['BN']='Brunei Darussalam';  
        $this->countries['BO']='Bolivia';  
        $this->countries['BR']='Brazil';  
        $this->countries['BS']='Bahamas';  
        $this->countries['BT']='Bhutan';  
        $this->countries['BV']='Bouvet Island';  
        $this->countries['BW']='Botswana';  
        $this->countries['BY']='Belarus';  
        $this->countries['BZ']='Belize';  
        $this->countries['CA']='Canada';  
        $this->countries['CC']='Cocos (Keeling) Islands';  
        $this->countries['CD']='Congo, the Democratic Republic of the';  
        $this->countries['CF']='Central African Republic';  
        $this->countries['CG']='Congo';  
        $this->countries['CH']='Switzerland';  
        $this->countries['CI']='Cote D\'Ivoire';  
        $this->countries['CK']='Cook Islands';  
        $this->countries['CL']='Chile';  
        $this->countries['CM']='Cameroon';  
        $this->countries['CN']='China';  
        $this->countries['CO']='Colombia';  
        $this->countries['CR']='Costa Rica';  
        $this->countries['CS']='Serbia and Montenegro';  
        $this->countries['CU']='Cuba';  
        $this->countries['CV']='Cape Verde';  
        $this->countries['CX']='Christmas Island';  
        $this->countries['CY']='Cyprus';  
        $this->countries['CZ']='Czech Republic';  
        $this->countries['DE']='Germany';  
        $this->countries['DJ']='Djibouti';  
        $this->countries['DK']='Denmark';  
        $this->countries['DM']='Dominica';  
        $this->countries['DO']='Dominican Republic';  
        $this->countries['DZ']='Algeria';  
        $this->countries['EC']='Ecuador';  
        $this->countries['EE']='Estonia';  
        $this->countries['EG']='Egypt';  
        $this->countries['EH']='Western Sahara';  
        $this->countries['ER']='Eritrea';  
        $this->countries['ES']='Spain';  
        $this->countries['ET']='Ethiopia';  
        $this->countries['FI']='Finland';  
        $this->countries['FJ']='Fiji';  
        $this->countries['FK']='Falkland Islands (Malvinas)';  
        $this->countries['FM']='Micronesia, Federated States of';  
        $this->countries['FO']='Faroe Islands';  
        $this->countries['FR']='France';  
        $this->countries['GA']='Gabon';  
        $this->countries['GB']='United Kingdom';  
        $this->countries['GD']='Grenada';  
        $this->countries['GE']='Georgia';  
        $this->countries['GF']='French Guiana';  
        $this->countries['GH']='Ghana';  
        $this->countries['GI']='Gibraltar';  
        $this->countries['GL']='Greenland';  
        $this->countries['GM']='Gambia';  
        $this->countries['GN']='Guinea';  
        $this->countries['GP']='Guadeloupe';  
        $this->countries['GQ']='Equatorial Guinea';  
        $this->countries['GR']='Greece';  
        $this->countries['GS']='South Georgia and the South Sandwich Islands';  
        $this->countries['GT']='Guatemala';  
        $this->countries['GU']='Guam';  
        $this->countries['GW']='Guinea-Bissau';  
        $this->countries['GY']='Guyana';  
        $this->countries['HK']='Hong Kong';  
        $this->countries['HM']='Heard Island and Mcdonald Islands';  
        $this->countries['HN']='Honduras';  
        $this->countries['HR']='Croatia';  
        $this->countries['HT']='Haiti';  
        $this->countries['HU']='Hungary';  
        $this->countries['ID']='Indonesia';  
        $this->countries['IE']='Ireland';  
        $this->countries['IL']='Israel';  
        $this->countries['IN']='India';  
        $this->countries['IO']='British Indian Ocean Territory';  
        $this->countries['IQ']='Iraq';  
        $this->countries['IR']='Iran, Islamic Republic of';  
        $this->countries['IS']='Iceland';  
        $this->countries['IT']='Italy';  
        $this->countries['JM']='Jamaica';  
        $this->countries['JO']='Jordan';  
        $this->countries['JP']='Japan';  
        $this->countries['KE']='Kenya';  
        $this->countries['KG']='Kyrgyzstan';  
        $this->countries['KH']='Cambodia';  
        $this->countries['KI']='Kiribati';  
        $this->countries['KM']='Comoros';  
        $this->countries['KN']='Saint Kitts and Nevis';  
        $this->countries['KP']='Korea, Democratic People\'s Republic of';  
        $this->countries['KR']='Korea, Republic of';  
        $this->countries['KW']='Kuwait';  
        $this->countries['KY']='Cayman Islands';  
        $this->countries['KZ']='Kazakhstan';  
        $this->countries['LA']='Lao People\'s Democratic Republic';  
        $this->countries['LB']='Lebanon';  
        $this->countries['LC']='Saint Lucia';  
        $this->countries['LI']='Liechtenstein';  
        $this->countries['LK']='Sri Lanka';  
        $this->countries['LR']='Liberia';  
        $this->countries['LS']='Lesotho';  
        $this->countries['LT']='Lithuania';  
        $this->countries['LU']='Luxembourg';  
        $this->countries['LV']='Latvia';  
        $this->countries['LY']='Libyan Arab Jamahiriya';  
        $this->countries['MA']='Morocco';  
        $this->countries['MC']='Monaco';  
        $this->countries['MD']='Moldova, Republic of';  
        $this->countries['MG']='Madagascar';  
        $this->countries['MH']='Marshall Islands';  
        $this->countries['MK']='Macedonia, the Former Yugoslav Republic of';  
        $this->countries['ML']='Mali';  
        $this->countries['MM']='Myanmar';  
        $this->countries['MN']='Mongolia';  
        $this->countries['MO']='Macao';  
        $this->countries['MP']='Northern Mariana Islands';  
        $this->countries['MQ']='Martinique';  
        $this->countries['MR']='Mauritania';  
        $this->countries['MS']='Montserrat';  
        $this->countries['MT']='Malta';  
        $this->countries['MU']='Mauritius';  
        $this->countries['MV']='Maldives';  
        $this->countries['MW']='Malawi';  
        $this->countries['MX']='Mexico';  
        $this->countries['MY']='Malaysia';  
        $this->countries['MZ']='Mozambique';  
        $this->countries['NA']='Namibia';  
        $this->countries['NC']='New Caledonia';  
        $this->countries['NE']='Niger';  
        $this->countries['NF']='Norfolk Island';  
        $this->countries['NG']='Nigeria';  
        $this->countries['NI']='Nicaragua';  
        $this->countries['NL']='Netherlands';  
        $this->countries['NO']='Norway';  
        $this->countries['NP']='Nepal';  
        $this->countries['NR']='Nauru';  
        $this->countries['NU']='Niue';  
        $this->countries['NZ']='New Zealand';  
        $this->countries['OM']='Oman';  
        $this->countries['PA']='Panama';  
        $this->countries['PE']='Peru';  
        $this->countries['PF']='French Polynesia';  
        $this->countries['PG']='Papua New Guinea';  
        $this->countries['PH']='Philippines';  
        $this->countries['PK']='Pakistan';  
        $this->countries['PL']='Poland';  
        $this->countries['PM']='Saint Pierre and Miquelon';  
        $this->countries['PN']='Pitcairn';  
        $this->countries['PR']='Puerto Rico';  
        $this->countries['PS']='Palestinian Territory, Occupied';  
        $this->countries['PT']='Portugal';  
        $this->countries['PW']='Palau';  
        $this->countries['PY']='Paraguay';  
        $this->countries['QA']='Qatar';  
        $this->countries['RE']='Reunion';  
        $this->countries['RO']='Romania';  
        $this->countries['RU']='Russian Federation';  
        $this->countries['RW']='Rwanda';  
        $this->countries['SA']='Saudi Arabia';  
        $this->countries['SB']='Solomon Islands';  
        $this->countries['SC']='Seychelles';  
        $this->countries['SD']='Sudan';  
        $this->countries['SE']='Sweden';  
        $this->countries['SG']='Singapore';  
        $this->countries['SH']='Saint Helena';  
        $this->countries['SI']='Slovenia';  
        $this->countries['SJ']='Svalbard and Jan Mayen';  
        $this->countries['SK']='Slovakia';  
        $this->countries['SL']='Sierra Leone';  
        $this->countries['SM']='San Marino';  
        $this->countries['SN']='Senegal';  
        $this->countries['SO']='Somalia';  
        $this->countries['SR']='Suriname';  
        $this->countries['ST']='Sao Tome and Principe';  
        $this->countries['SV']='El Salvador';  
        $this->countries['SY']='Syrian Arab Republic';  
        $this->countries['SZ']='Swaziland';  
        $this->countries['TC']='Turks and Caicos Islands';  
        $this->countries['TD']='Chad';  
        $this->countries['TF']='French Southern Territories';  
        $this->countries['TG']='Togo';  
        $this->countries['TH']='Thailand';  
        $this->countries['TJ']='Tajikistan';  
        $this->countries['TK']='Tokelau';  
        $this->countries['TL']='Timor-Leste';  
        $this->countries['TM']='Turkmenistan';  
        $this->countries['TN']='Tunisia';  
        $this->countries['TO']='Tonga';  
        $this->countries['TR']='Turkey';  
        $this->countries['TT']='Trinidad and Tobago';  
        $this->countries['TV']='Tuvalu';  
        $this->countries['TW']='Taiwan, Province of China';  
        $this->countries['TZ']='Tanzania, United Republic of';  
        $this->countries['UA']='Ukraine';  
        $this->countries['UG']='Uganda';  
        $this->countries['UM']='United States Minor Outlying Islands';  
        $this->countries['US']='United States';  
        $this->countries['UY']='Uruguay';  
        $this->countries['UZ']='Uzbekistan';  
        $this->countries['VA']='Holy See (Vatican City State)';  
        $this->countries['VC']='Saint Vincent and the Grenadines';  
        $this->countries['VE']='Venezuela';  
        $this->countries['VG']='Virgin Islands, British';  
        $this->countries['VI']='Virgin Islands, U.s.';  
        $this->countries['VN']='Viet Nam';  
        $this->countries['VU']='Vanuatu';  
        $this->countries['WF']='Wallis and Futuna';  
        $this->countries['WS']='Samoa';  
        $this->countries['YE']='Yemen';  
        $this->countries['YT']='Mayotte';  
        $this->countries['ZA']='South Africa';  
        $this->countries['ZM']='Zambia';  
        $this->countries['ZW']='Zimbabwe';
        
        // Sort by Country Name
        asort($this->countries);
        
        // Create the Flipped Array
        $this->countriesFlip = array_flip($this->countries);
	}
    

    
    /**
    * Method to get a country's name by providing the two letter country code
    *
    * @param string $code: Two letter country code
    * @return string : name of the country
    */
    public function getCountryName($code)
    {
        $code = strtoupper($code);
        
        if (array_key_exists($code, $this->countries)) {
            return $this->countries[$code];
        } else {
            return 'unknown';
        }
    }
    
    /**
    * Method to get the country flag by providing country code
    *
    * @param string $code: two letter country code
    * @return $flagsrc string : Flag Image File Url
    */
    public function getCountryFlag($code)
    {
        
        $flagsrc = 'core_modules/utilities/resources/flags/'.strtolower($code).'.gif';

        if (!file_exists($this->objConfig->getsiterootPath().'/'.$flagsrc)) { 
           $flagsrc = 'core_modules/utilities/resources/flags/-.gif';
        }

        return '<img src="'.$flagsrc.'" alt="'.$this->getCountryName($code).'" title="'.$this->getCountryName($code).'" />';
    }
    
    /**
    * Method to get a dropdown with the list of countries
    *
    * @param string $name: Name of the Select Dropdown
    * @param string $defaultCountry: Two letter country code of the default country.
    * @return $flagsrc string : Flag Image File Url
    */
    public function getDropDown($name='country', $defaultCountry=NULL)
    {
        $dropdown = new dropdown($name);
        
        foreach ($this->countries as $code=>$country)
        {
            $dropdown->addOption($code, $country);
        }
        
        if ($defaultCountry != NULL)
        {
            $dropdown->setSelected($defaultCountry);
        }
        
        return $dropdown->show();
    }
    
	
}  #end of class
?>