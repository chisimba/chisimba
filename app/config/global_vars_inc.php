<?PHP
//-------------Variables used throughout---------
// NOTE THIS CODE IS NOT SKINS COMPATIBLE, IT IS FOR TESTING ONLY


//------ BEGIN CONFIGURABLE CONSTANTS ------------
// Use this to name your site
define( 'KEWL_SITENAME', 'paul' );
// Institutions short name (e.g UWC, MIT, MUN)
define( 'KEWL_INSTITUTION_SHORTNAME', 'paul' );
// Institutions normal name
define( 'KEWL_INSTITUTION_NAME', 'paul' );
// Proxy setting
define( 'KEWL_PROXY', 'http://pscott:scott@cache.uwc.ac.za:8080' );
// Provide the email address for the website
define( 'KEWL_SITEEMAIL', 'pscott@uwc.ac.za' );
// The timeout for a session in minutes
define( 'KEWL_SYSTEMTIMEOUT', '60' );

// The URL for the site root (KEWL.NextGen can be inside another site)
define( 'KEWL_SITE_ROOT', '/5ive/app/' );

//The default ICON folder
define( 'KEWL_DEFAULTICONFOLDER', '/icons/' );
// The URL for the location of the KEWL.NextGen skins
define( 'KEWL_SKIN_ROOT', 'skins/' );
// The default skin to use
define( 'KEWL_DEFAULT_SKIN', 'classroom' );
// The default language as a word
define( 'KEWL_DEFAULT_LANGUAGE', 'english' );
// The abbreviation for the default language
define( 'KEWL_DEFAULT_LANGUAGE_ABBREV', 'EN' );
// Extension to use for banners and file names for icons
define( 'KEWL_BANNER_EXT', 'jpg' );
// Post login module - the module called after login
 define( 'KEWL_POSTLOGIN_MODULE', 'postlogin' );
// Pre login module - the module called before login
 define( 'KEWL_PRELOGIN_MODULE', 'splashscreen' );

// Name of default layout template
 define( 'KEWL_DEFAULT_LAYOUT_TEMPLATE', 'default_layout_tpl.php' );
// Name of login page template
define( 'KEWL_LOGIN_TEMPLATE', 'login_tpl.php' );
// Name of logged in page template
 define( 'KEWL_LOGGED_IN_TEMPLATE', 'loggedin_tpl.php' );
// The file system path for the above URL
define( 'KEWL_SITEROOT_PATH', '/var/www/5ive/app/' );
// The path to template files
define( 'KEWL_TEMPLATE_PATH', '/var/www/5ive/app/templates/' );
define( 'KEWL_CONTENT_BASEPATH', '/var/www/5ive/app/usrfiles/' );
define( 'KEWL_CONTENT_PATH', 'usrfiles/' );
define( 'KEWL_CONTENT_ROOT', 'usrfiles' );
define( 'KEWL_BLOGS_BASEPATH', '/var/www/5ive/app/blog/' );
//-----------END CONFIGURABLE CONSTANTS ----------------
 // ----------- Site options -------------- 
 define( 'KEWL_ALLOW_SELFREGISTER', '1' );
define( 'KEWL_ENABLE_LOGGING', 'TRUE' );
define( 'LDAP_USED', '' );
// -------- DO NOT EDIT BELOW THIS LINE ----------------
 ?>