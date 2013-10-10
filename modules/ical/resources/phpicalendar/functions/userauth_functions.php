<?php
// Generate the login query string.
//
// Returns the login query string.
function login_querys() {
	global $QUERY_STRING;
	
	// Remove the username, password, and action values.
	$querys = preg_replace('/(username|password|action)=[^&]+/', '', $QUERY_STRING);

	// Return the login query string.
	$querys = preg_replace('/&&/', '', $querys);
	return $querys;
}

// Generate the logout query string.
//
// Returns the logout query string.
function logout_querys() {
	global $QUERY_STRING;
	
	// Make sure the action is logout.
	$querys = preg_replace('/action=[^&]+/', 'action=logout', $QUERY_STRING);
	if ($querys == $QUERY_STRING) $querys .= '&action=logout';
	
	// Remove references to the username or password.
	$querys = preg_replace('/(username|password)=[^&]+/', '', $querys);
	
	// Return the logout query string.
	$querys = preg_replace('/&&/', '', $querys);
	return $querys;
}

// Authenticate the user. The submitted login data is checked for
// validity against the locked map. The login data will be saved in
// cookies or the session depending on the configuration. The variable
// $invalid_login will be set true or false depending on whether or not
// a valid login was found.
//
// This authentication method only applies to non-HTTP authentication.
//
// Returns the username and password found, which will be empty strings
// if no valid login is found. Returns a boolean invalid_login to
// indicate that the login is invalid.
function user_login() {
	global $_COOKIE, $_GET, $_POST, $_SERVER;
	global $login_cookies, $cookie_uri, $locked_map;
	
	// Initialize return values.
	$invalid_login = false;
	$username = ''; $password = '';
	
	// If not HTTP authenticated, try login via cookies or the web page.
	if (isset($_SERVER['PHP_AUTH_USER'])) {
		return array($username, $password, $invalid_login);
	}

	// Look for a login cookie.
	if ($login_cookies == 'yes' &&
		isset($_COOKIE['phpicalendar_login']))
	{
		$login_cookie = unserialize(stripslashes($_COOKIE['phpicalendar_login']));
		if (isset($login_cookie['username']) &&
			isset($login_cookie['password']))
		{
			$username = $login_cookie['username'];
			$password = $login_cookie['password'];
		}
	}
	
	// Look for session authentication.
	if ($login_cookies != 'yes') {
		if (!session_id()) {
			session_start();
			setcookie(session_name(), session_id(), time()+(60*60*24*7*12*10), '/', $cookie_uri, 0);
		}
		if (isset($_SESSION['username']) &&
			isset($_SESSION['password']))
		{
			$username = $_SESSION['username'];
			$password = $_SESSION['password'];
		}
	}
	
	// Look for a new username and password.
	if (isset($_GET['username']) &&
		isset($_GET['password']))
	{
		$username = $_GET['username'];
		$password = $_GET['password'];
	} else if (isset($_POST['username']) &&
			   isset($_POST['password']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
	}
	
	// Check to make sure the username and password is valid.
	if (!array_key_exists("$username:$password", $locked_map)) {
		// Remember the invalid login, because we may want to display
		// a message elsewhere or check validity.
		return array($username, $password, true);
	}
	
	// Set the login cookie or session authentication values.
	if ($login_cookies == 'yes') {
		$the_cookie = serialize(array('username' => $username, 'password' => $password));
		setcookie('phpicalendar_login', $the_cookie, time()+(60*60*24*7*12*10), '/', $cookie_uri, 0);
	} else {
		$_SESSION['username'] = $username;
		$_SESSION['password'] = $password;
	}
	
	// Return the username and password.
	return array($username, $password, $invalid_login);
}

// Logout the user. The username and password stored in cookies or the
// session will be deleted.
//
// Returns an empty username and password.
function user_logout() {
	global $login_cookies, $cookie_uri;
	
	// Clear the login cookie or session authentication values.
	if ($login_cookies == 'yes') {
		setcookie('phpicalendar_login', '', time()-(60*60*24*7), '/', $cookie_uri, 0);
	} else {
		// Check if the session has already been started.
		if (!session_id()) {
			session_start();
			setcookie(session_name(), session_id(), time()+(60*60*24*7*12*10), '/', $cookie_uri, 0);
		}
	
		// Clear the session authentication values.
		unset($_SESSION['username']);
		unset($_SESSION['password']);
	}
	
	// Return empty username and password.
	return array('', '');
}
?>
