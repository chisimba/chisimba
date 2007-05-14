<?php
/**
* OpenDocument_Exception class
* 
* OpenDocument_Exception class extends PEAR_Exception
*
* PHP version 5
*
* LICENSE: This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
* 
* You should have received a copy of the GNU Lesser General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
* 
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/

require_once 'PEAR/Exception.php';

/**
* OpenDocument_Exception class
*
* OpenDocument_Exception class extends PEAR_Exception
*
* @category   File Formats
* @package    OpenDocument
* @author     Alexander Pak <irokez@gmail.com>
* @license    http://www.gnu.org/copyleft/lesser.html  Lesser General Public License 2.1
* @version    0.1.0
* @link       http://pear.php.net/package/OpenDocument
* @since      File available since Release 0.1.0
*/
class OpenDocument_Exception extends PEAR_Exception
{
    /**
     * Error while accessing OpenDocument file
     */
    const ACCESS_FILE_ERR = 1;

    /**
     * Error while loading mimetype file
     */
    const LOAD_MIMETYPE_ERR = 2;
    
    /**
     * Error while loading content file
     */
    const LOAD_CONTENT_ERR = 3;

    /**
     * Error while loading meta file
     */
    const LOAD_META_ERR = 4;
    
    /**
     * Error while loading settings file
     */
    const LOAD_SETTINGS_ERR = 5;
    
    /**
     * Error while loading styles file
     */
    const LOAD_STYLES_ERR = 6;

    /**
     * Error while loading manifest file
     */
    const LOAD_MANIFEST_ERR = 7;

    /**
     * Error while writing mimetype file
     */
    const WRITE_MIMETYPE_ERR = 8;

    /**
     * Error while writing content file
     */
    const WRITE_CONTENT_ERR = 9;

    /**
     * Error while writing meta file
     */
    const WRITE_META_ERR = 10;

    /**
     * Error while writing settings file
     */
    const WRITE_SETTINGS_ERR = 11;

    /**
     * Error while writing styles file
     */
    const WRITE_STYLES_ERR = 12;

    /**
     * Error while writing manifest file
     */
    const WRITE_MANIFEST_ERR = 13;
    
    /**
     * OpenDocument_Element or OpenDocument expected
     *
     */
    const ELEM_OR_DOC_EXPECTED = 14;
}
?> 