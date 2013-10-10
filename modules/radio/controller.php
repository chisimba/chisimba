<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
*
* Controller class for Chisimba for the radio module
*
* @author  Prince Mbekwa
* @package radio
*
*/
class radio extends controller
{

    /**
    * @var $objLog String object property for holding the
    *              logger object for logging user activity
    */
    public $objLog;

    /**
     * Object playlist
     * @var    object
     * @access public
     */
    public $playlist;

    /**
     * Obj auth for admin authentication
     * @var    boolean
     * @access public
     */
    public $auth;

    /**
     * Stream object
     * @var    unknown
     * @access public
     */
	public $stream;

    /**
     * Settings object for radio stations
     * @var    object
     * @access public
     */
	public $settings;

    /**
     * Various stations obj
     * @var    object
     * @access public
     */
	public $stations;

    /**
     * Console object
     * @var    object
     * @access public
     */
	public $console;

    /**
     * Statistic object
     * @var    object
     * @access public
     */
	public $stats;

    /**
     * Language Object
     * @var    object
     * @access public
     */
	public $objLanguage;


    /**
     * Intialiser for the controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
    	$this->stream = $this->newObject('stream');
    	$this->playlist = $this->newObject('playlist');
		$this->stations = $this->newObject('stations');
		$this->settings = $this->newObject('settings');
		$this->console = $this->newObject('console');
		$this->stats = $this->newObject('stats');
		$this->objLanguage =  $this->newObject('language', 'language');
	    //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }


    /**
     *
     * The standard dispatch method for chisimba
     *
     */
    public function dispatch()
    {
    	clearstatcache();
    	  $this->setVar('pageSuppressXML', TRUE);
    	$action = $this->getParam('action',NULL);
    	$adminAction = $this->getParam('admin',NULL);
    	$page = $this->getParam('page',null);
    	$this->console->ban_check();
		$this->setVar('pageSuppressXML', TRUE);
		$station = $this->getParam('station');
		$key = $this->getParam('debug');
		if ($station==null) {
			$station = $this->stations->default_s($station);
		}
		$resources = $this->getResourceUri('images/','radio');
		if($action == "home"){
			$page = "home";
		}else{
			$page = $action;
		}

		$this->setVar('page', $page);
	    $this->setVar('extras', $resources);
	    $this->setVar('station', $station);
		$this->setPageTemplate("main_tpl.php");
    	switch ($action) {
    		case 'songlist':
    			$result = $this->uri(array('action'=>'loadlist','station'=>$station),'radio');
    			$this->setVar('songlist',$result);
				return 'main_tpl.php';
    			break;
    		case 'controlpanel':
    			$url = $this->uri(array('action'=>'login'),'radio');
    			$this->setVar('url',$url);
				return "admin_page.php";
    			break;
    		case 'login':
    			if ($_SESSION['id']=='1') {
    				return 'home.php';
    			}else{
    				return 'login.php';
    			}
    			break;
    		case "dologin":
    			$result = $this->_login($action);
    			if ($result) {
    				return "home.php";
    			}else{
    				$message = $this->objLanguage->languageText('mod_radio_error_login','radio');
    				$this->setVar('message',$message);
    				return 'login.php';
    			}
    			break;
    		case 'logout':
    			return 'logout.php';
    			break;
    		case 'loadlist':
    			$data = $this->_loadlist();
    			$this->setVar('data',$data);
    			$result = $this->uri(array('action'=>'loadlist'),'radio');
    			$this->setVar('songlist',$result);
    			 return 'songlist.php';
				 break;
    		case 'playlist':
    			//$this->_controlOps();
    			return 'playlist_admin.php';
    			break;
    		case 'playlistadmin':
    			$this->_controlOps();
    			$url = $this->uri(array('action'=>'playlist'),'radio');
    			$this->setVar('url',$url);
				return "admin_page.php";
    			break;
    		case 'usersadmin':
    			$url = $this->uri(array('action'=>'users'),'radio');
    			$this->setVar('url',$url);
				return "admin_page.php";
    			break;
    		case 'stationsadmin':
    			$url = $this->uri(array('action'=>'stations'),'radio');
    			$this->setVar('url',$url);
				return "admin_page.php";
    			break;
    		case 'addsongs':
    			return "addsongs.php";
    			break;
    		case 'stations':
    			return "stations.php";
    			break;
    		case 'users':
    			return 'users.php';
    			break;
    		case 'play':
    			$this->setPageTemplate('page_tpl.php');
    			return 'playlist.php';
    			break;
    		case 'stream':
    			$this->setPageTemplate('page_tpl.php');
    			return 'stream.php';
				break;
    		case'home':
    			return "main_tpl.php";
    			break;
    		case 'admins':
    			$url = $this->uri(array('action'=>'addAdmin'),'radio');
    			$this->setVar('url',$url);
				return "admin_page.php";
    			break;
    		case 'addAdmin':
    			return 'admins.php';
    			break;
    		default:
    			return "main_tpl.php";
    			break;
    	}


    }

    /**
     * Method to initialize station objects
     *
     * Long description (if any) ...
     *
     * @param  string $station Parameter description Station currently being played
     * @return array Return description information about the playlist of the station
     * @access private
     */
    private function _loadlist(){
    	$station = $this->getParam('station');
    	$station = $this->stations->default_s($station);
    	$playlist_name = $this->playlist->get_playlist_list($station);
    	$settings_data = $this->settings->get($station);
    	$settings_data_temp = explode("&", $settings_data);
    	$header_title = $settings_data_temp[0];
    	$header_genre = $settings_data_temp[1];
    	$header_bitrate = $this->stats->bitrate($station, $playlist_name);
    	if ($header_bitrate == "0" or $header_bitrate == "")
    	{
    		$header_bitrate = $settings_data_temp[2];
    	}
    	$header_site = $settings_data_temp[3];
    	$debugkey = $settings_data_temp[4];
    	$site_temp = explode("/", $_SERVER["PHP_SELF"]);
    	$laast_one = count($site_temp) -1;
    	$between = str_replace($site_temp[$laast_one], "", $_SERVER["PHP_SELF"]);
    	$station_site = "http://".$_SERVER["HTTP_HOST"].$between;
    	$data = explode(";", $this->playlist->get_playlist_info($station,$playlist_name));

    	if (!empty($data)) {
    		return $data;
    	}else{
    		return null;
    	}

    }

    /**
     * Login Method
     *
     * Long description Administer the admin panel
     *
     * @param  string  $action Parameter description evaluate activity in admin panel
     * @return boolean Return description true /false
     * @access private
     */
    private function _login($action){


    	if($action == "dologin")
    	{
    		$station_n = $this->getParam('station_n');
    		$uname = $this->getParam('uname');
    		$password =$this->getParam('password');
    		if($this->stations->login("$station_n","$uname","$password"))
    		{
    			$this->auth = true;
    			if($uname == "admin"){$_SESSION['id'] ="2";}else{
    				$_SESSION['id'] = "1";}
    				$_SESSION['station'] = $station_n;
    				return true;//'home.php';

    		}else{
				$this->auth = false;
    			return false; //'login.php';
    			//echo "Incorrect User Name or Password !";
			}

    	}
    }

    /**
     * Debug method
     *
     * Long description debug for testing
     *
     * @param  string  $key  Parameter description (if any) ...
     * @param  string  $key2 Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
    public function debug($key, $key2)
	{
			if($key != "" && $key2 != "" && $key == $key2)
			{
				return true;
			}else {return false;}
			if (debug($key, $debugkey))
			{
			$debug = true;
			}else {$debug = false;}
	}

    /**
     * Short description for private
     *
     * Long description (if any) ...
     *
     * @return unknown Return description (if any) ...
     * @access private
     */
    private function _controlOps(){

    	if ($_SESSION['id'] != ""){
    		$this->auth==true;
    		return ;
    	}else {
    		$this->nextAction('controlpanel');
    	}



    }

     /**
     * Overides the framework login
     *
     * Long description (if any) ...
     *
     * @return boolean Return description (if any) ...
     * @access public
     */
       public function requiresLogin()
        {
            return FALSE;

        }
}
?>