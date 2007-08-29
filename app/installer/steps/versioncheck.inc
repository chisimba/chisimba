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
* @todo Still to add XML_DTD check
* @todo Still to add MDB2_schema check

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
        //Register PHP check actions
        $this->actions[] = new PHPCheckAction(get_class($this));
        //Register PEAR check actions
        $this->actions[] =& new PEARCheckAction(get_class($this));

        $this->step_title = 'System Requirements.';

        $this->title_logo = 'version_check.gif';

        $this->auth_required = false;

        $this->conf_required = true;

        $this->help_topic = 'toc4';

        $this->step_help = "The Installer will check if you have all the required components installed to run Chisimba";



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

    * @var $min_version as string

    */

    public $min_version = '5.1.0';



    /**

    * Holds an array about what requirements are needed.

    * array( setting => Array('version'=>vernum, 'available'=>boolean, 'message' => string) )

    * @var array

    */

    public $required_settings = Array(    'PHP' => Array('version'=>'5.1.0', 'available'=>true, 'message'=> ''),

                                        'GD'=>Array('version'=>'', 'available' => true, 'message'=> ''),

                                        'Zlib'=>Array('version'=>'', 'available' => true, 'message'=> ''),

                                        'calendar'=>Array('version'=>'', 'available' => true, 'message'=> ''),

                                        'curl'=>Array('version'=>'7.10.5', 'available' => true, 'message'=> ''),

                                        'dom'=>Array('version'=>'', 'available' => true, 'message'=> ''),

                                        'libxml'=>Array('version'=>'2.6.0.', 'available' => true, 'message'=> ''),

                                        'mysqlclient'=>Array('version'=>'', 'available' => true, 'message'=> ''),

                                        'pgsql'=>Array('version'=>'', 'available' => true, 'message'=> ''),

                                        'imap'=>Array('version'=>'', 'available' => true, 'message'=> ''),

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

            $this->errors[] = '<span class="errors">You must have GD installed to continue.</span>';

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


        // check calendar

        if (!function_exists('cal_info')) {

            $this->required_settings['calendar']['available'] = false;

            $this->required_settings['calendar']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">calendar not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['calendar']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }



        // check curl

        if (!function_exists('curl_version')) {

            $this->required_settings['curl']['available'] = false;

            $this->required_settings['curl']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">curl not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['curl']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check dom
        $version = null;
        $encoding = null;

        $dom = new DOMDocument($version,$encoding);

        if (!is_object($dom)) {

            $this->required_settings['dom']['available'] = false;

            $this->required_settings['dom']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">dom not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['dom']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }



        // check libxml

        if (!function_exists('libxml_get_errors')) {

            $this->required_settings['libxml']['available'] = false;

            $this->required_settings['libxml']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">libxml not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['libxml']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }


        // check IMAP

        if (!extension_loaded('imap')) {

            $this->required_settings['imap']['available'] = false;

            $this->required_settings['imap']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">imap not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['imap']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check SimpleXML

        if (!extension_loaded('SimpleXML')) {

            $this->required_settings['SimpleXML']['available'] = false;

            $this->required_settings['SimpleXML']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">SimpleXML not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['SimpleXML']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check SOAP

        if (!extension_loaded('SOAP')) {

            $this->required_settings['SOAP']['available'] = false;

            $this->required_settings['SOAP']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">SOAP not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['SOAP']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check xml

        if (!extension_loaded('xml')) {

            $this->required_settings['xml']['available'] = false;

            $this->required_settings['xml']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">xml not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['xml']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check xmlreader

        if (!extension_loaded('xmlreader')) {

            $this->required_settings['xmlreader']['available'] = false;

            $this->required_settings['xmlreader']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">xmlreader not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['xmlreader']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check xmlwriter

        if (!extension_loaded('xmlwriter')) {

            $this->required_settings['xmlwriter']['available'] = false;

            $this->required_settings['xmlwriter']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">xmlwriter not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {

            $this->required_settings['xmlwriter']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }
        
        // check mysqlclient

        $_SESSION['allowed_db'] = Array(
                                    'MDB2_Driver_mysql' => FALSE,
                                    'MDB2_Driver_pgsql' => FALSE,
                                    'MDB2_Driver_mysqli' => FALSE);

        if (!function_exists('mysql_get_client_info')) {

            $this->required_settings['mysqlclient']['available'] = false;

            $this->required_settings['mysqlclient']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">mysqlclient not found.You can proceed with the install, however some functionality will not be available to you</span>';

			 $_SESSION['allowed_db']['MDB2_Driver_mysql'] = FALSE;
			 $_SESSION['allowed_db']['MDB2_Driver_mysqli'] = FALSE;

        } else {

            $client_ver = mysql_get_client_info();

            if(!$client_ver >='4.1.12'){

                $this->required_settings['mysqlclient']['available'] = false;

                $this->required_settings['mysqlclient']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

                $this->errors[] = '<span class="errors">mysqlclient is not meeting System requirements. You need to upgrade this extension</span>';
                $_SESSION['allowed_db']['MDB2_Driver_mysql'] = FALSE;
                $_SESSION['allowed_db']['MDB2_Driver_mysqli'] = FALSE;

            }
            $_SESSION['allowed_db']['MDB2_Driver_mysql'] = TRUE;
            $_SESSION['allowed_db']['MDB2_Driver_mysqli'] = TRUE;
                                    	
            $this->required_settings['mysqlclient']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }

        // check pgsql

        if (!extension_loaded('pgsql')) {

            $this->required_settings['pgsql']['available'] = false;

            $this->required_settings['pgsql']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            $this->errors[] = '<span class="errors">pgsql not found. You can proceed with the install, however some functionality will not be available to you</span>';

        } else {
        	$_SESSION['allowed_db']['MDB2_Driver_pgsql'] = TRUE;
            $this->required_settings['pgsql']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

        }
        
        
    //setup the chisimba PEAR PATH
    $_SESSION['pear_path'] = isset($_POST['pear_path']) ? $_POST['pear_path']: $_SESSION['sys_root']."lib/pear/";
	
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

        'MDB2'             => Array('version'=>'2.0.2','available'=>false,'message' => ''),
        'MDB2_Schema'   => Array('version'=> '0.4.3','available'=>false,'message'=>''),
        'Config'         => Array('version'=>'1.0.0','available'=>false,'message' => ''),
        'Log'         => Array('version'=>'1.0.0','available'=>false,'message' => '')
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

            //$this->errors[] = 'Could not find the PEAR registry. You can still proceed with install, '.

                //                'but you may encounter some errors.';



            // go through and manually try and instantiate PEAR classes.

            include_once 'MDB2.php';

            if (!class_exists('MDB2')) {

                $this->errors[] = '<span class="errors">Could not find PEAR::MDB2 </span>';

                $this->required_packages['MDB2']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
                $this->success = false;

            } else {

                $this->required_packages['MDB2']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';

            }

            @include_once 'MDB2/Schema.php';

            if (!class_exists('MDB2_Schema')) {

                $this->errors[] = '<span class="errors">Could not find PEAR::MDB2_Schema </span>';

                $this->required_packages['MDB2_Schema']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
                 $this->success = false;

            } else {

                $this->required_packages['MDB2_Schema']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';

            }

           
            @include_once 'Config.php';

            if (!class_exists('Config')) {

                $this->errors[] = '<span class="errors">Could not find PEAR::Config </span>';

                $this->required_packages['Config']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';
                $this->success = false;

            } else {

                $this->required_packages['Config']['message'] =  '<img src="./extra/yellow_help_on.png" border="0" alt="Help" title="Help"  />';

            }



            @include_once 'Log';

            if (!class_exists('Log')) {

                $this->errors[] = '<span class="errors">Could not find PEAR::Log </span>';

                $this->required_packages['Log']['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

            } else {

                $this->required_packages['Log']['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

            }

        } else { //end if registry not found

            include_once 'PEAR/Registry.php';
            $pear_registry = new PEAR_Registry($_SESSION['pear_path']);
            //chmod($pear_registry, 0777);


            if ($pear_registry == null) {

                $this->errors[] = '<span class="errors">PEAR Registry could not be found.</span>';

                $this->success = false;

                return false;

            }

            foreach ($this->required_packages as $package_name => $required_version) {

                $package_info = $pear_registry->packageInfo($package_name);

                if (empty($package_info)) {

                    $this->errors[] = '<br> Required PEAR module "'.$package_name.'" is not installed. Please run';

                    $this->errors[] = '<span class="errors"><pre>pear install --alldeps -f '.$package_name.'</pre></span>';

                    $this->required_packages[$package_name]['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

                    $this->success = false;

                } else {

                    if(is_array($package_info['version'])){
                        $package_info['version']=$package_info['version']['release'];
                    }

                    if(is_array($package_info['version']))
                    {
                        $dk = $package_info['version']['api'];

                        if($dk > 0)
                        {
                            $this->required_packages[$package_name]['available'] = true;
                            $this->required_packages[$package_name]['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';
                        }
                    }

                    elseif (version_compare($required_version['version'], $package_info['version']) < 0) {

                        $this->required_packages[$package_name]['available'] = true;

                        $this->required_packages[$package_name]['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

                        $this->errors[] = '<span class="success">Your version of '.$package_name.' meets minimum requirements</span>';



                    } elseif (version_compare($required_version['version'], $package_info['version']) == 0) {

                        $this->required_packages[$package_name]['available'] = true;

                        $this->required_packages[$package_name]['message'] =  '<img src="./extra/ok.png" border="0" alt="OK" title="OK"  />';

                    } else {

                        $this->required_packages[$package_name]['message'] =  '<img src="./extra/failed.png" border="0" alt="Failed" title="Failed"  />';

                        $this->errors[] = '<span class="errors">Please run <pre>pear upgrade '.$package_name.'</pre></span>';

                        $this->success = false;

                    }

                }

            }

        }// end if registry was found



        unset($pear_registry);

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

        $tpl->set('checking', 'PEAR DataBase Abstraction Packages');

        echo $tpl->fetch();


		
        $pear_dir  = isset($_SESSION['pear_path']) ? $_SESSION['pear_path'] : $this->_findPEARPath();

        $textbox = '<br><p></br>' .

                    '<br><p>Please enter your PEAR path. The installer has guessed that the directory below is </br>'.

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



        $tpl->set('checking', 'Database Abstraction Packages');

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

        $locations = Array( '/usr/lib/php/PEAR',

                                    '/usr/lib/php',

                                    '/usr/share/php',

                                    'usr/share/pear',

                                    '/usr/local/lib/php',

                                    '/usr/local/lib/php/PEAR',

                                    'c:/php/pear',

                                    'c:/php4/pear',

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