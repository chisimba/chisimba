<?php

/**
 * Engine object
 *
 * The engine object is the main class of the Chisimba framework. It kicks off all other operations in the
 * framework and controls all of the other classes
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
//increase memory_limit - added 28.07.06 by nappleby
ini_set ( 'memory_limit', '500M' );


/* --------------------------- engine class ------------------------*/

// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * The Object class
 */
require_once 'classes/core/object_class_inc.php';

/**
 * Access (permissions system) class
 */
require_once 'classes/core/access_class_inc.php';

/**
 * database abstraction object
 */
require_once 'classes/core/dbtable_class_inc.php';

/**
 * database management object
 */
require_once 'classes/core/dbtablemanager_class_inc.php';

/**
 * front end controller object
 */
require_once 'classes/core/controller_class_inc.php';

/**
 * log layer
 */
require_once 'lib/logging.php';

/**
 * error handler
 */
require_once 'classes/core/errorhandler_class_inc.php';

/**
 * the exception handler
 */
require_once 'classes/core/customexception_class_inc.php';

/**
 * include the dbdetails file
 */
include ('config/dbdetails_inc.php');

/**
 * set up all the files needed to effectively run lucene
 */
include ('lucene.php');

/**
 * config object
 */
require_once ('Config.php');

/**
 * Error callback
 *
 * function to enable the pear error callback method (global)
 *
 * @param  string $error The error messages
 * @return void
 */
function globalPearErrorCallback($error) {
    log_debug ( $error );
}

/**
 * Engine class
 *
 * Engine class to handle and kick off the Chisimba framework. All transactions go through this class at some stage
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class engine {
    /**
     * Version Number of the software. (engine)
     *
     */
    public $version = '2.0.8';

    /**
     * Template variable
     *
     * @var    string
     * @access public
     */
    public $_templateVars = NULL;

    /**
     * Template reference variable
     *
     * @var    unknown_type
     * @access public
     */
    public $_templateRefs = NULL;

    /**
     * Database abstraction method - can be MDB2 or PDO
     *
     * @var    string
     * @access public
     */
    public $_dbabs;

    /**
     * database object (global)
     *
     * @var    object
     * @access private
     */
    private $_objDb;

    /**
     * database manager object (global)
     *
     * @var    object
     * @access private
     */
    private $_objDbManager;

    /**
     * The User object
     *
     * @access public
     * @var    object
     */
    public $_objUser;

    /**
     * The logged in users object
     *
     * @access public
     * @var    object
     */
    public $_objLoggedInUsers;

    /**
     * The config object (config/* and /modules/config)
     *
     * @access private
     * @var    object
     */
    private $_objConfig;

    /**
     * The language object(s)
     *
     * @access private
     * @var    object
     */
    private $_objLanguage;

    /**
     * The DB config object
     *
     * @access private
     * @var    object
     */
    private $_objDbConfig;

    /**
     * The layout template default
     *
     * @access private
     * @var    string
     */
    private $_layoutTemplate;

    /**
     * The default page template
     *
     * @access private
     * @var    string
     */
    private $_pageTemplate = null;

    /**
     * Has an error been generated?
     *
     * @access private
     * @var    string
     */
    private $_hasError = FALSE;

    /**
     * Where was the error generated?
     *
     * @access private
     * @var    string
     */
    private $_errorField = '';

    /**
     * The page content
     *
     * @access private
     * @var    string
     */
    private $_content = '';

    /**
     * The layout content string
     *
     * @access private
     * @var    string
     */
    private $_layoutContent = '';

    /**
     * The module name currently in use
     *
     * @access private
     * @var    string
     */
    public $_moduleName = NULL;

    /**
     * The currently active controller
     *
     * @access private
     * @var    object
     */
    private $_objActiveController = NULL;

    /**
     * The global error message
     *
     * @access private
     * @var    string
     */
    private $_errorMessage = '';

    /**
     * The messages generated by the classes
     *
     * @access private
     * @var    string
     */
    private $_messages = NULL;

    /**
     * Has the session started?
     *
     * @access private
     * @var    bool
     */
    private $_sessionStarted = FALSE;

    /**
     * Property for cached objects
     *
     * @access private
     * @var    object
     */
    private $_cachedObjects = NULL;

    /**
     * Whether to enable access control
     *
     * @access private
     * @var    object
     */
    private $_enableAccessControl = TRUE;

    /**
     * Configuration Object
     *
     * @var object
     */
    private $_altconfig = null;

    /**
     * DSN - Data Source Name for the database connection object
     *
     * @var string
     */
    protected $dsn = KEWL_DB_DSN;

    /**
     * DSN - Data Source Name for the database connection object
     *
     * @var string
     */
    public $pdsn;

    /**
     * DSN - Data Source Name for the database management object
     *
     * @var string
     */
    protected $mdsn = KEWL_DB_DSN;

    /**
     * Core modules array
     * This is a hardcoded array of the absolute core modules. They cannot be deleted or removed
     * The core modules will live in a directory called core_modules in the app root
     *
     * @var array
     */
    public $coremods = array ('api', 'blocks', 'config', 'context', 'contextadmin', 'contextgroups', 'contextpermissions', 'creativecommons', 'decisiontable', 'errors', 'files', 'filemanager', 'filters', 'groupadmin', 'help', 'htmlelements', 'language', 'logger', 'mail', 'modulecatalogue', 'navigation', 'outputplugins', 'permissions', 'postlogin', 'prelogin', 'redirect', 'packages', 'search', 'security', 'sitemap', 'skin', 'stories', 'storycategoryadmin', 'strings', 'sysconfig', 'systext', 'tagging', 'toolbar', 'tree', 'useradmin', 'userdetails', 'userregistration', 'utilities' );

    public $objMemcache = FALSE;

    public $objAPC = FALSE;

    protected $cacheTTL = 3600;

    protected $luConfig;

    public $eventDispatcher;

    public $lu;
    /**
     * Constructor.
     * For use by application entry point script (usually /index.php)
     *
     * @param  void
     * @return void
     * @access public
     */
    public function __construct() {
        /*
		 * we only initiate session handling here if a session already exists;
		 * the session is only created once a successful login has taken place.
		 * this has the small security benefit (albeit an obscurity based one)
		 * of concealing any information about the session id generator from
		 * unauthenticated users. (see Engine->do_login for session creation)
		 */
        if (isset ( $_REQUEST [session_name ()] )) {
            $this->sessionStart ();
        }
        /*
		 * initialise member objects that *this object* is dependent on, and thus
		 * must be created on every request
		 * the config objects
		 * all configs now live in one place, referencing the config.xml file in the config directory
		 */
        $this->_objDbConfig = $this->getObject ( 'altconfig', 'config' );
        // check for which db abstraction to use - MDB2 or PDO
        $this->_dbabs = $this->_objDbConfig->getenable_dbabs ();
        // check for memcache
        if (extension_loaded ( 'memcache' )) {
            require_once 'classes/core/chisimbacache_class_inc.php';
            if ($this->_objDbConfig->getenable_memcache () == 'TRUE') {
                $this->objMemcache = TRUE;
            } else {
                $this->objMemcache = FALSE;
            }
            $this->cacheTTL = $this->_objDbConfig->getcache_ttl ();
        }

        // check for APC
        if (extension_loaded ( 'apc' )) {
            if ($this->_objDbConfig->getenable_apc () == 'TRUE') {
                $this->objAPC = TRUE;

            } else {
                $this->objAPC = FALSE;
            }
            $this->cacheTTL = $this->_objDbConfig->getcache_ttl ();
        }

        //and we need a general system config too
        $this->_objConfig = clone $this->_objDbConfig;
        ini_set ( 'include_path', ini_get ( 'include_path' ) . PATH_SEPARATOR . $this->_objConfig->getsiteRootPath () . 'lib/pear/' );
        // Grab the LiveUser code now
        require_once ($this->getPearResource ( 'LiveUser.php' ));
        // Grab the LiveUser Admin code
        require_once ($this->getPearResource ( 'LiveUser/Admin.php' ));
        //initialise the event messages framework
        $this->eventDispatcher =& Event_Dispatcher::getInstance();
        //initialise the db factory method of MDB2
        $this->getDbObj ();
        //initialise the db factory method of MDB2_Schema
        $this->getDbManagementObj ();
        // init LiveUser
        $this->getLU ();
        //the user security module
        $this->_objUser = $this->getObject ( 'user', 'security' );
        //the language elements module
        $this->_objLanguage = $this->getObject ( 'language', 'language' );

        //check that the user is logged in and update the login
        if ($this->_objUser->isLoggedIn ()) {
            $this->_objUser->updateLogin ();
        }

        // other fields
        //set the messages array
        $this->_messages = array ();
        //array for the template vars
        $this->_templateVars = array ();
        //the template references
        $this->_templateRefs = array ();
        //bust up the cached objects
        $this->_cachedObjects = array ();

        //Load the Skin Object
        $this->_objSkin = $this->getObject ( 'skin', 'skin' );

        //Get default page template
        $this->_pageTemplate = $this->_objSkin->getPageTemplate ();
        // Get Layout Template from Config files
        $this->_layoutTemplate = $this->_objSkin->getLayoutTemplate ();
    }

    /**
     * This method is for use by the application entry point. It dispatches the
     * request to the appropriate module controller, and then renders the returned template
     * inside of the appropriate layout template.
     *
     * @param  string $presetModuleName default NULL
     * @param  string $presetAction     default NULL
     * @access public
     * @return void
     */
    public function run($presetModuleName = NULL, $presetAction = NULL) {

        if (empty ( $presetModuleName )) {
            $requestedModule = strtolower ( $this->getParam ( 'module', '_default' ) );
        } else {
            $requestedModule = $presetModuleName;
        }
        if (empty ( $presetAction )) {
            $requestedAction = strtolower ( $this->getParam ( 'action', '' ) );
        } else {
            $requestedAction = $presetAction;
        }
        list ( $template, $moduleName ) = $this->_dispatch ( $requestedAction, $requestedModule );
        if ($template != NULL) {
            $this->_content = $this->_callTemplate ( $template, $moduleName, 'content', TRUE );
            if (! empty ( $this->_layoutTemplate )) {
                $this->_layoutContent = $this->_callTemplate ( $this->_layoutTemplate, $moduleName, 'layout', TRUE );
            } else {
                $this->_layoutContent = $this->_content;
            }
            if (! empty ( $this->_pageTemplate )) {
                $this->_callTemplate ( $this->_pageTemplate, $moduleName, 'page' );
            } else {
                echo $this->_layoutContent;
            }
        }
        $this->_finish ();
    }

    /**
     * Method to return the db object. Evaluates lazily,
     * so class file is not included nor object instantiated
     * until needed.
     *
     * @param  void
     * @access public
     * @return kngConfig The config object
     */
    public function getDbObj() {

        global $_globalObjDb;
        /*
		* do the checks that the db object gets instantiated once, then let MDB2 take over for the on-demand * *construction
		*/
        if ($this->_objDb == NULL || $_globalObjDb == NULL) {
            $this->_objDbConfig = $this->getObject ( 'altconfig', 'config' );
            /*
			 * set up the DSN. Some RDBM's do not operate with the string style DSN (most noticeably Oracle)
			 * so we parse the DSN to an array and then send that to the object instantiation to be safe
			 */
            $dsn = KEWL_DB_DSN;
            $this->dsn = $this->parseDSN ( $dsn );
            $this->pdsn = $this->dsn;

            // now check whether to use PDO or MDB2
            if ($this->_dbabs === 'MDB2') {
                // Connect to the database
                require_once ('MDB2.php');
                $_globalObjDb = &MDB2::singleton ( $this->dsn );

                //Check for errors on the factory method
                if (PEAR::isError ( $_globalObjDb )) {
                    $this->_pearErrorCallback ( $_globalObjDb );
                    //return the db object for use globally
                    return $_globalObjDb;
                }
                // a much nicer mode than the default MDB2_FETCHMODE_ORDERED
                $_globalObjDb->setFetchMode ( MDB2_FETCHMODE_ASSOC );
                //set the options for portability!
                $_globalObjDb->setOption ( 'portability', MDB2_PORTABILITY_FIX_CASE | MDB2_PORTABILITY_ALL );

                //Check for errors
                if (PEAR::isError ( $_globalObjDb )) {
                    /*
					* manually call the callback function here, as we haven't had a chance to install it as
					* the error handler
					*/
                    $this->_pearErrorCallback ( $_globalObjDb );
                    //return the db object for use globally
                    return $_globalObjDb;
                }
                // keep a copy as a field as well
                $this->_objDb = $_globalObjDb;

                //Load up some of the extra MDB2 modules:
                MDB2::loadFile ( 'Date' );
                MDB2::loadFile ( 'Iterator' );

                // Load the MDB2 Functions module
                $_globalObjDb->loadModule ( 'Function' );

                // install the error handler with our custom callback on error
                $this->_objDb->setErrorHandling ( PEAR_ERROR_CALLBACK, array ($this, '_pearErrorCallback' ) );
                /* set the default fetch mode for the DB to assoc, as that's a much nicer mode than the default  * MDB2_FETCHMODE_ORDERED
				 */
                $this->_objDb->setFetchMode ( MDB2_FETCHMODE_ASSOC );
                if ($this->_objDb->phptype == 'oci8') {
                    $this->_objDb->setOption ( 'field_case', CASE_LOWER );
                    //oracle numRows() hack plus some extras
                    $this->_objDb->setOption ( 'portability', MDB2_PORTABILITY_NUMROWS | MDB2_PORTABILITY_FIX_CASE | MDB2_PORTABILITY_RTRIM | MDB2_PORTABILITY_ALL );
                } else {
                    $this->_objDb->setOption ( 'portability', MDB2_PORTABILITY_FIX_CASE | MDB2_PORTABILITY_ALL );
                }
                // include the dbtable base class for future use
            } elseif ($this->_dbabs === 'PDO') {
                // PDO stuff
                if (! extension_loaded ( 'PDO' )) {
                    die ( "You must install the PDO extension before trying to use it!" );
                }
                // dsn is in the form of 'mysql:host=localhost;dbname=test', $user, $pass
                if ($this->_objDb === NULL) {
                    try {
                        $this->_objDb = new PDO ( $this->dsn ['phptype'] . ":" . "host=" . $this->dsn ['hostspec'] . ";dbname=" . $this->dsn ['database'], $this->dsn ['username'], $this->dsn ['password'] );
                        $this->_objDb->setAttribute ( PDO::ATTR_EMULATE_PREPARES, true );
                        $this->_objDb->setAttribute ( PDO::ATTR_CASE, PDO::CASE_LOWER );
                        $this->_objDb->setAttribute ( PDO::ATTR_PERSISTENT, true );
                        if ($this->dsn ['phptype'] == 'pgsql') {
                            $this->_objDb->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                        }
                    } catch ( PDOException $e ) {
                        echo $e->getMessage ();
                        exit ();
                    }
                }
            }
            //return the local copy
            return $this->_objDb;
        }

    }

    /**
     * Method to return the db management object. Evaluates lazily,
     * so class file is not included nor object instantiated
     * until needed.
     *
     * @param  void
     * @access public
     * @return kngConfig The config object
     */
    public function getDbManagementObj() {
        //global for the management object
        global $_globalObjDbManager;
        /*
		*do the checks that the db object gets instantiated once, then
		*let MDB2 take over for the on-demand construction
		*/
        if ($this->_objDbManager == NULL || $_globalObjDbManager == NULL) {
            //load the config object (same as the db Object)
            $this->_objDbConfig = $this->getObject ( 'altconfig', 'config' );
            $mdsn = KEWL_DB_DSN; //$this->_objDbConfig->getDsn();
            $this->mdsn = $this->parseDSN ( $mdsn );
            // Connect to the database
            require_once $this->getPearResource ( 'MDB2/Schema.php' );
            //MDB2 has a factory method, so lets use it now...
            $_globalObjDbManager = &MDB2::connect ( $this->dsn );

            //Check for errors
            if (PEAR::isError ( $_globalObjDbManager )) {
                /*
				 * manually call the callback function here,
				 * as we haven't had a chance to install it as
				 * the error handler
				 */
                $this->_pearErrorCallback ( $_globalObjDbManager );
                //return the db object for use globally
                return $_globalObjDbManager;
            }
            // keep a copy as a field as well
            $this->_objDbManager = $_globalObjDbManager;
            // install the error handler with our custom callback on error
            $this->_objDbManager->setErrorHandling ( PEAR_ERROR_CALLBACK, array ($this, '_pearErrorCallback' ) );

        }
        //return the local copy
        return $this->_objDbManager;
    }

    public function getLU() {
        if ($this->lu == NULL || $this->luAdmin == NULL) {
            //global $_lu;
            //global $_luAdmin;


            // get the configs from sysconfig that we will be needing
            $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );

            // get the session configs
            $sessname = $this->objSysConfig->getValue ( 'sess_name', 'security', 'PHPSESSION' );
            $sessvarname = $this->objSysConfig->getValue ( 'sess_varname', 'security', 'chisimba' );

            // login
            $loginforce = $this->objSysConfig->getValue ( 'auth_forcelogins', 'security', true );
            if( $loginforce == 'true' || $loginforce == 'TRUE' || $loginforce == 'True') {
                $loginforce = true;
            }
            else {
                $loginforce = false;
            }
            $logoutdestroy = $this->objSysConfig->getValue ( 'auth_logoutdestroy', 'security', true );
            if( $logoutdestroy == 'true' || $logoutdestroy == 'TRUE' || $logoutdestroy == 'True') {
                $logoutdestroy = true;
            }
            else {
                $logoutdestroy = false;
            }
            // cookies
            $cookiename = $this->objSysConfig->getValue ( 'auth_cookiename', 'security', 'chisimbaLogin' );
            $cookielifetime = $this->objSysConfig->getValue ( 'auth_cookielifetime', 'security', 30 );
            $cookiepath = $this->objSysConfig->getValue ( 'auth_cookiepath', 'security', NULL );
            if( $cookiepath == 'NULL' || $cookiepath == 'Null' || $cookiepath == 'null') {
                $cookiepath = NULL;
            }
            $cookiedomain = $this->objSysConfig->getValue ( 'auth_cookiedomain', 'security', NULL );
            if( $cookiedomain == 'NULL' || $cookiedomain == 'Null' || $cookiedomain == 'null') {
                $cookiedomain = NULL;
            }

            $cookiesecret = $this->objSysConfig->getValue ( 'auth_cookiesecret', 'security', 'test' );
            $cookiesavedir = $this->objSysConfig->getValue ( 'auth_cookiesavedir', 'security', '.' );
            $cookiesecure = $this->objSysConfig->getValue ( 'auth_cookiesecure', 'security', false );
            if( $cookiesecure == 'true' || $cookiesecure == 'TRUE' || $cookiesecure == 'True') {
                $cookiesecure = true;
            }
            else {
                $cookiesecure = false;
            }

            // Auth container(s)
            $authcontype = $this->objSysConfig->getValue ( 'auth_containertype', 'security', 'MDB2' );
            $authcontexptime = $this->objSysConfig->getValue ( 'auth_cont_expiretime', 'security', 3600 );
            $authcontidletime = $this->objSysConfig->getValue ( 'auth_cont_idletime', 'security', 1800 );
            $duphandles = $this->objSysConfig->getValue ( 'auth_allowduphandles', 'security', 0 );
            $emptypw = $this->objSysConfig->getValue ( 'auth_allowemptypw', 'security', 0 );
            $pwenc = $this->objSysConfig->getValue ( 'auth_pwencryption', 'security', 'sha1' );

            $this->luConfig = array (
                'debug' => false,
                'session' => array (
                    'name' => $sessname,
                    'varname' => $sessvarname,
                ),
                'session_cookie_params' => array(
                    'lifetime' => intval($cookielifetime),
                    'path'     => $cookiepath,
                    'domain'   => $cookiedomain,
                    'secure'   => $cookiesecure,
                    'httponly' => false,
                ),
                'login' => array (
                    'force' => $loginforce,
                ),
                'logout' => array (
                    'destroy' => $logoutdestroy,
                ),
                'cookie' => array (
                    'name' => $cookiename,
                    'lifetime' => intval($cookielifetime),
                    'path' => $cookiepath,
                    'domain' => $cookiedomain,
                    'secret' => $cookiesecret,
                    'savedir' => $cookiesavedir,
                    'secure' => $cookiesecure,
                ),
                'authContainers' => array (
                    'database_local' => array (
                        'type' => $authcontype,
                        'expireTime' => intval($authcontexptime),
                        'idleTime' => intval($authcontidletime),
                        'allowDuplicateHandles' => intval($duphandles),
                        'allowEmptyPasswords' => intval($emptypw),
                        'passwordEncryptionMode' => $pwenc,
                        'storage' => array (
                            'dsn' => KEWL_DB_DSN,
                            'prefix' => 'tbl_',
                            'alias' => array (
                                'lastlogin' => 'last_login',
                                'auth_user_id' => 'userId',
                                'is_active' => 'isActive',
                                'handle' => 'username',
                                'owner_user_id' => 'owner_user_id',
                                'owner_group_id' => 'owner_group_id',
                                'passwd' => 'pass'
                            ),
                            'fields' => array (
                                'lastlogin' => 'timestamp',
                                'is_active' => 'text',
                                'owner_user_id' => 'integer',
                                'owner_group_id' => 'integer',
                            ),
                            'tables' => array (
                                'tbl_users' => array (
                                    'fields' => array (
                                        'lastlogin' => false,
                                        'is_active' => false,
                                        'owner_user_id' => false,
                                        'owner_group_id' => false,
                            )
                        )
                    )
                )
            )
        ),
        'permContainer' => array (
            'type' => 'Complex',
            'alias' => array (),
            'storage' => array (
                'MDB2' => array (
                    'connection' => $this->_objDb,
                    'dsn' => KEWL_DB_DSN,
                    'prefix' => 'tbl_perms_',
                    'tables' => array (),
                    'fields' => array (),
                    'alias' => array ()
                )
                // 'force_seq' => false
            )
        )
    );

            $_lu = LiveUser::singleton ( $this->luConfig );
            $_lu->dispatcher->addObserver ( array (&$this, 'authNotification' ) );
            $this->lu = $_lu;
            if (! $_lu->init ()) {
                var_dump ( $_lu->getErrors () );
                die ();
            }
            // and then the admin part
            $this->luAdmin = LiveUser_Admin::factory ( $this->luConfig );
            $this->luAdmin->init ();
            $_luAdmin = $this->luAdmin;

        }
        return;
    }

    public function authNotification(&$notification) {
        if ($notification->getNotificationName () == 'onIdled') {
            log_debug ( "Session timed out..." );
        }
    }

    /**
     * Method to parse the DSN from a string style DSN to an array for portability reasons
     *
     * @access public
     * @param  string $dsn
     * @return void
     * @TODO   get the port settings too!
     */
    public function parseDSN($dsn) {
        $parsed = NULL;
        $arr = NULL;
        if (is_array ( $dsn )) {
            $dsn = array_merge ( $parsed, $dsn );
            return $dsn;
        }
        //find the protocol
        if (($pos = strpos ( $dsn, '://' )) !== false) {
            $str = substr ( $dsn, 0, $pos );
            $dsn = substr ( $dsn, $pos + 3 );
        } else {
            $str = $dsn;
            $dsn = null;
        }
        if (preg_match ( '|^(.+?)\((.*?)\)$|', $str, $arr )) {
            $parsed ['phptype'] = $arr [1];
            $parsed ['phptype'] = ! $arr [2] ? $arr [1] : $arr [2];
        } else {
            $parsed ['phptype'] = $str;
            $parsed ['phptype'] = $str;
        }

        if (! count ( $dsn )) {
            return $parsed;
        }
        // Get (if found): username and password
        if (($at = strrpos ( $dsn, '@' )) !== false) {
            $str = substr ( $dsn, 0, $at );
            $dsn = substr ( $dsn, $at + 1 );
            if (($pos = strpos ( $str, ':' )) !== false) {
                $parsed ['username'] = rawurldecode ( substr ( $str, 0, $pos ) );
                $parsed ['password'] = rawurldecode ( substr ( $str, $pos + 1 ) );
            } else {
                $parsed ['username'] = rawurldecode ( $str );
            }
        }
        //server
        if (($col = strrpos ( $dsn, ':' )) !== false) {
            $strcol = substr ( $dsn, 0, $col );
            $dsn = substr ( $dsn, $col + 1 );
            if (($pos = strpos ( $strcol, '+' )) !== false) {
                $parsed ['hostspec'] = rawurldecode ( substr ( $strcol, 0, $pos ) );
            } else {
                $parsed ['hostspec'] = rawurldecode ( $strcol );
            }
        }
        /*
		 * now we are left with the port and databsource so we can just explode the string
		 * and clobber the arrays together
		 */
        $pm = explode ( "/", $dsn );
        $parsed ['hostspec'] = $pm [0];
        $parsed ['database'] = $pm [1];
        $dsn = NULL;

        $parsed ['hostspec'] = str_replace ( "+", "/", $parsed ['hostspec'] );

        if ($this->objMemcache == TRUE) {
            if (chisimbacache::getMem ()->get ( 'dsn' )) {
                $parsed = chisimbacache::getMem ()->get ( 'dsn' );
                $parsed = unserialize ( $parsed );
                return $parsed;
            } else {
                chisimbacache::getMem ()->set ( 'dsn', serialize ( $parsed ), FALSE, $this->cacheTTL );
                return $parsed;
            }
        }
        return $parsed;
    }

    /**
     * Method to return current page content. For use within layout templates.
     *
     * @access public
     * @param  void
     * @return string Content of rendered content script
     */
    public function getContent() {
        return $this->_content;
    }

    /**
     * Method to return the currently selected layout template name.
     *
     * @access public
     * @param  void
     * @return string Name of layout template
     */
    public function getLayoutTemplate() {
        return $this->_layoutTemplate;
    }

    /**
     * Method to set the name of the layout template to use.
     *
     * @access public
     * @param  string $templateName The name of the layout template to use
     * @return string Name of the layout template
     */
    public function setLayoutTemplate($templateName) {
        $this->_layoutTemplate = $templateName;
    }

    /**
     * Method to return the content of the rendered layout template.
     *
     * @access public
     * @param  void
     * @return string Content of rendered layout script
     */
    public function getLayoutContent() {
        return $this->_layoutContent;
    }

    /**
     * Method to return the currently selected layout template name.
     *
     * @access public
     * @param  void
     * @return string Name of layout template
     */
    public function getPageTemplate() {
        return $this->_pageTemplate;
    }

    /**
     * Method to set the name of the page template to use.
     *
     * @access public
     * @param  string $templateName The name of the page template to use
     * @return string $templateName The name of the page template to use
     */
    public function setPageTemplate($templateName) {
        $this->_pageTemplate = $templateName;
    }

    public function getPatchObject($name, $moduleName = '') {
        $engine = $this;
        $objname = $name . "_installscripts";
        $filename = $this->_objConfig->getModulePath () . $name . "/patches/installscripts_class_inc.php";
        if (file_exists ( $filename )) {
            require_once ($filename);
            if (is_subclass_of ( $objname, 'object' )) {
                // Class inherits from class 'object', so pass it the expected parameters
                $objNew = new $objname ( $this, $objname );
            } else {
                // Class does not inherit from class 'object', so don't pass it any parameters
                $objNew = new $objname ( );
            }
            if (is_null ( $objNew )) {
                throw new customException ( "Could not instantiate patch class $name from module $moduleName " . __FILE__ . __CLASS__ . __FUNCTION__ . __METHOD__ );
            }
            return $objNew;
        } else {
            return NULL;
        }
    }

    /**
     * Method to load a class definition from the given module.
     * Used when you wish to instantiate objects of the class yourself.
     *
     * @access public
     * @param  $name       string The name of the class to load
     * @param  $moduleName string The name of the module to load the class from (optional)
     * @return a           reference to the loaded object in engine ($this)
     */
    public function loadClass($name, $moduleName = '') {
        if ($name == 'config' && $moduleName == 'config' && $this->_objConfig) {
            // special case: skip if config and objConfig exists, this means config
            // class is already loaded using relative path, and an attempt to load with absolute
            // path will fail because the require_once feature matches filenames exactly.
            return;
        }
        if ($name == 'altconfig' && $moduleName == 'config' && $this->_objConfig) {
            // special case: skip if config and objConfig exists, this means config
            // class is already loaded using relative path, and an attempt to load with absolute
            // path will fail because the require_once feature matches filenames exactly.
            return;
        }
        if ($name == 'altconfig' && $moduleName == 'config' && ! $this->_objConfig) {
            $filename = "core_modules/" . $moduleName . "/classes/" . strtolower ( $name ) . "_class_inc.php";
            $engine = $this;
            if (! ($this->_objConfig instanceof altconfig)) {
                require_once ($filename);
                $this->_objConfig = new altconfig ( );
                if ($this->objMemcache == TRUE) {
                    if (chisimbacache::getMem ()->get ( 'altconfig' )) {
                        $this->_objConfig = chisimbacache::getMem ()->get ( 'altconfig' );
                        return $this->_objConfig;
                    } else {
                        require_once ($filename);
                        $this->_objConfig = new altconfig ( );
                        chisimbacache::getMem ()->set ( 'altconfig', $this->_objConfig, MEMCACHE_COMPRESSED, $this->cacheTTL );
                        return $this->_objConfig;
                    }
                } elseif ($this->objAPC == TRUE) {
                    $this->_objConfig = apc_fetch ( 'altconfig' );
                    if ($this->_objConfig == FALSE) {
                        $this->_objConfig = new altconfig ( );
                        apc_store ( 'altconfig', $this->_objConfig, $this->cacheTTL );
                    }
                } else {
                    require_once ($filename);
                    $this->_objConfig = new altconfig ( );
                    return $this->_objConfig;
                }
            } else {
                return;
            }
        }
        if (in_array ( $moduleName, $this->coremods )) {
            $filename = $this->_objConfig->getSiteRootPath () . "core_modules/" . $moduleName . "/classes/" . strtolower ( $name ) . "_class_inc.php";
        } elseif ($moduleName == '_core') {
            $filename = "classes/core/" . strtolower ( $name ) . "_class_inc.php";
        } else {
            $filename = $this->_objConfig->getModulePath () . $moduleName . "/classes/" . strtolower ( $name ) . "_class_inc.php";
        }
        // add the site root path to make an absolute path if the config object has been loaded
        if (! file_exists ( $filename )) {
            if ($this->_objConfig->geterror_reporting () == "developer") {
                if (extension_loaded ( "xdebug" )) {
                    throw new customException ( "Could not load class $name from module $moduleName: filename $filename " );
                } else {
                    throw new customException ( "Could not load class $name from module $moduleName: filename $filename " );
                }

                die ();
            }
            throw new customException ( "Could not load class $name from module $moduleName: filename $filename " );
        }
        $engine = $this;
        $this->__autoload ( $filename );
    }

    public function __autoload($class_name) {
        require_once $class_name;
    }

    /**
     * Method to get a new instance of a class from the given module.
     * Note that this relies on the naming convention for class files
     * being adhered to, e.g. class moduleAdmin should live in file:
     * 'moduleadmin_class_inc.php'.
     * This engine object is offered to the constructor as a parameter
     * when creating a new object although it need not be used.
     *
     * @access public
     * @see    loadclass
     * @param  $name       string The name of the class to load
     * @param  $moduleName string The name of the module to load the class from
     * @return mixed       The object asked for
     */
    public function newObject($name, $moduleName) {
        $this->loadClass ( $name, $moduleName );
        if ($this->objMemcache == TRUE) {
            if (chisimbacache::getMem ()->get ( md5 ( $name ) )) {
                //log_debug("retrieve $name from cache...new object");
                $objNew = chisimbacache::getMem ()->get ( md5 ( $name ) );

                return $objNew;
            } else {
                if (is_subclass_of ( $name, 'object' )) {
                    $objNew = new $name ( $this, $moduleName );
                    return $objNew;
                } else {
                    $objNew = new $name ( );
                    //log_debug("setting newObject $name from cache...");
                    chisimbacache::getMem ()->set ( md5 ( $name ), $objNew, MEMCACHE_COMPRESSED, $this->cacheTTL );
                }
            }
        } elseif ($this->objAPC == TRUE) {
            $objNew = apc_fetch ( $name );
            if ($objNew == FALSE) {
                if (is_subclass_of ( $name, 'object' )) {
                    $objNew = new $name ( $this, $moduleName );
                    return $objNew;
                } else {
                    $objNew = new $name ( );
                    apc_store ( $name, $objNew, $this->cacheTTL );
                }
            }
        } else {
            // Fix to allow developers to load htmlelements which do not inherit from class 'object'
            if (is_subclass_of ( $name, 'object' )) {
                // Class inherits from class 'object', so pass it the expected parameters
                $objNew = new $name ( $this, $moduleName );

            } else {
                // Class does not inherit from class 'object', so don't pass it any parameters
                $objNew = new $name ( );
            }
            if (is_null ( $objNew )) {
                throw new customException ( "Could not instantiate class $name from module $moduleName " . __FILE__ . __CLASS__ . __FUNCTION__ . __METHOD__ );
            }
        }
        return $objNew;
    }

    /**
     * Method to get an instance of a class from the given module.
     * If this is the first call for that class a new instance will be created,
     * otherwise the existing instance will be returned.
     * Note that this relies on the naming convention for class files
     * being adhered to, e.g. class moduleAdmin should live in file:
     * 'moduleadmin_class_inc.php'.
     * This engine object is offered to the constructor as a parameter
     * when creating a new object although it need not be used.
     *
     * @access public
     * @see    loadclass
     * @param  $name       string The name of the class to load
     * @param  $moduleName string The name of the module to load the class from
     * @return mixed       The object asked for
     */
    public function getObject($name, $moduleName) {
        $instance = NULL;
        if (isset ( $this->_cachedObjects [$moduleName] [$name] )) {
            $instance = $this->_cachedObjects [$moduleName] [$name];
        } else {
            $this->loadClass ( $name, $moduleName );
            if (is_subclass_of ( $name, 'object' )) {
                $instance = new $name ( $this, $moduleName );
            } else {
                $instance = new $name ( );
            }
            if (is_null ( $instance )) {
                throw new customException ( "Could not instantiate class $name from module $moduleName " . __FILE__ . __CLASS__ . __FUNCTION__ . __METHOD__ );
            }
            // first check that the map for the given module exists
            if (! isset ( $this->_cachedObjects [$moduleName] )) {
                $this->_cachedObjects [$moduleName] = array ();
            }
            // now store the instance in the map
            $this->_cachedObjects [$moduleName] [$name] = $instance;
        }
        return $instance;
    }

    /**
     * Method to return a template variable. These are used to pass
     * information from module to template.
     *
     * @access public
     * @param  $name    string The name of the variable
     * @param  $default mixed  The value to return if the variable is unset (optional)
     * @return mixed    The value of the variable, or $default if unset
     */
    public function getVar($name, $default = NULL) {
        return isset ( $this->_templateVars [$name] ) ? $this->_templateVars [$name] : $default;
    }

    /**
     * Method to set a template variable. These are used to pass
     * information from module to template.
     *
     * @access public
     * @param  $name  string The name of the variable
     * @param  $val   mixed  The value to set the variable to
     * @return string as associative array of template name
     */
    public function setVar($name, $val) {
        $this->_templateVars [$name] = $val;
    }

    /**
     * Method to return a template reference variable. These are used to pass
     * objects from module to template.
     *
     * @access public
     * @param  $name  string The name of the reference variable
     * @return mixed  The value of the reference variable, or NULL if unset
     */
    public function getVarByRef($name) {
        return isset ( $this->_templateRefs [$name] ) ? $this->_templateRefs [$name] : NULL;
    }

    /**
     * Method to set a template refernce variable. These are used to pass
     * objects from module to template.
     *
     * @access public
     * @param  $name  string The name of the reference variable
     * @param  $ref   mixed  A reference to the object to set the reference variable to
     */
    public function setVarByRef($name, $ref) {
        $this->_templateRefs [$name] = $ref;
    }

    /**
     * Method to append a value to a template variable holding an array. If the
     * array does not exist, it is created
     *
     * @access public
     * @param  string $name  The name of the variable holding an array
     * @param  mixed  $value The value to append to the array
     * @return string as associative array
     */
    public function appendArrayVar($name, $value) {
        if (! isset ( $this->_templateVars [$name] )) {
            $this->_templateVars [$name] = array ();
        }
        if (! is_array ( $this->_templateVars [$name] )) {
            throw new customException ( "Attempt to append to a non-array template variable $name" );
        }
        if (! in_array ( $value, $this->_templateVars [$name] )) {
            $this->_templateVars [$name] [] = $value;
        }
    }

    /**
     * Method to return a request parameter (i.e. a URL query parameter,
     * a form field value or a cookie value).
     *
     * @access public
     * @param  $name    string The name of the parameter
     * @param  $default mixed  The value to return if the parameter is unset (optional)
     * @return mixed    The value of the parameter, or $default if unset
     */
    public function getParam($name, $default = NULL) {
        $result = isset ( $_REQUEST [$name] ) ? is_string ( $_REQUEST [$name] ) ? trim ( $_REQUEST [$name] ) : $_REQUEST [$name] : $default;

        return $this->install_gpc_stripslashes ( $result );
    }

    /**
     * Method to return a request parameter (i.e. a URL query parameter,
     * a form field value or a cookie value).
     *
     * @access public
     * @param  $name    string The name of the parameter
     * @param  $default mixed  The value to return if the parameter is unset (optional)
     * @return mixed    The value of the parameter, or $default if unset
     */
    public function getArrayParam($name, $default = NULL) {
        if ((isset ( $_REQUEST [$name] )) && (is_array ( $_REQUEST [$name] ))) {
            return $_REQUEST [$name];
        } else {
            return $default;
        }
    }

    /**
     * Strips the slashes from a variable if magic quotes is set for GPC
     * Handle normal variables and array
     *
     * @param mixed $var	the var to cleanup
     * @return mixed
     * @access public
     */
    public function install_gpc_stripslashes($var) {
        if (get_magic_quotes_gpc ()) {
            if (is_array ( $var ))
                $this->install_stripslashes_array ( $var, true );
            else
                $var = stripslashes ( $var );
        }
        return $var;
    }

    /**
     * Strips the slashes from an entire associative array
     *
     * @param array		$array			the array to stripslash
     * @param boolean	$strip_keys		whether or not to stripslash the keys as well
     * @return array
     * @access public
     */
    public function install_stripslashes_array($array, $strip_keys = false) {
        if (is_string ( $array ))
            return stripslashes ( $array );
        $keys_to_replace = Array ();
        foreach ( $array as $key => $value ) {
            if (is_string ( $value )) {
                $array [$key] = stripslashes ( $value );
            } elseif (is_array ( $value )) {
                $this->install_stripslashes_array ( $array [$key], $strip_keys );
            }
            if ($strip_keys && $key != ($stripped_key = stripslashes ( $key ))) {
                $keys_to_replace [$key] = $stripped_key;
            }
        }
        // now replace any of the keys that needed strip slashing
        foreach ( $keys_to_replace as $from => $to ) {
            $array [$to] = $array [$from];
            unset ( $array [$from] );
        }
        return $array;
    }

    /**
     * Method to return a session value.
     *
     * @access public
     * @param  $name    string The name of the session value
     * @param  $default mixed  The value to return if the session value is unset (optional)
     * @return mixed    the value of the parameter, or $default if unset
     */
    public function getSession($name, $default = NULL) {
        $val = $default;
        if (isset ( $_SESSION [$name] )) {
            $val = $_SESSION [$name];
        }
        return $val;
    }

    /**
     * Method to set a session value.
     *
     * @access public
     * @param  $name  string The name of the session value
     * @param  $val   mixed  The value to set the session value to
     * @return void
     */
    public function setSession($name, $val) {
        if (! $this->_sessionStarted) {
            $this->sessionStart ();
        }
        $_SESSION [$name] = $val;
    }

    /**
     * Method to unset a session parameter.
     *
     * @access public
     * @param  $name  string The name of the session parameter
     * @return void
     */
    public function unsetSession($name) {
        unset ( $_SESSION [$name] );
    }

    /**
     * Method to set the global error message, and an error field if appropriate
     *
     * @access public
     * @param  $errormsg string The error message
     * @param  $field    string The name of the field the error applies to (optional)
     * @return FALSE
     */
    public function setErrorMessage($errormsg, $field = NULL) {
        if (! $this->_hasError) {
            $this->_errorMessage = $errormsg;
            $this->_hasError = TRUE;
        }
        if ($field) {
            $this->_errorField = $field;
        }
        // error return code if needed by caller
        return FALSE;
    }

    /**
     * Method to add a global system message.
     *
     * @access public
     * @param  $msg   string The message
     * @return string the message
     */
    public function addMessage($msg) {
        $this->_messages [] = $msg;
    }

    /**
     * Method to call a further action within a module
     *
     * @access public
     * @param  string $action Action to perform next
     * @param  array  $params Parameters to pass to action
     * @return string template
     */
    public function nextAction($action, $params = array()) {
        list ( $template, $_ ) = $this->_dispatch ( $action, $this->_moduleName );
        return $template;
    }

    /**
     * Method to return an application URI. All URIs pointing at the application
     * must be generated by this method. It is recommended that an action parameter
     * is used to indicate the action being performed.
     * The $mode parameter allows the use of a push/pop mechanism for storing
     * user context for return later. **This needs more work, both implementation
     * and documentation **
     *
     * @access  public
     * @param   array  $params         Associative array of parameter values
     * @param   string $module         Name of module to point to (blank for core actions)
     * @param   string $mode           The URI mode to use, must be one of 'push', 'pop', or 'preserve'
     * @param   string $omitServerName flag to produce relative URLs
     * @param   bool   $javascriptCompatibility flag to produce javascript compatible URLs
     * @returns string $uri the URL
     */
    public function uri($params = array(), $module = '', $mode = '', $omitServerName = FALSE, $javascriptCompatibility = FALSE, $Strict = FALSE) {
        if (! empty ( $action )) {
            $params ['action'] = $action;
        }
        if ($omitServerName) {
            $uri = $_SERVER ['PHP_SELF'];
        } else {
            $uri = "http://" . $_SERVER ['HTTP_HOST'] . $_SERVER ['PHP_SELF'];
        }
        if ($mode == 'push' && $this->getParam ( '_pushed_action' )) {
            $mode = 'preserve';
        }
        if ($mode == 'pop') {
            $params ['module'] = $this->getParam ( '_pushed_module', '' );
            $params ['action'] = $this->getParam ( '_pushed_action', '' );
        }
        if (in_array ( $mode, array ('push', 'pop', 'preserve' ) )) {
            $excluded = array ('action', 'module' );
            if ($mode == 'pop') {
                $excluded [] = '_pushed_action';
                $excluded [] = '_pushed_module';
            }
            foreach ( $_GET as $key => $value ) {
                //echo "using GET";
                if (! isset ( $params [$key] ) && ! in_array ( $key, $excluded )) {
                    $params [$key] = $value;
                }
            }
            if ($mode == 'push') {
                $params ['_pushed_module'] = $this->_moduleName;
                $params ['_pushed_action'] = $this->_action;
            }
        } elseif ($mode != '') {
            throw new customException ( "Incorrect URI mode in Engine::uri" );
        }
        if (count ( $params ) > 1) {
            $params = array_reverse ( $params, TRUE );
        }
        $params ['module'] = $module;
        $params = array_reverse ( $params, TRUE );
        if (! empty ( $params )) {
            $output = array ();

            foreach ( $params as $key => $item ) {
                if (! is_null ( $item )) {
                    $output [] = urlencode ( $key ) . "=" . urlencode ( $item );
                }
            }
            $uri .= '?' . implode ( $javascriptCompatibility ? ($Strict ? '&' : '&#38;') : '&amp;', $output );
        }
        return $uri;
    }

    /**
     * Method to generate a URI to a static resource stored in a module.
     * The resource should be stored within the 'resources' subdirectory of
     * the module directory.
     *
     * @access public
     * @param  string $resourceFile The path to the file within the resources
     *                              subdirectory of the module
     * @param  string $moduleName   The name of the module the resource belongs to
     * @return string URI to a resource in the module
     */
    public function getResourceUri($resourceFile, $moduleName) {
        if (in_array ( $moduleName, $this->coremods )) {
            return "core_modules/" . $moduleName . "/resources/" . $resourceFile;
        }
        $moduleURI = $this->_objConfig->getModuleURI () . "/$moduleName/resources/$resourceFile";
        // Convert back slashes to forward slashes.
        $moduleURI = preg_replace ( '/\\\\/', '/', $moduleURI );
        // Replace multiple instances of forward slashes with single ones.
        $moduleURI = preg_replace ( '/\/+/', '/', $moduleURI );
        return $moduleURI;
    }

    /**
     * Method to generate a path to a static resource stored in a module.
     * The resource should be stored within the 'resources' subdirectory of
     * the module directory.
     *
     * @access public
     * @param  string $resourceFile The path to the file within the resources
     *                              subdirectory of the module
     * @param  string $moduleName   The name of the module the resource belongs to
     * @return string Path to the Resource in a module
     */
    public function getResourcePath($resourceFile, $moduleName) {
        if (in_array ( $moduleName, $this->coremods )) {
            return $this->_objConfig->getsiteRootPath () . "core_modules/" . $moduleName . "/resources/" . $resourceFile;
        }
        return $this->_objConfig->getModulePath () . $moduleName . "/resources/" . $resourceFile;
    }

    /**
     * Method to generate a path to a static resource stored in a module.
     * The resource should be stored within the 'resources' subdirectory of
     * the module directory.
     *
     * @access public
     * @param  string $resourceFile The path to the file within the resources
     *                              subdirectory of the module
     * @return string Path to the Resource in a module
     */
    public function getPearResource($resourceFile) {
        if (@include_once ($resourceFile)) {
            return $resourceFile;
        } else {
            return $this->_objConfig->getsiteRootPath () . "lib/pear/" . $resourceFile;
        }

    }

    /**
     * Method that generates a URI to a static javascript
     * file that is stored in the resources folder in the subdirectory
     * in the modules directory
     *
     * @access public
     * @param  string $javascriptFile The javascript file name
     * @param  string $moduleName     The name of the module that the script is in
     * @return string Javascript headers
     */
    public function getJavascriptFile($javascriptFile, $moduleName) {
        return '<script type="text/javascript" src="' . $this->getResourceUri ( $javascriptFile, $moduleName ) . '"></script>';
    }

    /**
     * Method to output javascript that will display system error message and/or
     * system messages as set by setErrorMessage and addMessage
     *
     * @access public
     * @param  void
     * @return string
     */
    public function putMessages() {
        $str = '';
        if ($this->_hasError) {
            $str .= '<script language="JavaScript" type="text/javascript">' . 'alert("' . $this->javascript_escape ( $this->_errorMessage ) . '");' . '</script>';
        }
        if (is_array ( $this->_messages )) {
            foreach ( $this->_messages as $msg ) {
                $str .= '<script language="JavaScript" type="text/javascript">' . 'alert("' . $this->javascript_escape ( $msg ) . '");' . '</script>';
            }
        }
        echo $str;
    }

    /**
     * Method to find the given template, either in the given module's template
     * subdir (if a module is specified) or in the core templates subdir.
     * Type must be 'content' or 'layout'
     *
     * @access public
     * @param  $tpl        string The name of the template to find,
     *                            including file extension but excluding path
     * @param  $moduleName string The name of the module to search (can be empty to search only core)
     * @param  $type       string The type of template to load: 'content' or 'layout' are current options
     * @return string      The full path to the found template
     */
    public function _findTemplate($tpl, $moduleName, $type) {
        $path = '';
        if (! empty ( $moduleName )) {
            if (in_array ( $moduleName, $this->coremods )) {
                $path = "core_modules/" . "${moduleName}/templates/${type}/${tpl}";
            } else {
                $path = $this->_objConfig->getModulePath () . "${moduleName}/templates/${type}/${tpl}";
            }
        }
        if (empty ( $path ) || ! file_exists ( $path )) {
            $firstpath = $path;
            $path = $this->_objSkin->getTemplate ( $type );
            if (! file_exists ( $path )) {
                throw new customException ( "Template $tpl not found (looked in $firstpath)!" );
            }
        }
        return $path;
    }

    /**
     * Method to start the session
     *
     * @access public
     * @param  void
     * @return set    property to true
     */
    public function sessionStart() {
        //session_start();
        $this->_sessionStarted = TRUE;
    }

    /**
     * Method to instantiate the pear error handler callback
     *
     * @access public
     * @param  string $error
     * @return void   (die)
     */
    public function _pearErrorCallback($error) {

        $msg = $error->getMessage () . ': ' . $error->getUserinfo ();
        $errConfig = $this->_objConfig->geterror_reporting ();
        if ($errConfig == "developer") {
            $usermsg = $msg;
            $this->setErrorMessage ( $usermsg );
            echo $this->putMessages ();
            die ();
        } else {
            $usermsg = $error->getMessage ();
        }
        log_debug ( __LINE__ . "  " . $msg );
        $messages = array ($usermsg, $msg );

        return customException::dbDeath ( $messages );
    }

    /**
     * Method that escapes a string suitable for inclusion as a JavaScript
     * string literal. Add's backslashes for
     *
     * @access public
     * @param  $str   string String to escape
     * @return string Escaped string
     */
    public function javascript_escape($str) {
        return addcslashes ( $str, "\0..\37\"\'\177..\377" );
    }

    /*
	 * Private methods to implement module dispatch and templating
	 */

    /**
     * Main dispatch method. Called by run to dispatch this request
     * to the appropriate module, as specified by the 'module'
     * request parameter.
     *
     * @access private
     * @param  string       $action
     * @param  string       $requestedModule
     * @return list(string, string) Template name and module name
     */
    private function _dispatch($action, $requestedModule) {
        $this->_action = $action;
        strtolower ( $this->getParam ( 'action', '' ) );
        if (! $this->_loadModule ( $requestedModule )) {
            $this->_loadModule ( '_default' );
            if (! $this->_objActiveController) {
                throw new customException ( "Default module not found!" );
            }
            $this->setErrorMessage ( "Module {$requestedModule} not found" );
        }
        // ensure no caching
        if ($this->_objActiveController->sendNoCacheHeaders ( $this->_action )) {
            // Date in the past
            header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
            // always modified
            header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
            // HTTP/1.1
            header ( "Cache-Control: no-store, no-cache, must-revalidate" );
            header ( "Cache-Control: post-check=0, pre-check=0", false );
            // HTTP/1.0
            header ( "Pragma: no-cache" );
        }
        /*
		 * TODO:note $error->getMessage() returns a shorter and friendlier but
		 *      less informative message, for production should use getMessage
		 * TODO:note 2: Appending the getUserinfo method from the PEAR
		 *      error stack will give you the same detail as toString()
		 *      but it will look decent and not confuse the crap out of users
		 *      that being said, we should still go for just getMessage() in prod
		 */
        if ((! $this->_objActiveController->requiresLogin ( $this->_action )) || ($this->_objUser->isLoggedIn ())) {
            return array ($this->_dispatchToModule ( $this->_objActiveController, $this->_action ), $this->_moduleName );
        } else {
            if (! $this->_loadModule ( 'security' )) {
                throw new customException ( "Security module not found!" );
            }
            $this->_moduleName = 'security';
            return array ($this->_dispatchToModule ( $this->_objActiveController, 'showlogin' ), $this->_moduleName );
        }
    }

    /**
     * Method to load a module controller class and create a new
     * object of that class.
     * TODO: make main module an actual module, and if no module requested,
     * load that module (should be a configurable name)
     *
     * @access private
     * @param  $moduleName         string The name of the module to load
     * @return controller-subclass The new module controller object
     */
    private function _loadModule($moduleName) {
        $moduleName = str_replace ( "/", "", $moduleName );
        if ($moduleName == '_default') {
            if ($this->_objUser->isLoggedIn ()) {
                $moduleName = $this->_objConfig->getdefaultModuleName ();
            } else {
                $moduleName = $this->_objConfig->getPrelogin ();
            }
        }

        if (in_array ( $moduleName, $this->coremods )) {
            $controllerFile = "core_modules/" . $moduleName . "/controller.php";
        } else {
            $controllerFile = $this->_objConfig->getModulePath () . $moduleName . "/controller.php";
            //$controllerFile = "modules/" . $moduleName . "/controller.php";
        }
        $objActiveController = NULL;
        if (file_exists ( $controllerFile ) && include_once $controllerFile) {
            $this->_objActiveController = new $moduleName ( $this, $moduleName );
        }
        if ($this->_objActiveController) {
            $this->_moduleName = $moduleName;
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Method to dispatch request to given module, providing given action.
     * If no module object is provided, the main module is dispatched to.
     * TODO: eliminate main handling here when main becomes a module.
     *       Can probably eliminate this method altogether at that point.
     *
     * @access private
     * @param  $objActiveController controller-subclass The module controller to
     *                                                  dispatch to (or NULL for main)
     * @param  $action              string              The action parameter
     * @return string               Template name returned from dispatch method
     */
    private function _dispatchToModule($module, $action) {
        if ($module) {
            $tpl = $this->_enableAccessControl ? // with module access control
            $module->dispatchControl ( $module, $action ) : // without module access control
            $module->dispatch ( $action );
            return $tpl;
        } else {
            return $this->_getLoggedInTemplate ();
        }
    }

    /**
     * Method to call the given template, looking first at the given modules templates
     * and then at the core templates (uses _findTemplate).
     * Output is either buffered ($buffer = TRUE) and returned as a string, or send directly
     * to browser.
     *
     * @access private
     * @param  $tpl        string     Name of template to call, including file extension but excluding path
     * @param  $moduleName string     The name of the module to search for the template
     *                                (if empty, search core)
     * @param  $type       string     The type of template to call: 'content' or 'layout'
     * @param  $buffer     TRUE|FALSE If TRUE buffer output and return as string, else send to browser
     * @return string|NULL If buffering returns output, else returns NULL
     */
    private function _callTemplate($tpl, $moduleName, $type, $buffer = FALSE) {
        return $this->_objActiveController->callTemplate ( $tpl, $type, $buffer );
    }

    /**
     * Method to clean up at end of page rendering.
     *
     * @access private
     * @param  void
     * @return __destruct object db
     */
    private function _finish() {
        if ($this->_dbabs === 'MDB2') {
            $this->_objDb->disconnect ();
        } elseif ($this->_dbabs === 'PDO') {
            $this->_objDb = NULL;
        }
    }

    public function __destruct() {
        if ($this->_dbabs === 'MDB2') {
            $this->_objDb->disconnect ();
        } elseif ($this->_dbabs === 'PDO') {
            $this->_objDb = NULL;
        }
    }
}
?>