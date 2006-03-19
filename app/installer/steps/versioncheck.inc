<?php
require_once INSTALL_DIR.'/step.inc';
require_once INSTALL_DIR.'/stepaction.inc';

/**
* VersionCheck
* * 
* Checks all the required version numbers of the target system 
* 
* 
* @author Prince Mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class VersionCheck extends Step
{
	
	
	/**
	* Checks all version requirements
	* 
	* @return void
	* @access public
	*/
	function VersionCheck() 
	{
		$this->actions[] =& new PHPCheckAction(get_class($this));
		$this->actions[] =& new PEARCheckAction(get_class($this));
		
		$this->step_title = 'System Requirements.';
		$this->title_logo = 'version_check.gif';
		$this->auth_required = false;
		$this->conf_required = true;
		$this->help_topic = 'version_check';
		
	}//end VersionCheck()
	
	
}//end class



/**
* PHPCheckAction
* 
* Perform checks for the PHP version
* 
*
*/
class PHPCheckAction extends StepAction
{
	/**
	* The minumum version that 5ive will install on.
	* @var type
	*/
	var $min_version = '5.1.2';
	
	/**
	* Holds an array about what requirements are needed.
	* array( setting => Array('version'=>vernum, 'available'=>boolean, 'message' => string) )
	* @var array
	*/
	var $required_settings = Array(	'PHP' => Array('version'=>'5.1.2', 'available'=>true, 'message'=> ''),
										'GD'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'Zlib'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'PSpell'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'calendar'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'curl'=>Array('version'=>'7.10.5', 'available' => true, 'message'=> ''),
										'dom'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'libxml'=>Array('version'=>'2.6.0.', 'available' => true, 'message'=> ''),
										'mysqlclient'=>Array('version'=>'4.1.12', 'available' => true, 'message'=> ''),
										'mysql_server'=>Array('version'=>'5.1.8', 'available' => true, 'message'=> ''),
										'mysqli'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'PDO'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'pdo_mysql'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'pdo_pgsql'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'pdo_sqlite'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'pgsql'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'SQLite'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'SimpleXML'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'SOAP'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'xml'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'xmlreader'=>Array('version'=>'', 'available' => true, 'message'=> ''),
										'xmlwriter'=>Array('version'=>'', 'available' => true, 'message'=> ''),
									);
	
	
	/**
	* Figure out whether the current PHP version is sufficient
	* 
	* @return boolean Whether this check succeeded or not.
	* @access public
	*/
	function processAction()
	{
		
		
		if(version_compare(phpversion(), $this->required_settings['PHP']['version']) < 0) {
			$this->success = false;
			$this->required_settings['PHP']['message'] =  '<img src="./extra/failed.png" border="0"  alt="Failed" title="Failed" />';
		} else {
			$this->required_settings['PHP']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		
		// check if gd available
		if (!function_exists('gd_info')) {
			$this->required_settings['GD']['available'] = false;
			$this->required_settings['GD']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'You must have GD installed to continue.';
			$this->success = false;
		} else {
			$this->required_settings['GD']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		
		// check zlib
		if (!function_exists('gzopen')) {
			$this->required_settings['Zlib']['available'] = false;
			$this->required_settings['Zlib']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->success = false;
		} else {
			$this->required_settings['Zlib']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		
		// check pspell
		if (!function_exists('pspell_check')) {
			$this->required_settings['PSpell']['available'] = false;
			$this->required_settings['PSpell']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'PSpell not found. You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['PSpell']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check calendar
		if (!function_exists('cal_info')) {
			$this->required_settings['calendar']['available'] = false;
			$this->required_settings['calendar']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'calendar not found. You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['calendar']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		
		// check curl
		if (!function_exists('curl_version')) {
			$this->required_settings['curl']['available'] = false;
			$this->required_settings['curl']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'curl not found. You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['curl']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check dom
		$dom = new DOMDocument('1.0', 'iso-8859-1');
		$element = $dom->appendChild(new DOMElement('root'));
		
		if (!is_object($element)) {
			$this->required_settings['dom']['available'] = false;
			$this->required_settings['dom']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'dom not found. You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['dom']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		
		// check libxml
		if (!function_exists('libxml_get_errors')) {
			$this->required_settings['libxml']['available'] = false;
			$this->required_settings['libxml']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'libxml not found. You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['libxml']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
				
		// check mysqlclient
					
		if (!function_exists('mysql_get_client_info')) {
			$this->required_settings['mysqlclient']['available'] = false;
			$this->required_settings['mysqlclient']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'mysqlclient not found. You need to install this extension';
			
		} else {
			$client_ver = mysql_get_client_info();
			if(!$client_ver >='4.1.12'){
				$this->required_settings['mysqlclient']['available'] = false;
				$this->required_settings['mysqlclient']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
				$this->errors[] = 'mysqlclient is not meeting System requirements. You need to upgrade this extension';
			}
			$this->required_settings['mysqlclient']['message'] =  $client_ver;
		}
		// check mysql_server
		
		if (!function_exists('mysql_get_server_info')) {
			$this->required_settings['mysql_server']['available'] = false;
			$this->required_settings['mysql_server']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'mysql_server not found. You need to install this extension';
		} else {
			$this->required_settings['mysql_server']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check mysqli
		if (!extension_loaded('mysqli')) {
			$this->required_settings['mysqli']['available'] = false;
			$this->required_settings['mysqli']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'mysqli not found. You need to install this extension';
		} else {
			$this->required_settings['mysqli']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check PDO
		if (!extension_loaded('PDO')) {
			$this->required_settings['PDO']['available'] = false;
			$this->required_settings['PDO']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'PDO not found.. You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['PDO']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check pdo_mysql
		if (!extension_loaded('pdo_mysql')) {
			$this->required_settings['pdo_mysql']['available'] = false;
			$this->required_settings['pdo_mysql']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'pdo_mysql not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['pdo_mysql']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check pdo_pgsql
		if (!extension_loaded('pdo_pgsql')) {
			$this->required_settings['pdo_pgsql']['available'] = false;
			$this->required_settings['pdo_pgsql']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'pdo_pgsql not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['pdo_pgsql']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check pdo_sqlite
		if (!extension_loaded('pdo_sqlite')) {
			$this->required_settings['pdo_sqlite']['available'] = false;
			$this->required_settings['pdo_sqlite']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'pdo_sqlite not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['pdo_sqlite']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check pgsql
		if (!extension_loaded('pgsql')) {
			$this->required_settings['pgsql']['available'] = false;
			$this->required_settings['pgsql']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'pgsql not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['pgsql']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check SQLite
		if (!extension_loaded('SQLite')) {
			$this->required_settings['SQLite']['available'] = false;
			$this->required_settings['SQLite']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'SQLite not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['SQLite']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check SimpleXML
		if (!extension_loaded('SimpleXML')) {
			$this->required_settings['SimpleXML']['available'] = false;
			$this->required_settings['SimpleXML']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'SimpleXML not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['SimpleXML']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check SOAP
		if (!extension_loaded('SOAP')) {
			$this->required_settings['SOAP']['available'] = false;
			$this->required_settings['SOAP']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'SOAP not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['SOAP']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check xml
		if (!extension_loaded('xml')) {
			$this->required_settings['xml']['available'] = false;
			$this->required_settings['xml']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'xml not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['xml']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check SOAP
		if (!extension_loaded('SOAP')) {
			$this->required_settings['SOAP']['available'] = false;
			$this->required_settings['SOAP']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'SOAP not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['SOAP']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check xmlreader
		if (!extension_loaded('xmlreader')) {
			$this->required_settings['xmlreader']['available'] = false;
			$this->required_settings['xmlreader']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'xmlreader not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['xmlreader']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		// check xmlwriter
		if (!extension_loaded('xmlwriter')) {
			$this->required_settings['xmlwriter']['available'] = false;
			$this->required_settings['xmlwriter']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			$this->errors[] = 'xmlwriter not found. . You can proceed with the install, however some functionality will not be available to you';
		} else {
			$this->required_settings['xmlwriter']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
		}
		return $this->success;
		
	}//end processAction()
	
	
	/**
	* Display the result of this check.
	* 
	* @param
	* @return void
	* @access public
	*/
	function paintAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/versioncheck.tpl');
		
		$tpl->set('checking', 'PHP Modules');
		$tpl->set('step_name', $this->parent_step);
		$tpl->set('required', $this->required_settings);
		
		echo $tpl->fetch();
		
	}//end paintAction()
	
	
	/**
	* Show the confirmation of what passed and what failed.
	* 
	* @return void
	* @access public
	*/
	function confirmAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/versioncheck.tpl');
		$tpl->set('step_name', $this->parent_step);
		$tpl->set('checking', 'PHP Modules');
		$tpl->set('required', $this->required_settings);
		
		echo $tpl->fetch();
		
	}//end confirmAction()
	
	
}//end class


/**
* PEARCheckAction
* 
* Check whether all the PEAR versions are up to date and available.
*
*/
class PEARCheckAction extends StepAction
{
	
	/**
	* The required packages for PEAR
	* @var array
	*/
	var $required_packages = Array(
									'MDB2'         	=> Array('version'=>'1.6.2','available'=>false,'message' => ''),
									'Archive_Tar' 	=> Array('version'=>'1.1','available'=>false,'message' => ''),
									'Mail'        	=> Array('version'=>'1.1.3','available'=>false,'message' => ''),
									'Mail_Mime'   	=> Array('version'=>'1.2.1','available'=>false,'message' => ''),
									'XML_HTMLSax' 	=> Array('version'=>'2.1.2','available'=>false,'message' => ''),
									'XML_Parser'  	=> Array('version'=>'1.0.1','available'=>false,'message' => ''),
									'XML_Util'  	=> Array('version'=>'1.5.3','available'=>false,'message' => ''),
									'Config'     	=> Array('version'=>'1.0.0','available'=>false,'message' => ''),
								  );
	
	
	/**
	* Check the pear versions
	* 
	* @return boolean Whether this check succeeded or not.
	* @access public
	*/
	function processAction()
	{
		$_SESSION['pear_path'] = isset($_POST['pear_path']) ? $_POST['pear_path'] : '';
		// check if the pear classes exist
		if ($_SESSION['pear_path'] == '') {
			$this->errors[] = 'Could not find the PEAR registry. You can still proceed with install, '.
								'but you may encounter some errors.';
			
			// go through and manually try and instantiate PEAR classes.
			@include_once 'MDB2.php';
			if (!class_exists('MDB2')) {
				$this->errors[] = 'Could not find PEAR::MDB2';
				$this->required_packages['MDB2']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			} else {
				$this->required_packages['MDB2']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'XML/XML_HTMLSax.php';
			if (!class_exists('XML_HTMLSax')) {
				$this->errors[] = 'Could not find PEAR::XML_HTMLSax';
				$this->required_packages['XML_HTMLSax']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Help"  />';
			} else {
				$this->required_packages['XML_HTMLSax']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'Archive/Tar.php';
			if (!class_exists('Archive_Tar')) {
				$this->errors[] = 'Could not find PEAR::Archive_Tar';
				$this->required_packages['Archive_Tar']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Help"  />';
			} else {
				$this->required_packages['Archive_Tar']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'Mail.php';
			if (!class_exists('Mail')) {
				$this->errors[] = 'Could not find PEAR::Mail';
				$this->required_packages['Mail']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			} else {
				$this->required_packages['Mail']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'Mail/mime.php';
			if (!class_exists('Mail_mime')) {
				$this->errors[] = 'Could not find PEAR::Mail_Mime';
				$this->required_packages['Mail_Mime']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			} else {
				$this->required_packages['Mail_Mime']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'XML/Parser.php';
			if (!class_exists('XML_Parser')) {
				$this->errors[] = 'Could not find PEAR::XML_Parser';
				$this->required_packages['XML_Parser']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			} else {
				$this->required_packages['XML_Parser']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'Config.php';
			if (!class_exists('Config')) {
				$this->errors[] = 'Could not find PEAR::Config';
				$this->required_packages['Config']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			} else {
				$this->required_packages['Config']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
			
			@include_once 'XML/Util.php';
			if (!class_exists('XML_Util')) {
				$this->errors[] = 'Could not find PEAR::XML_Util';
				$this->required_packages['XML_Util']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
			} else {
				$this->required_packages['XML_Util']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
			}
						
		} else { //end if registry not found
			include_once 'PEAR/Registry.php';
			$pear_registry = new PEAR_Registry($_SESSION['pear_path']); 
			
			if ($pear_registry == null) {
				$this->errors[] = 'PEAR Registry could not be found.';
				$this->success = false;
				return false;
			}
			foreach ($this->required_packages as $package_name => $required_version) {
				$package_info = $pear_registry->packageInfo($package_name);
				if (empty($package_info)) {
					$this->errors[] = 'Required PEAR module "'.$package_name.'" is not installed. Please run ';
					$this->errors[] = '<pre>pear install '.$package_name;
					$this->required_packages[$package_name]['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
					$this->success = false;
				} else {
					if (version_compare($required_version['version'], $package_info['version']) < 0) {
						$this->required_packages[$package_name]['available'] = true;
						$this->required_packages[$package_name]['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';
						$this->errors[] = 'Your version of '.$package_name.' meets the requirements, but has '.
						'not been fully tested with 5ive and may cause some undesirable side-effects.';
						
					} elseif (version_compare($required_version['version'], $package_info['version']) == 0) {
						$this->required_packages[$package_name]['available'] = true;
						$this->required_packages[$package_name]['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
					} else {
						$this->required_packages[$package_name]['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
						$this->errors[] = 'Please run <pre>pear upgrade '.$package_name.'</pre>';
						$this->success = false;
					}
				}
			}
		}// end if registry was found
		

		return $this->success;
		
	}//end processAction()
	
	
	/**
	* Display whether the PEAR versions where up to date or not.
	* 
	* @return void
	* @access public
	*/
	function paintAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/versioncheck.tpl');
		
		$tpl->set('step_name', $this->parent_step);
		$tpl->set('required', $this->required_packages);
		$tpl->set('checking', 'PEAR Packages');
		
		echo $tpl->fetch();
									
		$pear_dir  = isset($_SESSION['pear_path']) ? $_SESSION['pear_path'] : $this->_findPEARPath();
		
		$pear_dir = 
		
		$textbox = '<p>Please enter your PEAR path. The installer has guessed that the directory below is '.
					'where PEAR is installed, but it may be in a different directory. '.
					'PEAR by default is installed underneath your PHP directory in /path/to/php/PEAR.</p>'.
					'<p>If the installer cannot find the PEAR registry, set this value blank before clicking '.
					'next, and the installer will try to detect the classes manually. You will then be able '.
					'to continue installing regardless of whether the PEAR classes were found.</p>'.
					'<input type="text" name="pear_path" size="40" value="'.$pear_dir.'" />';
		echo $textbox;
		
	}//end paintAction()
	
	
	/**
	* Show the confirmation of what passed and what failed.
	* 
	* @return void
	* @access public
	*/
	function confirmAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/versioncheck.tpl');
		
		$tpl->set('checking', 'PEAR Packages');
		$tpl->set('step_name', $this->parent_step);
		$tpl->set('required', $this->required_packages);
		
		echo $tpl->fetch();
		
	}//end confirmAction()
	
	
	/**
	* Try and find the PEAR path.
	* 
	* @return string the likely PEAR path.
	* @access private
	*/
	function _findPEARPath()
	{
		$locations = Array('/usr/lib/php/PEAR', 
									'/usr/lib/php', 
									'/usr/local/lib/php',
									'/usr/local/lib/php/PEAR',
									'c:/php/pear',
									'c:/php5/pear'
									);
									
		foreach ($locations as $location) {
			// if the location is a directory, see if there's a PEAR.php file there.
			if(is_dir($location)) {
				$pear_file = $location.'/PEAR.php';
				if(file_exists($pear_file)) {
					return $location;
				}
			}
			
		}
		
		$pear_dir = '';
		// if none of them were found, pass back a guess.
		/*if ((strtolower(PHP_OS) == 'winnt') || (strtolower(PHP_OS) == 'win32')) {
			$pear_dir = 'c:/php/pear';
		} else {
			$pear_dir = '/usr/local/lib/php';
		}*/
		
		return $pear_dir;
		
	}//end _findPEARPath()
	
	
}//end class
?>
