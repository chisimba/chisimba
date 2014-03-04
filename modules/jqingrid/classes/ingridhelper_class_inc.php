<?php
/**
 *
 * Interface to the jQuery ingrid plugin
 *
 * Interface to the jQuery ingrid plugin for building a table grid. Ingrid is
 * an unobtrusive jQuery component that adds datagrid behaviors (column
 * resizing, paging, sorting, row and column styling, and more) to tables.
 * Ingrid was developed by matt@reconstrukt.com and is available from
 * http://www.reconstrukt.com/ingrid/
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
 * @package   jqingrid
 * @author    Interface to the jQuery ingrid plugin derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbjqingrid.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Interface helper for the jQuery ingrid plugin
*
* @author Derek Keats <derek.keats@wits.ac.za>
* @package jqingrid
*
*/
class ingridhelper extends object
{

    /**
    *
    * Intialiser for the jqingrid database connector
    * @access public
    *
    */
    public function init()
    {
        //
    }
    /**
    *
    * Load the ingrid jQuery plugin
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadIngrid()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("js/jquery.ingrid.js", "jqingrid")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the ingrid CSS stylesheet
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadCss()
    {
        $css = $this->getResourceUri("css/ingrid.css", "jqingrid");
        $script = " <link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" media=\"screen\" />";
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the ingrid jQuery plugin ready function that is the business end
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadReadyFunction($targetUrl, $whichTable='table1', $tableHeight='350')
    {
        $targetUrl = str_replace('&amp;', '&', $targetUrl);
        $script = '<script type="text/javascript">
jQuery(document).ready(
    function() {
        jQuery("#' . $whichTable . '").ingrid({
            url: \'' . $targetUrl . '\',
            height: ' . $tableHeight . '
        });
    }
);
</script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

}
?>