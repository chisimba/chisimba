<?php

// +----------------------------------------------------------------------+
// | Decode and Encode data in Bittorrent format                          |
// +----------------------------------------------------------------------+
// | Copyright (C) 2004-2006 Markus Tacker <m@tacker.org>                 |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// |                                                                      |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// |                                                                      |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the                |
// | Free Software Foundation, Inc.                                       |
// | 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA               |
// +----------------------------------------------------------------------+

/**
* Exception for File_Bittorrent2
*
* @package File_Bittorrent2
* @category File
* @author Markus Tacker <m@tacker.org>
* @version $Id$
*/

/**
* Include required classes
*/
require_once 'PEAR/Exception.php';

/**
* Exception for File_Bittorrent2
*
* @package File_Bittorrent2
* @category File
* @author Markus Tacker <m@tacker.org>
* @version $Id$
*/
class File_Bittorrent2_Exception extends PEAR_Exception {
	/**
	* @global int Exception happened during data decoding
	*/
	const decode = 1;

	/**
	* @global int Exception happened during data encoding
	*/
	const encode = 2;

	/**
	* @global int There is a problem with the source of the data (file, dir)
	*/
	const source = 3;

	/**
	* @global int Exception happened while making a torrent
	*/
	const make   = 4;
}

?>