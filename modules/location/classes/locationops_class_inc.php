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
// end security check

/**
 * Library class to provide easy access to location related functions 
 *
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright GNU/GPL, AVOIR
 * @package   location
 * @access    public
 * @version   $Id: locationops_class_inc.php 11229 2008-11-02 10:36:19Z charlvn $
 */
class locationops extends object
{
    protected $objJson;
    protected $objModules;
    protected $objTwitterLib;
    protected $objSimpleBuildMap;
    protected $objGMapApi;
    protected $objUser;
    protected $objSysConfig;
    protected $feKey;
    protected $feSecret;
    protected $feGeneralToken;
    protected $feGeneralSecret;
    protected $objDbLocation;
    protected $feToken;
    protected $feTokenSecret;
    protected $feUser;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables
     */
    public function init()
    {
        // Load resources
        include $this->getResourcePath('oauth/OAuth.php', 'utilities');
        include $this->getResourcePath('fireeagle/fireeagle.php');

        // Create the JSON object for later use in the Fire Eagle library
        $this->json = $this->getObject('json', 'utilities');

        // Get module catalogue for checking if optional modules exist
        $this->objModules = $this->getObject('modules', 'modulecatalogue');

        // Load the Twitter library if available
        if ($this->objModules->checkIfRegistered('twitter')) {
            $this->objTwitterLib = $this->getObject('twitterlib', 'twitter');
        }

        // Load the simplemap build class if available
        if ($this->objModules->checkIfRegistered('simplemap')) {
            $this->objSimpleBuildMap = $this->getObject('simplebuildmap', 'simplemap');
        }

        // Load the gmaps api class if available
        if ($this->objModules->checkIfRegistered('gmaps')) {
            $this->objGMapApi = $this->getObject('gmapapi', 'gmaps');
        }

        // Load the user object
        $this->objUser = $this->getObject('user', 'security');

        // Read system configuration
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->feKey = $this->objSysConfig->getValue('fireeaglekey', 'location');
        $this->feSecret = $this->objSysConfig->getValue('fireeaglesecret', 'location');
        $this->feGeneralToken = $this->objSysConfig->getValue('fireeagletoken', 'location');
        $this->feGeneralSecret = $this->objSysConfig->getValue('fireeagletokensecret', 'location');

        // Load the database location object for setting and retrieving information about users
        $this->objDbLocation = $this->getObject('dblocation', 'location');

        // Retrieve the Fire Eagle user-specific tokens if a user is currently logged in
        if ($this->objUser->userId()) {
            $this->loadUserConfiguration();
        }
    }

    /**
     * Load user-specific tokens and set object properties accordingly
     *
     * @param string $token The Fire Eagle token of the user
     */
    private function loadUserConfiguration($token=NULL)
    {
        if ($token !== NULL) {
            $this->objDbLocation->loadByFireEagleToken($token);
        }

        $this->feToken = $this->objDbLocation->getFireEagleToken();
        $this->feTokenSecret = $this->objDbLocation->getFireEagleSecret();
    }

    /**
     * Returns the Fire Eagle user location array
     *
     * @return array User location information
     */
    public function getFireEagleUser()
    {
        if ($this->feKey && $this->feSecret && $this->feToken && $this->feTokenSecret && !$this->feUser) {
            $fireeagle = new FireEagle($this->feKey, $this->feSecret, $this->feToken, $this->feTokenSecret, $this->json);
            $this->feUser = $fireeagle->user();
        }
        return $this->feUser;
    }

    /**
     * Retrieves the authorisation URL to initialise the Fire Eagle OAuth handshake
     * and sets some session variables to be used on callback
     *
     * @return string The URL
     */
    public function getFireEagleAuthoriseUrl()
    {
        // Perform an API call to Fire Eagle in order to retrieve the OAuth token
        $fireeagle = new FireEagle($this->feKey, $this->feSecret, NULL, NULL, $this->json);
        $token = $fireeagle->getRequestToken();

        // Set session variables to be used on callback
        $_SESSION['request_token'] = $token['oauth_token'];
        $_SESSION['request_secret'] = $token['oauth_token_secret'];

        // Retrieve the authorise URL
        $url = $fireeagle->getAuthorizeURL($token['oauth_token']);

        return $url;
    }

    /**
     * Handles the Fire Eagle authentication handshake callback
     */
    public function handleFireEagleCallback()
    {
        if ($_GET['oauth_token'] != $_SESSION['request_token']) {
            die('Token mismatch');
        }
        $fireeagle = new FireEagle($this->feKey, $this->feSecret, $_SESSION['request_token'], $_SESSION['request_secret'], $this->json);
        $token = $fireeagle->getAccessToken();
        $this->feToken = $token['oauth_token'];
        $this->feTokenSecret = $token['oauth_token_secret'];
        $this->objDbLocation->setFireEagleToken($token['oauth_token']);
        $this->objDbLocation->setFireEagleSecret($token['oauth_token_secret']);
        $this->objDbLocation->put();
   }

    /**
     * Updates the local database with the latest data from Fire Eagle
     */
    public function update()
    {
        $oldLongitude = $this->objDbLocation->getLongitude();
        $oldLatitude = $this->objDbLocation->getLatitude();
        $oldName = $this->objDbLocation->getName();

        $location = $this->getFireEagleUser();
        $name = $location['user']['location_hierarchy'][0]['name'];
        $longitude = $location['user']['location_hierarchy'][0]['geometry']['coordinates'][0][0][0];
        $latitude = $location['user']['location_hierarchy'][0]['geometry']['coordinates'][0][0][1];

        if ($oldLongitude != $longitude || $oldLatitude != $latitude || $oldName != $name) {
            $this->objDbLocation->setLongitude($longitude);
            $this->objDbLocation->setLatitude($latitude);
            $this->objDbLocation->setName($name);
            $this->objDbLocation->put();
            if ($this->objDbLocation->getTwitter() && $this->objTwitterLib) {
                $this->objTwitterLib->setUid($this->objUser->userName());
                $this->objTwitterLib->updateStatus("Current Location: $name #geo:lat=$latitude #geo:lon=$longitude");
            }
        }
    }

    /**
     * Has the current user already been authenticated?
     * @return boolean
     */
    public function isFireEagleAuthenticated()
    {
        return $this->feToken && $this->feTokenSecret;
    }

    /**
     * Synchronise local database with Fire Eagle across all users
     */
    public function synchroniseFireEagle()
    {
        if ($this->feKey && $this->feSecret) {
            $fireeagle = new FireEagle($this->feKey, $this->feSecret, $this->feGeneralToken, $this->feGeneralSecret, $this->json);
            $recent = $fireeagle->recent();
            if (isset($recent['users']) && is_array($recent['users'])) {
                foreach ($recent['users'] as $user) {
                    $this->loadUserConfiguration($user['token']);
                    $this->update();
                }
            }
        }
    }

    /**
     * Adds all the necessary information to the GMapApi so that a map can be rendered.
     *
     * @param bool $allUsers Add markers for all users or only the current one
     */
    public function setupMap($allUsers=FALSE)
    {
        if ($this->objSimpleBuildMap && $this->objGMapApi) {
            $key = $this->objSimpleBuildMap->getApiKey();
            $this->objGMapApi->setAPIKey($key);

            $headerParams = $this->objGMapApi->getHeaderJS();
            $this->appendArrayVar('headerParams', $headerParams);

            $bodyParams = 'onload="onLoad()" onunload="GUnload()"';
            $this->setVar('bodyParams', $bodyParams);

            $this->objGMapApi->GoogleMapAPI('locationmap');
            $this->setVar('objGMapApi', $this->objGMapApi);

            if ($allUsers) {
                $locations = $this->objDbLocation->getAll();
                foreach ($locations as $location) {
                    $fullname = $this->objUser->fullname($location['userid']);
                    if ($location['longitude'] && $location['latitude']) {
                        $this->objGMapApi->addMarkerByCoords($location['longitude'], $location['latitude'], $fullname);
                    }
                }
            } else {
                $longitude = $this->objDbLocation->getLongitude();
                $latitude = $this->objDbLocation->getLatitude();
                $fullname = $this->objUser->fullname();
                if ($longitude && $latitude) {
                    $this->objGMapApi->addMarkerByCoords($longitude, $latitude, $fullname);
                }
            }
        }
    }

    /**
     * Enable Twitter integration for current user
     */
    public function enableTwitter()
    {
        $this->objDbLocation->setTwitter(TRUE);
        $this->objDbLocation->put();
    }

    /**
     * Disable Twitter integration for current user
     */
    public function disableTwitter()
    {
        $this->objDbLocation->setTwitter(FALSE);
        $this->objDbLocation->put();
    }

    /**
     * Set the location data as template vars
     */
    public function setTemplateVars()
    {
        $longitude = $this->objDbLocation->getLongitude();
        $latitude = $this->objDbLocation->getLatitude();
        $name = $this->objDbLocation->getName();
        $twitter = $this->objDbLocation->getTwitter();
        $this->setVar('locationLongitude', $longitude);
        $this->setVar('locationLatitude', $latitude);
        $this->setVar('locationName', $name);
        $this->setVar('locationTwitter', $twitter);
    }
}

?>
