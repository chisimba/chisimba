<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
 * Data access class for the cmsadmin module.
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 */

class dbcontent_group extends dbTable
{

	public function init()
	{
		try {
			parent::init('tbl_cms_content_group');
			
		} catch (Exception $e){
			throw customException($e->getMessage());
			exit();
		}
	}
}