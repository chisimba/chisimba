<?php

require_once("OAuth.php");

define("OAUTH_PARAMS_IN_HEADERS", "HEADERS");
define("OAUTH_PARAMS_IN_POST_BODY", "POSTBODY");
define("OAUTH_SIGNATURE_PLAINTEXT", "PLAINTEXT");
define("OAUTH_SIGNATURE_HMAC_SHA1", "HMAC_SHA1");

define("YAHOO_YAP_SESSION_TYPE", "YAHOO_YAP_SESSION_TYPE");
define("YAHOO_OAUTH_RT_SESSION_TYPE", "YAHOO_OAUTH_RT_SESSION_TYPE");
define("YAHOO_OAUTH_AT_SESSION_TYPE", "YAHOO_OAUTH_AT_SESSION_TYPE");

define("SOCIAL_WS_HOSTNAME", "social.yahooapis.com");
define("PRESENCE_WS_HOSTNAME", "social.yahooapis.com");
define("UPDATES_WS_HOSTNAME", "social.yahooapis.com");
define("QUERY_WS_HOSTNAME", "query.yahooapis.com");
define("OAUTH_HOSTNAME", "api.login.yahoo.com");
define("YAP_WS_HOSTNAME", "appstore.apps.yahooapis.com");

$GLOBAL_YAHOO_SESSION = NULL;

$GLOBAL_YAHOO_LOGGER_DEBUG = false;
$GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION = "LOG";

/**
 * Logging wrapper for the Yahoo objects.
 *
 * @brief Logging wrapper for the Yahoo objects.
 */
class YahooLogger {
    /**
     * Log a message at the debug level.
     *
     * @param $message The message to log.
     */
    function debug($message, $object = NULL) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG;
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($GLOBAL_YAHOO_LOGGER_DEBUG) {
            if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "CONSOLE") {
                print("DEBUG - $message\n");
                if(!is_null($object)) {
                    print("DEBUG OBJECT - " . print_r($object, true) . "\n");
                }
            }
            else if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "LOG") {
                error_log("DEBUG - $message");
                if(!is_null($object)) {
                    error_log("DEBUG OBJECT - " . print_r($object, true));
                }
            }
        }
    }

    /**
     * Log a message at the info level.
     *
     * @param $message The message to log.
     */
    function info($message, $object = NULL) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "CONSOLE") {
            print("INFO - $message\n");
            if(!is_null($object)) {
                print("INFO OBJECT - " . print_r($object, true) . "\n");
            }
        }
        else if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "LOG") {
            error_log("INFO - $message");
            if(!is_null($object)) {
                error_log("INFO OBJECT - " . print_r($object, true));
            }
        }
    }

    /**
     * Log a message at the error level.
     *
     * @param $message The message to log.
     */
    function error($message, $object = NULL) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "CONSOLE") {
            print("ERROR - $message\n");
            if(!is_null($object)) {
                print("ERROR OBJECT - " . print_r($object, true) . "\n");
            }
        }
        else if($GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION == "LOG") {
            error_log("ERROR - $message");
            if(!is_null($object)) {
                error_log("ERROR OBJECT - " . print_r($object, true));
            }
        }
    }

    /**
     * Enables/disables session debugging.
     *
     * @param $debug Boolean to enable/disable debugging.
     */
    function setDebug($debug) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG;
        $GLOBAL_YAHOO_LOGGER_DEBUG = (bool) $debug;
    }

    /**
     * Allows callers to configure where debugging output is sent.
     *
     * @param $destination "LOG" to use error_log, "CONSOLE" to use printf, 
     *                     "NULL" to disable all logging output.
     * @return boolean True on success, false on failure.
     */
    function setDebugDestination($destination) {
        global $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION;
        if($destination == "LOG" || $destination == "CONSOLE" || 
                $destination == "NULL") {
            $GLOBAL_YAHOO_LOGGER_DEBUG_DESTINATION = $destination;
            return true;
        }
        else {
            return false;
        }
    }
}

/**
 * Represents a Yahoo! application.
 *
 * @brief Represents a Yahoo! application.
 */
class YahooApplication {
    /**
     * @private
     */
    var $consumer = NULL;

    /**
     * @private
     */
    var $client = NULL;

    /**
     * Constructs a new YahooApplication object.
     *
     * @param $consumerKey The consumer key of the application.
     * @param $consumerKeySecret The consumer key secret of the application.
     */
    function YahooApplication($consumerKey, $consumerKeySecret) {
        $this->consumer = new OAuthConsumer($consumerKey, $consumerKeySecret);
        $this->client = new OAuthClient($this->consumer);
    }

    /**
     * Sets the small view for the user given by the GUID.
     *
     * @param $guid The GUID of the user to set the small view for.
     * @param $content The content to set the small view to.
     * @return True on success, false otherwise.
     */
    function setSmallView($guid, $content) {
        $url = sprintf("http://%s/v1/cache/view/small/%s", YAP_WS_HOSTNAME, 
                urlencode($guid));
        $response = $this->client->put($url, "text/html;charset=utf-8", 
                $content);

        return !is_null($response) && ($response["code"] == 200);
    }

    /**
     * Executes the given YQL query.
     *
     * @param $yql The query to execute.
     * @return The response or NULL if the request fails..
     */
    function query($yql) {
        $response = $this->client->get(
            sprintf("http://%s/v1/yql",
            QUERY_WS_HOSTNAME),
            array('q' => $yql, 'format' => 'json'), 30);

        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $resultSet = json_decode($response["responseBody"]);

        return $resultSet;
    }
}

/**
 * Defines a session between an application and the Yahoo! platform.
 *
 * @brief Defines a session between an application and the Yahoo! platform.
 */
class YahooSession {
    /**
     * @private
     */
    var $guid = NULL;

    /**
     * @private
     */
    var $consumer = NULL;

    /**
     * @private
     */
    var $accessToken = NULL;

    /**
     * @private
     */
    var $applicationId = NULL;

    /**
     * @private
     */
    var $client = NULL;

    /**
     * @private
     */
    var $application = NULL;

    /**
     * @private
     */
    function YahooSession($consumer, $accessToken, $applicationId) {
        $this->consumer = $consumer;
        $this->accessToken = $accessToken;
        $this->applicationId = $applicationId;
        $this->guid = $accessToken->guid;

        $this->client = new OAuthClient($consumer, $accessToken);
        $this->application = new YahooApplication($consumer->key, 
                $consumer->secret);
    }

    /**
     * @private
     */
    function getConsumer() {
        return $this->consumer;
    }

    /**
     * @private
     */
    function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * @private
     */
    function getApplicationId() {
        return $this->applicationId;
    }

    /**
     * Gets the currently sessioned user.
     *
     * @return YahooUser The currently sessioned YahooUser.
     */
    function getSessionedUser() {
        return new YahooUser($this, $this->guid, true);
    }

    /**
     * Gets the user who owns the application install.
     * Only valid when viewed in YAP, otherwise will default
     * to the logged-in user.
     *
     * @return YahooUser The currently sessioned YahooUser.
     */
    function getOwner() {
        if(isset($this->accessToken->owner)) {
            return $this->getUser($this->accessToken->owner);
        }
        else {
            return $this->getSessionedUser();
        }
    }

    /**
     * Gets the user indicated by the GUID given.
     *
     * @param $guid The GUID of the user to get.
     * @return YahooUser The user indicated by the GUID given.
     */
    function getUser($guid) {
        return new YahooUser($this, $guid, isset($this->guid) && ($guid == $this->guid));
    }

    /**
     * @private
     */
    function redirectForAuthorization($consumerKey, $consumerSecret, 
            $callback = NULL) {
        global $GLOBAL_YAHOO_SESSION;
        // Redirect the user to log in.
        $requestToken = YahooAuthentication::createRequestToken(
                $consumerKey, $consumerSecret);
        if(!is_null($requestToken)) {
            setcookie("yosdk_rt", 
                    base64_encode(json_encode($requestToken)),
                    time() + 600);

            if(is_null($callback)) {
                // No callback URL supplied. Build one from the current 
                // URL.
                $callback = sprintf("http://%s%s", 
                        $_SERVER["HTTP_HOST"],
                        $_SERVER["REQUEST_URI"]);
            }

            $authorizationUrl = YahooAuthentication::createAuthorizationUrl(
                    $requestToken, $callback);

            header("Location: $authorizationUrl");
            exit();
        }
        else {
            YahooLogger::error("Failed to create request token");
            $GLOBAL_YAHOO_SESSION = NULL;
        }
    }

    /**
     * Destroys the current session, effectively logging out the current
     * user.
     */
    function clearSession() {
        global $GLOBAL_YAHOO_SESSION;
        setcookie("yosdk_rt", "", time() - 600);
        setcookie("yosdk_at", "", time() - 600);
        $GLOBAL_YAHOO_SESSION = NULL;
    }

    /**
     * Checks to see if there is a session in this PHP page request. 
     * Doesn't cause any redirects for the user to log in, for that 
     * you should call requireSession().
     */
    function hasSession($consumerKey, $consumerSecret, $applicationId = NULL) {
        $session = YahooSession::initSession($consumerKey, $consumerSecret, 
                $applicationId, FALSE, NULL);
        return !is_null($session);
    }

    /**
     * Requires that there be a session in this PHP page request. Generates 
     * a redirect for the user to log in, if necessary. You must call 
     * requireSession() before any data is sent back to the user in order 
     * for the redirect to work.
     *
     * @param $consumerKey The OAuth consumer key.
     * @param $consumerSecret The OAuth consumer key secret.
     * @param $applicationId The application ID, optional.
     * @param $callback The callback URL to redirect the user to after 
     *                  they verify the application access.
     * @return YahooSession The current session or NULL if a session cannot 
     *                      be established.
     */
    function requireSession($consumerKey, $consumerSecret, 
            $applicationId = NULL, $callback = NULL) {
        return YahooSession::initSession($consumerKey, $consumerSecret,
                $applicationId, TRUE, $callback);
    }

    /**
     * @private
     */
    function initSession($consumerKey, $consumerSecret, $applicationId, 
            $redirect, $callback) {
        global $GLOBAL_YAHOO_SESSION;
        if(!is_null($GLOBAL_YAHOO_SESSION)) {
            return $GLOBAL_YAHOO_SESSION;
        }

        $consumer = new stdclass();
        $consumer->key = $consumerKey;
        $consumer->secret = $consumerSecret;

        $checkSession = YahooSession::checkSession($type);
        if(!$checkSession) {
            // There doesn't appear to be a session here.
            if($redirect) {
                $GLOBAL_YAHOO_SESSION = NULL;
                YahooSession::redirectForAuthorization($consumerKey, $consumerSecret, $callback);
            }
            else {
                // Don't redirect the user, just inform the caller that 
                // no session is present.
                $GLOBAL_YAHOO_SESSION = NULL;
            }
        }
        else if($type == YAHOO_YAP_SESSION_TYPE) {
            // Found a YAP session.
            if ($consumer->key != $_POST["yap_consumer_key"]) {
                error_log("Consumer key from YAP does not match config.");
                $GLOBAL_YAHOO_SESSION = NULL;
                return;
            }

            if (!YahooSession::checkSignature($consumer, $_REQUEST['oauth_signature'])) {
                error_log("Signature from YAP failed.");
                $GLOBAL_YAHOO_SESSION = NULL;
                return;
            }

            $accessToken = new stdclass();
            $accessToken->key = $_POST["yap_viewer_access_token"];
            $accessToken->secret = $_POST["yap_viewer_access_token_secret"];
            $accessToken->guid = $_POST["yap_viewer_guid"];
            $accessToken->owner = $_POST["yap_owner_guid"];
            $accessToken->tokenExpires = -1;
            YahooLogger::debug("YAP AT: " . $accessToken->key . "   ATS: " . 
                    $accessToken->secret);
            $applicationId = $_POST["yap_appid"];
            $GLOBAL_YAHOO_SESSION = new YahooSession($consumer, $accessToken, 
                    $applicationId);
        }
        else if($type == YAHOO_OAUTH_AT_SESSION_TYPE) {
            // Found an OAuth Access Token session.
            $accessToken = json_decode(base64_decode($_COOKIE["yosdk_at"]));
            $now = time(); 
            YahooLogger::debug("OAuth AT: " . $accessToken->key . "   ATS: ". $accessToken->secret);

            if($accessToken->consumer != $consumerKey) {
                YahooLogger::error("Consumer key for token does not match the defined Consumer Key.  The Consumer Key has probably changed since the user last authorized the application.");
                YahooSession::clearSession();
                if($redirect) {
                    YahooSession::redirectForAuthorization($consumerKey, $consumerSecret, $callback);
                }
            }
            if($accessToken->tokenExpires >= 0) {
                YahooLogger::debug('AT Expires in: ' . ($accessToken->tokenExpires - $now));
            }
            if(($accessToken->tokenExpires >= 0) &&
                    ($accessToken->tokenExpires - $now) < 30) {
                // The access token will expire in less than 30 seconds or 
                // it may have expired already. Try to get a new one.
                YahooSession::accessTokenExpired(
                        $accessToken, $consumer, $applicationId);
            }
            else {
                // The access token is still good for a little while, 
                // continue using it.
                $GLOBAL_YAHOO_SESSION = new YahooSession(
                        $consumer, $accessToken, $applicationId);
            }
        }
        else if($type == YAHOO_OAUTH_RT_SESSION_TYPE) {
            // Found an OAuth Request Token session.
            $requestToken = json_decode(base64_decode(
                        $_COOKIE["yosdk_rt"]));

            $accessToken = YahooAuthentication::fetchAccessToken(
                    $consumerKey, $consumerSecret, $requestToken);
            if(!is_null($accessToken)) {
                setcookie("yosdk_at", 
                        base64_encode(json_encode($accessToken)), 
                        time() + (30 * 24 * 60 * 60));
                setcookie("yosdk_rt", "", time() - 600);

                $GLOBAL_YAHOO_SESSION = new YahooSession(
                        $consumer, $accessToken, $applicationId);
            }
            else if($redirect) {
                // The fetch for the access token failed. Generate a new 
                // request token and try again.
                $GLOBAL_YAHOO_SESSION = NULL;
                YahooSession::redirectForAuthorization($consumerKey, 
                        $consumerSecret, $callback);
            }
            else {
                // Don't redirect the user, just inform the caller that 
                // no session is present.
                setcookie("yosdk_rt", "", time() - 600);
                $GLOBAL_YAHOO_SESSION = NULL;
            }
        }
        else {
            trigger_error("Unknown session type found", E_USER_ERROR);
            $GLOBAL_YAHOO_SESSION = NULL;
        }

        return $GLOBAL_YAHOO_SESSION;
    }

    /**
     * Executes the given YQL query.
     *
     * @param $yql The query to execute.
     * @return The response or NULL if the request fails..
     */
    function query($yql) {
        $response = $this->client->get(
            sprintf("http://%s/v1/yql",
            QUERY_WS_HOSTNAME),
            array('q' => $yql, 'format' => 'json'), 30);

        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $resultSet = json_decode($response["responseBody"]);

        return $resultSet;
    }

    /**
     * @private
     */
    function checkSignature($consumer, $oauth_signature) {
        $oauth_sign_method = new OAuthSignatureMethod_HMAC_SHA1();
        $oauth_consumer = new OAuthConsumer($consumer->key, $consumer->secret);
        $oauth_token = null; // since this is a 2-legged signing only, no token is needed
        $oauth_request = OAuthRequest::from_request();
        $ok = $oauth_sign_method->check_signature($oauth_request, $oauth_consumer, $oauth_token, $oauth_signature);

        return $ok;
    }

    /**
     * @private
     */
    function accessTokenExpired($accessToken, $consumer, $applicationId) {
        global $GLOBAL_YAHOO_SESSION;
        $now = time();
        if(($accessToken->handleExpires === -1) ||
                ($now < $accessToken->handleExpires)) {
            // Either the access session handle doesn't expire 
            // or it hasn't expired yet. Get a new access token.
            $newAccessToken = YahooAuthentication::fetchAccessToken(
                    $consumer->key, $consumer->secret, $accessToken);
            if(is_null($newAccessToken)) {
                YahooLogger::error("Failed to fetch access token");
                $GLOBAL_YAHOO_SESSION = NULL;
            }

            setcookie("yosdk_at", 
                    base64_encode(json_encode($newAccessToken)), 
                    time() + (30 * 24 * 60 * 60));

            YahooLogger::debug("Got new AT/ATS from ASH!");
            YahooLogger::debug("OAuth AT: " . $newAccessToken->key . "   ATS: ". $newAccessToken->secret);

            $GLOBAL_YAHOO_SESSION = new YahooSession(
                    $consumer, $newAccessToken, $applicationId);
        }
        else {
            // The access token is expired and we don't have 
            // a sufficient access session handle to renew 
            // the access token. Clear the cookie and redirect 
            // to authorization point or return a NULL session.
            setcookie("yosdk_at", "", time() - 600);
            if ($redirect) {
                YahooSession::redirectForAuthorization($consumer->key, $consumer->secret, $callback);
            }
            else {
                $GLOBAL_YAHOO_SESSION = NULL;
            }
        }
    }

    /**
     * @private
     *
     * Checks to see if the current PHP page request has a session and, if so,
     * indicates what type of session is present.
     *
     * @param[out] $sessionType The session type present, if any.
     * @return boolean True if a session is present, false otherwise.
     */
    function checkSession(&$sessionType) {
        if(array_key_exists("yap_appid", $_POST)) {
            $sessionType = YAHOO_YAP_SESSION_TYPE;
            return true;
        }
        else if(array_key_exists("yosdk_at", $_COOKIE) && 
                strlen($_COOKIE["yosdk_at"]) > 0) {
            $sessionType = YAHOO_OAUTH_AT_SESSION_TYPE;
            return true;
        }
        else if(array_key_exists("yosdk_rt", $_COOKIE) && 
                strlen($_COOKIE["yosdk_rt"]) > 0) {
            $sessionType = YAHOO_OAUTH_RT_SESSION_TYPE;
            return true;
        }
        else {
            return false;
        }
    }
}

/**
 * Represents a Yahoo! user.
 *
 * @brief Represents a Yahoo! user.
 */
class YahooUser {
    /**
     * @private
     */
    var $session = NULL;

    /**
     * @private
     */
    var $guid = NULL;

    /**
     * @private
     */
    var $sessioned = false;

    /**
     * @private
     */
    var $client = NULL;

    /**
     * @private
     */
    function YahooUser($session, $guid, $sessioned) {
        $this->session = $session;
        $this->client = $session->client;
        $this->guid = $guid;
        $this->sessioned = $sessioned;
    }

    /**
     * Gets the presence of the user, including the status.
     *
     * @return The presence of the user or NULL if the fetch fails.
     */
    function getPresence() {
        $response = $this->client->get(
                sprintf("http://%s/v1/user/%s/presence/presence", 
                PRESENCE_WS_HOSTNAME,
                urlencode($this->guid)));

        if(is_null($response)) {
            return NULL;
        }
        else if($response["code"] == 404) {
            // No presence is set, return an empty presence.
            $presence = new stdclass();
            $presence->value = new stdclass();
            $presence->value->status = "";
            return $presence;
        }
        else if($response["code"] != 200) {
            return NULL;
        }
        else {
            $presence = json_decode($response["responseBody"]);
            return $presence->presence;
        }
    }

    /**
     * Sets the presence of the user.
     *
     * @param $status The new status message for the user.
     * @return The status message on success, NULL on failure.
     */
    function setPresence($status) {
        if(!$this->sessioned) {
            YahooLogger::error("Can't set the presence of an unsessioned user");
            return NULL;
        }

        $presence = array("status" => $status);
        $response = $this->client->put(
                sprintf("http://%s/v1/user/%s/presence/presence",
                PRESENCE_WS_HOSTNAME, $this->guid), "application/json", 
                json_encode($presence));
        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }
        $presence = json_decode($response["responseBody"]);
        return $presence;
    }

    /**
     * Lists the updates for the current user.
     *
     * @param $start The starting offset to list updates from.
     * @param $count The number of updates to request.
     * @return A list of updates for the current user.
     */
    function listUpdates($start = 0, $count = 10) {
        $response = $this->client->get(
                sprintf("http://%s/v1/user/%s/updates",
                UPDATES_WS_HOSTNAME,
                urlencode($this->guid),
                $start, $count), array("start" => $start, "count" => $count));
        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $updates = json_decode($response["responseBody"]);
        return $updates->updates;
    }

    /**
     * Lists the updates for the connections of the current user.
     *
     * @param $start The starting offset to list updates from.
     * @param $count The number of updates to request.
     * @return A list of updates for the connections of the current user.
     */
    function listConnectionUpdates($start = 0, $count = 10) {
        $response = $this->client->get(
                sprintf("http://%s/v1/user/%s/updates/connections",
                UPDATES_WS_HOSTNAME,
                urlencode($this->guid),
                $start, $count), array("start" => $start, "count" => $count));
        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $updates = json_decode($response["responseBody"]);
        return $updates->updates;
    }

    /**
     * Inserts an update for the current user.
     *
     * @param $suid Identifier that globally unique for a given 
     *              collectionId within producing source.
     * @param $title Title for the update.
     * @param $link Link back to the cause of the event.
     * @param $description Descriptive text associated with the update, 
     *                     optional.
     * @param $date The date of the update event, optional, defaults to now.
     */
    function insertUpdate($suid, $title, $link, 
            $description = "", $date = null) {
        // Make sure this YahooUser is sessioned.
        if(!$this->sessioned) {
            YahooLogger::error("Can't insert updates for an unsessioned user");
            return NULL;
        }

        if (is_null($date)) {
            $date = time();
        }

        // Make sure an application ID was given.
        $applicationId = $this->session->getApplicationId();
        if(empty($applicationId)) {
            YahooLogger::error("No application ID given, can't insert update");
            return NULL;
        }

        $source = sprintf("APP.%s", $applicationId);
        $body = array("updates" => array(array(
                "collectionID" => $this->guid,
                "collectionType" => "guid",
                "class" => "app",
                "source" => $source,
                "type" => 'appActivity',
                "suid" => $suid,
                "title" => $title,
                "description" => $description,
                "link" => $link,
                "pubDate" => (string)$date)));

        $response = $this->client->put(
            sprintf("http://%s/v1/user/%s/updates/%s/%s",
                UPDATES_WS_HOSTNAME, $this->guid, $source, urlencode($suid)),
            "application/json",
            json_encode($body));

        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        return TRUE;
    }

    /**
     * Deletes the update of the given SUID. Only allows deleting updates
     * that were inserted by your own application. You won't be able to 
     * delete updates from other applications.
     *
     * @param $suid The SUID of the update to be deleted.
     * @return boolean True on success, false on failure.
     */
    function deleteUpdate($suid) {
        // Make sure this YahooUser is sessioned.
        if(!$this->sessioned) {
            YahooLogger::error("Can't delete updates for an unsessioned user");
            return FALSE;
        }

        // Make sure an application ID was given.
        $applicationId = $this->session->getApplicationId();
        if(empty($applicationId)) {
            YahooLogger::error("No application ID given, can't delete update");
            return FALSE;
        }

        $source = sprintf("APP.%s", $applicationId);

        $response = $this->client->delete(
                sprintf("http://%s/v1/user/%s/updates/%s/%s",
                UPDATES_WS_HOSTNAME, $this->guid, $source, urlencode($suid)));

        if(is_null($response) || $response["code"] != 200) {
            YahooLogger::error("Failed to delete update $suid, HTTP response code $responseCode");
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Loads the extended profile of the current user.
     *
     * @return The extended profile of the current user.
     */
    function loadProfile() {
        $response = $this->client->get(
                sprintf("http://%s/v1/user/%s/profile",
                SOCIAL_WS_HOSTNAME,
                urlencode($this->guid)));
        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $profile = json_decode($response["responseBody"]);
        return $profile->profile;
    }

    /**
     * Gets a list of connections for the current user.
     *
     * @param[in,out] $start The starting offset.
     * @param[in,out] $count The number of connections to fetch.
     * @param[out] $total The total number of contacts available.
     * @return List of connections for the current user.
     */
    function getConnections(&$start, &$count, &$total) {
        $response = $this->client->get(
                sprintf("http://%s/v1/user/%s/connections",
                SOCIAL_WS_HOSTNAME,
                urlencode($this->guid)), 
                array("view" => "usercard", "start" => $start, 
                        "count" => $count));
        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $connections = json_decode($response["responseBody"]);

        $start = $connections->connections->start;
        $count = $connections->connections->count;
        $total = $connections->connections->total;

        return $connections->connections->connection;
    }

    /**
     * Gets a list of contacts for the current user.
     *
     * @param $start The starting offset.
     * @param $count The number of contacts to fetch.
     * @return List of contacts for the current user.
     */
    function getContacts($start = 0, $count = 10) {
        if(!$this->sessioned) {
            YahooLogger::error("Can't get contacts for an unsessioned user");
            return NULL;
        }

        $response = $this->client->get(
                sprintf("http://%s/v1/user/%s/contacts",
                SOCIAL_WS_HOSTNAME,
                urlencode($this->guid)), array("view" => "tinyusercard", "start" => $start, "count" => $count));
        if(is_null($response) || $response["code"] != 200) {
            return NULL;
        }

        $contacts = json_decode($response["responseBody"]);

        return $contacts;
    }

    /**
     * Sets the small view for the current user.
     *
     * @param $content The content to set the small view to.
     * @return True on success, false otherwise.
     */
    function setSmallView($content) {
        return $this->session->application->setSmallView($this->guid, $content);
    }
}

/**
 * A simple OAuth client class for making 2 and 3 legged OAuth HTTP requests.
 * 
 * @brief A simple OAuth client class for making 2 and 3 legged OAuth HTTP requests.
 */
class OAuthClient {
    /**
     * @private
     */
    var $consumer = NULL;

    /**
     * @private
     */
    var $token = NULL;

    /**
     * @private
     */
    var $defaultTimeout = 3;

    /**
     * @private
     */
    var $oauthParamsLocation = NULL;

    /**
     * @private
     */
    var $signatureMethod = NULL;

    /**
     * Constructs a new OAuth client.
     * 
     * @param $consumer The OAuthConsumer object to use for the requests.
     * @param $token The OAuthToken to use for the requests. Optional.
     * @param $oauthParamsLocation OAUTH_PARAMS_IN_HEADERS or OAUTH_PARAMS_IN_POST_BODY, depending on where you want the OAuth parameters to show up. Optional, defaults to using the headers.
     * @param $signatureMethod OAUTH_SIGNATURE_PLAINTEXT or OAUTH_SIGNATURE_HMAC_SHA1, depending on what request signing mechanism to use. Optional, defaults to HMAC SHA1 signatures.
     */
    function OAuthClient($consumer, $token = NULL, 
            $oauthParamsLocation = OAUTH_PARAMS_IN_HEADERS,
            $signatureMethod = OAUTH_SIGNATURE_HMAC_SHA1) {
        $this->consumer = $consumer;
        $this->token = $token;
        $this->oauthParamsLocation = $oauthParamsLocation;

        if($signatureMethod == OAUTH_SIGNATURE_HMAC_SHA1) {
            $this->signatureMethod = new OAuthSignatureMethod_HMAC_SHA1();
        }
        else if($signatureMethod == OAUTH_SIGNATURE_PLAINTEXT) {
            $this->signatureMethod = new OAuthSignatureMethod_PLAINTEXT();
        }
        else {
            YahooLogger::error("Invalid signature method: $signatureMethod");
        }
    }

    /**
     * Executes a properly signed OAuth HTTP GET request.
     *
     * @param $url The URL to request.
     * @param $queryParameters Any query string parameters to be sent in the request.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    function get($url, $queryParameters = array(), $timeout = NULL) {
        if(strpos($url, "?") !== FALSE) {
            YahooLogger::error("Put the query parameters in the second argument to OAuthClient::get(), not in the URL itself: URL = $url");
            return NULL;
        }

        return $this->request(array(
                "method" => "GET", 
                "url" => $url, 
                "query" => $queryParameters,
                "timeout" => $timeout));
    }

    /**
     * Executes a properly signed OAuth HTTP DELETE request.
     *
     * @param $url The URL to request.
     * @param $queryParameters Any query string parameters to be sent in the request.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    function delete($url, $queryParameters = array(), $timeout = NULL) {
        if(strpos($url, "?") !== FALSE) {
            YahooLogger::error("Put the query parameters in the second argument to OAuthClient::delete(), not in the URL itself: URL = $url");
            return NULL;
        }

        return $this->request(array(
                "method" => "DELETE", 
                "url" => $url, 
                "query" => $queryParameters,
                "timeout" => $timeout));
    }

    /**
     * Executes a properly signed OAuth HTTP PUT request.
     *
     * @param $url The URL to request.
     * @param $contentType The Content-Type of the PUT data.
     * @param $content The raw content to be PUT.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    function put($url, $contentType, $content, $timeout = NULL) {
        return $this->request(array(
                "method" => "PUT", 
                "url" => $url, 
                "content" => $content, 
                "contentType" => $contentType,
                "timeout" => $timeout));
    }

    /**
     * Executes a properly signed OAuth HTTP POST request.
     *
     * @param $url The URL to request.
     * @param $contentType The Content-Type of the POST data.
     * @param $content The content to be POST.
     * @param $timeout Optional, the number of seconds to wait for the request to return.
     * @return The response object.
     */
    function post($url, $contentType = "application/x-www-form-urlencoded", 
                $content = array(), $timeout = NULL) {
        return $this->request(array(
                "method" => "POST", 
                "url" => $url, 
                "content" => $content, 
                "contentType" => $contentType, 
                "timeout" => $timeout));
    }

    /**
     * @private
     */
    function request($request) {
        if(!array_key_exists("content", $request)) {
            $request["content"] = array();
        }
        if(!array_key_exists("query", $request)) {
            $request["query"] = array();
        }

        if(is_array($request["content"])) {
            $combinedParams = array_merge(
                    $request["query"], $request["content"]);
        }
        else {
            $combinedParams = $request["query"];
        }

        $oauthRequest = OAuthRequest::from_consumer_and_token(
                $this->consumer, $this->token, $request["method"], 
                $request["url"], $combinedParams);
        $oauthRequest->sign_request($this->signatureMethod, $this->consumer, 
                $this->token);

        $headers = array("Accept: application/json");
        if($this->oauthParamsLocation == OAUTH_PARAMS_IN_HEADERS) {
            $headers[] = $oauthRequest->to_header();
        }
        if(!empty($request["content"]) || $this->oauthParamsLocation == OAUTH_PARAMS_IN_POST_BODY) {
            $headers[] = "Content-Type: " . $request["contentType"];
        }

        if(!empty($request["query"])) {
            $requestUrl = sprintf("%s?%s", $request["url"], 
                    oauth_http_build_query($request["query"]));
        }
        else {
            $requestUrl = $request["url"];
        }

        $requestTimeout = array_key_exists("timeout", $request) ? 
                $request["timeout"] : $this->defaultTimeout;

        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $requestTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request["method"]);
        if(($this->oauthParamsLocation == OAUTH_PARAMS_IN_POST_BODY) || 
                (!empty($request["content"]) && is_array($request["content"]))) {
            // Content is an array, URL encode it.
            if($this->oauthParamsLocation == OAUTH_PARAMS_IN_POST_BODY) {
                $request["content"] = $oauthRequest->to_postdata();
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request["content"]);
            }
            else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, 
                        oauth_http_build_query($request["content"]));
            }
        }
        else if(!empty($request["content"])) {
            // Content is raw.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request["content"]);
        }

        $headerParser = new YahooHeaderParser();
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$headerParser, "read"));
        $response = curl_exec($ch);
        if(is_bool($response) && !$response) {
            YahooLogger::error("Error making libcurl request(" . 
                    $requestUrl . "): " . curl_error($ch));
            return NULL;
        }

        $response = array(
            'method' => $request["method"],
            'url' => $requestUrl,
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'requestHeaders' => $headers,
            'requestBody' => !empty($request["content"]) ? $request["content"] : NULL,
            'responseHeaders' => $headerParser->headers,
            'responseBody' => $response
            );
        if($response["code"] != 200) {
            YahooLogger::error("HTTP request failed", $response);

            $this->checkExpired($response["code"], $headerParser);
            return NULL;
        }
        YahooLogger::debug("HTTP request details", $response);

        return $response;
    }

    /**
     * Checks to see if the code and headers indicate an expired OAuth token.
     * If so, requests a new one.
     *
     * @private
     */
    function checkExpired($code, $headerParser) {
        if ($code != 401) return; // HTTP Unauthorized
        $authenticateHeader = $headerParser->get('WWW-Authenticate');
        if (!$authenticateHeader) return;
        if (!preg_match('/oauth_problem="([^"]+)"/', $authenticateHeader, $match)) return;
        $oauth_problem = $match[1];
        if ($oauth_problem == 'token_expired') {
            YahooSession::accessTokenExpired($this->token, $this->consumer, 
                    $this->applicationId);
        }
        if ($oauth_problem == 'consumer_key_unknown') {
            YahooLogger::error('Consumer Key unkown.  Please check that the Consumer Key is valid.');
        }
        if ($oauth_problem == 'additional_authorization_required') {
            YahooLogger::error('The app identified by this Consumer Key is not authorized to access this resource.  Authorization is defined under Access Scopes on the application\'s settings page.');
        }
    }
}

/**
 * @private
 */
class YahooHeaderParser {
    var $headers = array();

    function YahooHeaderParser() {
    }

    function read($ch, $header) {
        $pos = strpos($header, ":");
        if($pos !== FALSE) {
            $name = substr($header, 0, $pos);
            $value = trim(substr($header, $pos + 1));
            $this->headers[$name] = $value;
        }
        return strlen($header);
    }

    function get($name) {
        if(array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        else {
            return NULL;
        }
    }
}

/**
 * @private
 */
class YahooAuthentication {
    function createRequestToken($consumerKey, $consumerSecret) {
        $consumer = new OAuthConsumer($consumerKey, $consumerSecret);
        $client = new OAuthClient($consumer, NULL, OAUTH_PARAMS_IN_POST_BODY, 
                OAUTH_SIGNATURE_PLAINTEXT);

        $baseUrl = sprintf("https://%s/oauth/v2/get_request_token",
                OAUTH_HOSTNAME);
        $response = $client->post($baseUrl);

        if(is_null($response)) {
            YahooLogger::error("OAuth call to get request token failed");
            return NULL;
        }

        parse_str($response["responseBody"], $token);

        if($response["code"] != 200) {
            $problem = array_key_exists("oauth_problem", $token) ? 
                    $token["oauth_problem"] : "unknown problem";
            YahooLogger::error("Failed to create request token: $problem");
            return NULL;
        }

        $requestToken = new stdclass();
        $requestToken->key = $token["oauth_token"];
        $requestToken->secret = $token["oauth_token_secret"];
        return $requestToken;
    }

    function createAuthorizationUrl($requestToken, $callbackUrl = NULL) {
        if(!is_object($requestToken) || !property_exists($requestToken, "key")) {
            YahooLogger::error("Request token doesn't have a 'key' property");
            return NULL;
        }

        if(!empty($callbackUrl)) {
            return sprintf("https://%s/oauth/v2/request_auth?oauth_token=%s&oauth_callback=%s", 
                    OAUTH_HOSTNAME, urlencode($requestToken->key), urlencode($callbackUrl));
        }
        else {
            return sprintf("https://%s/oauth/v2/request_auth?oauth_token=%s", 
                    OAUTH_HOSTNAME, urlencode($requestToken->key));
        }
    }

    function fetchAccessToken($consumerKey, $consumerSecret, $requestToken) {
        $baseUrl = sprintf("https://%s/oauth/v2/get_token", OAUTH_HOSTNAME);
        $consumer = new OAuthConsumer($consumerKey, $consumerSecret);

        $parameters = array();
        if(property_exists($requestToken, "sessionHandle")) {
            $parameters["oauth_session_handle"] = $requestToken->sessionHandle;
        }

        $client = new OAuthClient($consumer, $requestToken, 
                OAUTH_PARAMS_IN_POST_BODY);
        $response = $client->post($baseUrl, "application/x-www-form-urlencoded",
                $parameters);

        if(is_null($response)) {
            YahooLogger::error("OAuth call to get access token failed");
            return NULL;
        }

        parse_str($response["responseBody"], $token);

        if($response["code"] != 200) {
            YahooLogger::error("Failed to fetch access token: " . $token["oauth_problem"]);
            return NULL;
        }

        $now = time();

        $accessToken = new stdclass();
        $accessToken->key = $token["oauth_token"];
        $accessToken->secret = $token["oauth_token_secret"];
        $accessToken->guid = $token["xoauth_yahoo_guid"];
        $accessToken->consumer = $consumerKey;
        $accessToken->sessionHandle = $token["oauth_session_handle"];

        // Check to see if the access token ever expires.
        YahooLogger::debug('AT expires in '.$token['oauth_expires_in'].'; ASH expires in '.$token["oauth_authorization_expires_in"]);
        if(array_key_exists("oauth_expires_in", $token)) {
            $accessToken->tokenExpires = $now + $token["oauth_expires_in"];
        }
        else {
            $accessToken->tokenExpires = -1;
        }

        // Check to see if the access session handle ever expires.
        if(array_key_exists("oauth_authorization_expires_in", $token)) {
            $accessToken->handleExpires = $now + 
                    $token["oauth_authorization_expires_in"];
        }
        else {
            $accessToken->handleExpires = -1;
        }
        return $accessToken;
    }
}

// If json_decode doesn't exist, then php-json must not be included in this 
// version of PHP. Include fake versions of json_encode/json_decode that 
// are backed by the native PHP php-json library, which is available in PEAR.
if(!function_exists("json_decode")) {
    // Include JSON.php. If it's not found, tell the user to download the 
    // JSON library from PEAR.
    //
    // http://pear.php.net/pepr/pepr-proposal-show.php?id=198
    $includeStatus = include_once("JSON.php");
    if(!$includeStatus) {
        error_log("Missing JSON functions, please download Services_JSON and include JSON.php in the same directory as Yahoo.inc - http://pear.php.net/pepr/pepr-proposal-show.php?id=198");
        echo("Missing JSON functions, please download Services_JSON and include JSON.php in the same directory as Yahoo.inc - http://pear.php.net/pepr/pepr-proposal-show.php?id=198");
        exit();
    }

    function json_decode($json) {
        $js = new Services_JSON();
        return $js->decode($json);
    }

    function json_encode($value) {
        $js = new Services_JSON();
        return $js->encode($value);
    }
}

if(!function_exists("property_exists")) {
    function property_exists( $class, $property ) {
        if ( is_object( $class ) ) {
            $vars = get_object_vars( $class );
        } else {
            $vars = get_class_vars( $class );
        }
        return array_key_exists( $property, $vars );
    }
}

/**
 * An OAuth compatible version of http_build_query. http_build_query 
 * doesn't work because it turns spaces into "+", which isn't allowed 
 * by OAuth.
 */
function oauth_http_build_query($parameters) {
    $strings = array();
    foreach($parameters as $name => $value) {
        $strings[] = sprintf("%s=%s", rawurlencode($name), rawurlencode($value));
    }
    $query = implode("&", $strings);
    return $query;
}

?>
