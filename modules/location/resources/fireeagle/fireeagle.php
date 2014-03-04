<?php

/**
 * FireEagle OAuth+API PHP bindings
 *
 * Copyright (C) 2007-08 Yahoo! Inc
 *
 * See http://fireeagle.yahoo.net/developer/documentation/walkthru_php
 * for usage instructions.
 *
 */

/*

NOTES:

- You'll probably need PHP 5.2.3+.

- To get HTTPS working on Windows, download cacert.pem from here:

    http://curl.haxx.se/ca/cacert.pem

  Then let this library know where it is like this:

    define('CURL_CA_BUNDLE_PATH', 'c:/web/cacert.pem');

*/

// Requires OAuth.php from http://oauth.googlecode.com/svn/code/php/OAuth.php
//require_once(dirname(__FILE__)."/OAuth.php");

if (!function_exists("hash_hmac")) {
  // Earlier versions of PHP5 are missing hash_hmac().  Here's a
  // pure-PHP version in case you're using one of them.
  function hash_hmac($algo, $data, $key) {
    // Thanks, Kellan: http://laughingmeme.org/code/hmacsha1.php.txt
    if ($algo != 'sha1') throw new Exception("fireeagle.php's hash_hmac() can only do sha1, sorry");

    $blocksize = 64;
    $hashfunc = 'sha1';
    if (strlen($key)>$blocksize)
      $key = pack('H*', $hashfunc($key));
    $key = str_pad($key,$blocksize,chr(0x00));
    $ipad = str_repeat(chr(0x36),$blocksize);
    $opad = str_repeat(chr(0x5c),$blocksize);
    $hmac = pack(
      'H*',$hashfunc(
	($key^$opad).pack(
	  'H*',$hashfunc(
	    ($key^$ipad).$data
	    )
	  )
	)
      );
    return $hmac;
  }
}

// Various things that can go wrong
class FireEagleException extends Exception {
  const TOKEN_REQUIRED = 1; // call missing an oauth request/access token
  const LOCATION_REQUIRED = 2; // call to update() without a location
  const REMOTE_ERROR = 3; // FE sent an error
  const REQUEST_FAILED = 4; // empty or malformed response from FE
  const CONNECT_FAILED = 5; // totally failed to make an HTTP request
  const INTERNAL_ERROR = 6; // totally failed to make an HTTP request
  const CONFIG_READ_ERROR = 7; // can't find or parse fireeaglerc

  const REMOTE_SUCCESS = 0; // Request succeeded.
  const REMOTE_UPDATE_PROHIBITED = 1; // Update not permitted for that user.
  const REMOTE_UPDATE_ONLY = 2; // Update successful, but read access prohibited.
  const REMOTE_QUERY_PROHIBITED = 3; // Query not permitted for that user.
  const REMOTE_SUSPENDED = 4; // User account is suspended.
  const REMOTE_PLACE_NOT_FOUND = 6; // Place can't be identified.
  const REMOTE_USER_NOT_FOUND = 7; // Authentication token can't be matched to a user.
  const REMOTE_INVALID_QUERY = 8; // Invalid location query.
  const REMOTE_IS_FROB = 10; // Token provided is a request token, not an auth token.
  const REMOTE_NOT_VALIDATED = 11; // Request token has not been validated.
  const REMOTE_REQUEST_TOKEN_REQUIRED = 12; // Token provided must be an access token.
  const REMOTE_EXPIRED = 13; // Token has expired.
  const REMOTE_GENERAL_TOKEN_REQUIRED = 14; // Token provided must be an general purpose token.
  const REMOTE_UNKNOWN_CONSUMER = 15; // Unknown consumer key.
  const REMOTE_UNKNOWN_TOKEN = 16; // Token not found.
  const REMOTE_BAD_IP_ADDRESS = 17; // Request made from non-blessed ip address.
  const REMOTE_OAUTH_CONSUMER_KEY_REQUIRED = 20; // oauth_consumer_key parameter required.
  const REMOTE_OAUTH_TOKEN_REQUIRED = 21; // oauth_token parameter required.
  const REMOTE_BAD_SIGNATURE_METHOD = 22; // Unsupported signature method.
  const REMOTE_INVALID_SIGNATURE = 23; // Invalid OAuth signature.
  const REMOTE_REPEATED_NONCE = 24; // Provided nonce has been seen before.
  const REMOTE_YAHOOAPIS_REQUIRED = 30; // All api methods should use fireeagle.yahooapis.com.
  const REMOTE_SSL_REQUIRED = 31; // SSL / https is required.
  const REMOTE_RATE_LIMITING = 32; // Rate limit/IP Block due to excessive requests.
  const REMOTE_INTERNAL_ERROR = 50; // Internal error occurred; try again later.

  public $response; // for REMOTE_ERROR codes, this is the response from FireEagle (useful: $response->code and $response->message)

  function __construct($msg, $code, $response=null) {
    parent::__construct($msg, $code);
    $this->response = $response;
  }
}

/**
 * FireEagle API access helper class.
 */
class FireEagle {

  public static $FE_ROOT = "http://fireeagle.yahoo.net";
  public static $FE_API_ROOT = "https://fireeagle.yahooapis.com";

  public static $FE_DEBUG = false; // set to true to print out debugging info
  public static $FE_DUMP_REQUESTS = false; // set to a pathname to dump out http requests to a log

  // OAuth URLs
  function requestTokenURL() { return self::$FE_API_ROOT.'/oauth/request_token'; }
  function authorizeURL() { return self::$FE_ROOT.'/oauth/authorize'; }
  function accessTokenURL() { return self::$FE_API_ROOT.'/oauth/access_token'; }
  // API URLs
  function methodURL($method) { return self::$FE_API_ROOT.'/api/0.1/'.$method.'.json'; }

  function __construct($consumerKey,
		       $consumerSecret, 
		       $oAuthToken = null, 
		       $oAuthTokenSecret = null,
		       $json = null)  {
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer($consumerKey, $consumerSecret, NULL);
    if (!empty($oAuthToken) && !empty($oAuthTokenSecret)) {
      $this->token = new OAuthConsumer($oAuthToken, $oAuthTokenSecret);
    } else {
      $this->token = NULL;
    }
    $this->json = $json;
  }

  // read consumer key and secret, and optionally fireeagle auth and api urls, from a .fireeaglerc file
  public static function from_fireeaglerc($fn, $token=null, $secret=null) {
    $text = @file_get_contents($fn);
    if ($text === false) throw new FireEagleException("Could not read $fn", FireEagleException::CONFIG_READ_ERROR);
    $info = array();
    foreach (preg_split("/\n/", $text) as $line) {
      $line = trim(preg_replace("/#.*/", "", $line));
      if (empty($line)) continue;
      if (!preg_match("/^([^\s=]+)\s*\=\s*(.*)$/", $line, $m)) throw new FireEagleException("Failed to parse line '$line' in $fn", FireEagleException::CONFIG_READ_ERROR);
      list(, $k, $v) = $m;
      $info[$k] = $v;
    }

    if (empty($info['consumer_key'])) throw new FireEagleException("Missing consumer_key in $fn", FireEagleException::CONFIG_READ_ERROR);
    if (empty($info['consumer_secret'])) throw new FireEagleException("Missing consumer_secret in $fn", FireEagleException::CONFIG_READ_ERROR);

    if (isset($info['api_server'])) self::$FE_API_ROOT = self::build_server_url($info, 'api');
    if (isset($info['auth_server'])) self::$FE_ROOT = self::build_server_url($info, 'auth');

    return new FireEagle($info['consumer_key'], $info['consumer_secret'], $token, $secret);
  }

  private static function build_server_url($info, $role) {
    $proto = isset($info["${role}_protocol"]) ? $info["${role}_protocol"] : 'https';
    $default_port = ($proto == 'https' ? 443 : 80);
    $port = isset($info["${role}_port"]) ? $info["${role}_port"] : $default_port;
    $url = $proto . "://" . $info["${role}_server"];
    if ($port != $default_port) $url .= ":" . $port;
    return $url;
  }

  /**
   * Get a request token for authenticating your application with FE.
   *
   * @returns a key/value pair array containing: oauth_token and
   * oauth_token_secret.
   */
  public function getRequestToken() {
    $r = $this->oAuthRequest($this->requestTokenURL());
    $token = $this->oAuthParseResponse($r);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); // use this token from now on
    if (self::$FE_DUMP_REQUESTS) self::dump("Now the user is redirected to ".$this->getAuthorizeURL($token['oauth_token'])."\nOnce the user returns, via the callback URL for web authentication or manually for desktop authentication, we can get their access token and secret by calling /oauth/access_token.\n\n");
    return $token;
  }
  public function request_token() { return $this->getRequestToken(); }

  /**
   * Get the URL to redirect to to authorize the user and validate a
   * request token.
   *
   * @returns a string containing the URL to redirect to.
   */
  public function getAuthorizeURL($token) {
    // $token can be a string, or an array in the format returned by getRequestToken().
    if (is_array($token)) $token = $token['oauth_token'];
    return $this->authorizeURL() . '?oauth_token=' . $token;
  }
  public function authorize($token) { return $this->getAuthorizeURL($token); }
  
  /**
   * Exchange the request token and secret for an access token and
   * secret, to sign API calls.
   *
   *
   * @returns array("oauth_token" => the access token,
   *                "oauth_token_secret" => the access secret)
   */
  public function getAccessToken($token=NULL) {
    $this->requireToken();
    $r = $this->oAuthRequest($this->accessTokenURL());
    $token = $this->oAuthParseResponse($r);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); // use this token from now on
    return $token;
  }
  public function access_token() { return $this->getAccessToken(); }

  /**
   * Generic method call function.  You can use this to get the raw
   * output from an API method, or to call future API methods.
   *
   * e.g.
   *   Get a user's location: $fe->call("user")
   *     or $fe->user()
   *   Set a user's location: $fe->call("update", array("q" => "new york, new york"))
   *     or $fe->update(array("q" => "new york, new york"))
   */

  public function call($method, $params=array(), $request_method=NULL) {
    $this->requireToken();
    $r = $this->oAuthRequest($this->methodURL($method), $params, $request_method);
    return $this->parseJSON($r);
  }

  // --- Wrappers for individual methods ---
  
  /**
   * Wrapper for 'user' API method, which fetches the current location
   * for a user.
   */
  public function user() {
    $r = $this->call("user");
    // add latitudes and longitudes, and extract best guess
    if (isset($r->user->location_hierarchy)) {
      $r->user->best_guess = NULL;
      foreach ($r->user->location_hierarchy as &$loc) {
	$c = $loc->geometry->coordinates;
	switch ($loc->geometry->type) {
	case 'Box': // DEPRECATED
	  $loc->bbox = $c;
	  $loc->longitude = ($c[0][0] + $c[1][0]) / 2;
	  $loc->latitude = ($c[0][1] + $c[1][1]) / 2;
	  $loc->geotype = 'box';
	  break;
	case 'Polygon':
	  $loc->bbox = $bbox = $loc->geometry->bbox;
	  $loc->longitude = ($bbox[0][0] + $bbox[1][0]) / 2;
	  $loc->latitude = ($bbox[0][1] + $bbox[1][1]) / 2;
	  $loc->geotype = 'box';
	  break;
	case 'Point':
	  list($loc->longitude, $loc->latitude) = $c;
	  $loc->geotype = 'point';
	  break;
	}
	if ($loc->best_guess) $r->user->best_guess = $loc; // add shortcut to get 'best guess' loc
	unset($loc);
      }
    }
    
    return $r;
  }

  /**
   * Wrapper for 'update' API method, to set a user's location.
   */
  public function update($args=array()) {
    if (empty($args)) throw new FireEagleException("FireEagle::update() needs a location", FireEagleException::LOCATION_REQUIRED);
    return $this->call("update", $args);
  }

  /**
   * Wrapper for 'lookup' API method, to run a location query without
   * setting the user's location (so an application can show a list of
   * possibilities that match a user-supplied query -- not to be used
   * as a generic geocoder).
   */
  public function lookup($args=array()) {
    if (!is_array($args)) throw new FireEagleException("\$args parameter to FireEagle::lookup() should be an array", FireEagleException::LOCATION_REQUIRED);
    if (empty($args)) throw new FireEagleException("FireEagle::lookup() needs a location", FireEagleException::LOCATION_REQUIRED);
    return $this->call("lookup", $args, "GET");
  }

  /**
   * Wrapper for 'recent' API method
   */
  public function recent($since=NULL, $per_page=NULL, $page=NULL) {
    $params = array(
		    "per_page" => ($per_page === NULL) ? 10 : $per_page,
		    "page" => ($page === NULL) ? 1 : $page,
		    );
    if (!empty($since)) $params['time'] = $since;

    return $this->call("recent", $params, "GET");
  }

  /**
   * Wrapper for 'within' API method
   */
  public function within($params=array()) {
    return $this->call("within", $params, "GET");
  }

  // --- Internal bits and pieces ---

  protected function parseJSON($json) {
    $r = $this->json->decode($json);
    if (empty($r)) throw new FireEagleException("Empty JSON response", FireEagleException::REQUEST_FAILED);
    if (isset($r->rsp) && $r->rsp->stat != 'ok') {
      throw new FireEagleException($r->rsp->code.": ".$r->rsp->message, FireEagleException::REMOTE_ERROR, $r->rsp);
    }
    return $r;
  }

  protected function requireToken() {
    if (!isset($this->token)) {
      throw new FireEagleException("This function requires an OAuth token", FireEagleException::TOKEN_REQUIRED);
    }
  }
  
  // Parse a URL-encoded OAuth response
  protected function oAuthParseResponse($responseString) {
    $r = array();
    foreach (explode('&', $responseString) as $param) {
      $pair = explode('=', $param, 2);
      if (count($pair) != 2) continue;
      $r[urldecode($pair[0])] = urldecode($pair[1]);
    }  
    return $r;
  }

  // Format and sign an OAuth / API request
  function oAuthRequest($url, $args=array(), $method=NULL) {
    if (empty($method)) $method = empty($args) ? "GET" : "POST";
    $req = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $args);
    $req->sign_request($this->sha1_method, $this->consumer, $this->token);
    if (self::$FE_DEBUG) {
      echo "<div>[OAuth request: <blockquote><code>".nl2br(htmlspecialchars(var_export($req, TRUE)))."</code><br>base string: ".htmlspecialchars($req->base_string)."</blockquote>]</div>";
    }
    if (self::$FE_DUMP_REQUESTS) {
      $k = $this->consumer->secret . "&";
      if ($this->token) $k .= $this->token->secret;
      self::dump("---\n\nOAUTH REQUEST TO $url");
      if (!empty($args)) self::dump(" WITH PARAMS ".$this->json->encode($args));
      self::dump("\n\nBase string: ".$req->base_string."\nSignature string: $k\n");
    }
    switch ($method) {
    case 'GET': return $this->http($req->to_url());
    case 'POST': return $this->http($req->get_normalized_http_url(), $req->to_postdata());
    }
  }

  // Make an HTTP request, throwing an exception if we get anything other than a 200 response
  public function http($url, $postData=null) {
    if (self::$FE_DEBUG) {
      echo "[FE HTTP request: url: ".htmlspecialchars($url).", post data: ".htmlspecialchars(var_export($postData, TRUE))."]";
    }
    if (self::$FE_DUMP_REQUESTS) {
      self::dump("Final URL: $url\n\n");
      $url_bits = parse_url($url);
      if (isset($postData)) {
	self::dump("POST ".$url_bits['path']." HTTP/1.0\nHost: ".$url_bits['host']."\nContent-Type: application/x-www-urlencoded\nContent-Length: ".strlen($postData)."\n\n$postData\n");
      } else {
	$get_url = $url_bits['path'];
	if ($url_bits['query']) $get_url .= '?' . $url_bits['query'];
	self::dump("GET $get_url HTTP/1.0\nHost: ".$url_bits['host']."\n\n");
      }
    }
    $ch = curl_init();
    if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (isset($postData)) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
    $response = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ct = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    if ($ct) $ct = preg_replace("/;.*/", "", $ct); // strip off charset
    if (!$status) throw new FireEagleException("Connection to $url failed", FireEagleException::CONNECT_FAILED);
    if ($status != 200) {
      if ($ct == "application/json") {
	$r = $this->json->decode($response);
	if ($r && isset($r->rsp) && $r->rsp->stat != 'ok') {
	  throw new FireEagleException($r->rsp->code.": ".$r->rsp->message, FireEagleException::REMOTE_ERROR, $r->rsp);
	}
      }
      throw new FireEagleException("Request to $url failed: HTTP error $status ($response)", FireEagleException::REQUEST_FAILED);
    }
    if (self::$FE_DUMP_REQUESTS) {
      self::dump("HTTP/1.0 $status OK\n");
      if ($ct) self::dump("Content-Type: $ct\n");
      $cl = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
      if ($cl) self::dump("Content-Length: $cl\n");
      self::dump("\n$response\n\n");
    }
    curl_close ($ch);

    if (self::$FE_DEBUG) {
      echo "[HTTP response: <code>".nl2br(htmlspecialchars($response))."</code>]";
    }
    
    return $response;
  }

  private function dump($text) {
    if (!self::$FE_DUMP_REQUESTS) throw new Exception('FireEagle::$FE_DUMP_REQUESTS must be set to enable request trace dumping');
    file_put_contents(self::$FE_DUMP_REQUESTS, $text, FILE_APPEND);
  }

}

