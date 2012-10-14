<?php
/**
 * extjs_class_inc.php
 *
 * This class generates the styles and js scripts which is
 * needed to run any ExtJS script
 *
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wesleynitsckie@gmail.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id:$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}


class extjs extends object {

    /**
     * init function for compatability
     *
     * @return void
     * @access public
     */
    function init(){
		 $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
		 $this->theme =  $this->objSysConfig->getValue ( 'extjs_theme', 'htmlelements' );
    }


    /**
     * Method to show a textinput referencing variables which
     * dont exist.
     *
     * @return string Return description (if any) ...
     * @access public
     */
    function show(){
        //need the js
        $extbase_js = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
		$extall_js = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js', 'htmlelements').'" type="text/javascript"></script>';

		//need the css

		$extall_css = '<link rel="stylesheet" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'htmlelements').'" type="text/css" />';
		$extall_css .= '<link rel="stylesheet" href="skins/_common/css/extjs/silk/silk.css" type="text/css" />';
		$extall_css .= '<link rel="stylesheet" href="skins/_common/css/extjs/menus.css" type="text/css" />';
		$extall_css .= '<link rel="stylesheet" href="skins/_common/css/extjs/buttons.css" type="text/css" />';

		//append them to the header
		$this->appendArrayVar('headerParams', $extbase_js);
		$this->appendArrayVar('headerParams', $extall_js);
		$this->appendArrayVar('headerParams', $extall_css);

		if($this->theme != 'Blue')
		{
            if ($this->theme !=='' && $this->theme !== NULL) {
                $xtheme_css = '<link rel="stylesheet" href="skins/_common/css/extjs/themes/'.$this->theme.'/css/xtheme.css" type="text/css" />';
                $this->appendArrayVar('headerParams', $xtheme_css);
            }
		}


    }
}
?>