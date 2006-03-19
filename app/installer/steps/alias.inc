<?php

/**
* +--------------------------------------------------------------------+
* | Squiz.net Open Source Licence                                      |
* +--------------------------------------------------------------------+
* | Copyright (c), 2003 Squiz Pty Ltd (ABN 77 084 670 600).            |
* +--------------------------------------------------------------------+
* | This source file may be used subject to, and only in accordance    |
* | with, the Squiz Open Source Licence Agreement found at             |
* | http://www.squiz.net/licence.                                      |
* | Make sure you have read and accept the terms of that licence,      |
* | including its limitations of liability and disclaimers, before     |
* | using this software in any way. Your use of this software is       |
* | deemed to constitute agreement to be bound by that licence. If you |
* | modify, adapt or enhance this software, you agree to assign your   |
* | intellectual property rights in the modification, adaptation and   |
* | enhancement to Squiz Pty Ltd for use and distribution under that   |
* | licence.                                                           |
* +--------------------------------------------------------------------+
* 
* $Id$
* $Name$
*/

require_once INSTALL_DIR.'/step.inc';
require_once INSTALL_DIR.'/stepaction.inc';


/**
* Alias
*
* Give the user detailed instructions on creating aliases for 5ive.
*
* @author prince mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class Alias extends Step
{
	
	
	/**
	* Set up this step's actions
	* 
	* @return void
	* @access public
	*/
	function Alias()
	{
		$this->actions[] =& new AliasAction(get_class($this));
		$this->step_title = 'Apache Aliases';
		$this->title_logo = 'apache_aliases.gif';
		$this->help_topic = 'apache_aliases';
	}//end Alias()
	
	
}//end class


/**
* AliasAction
*
* Give details about what alias to create
*
* @author prince mbekwa	
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class AliasAction extends StepAction
{
	
	
	/**
	* Try and create all the needed database tables.
	* 
	* @return boolean Whether all the database tables were created or not.
	* @access public
	*/
	function processAction()
	{
		$this->next_step = 'setuptype';
		return true;
	}//end processAction()
	
	
	
	/**
	* Display information about the creation status to the user
	* 
	* @return void
	* @access public
	*/
	function paintAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/alias.tpl');
		
		$base_name = parse_url('http://'.$_SESSION['site_url']);
		// base_name path might be empty (ie top level directory)
		$base_name = @$base_name['path'];
		$web_root = $base_name;
		// if the base name is empty, make it the root
		if (strlen($base_name) == 0) {
			$web_root = '/';
		}
		
		$tpl->set('system_root', isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : '');
		$tpl->set('web_path', $base_name);
		$tpl->set('web_root', $web_root);
		
		
		echo $tpl->fetch();
	}//end paintAction
	
	
}//end class
?>
