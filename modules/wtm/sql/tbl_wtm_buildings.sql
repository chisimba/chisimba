<?php
/**
*
* WTM building database table
*
* This file provides the data structure of the WTM module's buildings database.
* 
* PHP version 5
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the
* Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*
* @category Chisimba
* @package WTM
* @author Yen-Hsiang Huang <wtm.jason@gmail.com>
* @copyright 2007 AVOIR
* @license http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version CVS: $Id: demo_class_inc.php,v 1.4 2007-08-03 10:33:34 Exp $
* @link http://avoir.uwc.ac.za
*/

/**
* Security check: the $GLOBALS is an array used to control access to certain constants.
* Here it is used to check if the file is opening in engine, if not it
* stops the file from running.
*
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name $kewl_entry_point_run
*/
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}

/**
Set the table name
*/
$tablename = 'tbl_wtm_buildings';

/*
Options line for comments, encoding and character set
*/
$options = array(
'comment' => 'Table for tbl_wtm_buildings',
'collate' => 'utf8_general_ci',
'character_set' => 'utf8');

/*
Create the table fields
*/
$fields = array(
'id' => array(
'type' => 'text',
'length' => 32,
'notnull' => 1
),
'building' => array(
'type' => 'text',
'length' => 40,
'notnull' => TRUE
),
'longcoordinate' => array(
'type' => 'integer',
'length' => 7,
'notnull' => TRUE
),
'latcoordinate' => array(
'type' => 'integer',
'length' => 7,
'notnull' => TRUE
),
'xexpand' => array(
'type' => 'integer',
'length' => 7,
'notnull' => TRUE
),
'yexpand' => array(
'type' => 'integer',
'length' => 7,
'notnull' => TRUE
),
'modified' => array(
'type' => 'timestamp',
'notnull' => TRUE
)
);

?>