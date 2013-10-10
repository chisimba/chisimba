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

class dbsection_user extends dbTable
{

	public function init()
	{
		try {
			parent::init('tbl_cms_section_user');
			
		} catch (Exception $e){
			throw customException($e->getMessage());
			exit();
		}
	}
}