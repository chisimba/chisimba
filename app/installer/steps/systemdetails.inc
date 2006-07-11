<?php

/* -------------------- systemdetails class ----------------*/





/**

* Class to verify system details 

*/



require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/stepaction.inc';



require_once INSTALL_DIR.'/webformat_class.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';





/**

* SystemDetails

*

* This step collects some initial data about the system

*

* @author prince mbekwa		

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class SystemDetails extends Step

{

	

	

	/**

	* Set up this step's actions

	* 

	* @return void

	* @access public

	*/

	function SystemDetails()

	{

		$this->actions[] =& new RootUserAction(get_class($this));

		

		$this->auth_required = false;

		$this->conf_required = true;

		

		$this->step_title = 'System Details';

		$this->title_logo = 'system_details.gif';

		$this->help_topic = 'system_details';

		$this->step_help = "The Installer will now setup you system details. Please refer to the help for more info on each section,";


	}//end SystemDetails()

	

	

}//end class





/**

* RootUserAction

*

* Prompts the user for a root password and email address.

*

*/

class RootUserAction extends StepAction

{

	

	

	/**

	* Try and create all the needed database tables.

	* 

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{

		

		$_SESSION['root_email'] = (isset($_POST['root_email']) && (strlen($_POST['root_email']) > 0)) ? $_POST['root_email'] : '';

		$_SESSION['tech_email'] = (isset($_POST['tech_email']) && (strlen($_POST['tech_email']) > 0)) ? $_POST['tech_email'] : 'nextgen-users@cvs.uwc.ac.za';

		$_SESSION['sys_name'] = (isset($_POST['sys_name'])) ? $_POST['sys_name'] : '';

		$_SESSION['sys_owner'] = (isset($_POST['sys_owner'])) ? $_POST['sys_owner'] : '';

		$_SESSION['log_path'] = (isset($_POST['log_path'])) ? $_POST['log_path'] : '';

		$_SESSION['postLogin'] = (isset($_POST['postLogin'])) ? $_POST['postLogin'] : '';	

		$_SESSION['defaultskin'] = (isset($_POST['defaultskin'])) ? $_POST['defaultskin'] : '';

		$_SESSION['serverName'] = (isset($_POST['serverName'])) ? $_POST['serverName'] : '';

		$_SESSION['main_server'] = (isset($_POST['main_server'])) ? $_POST['main_server'] : 'http://kngforge.uwc.ac.za/kng_unstable';			

		$_SESSION['serverLocation'] = (isset($_POST['serverLocation'])) ? $_POST['serverLocation'] : '';

		$_SESSION['contentPath'] = (isset($_POST['contentPath'])) ? $_POST['contentPath'] :$_SESSION['contentPath'] ;

		$_SESSION['relContentPath'] = (isset($_POST['relContentPath'])) ? $_POST['relContentPath'] :$_SESSION['relContentPath'] ;

		$_SESSION['log_enable'] = (isset($_POST['log_enable'])) ? $_POST['log_enable'] : '';
		
		$_SESSION['debug_enable'] = (isset($_POST['debug_enable'])) ? $_POST['debug_enable'] : '';

		$_SESSION['proxy'] = (isset($_POST['proxy'])) ? $_POST['proxy'] : '';

		

		

		

		// make sure the URL is lower case so that it matches the 5ive convention.

		$_SESSION['site_url'] = isset($_POST['site_url']) ? strtolower($_POST['site_url']) : '';

		

		if ($_SESSION['site_url'] == '') {

			$this->errors[] = 'You must define a site url.';

			$this->success = false;

			return parent::processAction();

		}

		

		// now check if there is the trailing / character at the end... if there is, strip it

		$_SESSION['site_url'] = rtrim($_SESSION['site_url'], '/');

		

		// get rid of any http:// part

		$_SESSION['site_url'] = str_replace('http://', '', $_SESSION['site_url']);

		

		if ($_SESSION['sys_owner']=='Captian John Picard') {

			$this->errors[] = 'You must set a system owner that is not.'.$_SESSION['sys_owner'];

			$this->success = false;

			return parent::processAction();

		}

				

		if (!valid_email($_SESSION['root_email'])) {

			$this->errors[] = 'You must supply a root email address.';

			$this->success = false;

			return parent::processAction();

		}

		

		if ($_SESSION['serverLocation']=='--') {

			$this->errors[] = 'You must select a server location.';

			$this->success = false;

			return parent::processAction();

		}

		if ($_SESSION['main_server']=='') {

			$this->errors[] = 'You must supply which server is your main server';

			$this->success = false;

			return parent::processAction();

		}

		if ($_SESSION['defaultskin']=='--') {

			$this->errors[] = 'You must select a default skin.';

			$this->success = false;

			return parent::processAction();

		}



		if ($_SESSION['postLogin']=='--') {

			$this->errors[] = 'You must select a post login module.';

			$this->success = false;

			return parent::processAction();

		}	

		return true;

		

	}//end processAction()

	

		

	/**

	* Display information about the creation status to the user

	* 

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/rootdetails.tpl');



		$def_email = isset($_SESSION['root_email']) ? $_SESSION['root_email'] : '';

		

		$tech_email = isset($_SESSION['tech_email']) ? $_SESSION['tech_email'] : 'nextgen-users@cvs.uwc.ac.za';

		

		$def_sysname = isset($_SESSION['sys_name']) ? $_SESSION['sys_name'] : 'Chisimba';

		

		$server_name = isset($_SESSION['serverName']) ? $_SESSION['serverName'] : $server_name;

		

		$serverLocation = isset($_SESSION['serverLocation']) ? $_SESSION['serverLocation'] : '';

		

		$proxy = isset($_SESSION['proxy']) ? $_SESSION['proxy'] : 'http://user:password@proxy.myhost.com:port';

		

		//Get a list of modules to select

				

		$defaultpostlogin = isset($_POST['postLogin']) ? $_POST['postLogin'] : '';

		

		$defaultskin = isset($_POST['defaultskin']) ? $_POST['defaultskin'] : '';

		

		$main_server = isset($_POST['main_server']) ? $_POST['main_server'] : 'http://kngforge.uwc.ac.za/';

		

		//Get content path

		$absContentPath = getcwd();
		
		$relContentRoot = "usrfiles";
		if (preg_match("/\/installer/i", "$absContentPath")) {

			$abspath = str_replace('/installer',"",$absContentPath);
			$relContentPath = "usrfiles/";

	        $_SESSION['relContentPath']=$relContentPath;

	        $absContentPath = $abspath.'/'.$relContentRoot.'/';

	        $_SESSION['contentPath']= $absContentPath;

		} else {

			$abspath = str_replace('\installer',"",$absContentPath);
			$relContentPath = "usrfiles\\";

	     	$_SESSION['relContentPath']=$relContentPath;

	     	$absContentPath = $abspath."\\".$relContentRoot."\\";

	     	$_SESSION['contentPath']= $absContentPath;


		}
  

		$def_sysowner = isset($_SESSION['sys_owner']) ? $_SESSION['sys_owner'] : 'Captian John Picard';

		

		$log_enable = isset($_SESSION['log_enable']) ? $_SESSION['log_enable'] : True;

		$debug_enable = isset($_SESSION['debug_enable']) ? $_SESSION['debug_enable'] : True;

		$this_url = isset($_SESSION['site_url'])?$_SESSION['site_url']:$_SERVER['SERVER_NAME'];

		$log_path = isset($_SESSION['log_path'])?$_SESSION['log_path']: $_SESSION['sys_root'].'/error_logs/error.log';

		ob_start();

			

			text_box('root_email', $def_email, '40', '', false, 'tabindex="5"');

			$email = ob_get_contents();

			ob_clean();

			text_box('proxy', $proxy, '40', '', false, 'tabindex="12"');

			$proxy = ob_get_contents();

			ob_clean();

			

			text_box('tech_email', $tech_email, '40', '', true, 'tabindex="6"');

			$tech_email = ob_get_contents();

			ob_clean();

			

			check_box('log_enable', 1, $log_enable, '', 'tabindex="7"');

			$log_enable = ob_get_contents();

			ob_clean();
			
			check_box('debug_enable', 1, $debug_enable, '', 'tabindex="8"');

			$debug_enable = ob_get_contents();

			ob_clean();
			

			text_box('sys_name', $def_sysname, '40', '', false, 'tabindex="1"');

			$sys_name = ob_get_contents();

			ob_clean();

			

			text_box('serverName',$serverName,'20','',false,'tabindex="2"');

			normal_button('getServerName','Generate','javascript:generateServerName();','tabindex="2"');

			$server_name = ob_get_contents();

			ob_clean();

			

			text_box('contentPath',$contentPath,'40','',true,'');

			$contentPath = ob_get_contents();

			ob_clean();

			

			text_box('relContentPath',$relContentPath,'40','',true,'');

			$rel_contentPath = ob_get_contents();

			ob_clean();

			

			text_box('sys_owner',$def_sysowner,'40','',false,'tabindex="8"');

			$sys_owner = ob_get_contents();

			ob_clean();

					

			text_box('log_path', $log_path, '40', '', false, 'tabindex="9"');

			$log_path = ob_get_contents();

			ob_clean();

			

			combo_box('serverLocation', 

					Array("--"=>'None','AF'=>'Afghanistan',

						'AL'=>'Albania',

					'DZ'=>'Algeria',

					'AS'=>'American',

					'AD'=>'Andorra ',

					'AO'=>'Angola',

					'AI'=>'Anguilla',

					'AQ'=>'Antarctica',

					'AG'=>'Antigua and Barbuda ',

					'AR'=>'Argentina ',

					'AM'=>'Armenia ',

					'AW'=>'Aruba ',

					'AU'=>'Australia ',

					'AT'=>'Austria ',

					'AZ'=>'Azerbaijan',

					'BS'=>'Bahamas ',

					'BH'=>'Bahrain ',

					'BD'=>'Bangladesh',

					'BB'=>'Barbados',

					'BY'=>'Belarus ',

					'BE'=>'Belgium ',

					'BZ'=>'Belize',

					'BJ'=>'Benin ',

					'BM'=>'Bermuda ',

					'BT'=>'Bhutan',

					'BO'=>'Bolivia ',

					'BA'=>'Bosnia and Herzegowina',

					'BW'=>'Botswana',

					'BV'=>'Bouvet Island ',

					'BR'=>'Brazil',

					'IO'=>'British Indian Ocean Territory',

					'BN'=>'Brunei Darussalam ',

					'BG'=>'Bulgaria',

					'BF'=>'Burkina Faso',

					'BI'=>'Burundi ',

					'KH'=>'Cambodia',

					'CM'=>'Cameroon',

					'CA'=>'Canada',

					'CV'=>'Cape Verde',

					'KY'=>'Cayman Islands',

					'CF'=>'Central African Republic',

					'TD'=>'Chad',

					'CL'=>'Chile ',

					'CN'=>'China ',

					'CX'=>'Christmas Island',

					'CC'=>'Cocos (Keeling) Islands ',

					'CO'=>'Colombia',

					'KM'=>'Comoros ',

					'CG'=>'Congo ',

					'CD'=>'Congo, the Democratic Republic of the ',

					'CK'=>'Cook Islands',

					'CR'=>'Costa Rica',

					'CI'=>"Cote d'Ivoire ",

					'HR'=>'Croatia (Hrvatska)',

					'CU'=>'Cuba',

					'CY'=>'Cyprus',

					'CZ'=>'Czech Republic',

					'DK'=>'Denmark ',

					'DJ'=>'Djibouti',

					'DM'=>'Dominica',

					'DO'=>'Dominican Republic',

					'TP'=>'East Timor',

					'EC'=>'Ecuador ',

					'EG'=>'Egypt ',

					'SV'=>'El Salvador ',

					'GQ'=>'Equatorial Guinea ',

					'ER'=>'Eritrea ',

					'EE'=>'Estonia ',

					'ET'=>'Ethiopia',

					'FK'=>'Falkland Islands (Malvinas) ',

					'FO'=>'Faroe Islands ',

					'FJ'=>'Fiji',

					'FI'=>'Finland ',

					'FR'=>'France',

					'FX'=>'France, Metropolitan',

					'GF'=>'French Guiana ',

					'PF'=>'French Polynesia',

					'TF'=>'French Southern Territories ',

					'GA'=>'Gabon ',

					'GM'=>'Gambia',

					'GE'=>'Georgia ',

					'DE'=>'Germany ',

					'GH'=>'Ghana ',

					'GI'=>'Gibraltar ',

					'GR'=>'Greece',

					'GL'=>'Greenland ',

					'GD'=>'Grenada ',

					'GP'=>'Guadeloupe',

					'GU'=>'Guam',

					'GT'=>'Guatemala ',

					'GN'=>'Guinea',

					'GW'=>'Guinea-Bissau ',

					'GY'=>'Guyana',

					'HT'=>'Haiti ',

					'HM'=>'Heard and Mc Donald Islands ',

					'VA'=>'Holy See (Vatican City State) ',

					'HN'=>'Honduras',

					'HK'=>'Hong Kong ',

					'HU'=>'Hungary ',

					'IS'=>'Iceland ',

					'IN'=>'India ',

					'ID'=>'Indonesia ',

					'IR'=>'Iran (Islamic Republic of)',

					'IQ'=>'Iraq',

					'IE'=>'Ireland ',

					'IL'=>'Israel',

					'IT'=>'Italy ',

					'JM'=>'Jamaica ',

					'JP'=>'Japan ',

					'JO'=>'Jordan',

					'KZ'=>'Kazakhstan',

					'KE'=>'Kenya ',

					'KI'=>'Kiribati',

					'KP'=>"Korea, Democratic People's Republic of",

					'KR'=>'Korea, Republic of',

					'KW'=>'Kuwait',

					'KG'=>'Kyrgyzstan',

					'LA'=>"Lao People's Democratic Republic",

					'LV'=>'Latvia',

					'LB'=>'Lebanon ',

					'LS'=>'Lesotho ',

					'LR'=>'Liberia ',

					'LY'=>'Libyan Arab Jamahiriya',

					'LI'=>'Liechtenstein ',

					'LT'=>'Lithuania ',

					'LU'=>'Luxembourg',

					'MO'=>'Macau ',

					'MK'=>'Macedonia, The Former Yugoslav Republic of',

					'MG'=>'Madagascar',

					'MW'=>'Malawi',

					'MY'=>'Malaysia',

					'MV'=>'Maldives',

					'ML'=>'Mali',

					'MT'=>'Malta ',

					'MH'=>'Marshall Islands',

					'MQ'=>'Martinique',

					'MR'=>'Mauritania',

					'MU'=>'Mauritius ',

					'YT'=>'Mayotte ',

					'MX'=>'Mexico',

					'FM'=>'Micronesia, Federated States of ',

					'MD'=>'Moldova, Republic of',

					'MC'=>'Monaco',

					'MN'=>'Mongolia',

					'MS'=>'Montserrat',

					'MA'=>'Morocco ',

					'MZ'=>'Mozambique',

					'MM'=>'Myanmar ',

					'NA'=>'Namibia ',

					'NR'=>'Nauru ',

					'NP'=>'Nepal ',

					'NL'=>'Netherlands ',

					'AN'=>'Netherlands Antilles',

					'NC'=>'New Caledonia ',

					'NZ'=>'New Zealand ',

					'NI'=>'Nicaragua ',

					'NE'=>'Niger ',

					'NG'=>'Nigeria ',

					'NU'=>'Niue',

					'NF'=>'Norfolk Island',

					'MP'=>'Northern Mariana Islands',

					'NO'=>'Norway',

					'OM'=>'Oman',

					'PK'=>'Pakistan',

					'PW'=>'Palau ',

					'PA'=>'Panama',

					'PG'=>'Papua New Guinea',

					'PY'=>'Paraguay',

					'PE'=>'Peru',

					'PH'=>'Philippines ',

					'PN'=>'Pitcairn',

					'PL'=>'Poland',

					'PT'=>'Portugal',

					'PR'=>'Puerto Rico ',

					'QA'=>'Qatar ',

					'RE'=>'Reunion ',

					'RO'=>'Romania ',

					'RU'=>'Russian Federation',

					'RW'=>'Rwanda',

					'KN'=>'Saint Kitts and Nevis ',

					'LC'=>'Saint LUCIA ',

					'VC'=>'Saint Vincent and the Grenadines',

					'WS'=>'Samoa ',

					'SM'=>'San Marino',

					'ST'=>'Sao Tome and Principe ',

					'SA'=>'Saudi Arabia',

					'SN'=>'Senegal ',

					'SC'=>'Seychelles',

					'SL'=>'Sierra Leone',

					'SG'=>'Singapore ',

					'SK'=>'Slovakia (Slovak Republic)',

					'SI'=>'Slovenia',

					'SB'=>'Solomon Islands ',

					'SO'=>'Somalia ',

					'ZA' =>'South Africa',

					'GS'=>'South Georgia and the South Sandwich Islands',

					'ES'=>'Spain ',

					'LK'=>'Sri Lanka ',

					'SH'=>'St. Helena',

					'PM'=>'St. Pierre and Miquelon ',

					'SD'=>'Sudan ',

					'SR'=>'Suriname',

					'SJ'=>'Svalbard and Jan Mayen Islands',

					'SZ'=>'Swaziland ',

					'SE'=>'Sweden',

					'CH'=>'Switzerland ',

					'SY'=>'Syrian Arab Republic',

					'TW'=>'Taiwan, Province of China ',

					'TJ'=>'Tajikistan',

					'TZ'=>'Tanzania, United Republic of',

					'TH'=>'Thailand',

					'TG'=>'Togo',

					'TK'=>'Tokelau ',

					'TO'=>'Tonga ',

					'TT'=>'Trinidad and Tobago ',

					'TN'=>'Tunisia ',

					'TR'=>'Turkey',

					'TM'=>'Turkmenistan',

					'TC'=>'Turks and Caicos Islands',

					'TV'=>'Tuvalu',

					'UG'=>'Uganda',

					'UA'=>'Ukraine ',

					'AE'=>'United Arab Emirates',

					'GB'=>'United Kingdom',

					'US'=>'United States ',

					'UM'=>'United States Minor Outlying Islands',

					'UY'=>'Uruguay ',

					'UZ'=>'Uzbekistan',

					'VU'=>'Vanuatu ',

					'VE'=>'Venezuela ',

					'VN'=>'Viet Nam',

					'VG'=>'Virgin Islands (British)',

					'VI'=>'Virgin Islands (U.S.) ',

					'WF'=>'Wallis and Futuna Islands ',

					'EH'=>'Western Sahara',

					'YE'=>'Yemen ',

					'YU'=>'Yugoslavia',

					'ZM'=>'Zambia',

					'ZW'=>'Zimbabwe'),

					false,

					Array(isset($_SESSION['serverLocation'])?$_SESSION['serverLocation']:''),

					0,

					'tabindex="9"');

			$cmbLocation = ob_get_contents();

			ob_clean();

		

			//loop through the folders and build an array of available modules

			$basedir= $_SESSION['sys_root']."/modules/";

			

			chdir($basedir);

			$dh=opendir($basedir);

			$dirList=array();

			$dirList = array("--"=>'None');

			while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php

				if ($file != "." && $file != ".." && strtolower($file)!="cvs") {

					if (is_dir($file) && file_exists($basedir.$file.'/controller.php')) {

						array_push($dirList, $file);

					}

				}

			}

		     closedir($dh);

		     

    	combo_box('postLogin',

    			  $dirList,false,

    			  array(isset($_SESSION['postLogin'])?$_SESSION['postLogin']:''),

    			  0,'tabindex="12"');

    	$cmbpostLogin = ob_get_contents();

		ob_clean();

    	//loop through the folders and build an array of available skins

		$basedir= $_SESSION['sys_root']."/skins/";

		

		chdir($basedir);

		$dh=opendir($basedir);

		$dirskinList=array();

		$dirskinList = array('--'=>'None');

		while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php

			if ($file != "." && $file != ".." && strtolower($file)!="cvs") {

				if (is_dir($file) && file_exists($basedir.$file.'/kewl_css.php')) {

					array_push($dirskinList, $file);

				}

			}

		}

	    closedir($dh);

	    combo_box('defaultskin',

    			  $dirskinList,false,

    			  array(isset($_SESSION['defaultskin'])?$_SESSION['defaultskin']:''),

    			  0,'tabindex="13"');

    	$cmbdefaultSkin = ob_get_contents();

		ob_clean();	

			  				

		text_box('site_url', isset($_SESSION['site_url'])?$_SESSION['site_url']:$this_url, '40', '', false, 'tabindex="11"');

		$box = ob_get_contents();

		ob_end_clean();

		

		$tpl->set('topic',$this->parent_step); 

		$tpl->set('site_url',$box);

		$tpl->set('root_email', $email);

		$tpl->set('tech_email', $tech_email);

		$tpl->set('rollback_enable', $rollback_enable);

		$tpl->set('sys_owner', $sys_owner);

		$tpl->set('serverName', $server_name);

		$tpl->set('contentPath', $contentPath);

		$tpl->set('relContentPath', $rel_contentPath);

		$tpl->set('serverLocation', $cmbLocation);

		$tpl->set('sys_name', $sys_name);

		$tpl->set('log_path', $log_path);

		$tpl->set('log_enable',$log_enable);
		
		$tpl->set('debug_enable',$debug_enable);

		$tpl->set('postLogin',$cmbpostLogin);

		$tpl->set('defaultskin',$cmbdefaultSkin);

		$tpl->set('proxy',$proxy);

		

		echo $tpl->fetch();

		

	}//end paintAction()

	

	/**

	* Show what stuff was saved. Make note about the  lowercase url

	* 

	* @return void

	* @access public

	*/

	function confirmAction()

	{

		

		$tpl = new Template(INSTALL_DIR.'/templates/rootdetails.tpl');

		

		$def_email = isset($_SESSION['root_email']) ? $_SESSION['root_email'] : '';

		

		$tech_email = isset($_SESSION['tech_email']) ? $_SESSION['tech_email'] : 'nextgen-users@cvs.uwc.ac.za';

		

		$def_sysname = isset($_SESSION['sys_name']) ? $_SESSION['sys_name'] : 'STAR SHIP: THE NEXT GENERATION';

		

		$server_name = isset($_SESSION['serverName']) ? $_SESSION['serverName'] : '';

		

		$serverLocation = isset($_SESSION['serverLocation']) ? $_SESSION['serverLocation'] : '';

		

		$proxy = isset($_SESSION['proxy']) ? $_SESSION['proxy'] :'http://user:password@proxy.myhost.com:port';

		

		//Get a list of modules to select

				

		$defaultpostlogin = isset($_SESSION['postLogin']) ? $_SESSION['postLogin'] : '';

		//We need to extract the actual value of the selected key

		//to match the real value of what was selected for post login

		$basedir= $_SESSION['sys_root']."/modules/";



		chdir($basedir);

		$dh=opendir($basedir);

		$dirList=array();

		$dirList = array("--"=>'None');

		while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php

			if ($file != "." && $file != ".." && strtolower($file)!="cvs") {

				if (is_dir($file) && file_exists($basedir.$file.'/controller.php')) {

					array_push($dirList, $file);

				}

			}

		}

		closedir($dh);

		foreach ($dirList as $key => $value) {

			if ($key ==$defaultpostlogin) {

				$defaultpostlogin =$value;
				$_SESSION['postLogin'] = $defaultpostlogin;	

				
			}

		}

		

		//save key for selected skin 

		$defaultskin = isset($_SESSION['defaultskin']) ? $_SESSION['defaultskin'] : '';

		//We need to extract the actual value of the selected key

		//to match the real value of what was selected for available skins

		$basedir= $_SESSION['sys_root']."/skins/";

		

		chdir($basedir);

		$dh=opendir($basedir);

		$dirskinList=array();

		$dirskinList = array('--'=>'None');

		while (false !== ($file = readdir($dh))) { #see http://www.php.net/manual/en/function.readdir.php

			if ($file != "." && $file != ".." && strtolower($file)!="cvs") {

				if (is_dir($file) && file_exists($basedir.$file.'/kewl_css.php')) {

					array_push($dirskinList, $file);

				}

			}

		}

	    closedir($dh);

	    foreach ($dirskinList as $key => $value) {

			if ($key ==$defaultskin) {

				$defaultskin =$value;
				$_SESSION['defaultskin'] = $defaultskin;

			}

		}

		$main_server = isset($_SESSION['main_server']) ? $_SESSION['main_server'] : 'http://kngforge.uwc.ac.za/kng_unstable';

		

		$contentPath = isset($_SESSION['contentPath']) ? $_SESSION['contentPath'] :'';

	        

		$def_sysowner = isset($_SESSION['sys_owner']) ? $_SESSION['sys_owner'] :'';

		

		$log_enable = isset($_SESSION['log_enable']) ? $_SESSION['log_enable'] : '';

		if ($log_enable==1)
		{
			$_SESSION['log_enable'] = "TRUE";
			$log_enable = "TRUE";
			
		}else {
			
			$_SESSION['log_enable'] = "FALSE";
			$log_enable = "FALSE";
		}
		
		$debug_enable = isset($_SESSION['debug_enable']) ? $_SESSION['debug_enable'] : '';

		if ($debug_enable==1)
		{
			$_SESSION['debug_enable'] = "developer";
			$debug_enable = "TRUE";
			
		}else {
			
			$_SESSION['debug_enable'] = "user";
			$debug_enable = "FALSE";
		}

		$this_url = isset($_SESSION['site_url'])?$_SESSION['site_url']:$_SERVER['SERVER_NAME'];

		

		$log_path = isset($_SESSION['log_path'])?$_SESSION['log_path']: $_SESSION['sys_root'].'/data/private/logs/error.log';

		

		$tpl->set('topic',$this->parent_step); 

		$tpl->set('site_url',$this_url. '<br/> (Please note that 5ive treats all URLS as lower case)');

		$tpl->set('root_email', $def_email);

		$tpl->set('tech_email', $tech_email);

		$tpl->set('rollback_enable', $rollback);

		$tpl->set('sys_owner', $def_sysowner);

		$tpl->set('sys_name', $def_sysname);

		$tpl->set('serverName', $server_name);

		$tpl->set('contentPath', $contentPath);

		$tpl->set('serverLocation', $serverLocation);

		$tpl->set('log_enable',$log_enable);
		
		$tpl->set('debug_enable',$debug_enable);

		$tpl->set('log_path', $log_path);

		$tpl->set('defaultskin', $defaultskin);

		$tpl->set('postLogin', $defaultpostlogin);

		$tpl->set('proxy',$proxy);

				

		echo $tpl->fetch();

		

	}//end confirmAction()

	

	

}//end class



?>

