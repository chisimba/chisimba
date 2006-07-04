<?
/**
*This is a Language class for kewlNextGen
*@author Tohir Solomons
*@copyright (c) 200-2004 University of the Western Cape
*@Version 1
*/
/**
* This class converts retrieves the name of a language by providing the ISO code and also vice versa
*
* The original list of code was taken from a class written by Florian Breit (florian at phpws dot org): 
*  http://www.phpclasses.org/browse/file/8143.html
*/
class languagecode extends object 
{
    /**
    * @var array $iso_639_2_tags contains an associative array of all the alpha2 languages
    */
    var $iso_639_2_tags = array('aa' => 'Afar',
                                  'ab' => 'Abkhazian',
                                  'af' => 'Afrikaans',
                                  'am' => 'Amharic',
                                  'ar' => 'Arabic',
                                  'as' => 'Assamese',
                                  'ay' => 'Aymara',
                                  'az' => 'Azerbaijani',
                                  'ba' => 'Bashkir',
                                  'be' => 'Byelorussian',
                                  'bg' => 'Bulgarian',
                                  'bh' => 'Bihari',
                                  'bi' => 'Bislama',
                                  'bn' => 'Bengali',
                                  'bo' => 'Tibetan',
                                  'br' => 'Breton',
                                  'ca' => 'Catalan',
                                  'co' => 'Corsican',
                                  'cs' => 'Czech',
                                  'cy' => 'Welsh',
                                  'da' => 'Danish',
                                  'de' => 'German',
                                  'dz' => 'Bhutani',
                                  'el' => 'Greek',
                                  'en' => 'English',
                                  'eo' => 'Esperanto',
                                  'es' => 'Spanish',
                                  'et' => 'Estonian',
                                  'eu' => 'Basque',
                                  'fa' => 'Persian',
                                  'fi' => 'Finnish',
                                  'fj' => 'Fiji',
                                  'fo' => 'Faeroese',
                                  'fr' => 'French',
                                  'fy' => 'Frisian',
                                  'ga' => 'Irish',
                                  'gd' => 'Gaelic',
                                  'gl' => 'Galician',
                                  'gn' => 'Guarani',
                                  'gu' => 'Gujarati',
                                  'ha' => 'Hausa',
                                  'hi' => 'Hindi',
                                  'hr' => 'Croatian',
                                  'hu' => 'Hungarian',
                                  'hy' => 'Armenian',
                                  'ia' => 'Interlingua',
                                  'ie' => 'Interlingue',
                                  'ik' => 'Inupiak',
                                  'in' => 'Indonesian',
                                  'is' => 'Icelandic',
                                  'it' => 'Italian',
                                  'iw' => 'Hebrew',
                                  'ja' => 'Japanese',
                                  'ji' => 'Yiddish',
                                  'jw' => 'Javanese',
                                  'ka' => 'Georgian',
                                  'kk' => 'Kazakh',
                                  'kl' => 'Greenlandic',
                                  'km' => 'Cambodian',
                                  'kn' => 'Kannada',
                                  'ko' => 'Korean',
                                  'ks' => 'Kashmiri',
                                  'ku' => 'Kurdish',
                                  'ky' => 'Kirghiz',
                                  'la' => 'Latin',
                                  'ln' => 'Lingala',
                                  'lo' => 'Laothian',
                                  'lt' => 'Lithuanian',
                                  'lv' => 'Latvian',
                                  'mg' => 'Malagasy',
                                  'mi' => 'Maori',
                                  'mk' => 'Macedonian',
                                  'ml' => 'Malayalam',
                                  'mn' => 'Mongolian',
                                  'mo' => 'Moldavian',
                                  'mr' => 'Marathi',
                                  'ms' => 'Malay',
                                  'mt' => 'Maltese',
                                  'my' => 'Burmese',
                                  'na' => 'Nauru',
                                  'ne' => 'Nepali',
                                  'nl' => 'Dutch',
                                  'no' => 'Norwegian',
                                  'oc' => 'Occitan',
                                  'om' => 'Oromo',
                                  'or' => 'Oriya',
                                  'pa' => 'Punjabi',
                                  'pl' => 'Polish',
                                  'ps' => 'Pashto',
                                  'pt' => 'Portuguese',
                                  'qu' => 'Quechua',
                                  'rm' => 'Rhaeto-Romance',
                                  'rn' => 'Kirundi',
                                  'ro' => 'Romanian',
                                  'ru' => 'Russian',
                                  'rw' => 'Kinyarwanda',
                                  'sa' => 'Sanskrit',
                                  'sd' => 'Sindhi',
                                  'sg' => 'Sangro',
                                  'sh' => 'Serbo-Croatian',
                                  'si' => 'Singhalese',
                                  'sk' => 'Slovak',
                                  'sl' => 'Slovenian',
                                  'sm' => 'Samoan',
                                  'sn' => 'Shona',
                                  'so' => 'Somali',
                                  'sq' => 'Albanian',
                                  'sr' => 'Serbian',
                                  'ss' => 'Siswati',
                                  'st' => 'Sesotho',
                                  'su' => 'Sudanese',
                                  'sv' => 'Swedish',
                                  'sw' => 'Swahili',
                                  'ta' => 'Tamil',
                                  'te' => 'Tegulu',
                                  'tg' => 'Tajik',
                                  'th' => 'Thai',
                                  'ti' => 'Tigtinya',
                                  'tk' => 'Turkmen',
                                  'tl' => 'Tagalog',
                                  'tn' => 'Setswana',
                                  'to' => 'Tonga',
                                  'tr' => 'Turkish',
                                  'ts' => 'Tsonga',
                                  'tt' => 'Tatar',
                                  'tw' => 'Twi',
                                  'uk' => 'Ukrainian',
                                  'ur' => 'Urdu',
                                  'uz' => 'Uzbek',
                                  'vi' => 'Vietnamese',
                                  'vo' => 'Volapuk',
                                  'wo' => 'Wolof',
                                  'xh' => 'Xhosa',
                                  'yo' => 'Yoruba',
                                  'zh' => 'Chinese',
                                  'zu' => 'Zulu'
                                 );
    
    /**
    * Standard constructor method 
    */
    function init()
    { 
    }
    
    /**
    * Method to get the name of a language by providing the ISO Code
    *
    * This method first lowercases the code (to match the array) and then checks if it exists in the array.
    * If it does, return the language, else NULL
    * @param string $isoKey The two letter ISO code
    * @return string |NULL The Name of the Language
    */
    function getLanguage($isoKey)
    {
        if (array_key_exists(strtolower($isoKey), $this->iso_639_2_tags)) {
            return $this->iso_639_2_tags[strtolower($isoKey)];
        } else {
            return NULL;
        }
    }
    
    /**
    * Method to get the name of a ISO Code of a language by providing the ISO Code
    *
    * @param string $language The language to check
    * @return string |NULL The ISO code of the Language
    */
    function getISO($language)
    {
        // Flip Array - makes key the values, and values the key
        //$tempArray = array_flip($this->iso_639_2_tags); 
        $tempArray = $this->iso_639_2_tags;
        // Upper Case the first letter of the Word
        $language = strtolower($language);
        
        if (array_key_exists($language, $tempArray)) {
        	//$tempArray = array_flip($tempArray);
        	//print_r($tempArray);
            return $language;
        } else {
        	//echo "returning null";
            return NULL;
        }
    }
    
} // End of Class
?>