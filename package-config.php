<?php
/**
 * package-config for Chisimba
 *
 * Config to to build PEAR packages
 *
 * $Id$
 *
 * @author      Paul Scott <pscott@uwc.ac.za>
 * @package     chisimba
 * @subpackage  tools
 */

/**
 * package name
 */
$name = 'chisimba_framework';

/**
 * package summary
 */
$summary = 'Powerful PHP5 Framework.';

/**
 * current version
 */
$version = '1.0.2';

/**
 * build version appendix
 */
$versionBuild = 'RC1';

/**
 * Current API version
 */
$apiVersion = '1.0.2';

/**
 * current state
 */
$state = 'stable';

/**
 * current API stability
 */
$apiStability = 'stable';

/**
 * release notes
 */
$notes = <<<EOT
Some release notes
	- Fixed up most modules
	- Created a new module or 2
	- Had some fun
EOT;

/**
 * package description
 */
$description = <<<EOT
Chisimba is a Web 2.0 enabled rapid application development framework for creating web applications that are platform independent, browser independent, xHTML compliant, and can use a number of common databases. Chisimba is written in PHP5 using the model-view-controller paradigm, implemented via a modular architecture. Over 100 modules of functionality are already available and these can be used out of the box to create a Content Management System, a feature-rich e-learning platform, a group-based collaboration platform, a blogging system that allows posting from mobile phones, and many other applications.

Chisimba modules are configured at install time from a simple text-based configuration file that contains common setting, and automates permissions and menu entries. Most settings are configurable from a dynamic configuration editor, including menu entries, permissions, and even the type of site. The Chisimba module catalogue includes some common configurations, allowing you to install an e-learning platform, a CMS, an organizational portal, or any of several other configurations from the same codebase. Modules can also be installed individually or combined in different ways to create entirely new application types without any programming.

Chisimba uses a group/role based access control system that is underpinned by and ACL system that allows permissions to be as fine or as corse grained as you like. Groups and roles can be edited in a GUI editor, and can be changed on a per module basis if necessary.

Chisimba includes a modest code generation engine that can use XML templates to generate code for common purposes such as accessing data, creating or consuming web services, wrapping foreign classes, etc.

Chisimba allows full internationalisation and localization using commonly available translation facilities, thus allowing interface elements to be presented in different languages without having to modify any core code.

Simple and extended help capability is built in, and allows for the presentation of simple textual help, extended help, or extended help via Flash files in multiple languages.

Chisimba is the Chichewa (Malawi) word for the framework used to build a traditional African house.

EOT;

$options    =   array ( 
    'license'           => 'GPL',
    'filelistgenerator' => 'cvs',
    'ignore'            => array( 'package.php', 'autopackage2.php', 'package-config.php', 'package.xml', '.cvsignore', '.svn' ),
    'simpleoutput'      => true,
    'baseinstalldir'    => 'chisimba_framework',
    'packagedirectory'  => './',
    'dir_roles'         => array(
                                 'docs' => 'doc',
                                 'examples' => 'doc',
                                 'tests' => 'test',
                                 )
    );
    
$license    =   array(
        'name'  => 'GPL', 
        'url'   =>  'http://www.gnu.org/copyleft/gpl.txt' 
    );

$roles          =   array();
$roles[]        =   array('role' => 'inc', 'type' => 'web');
$roles[]        =   array('role' => 'html', 'type' => 'web');
$roles[]        =   array('role' => 'tpl', 'type' => 'web');
$roles[]        =   array('role' => 'sql', 'type' => 'web');
$roles[]		=	array('role' => 'png', 'type' => 'web');
$roles[] 		=   array('role' => 'gif', 'type' => 'web');
$roles[] 		=   array('role' => 'jpg', 'type' => 'web');
$roles[]        =   array('role' => 'css', 'type' => 'web');
$roles[]        =   array('role' => 'js',  'type' => 'web');
$roles[]        =   array('role' => 'ini', 'type' => 'web');
$roles[]        =   array('role' => 'inc', 'type' => 'web');
$roles[]        =   array('role' => 'pl',  'type' => 'web');
$roles[]        =   array('role' => 'txt', 'type' => 'web');
$roles[]        =   array('role' => 'php', 'type' => 'web');
$roles[]        =   array('role' => 'jar', 'type' => 'web');
$roles[]        =   array('role' => 'swf', 'type' => 'web');
$roles[]        =   array('role' => 'zip', 'type' => 'web');
$roles[]        =   array('role' => 'conf','type' => 'web');
$roles[]        =   array('role' => 'xml', 'type' => 'web');
$roles[]        =   array('role' => 'htc', 'type' => 'web');
$roles[]        =   array('role' => 'cfm', 'type' => 'web');
$roles[]        =   array('role' => 'xsd', 'type' => 'web');
$roles[]        =   array('role' => 'pgp', 'type' => 'web');
$roles[]        =   array('role' => 'htm', 'type' => 'web');
$roles[]        =   array('role' => 'class','type' => 'web');
$roles[]        =   array('role' => 'java', 'type' => 'web');
$roles[]        =   array('role' => 'wsz',  'type' => 'web');
$roles[]        =   array('role' => 'ttf', 'type' => 'web');
$roles[]        =   array('role' => 'dtd', 'type' => 'web');
$roles[]        =   array('role' => 'xsl', 'type' => 'web');
$roles[]        =   array('role' => 'odt', 'type' => 'web');
$roles[]        =   array('role' => 'rdf', 'type' => 'web');
$roles[]        =   array('role' => 'n3', 'type' => 'web');
$roles[]        =   array('role' => 'pkg', 'type' => 'web');
$roles[]        =   array('role' => 'sh', 'type' => 'web');
$roles[]        =   array('role' => 'psd', 'type' => 'web');
$roles[]        =   array('role' => 'GIF', 'type' => 'web');
		
$maintainer     =   array();
$maintainer[]   =   array(
        'role'      => 'lead',
        'handle'    => 'pscott',
        'name'      => 'Paul Scott',
        'email'     => 'pscott@uwc.ac.za',
        'active'    => 'yes'
);
$maintainer[]   =   array(
        'role'      => 'developer',
        'handle'    => 'pmbekwa',
        'name'      => 'Prince Mbekwa',
        'email'     => 'pmbekwa@uwc.ac.za',
        'active'    => 'yes'
);
$maintainer[]   =   array(
        'role'      => 'developer',
        'handle'    => 'wnitsckie',
        'name'      => 'Wesley Nitsckie',
        'email'     => 'wnitsckie@uwc.ac.za',
        'active'    => 'yes'
);
$maintainer[]   =   array(
        'role'      => 'contributor',
        'handle'    => 'dkeats',
        'name'      => 'Derek Keats',
        'email'     => 'dkeats@uwc.ac.za',
        'active'    => 'yes'
);

$dependency     =   array();

$dependency[]   =   array(
    'type'      =>  'required',
    'package'   =>  'MDB2',
    'channel'   =>  'pear.php.net',
    'version'   =>  '2.4.1'
);

$dependency[]   =   array(
    'type'      =>  'required',
    'package'   =>  'MDB2_Schema',
    'channel'   =>  'pear.php.net',
    'version'   =>  '0.7.1',
    'stability' =>  'beta',
);

$dependency[]   =   array(
    'type'      =>  'required',
    'package'   =>  'MDB2_Driver_Mysql',
    'channel'   =>  'pear.php.net',
    'version'   =>  '1.4.1',
);

$dependency[]   =   array(
    'type'      =>  'required',
    'package'   =>  'MDB2_Driver_Pgsql',
    'channel'   =>  'pear.php.net',
    'version'   =>  '1.4.0',
);

$dependency[]   =   array(
    'type'      =>  'required',
    'package'   =>  'Log',
    'channel'   =>  'pear.php.net',
    'version'   =>  '1.9.10',
);

$dependency[]   =   array(
    'type'      =>  'required',
    'package'   =>  'Config',
    'channel'   =>  'pear.php.net',
    'version'   =>  '1.10.10',
);


$dependency[]   =   array(
    'type'      =>  'optional',
    'package'   =>  'Text_Wiki',
    'channel'   =>  'pear.php.net',
    'version'   =>  null
);

$channel    =   'pear.uwc.ac.za';
$require    =   array(
    'php'               =>  '5.1.0',
    'pear_installer'    => '1.4.0'
);

?>