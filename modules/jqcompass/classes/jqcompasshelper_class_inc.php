<?php
/**
 *
 * Compass datagrid
 *
 * Compass DataGrid is an ajax-driven data grid that relies on server-side
 * code for its data. Rather than manipulating an existing table or breaking
 * it down into multiple pages, Compass DataGrid takes an empty table and
 * populates it by connecting to a server-side url via ajax. As users interact
 * with the grid, the grid talks with the server-side script letting it know
 * what the user is requesting. The server-side script then provides JSON
 * encoded data for the plugin to update the table. This is a Chisimba PHP
 * wrapper for Compass.
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
 * @package   jqcompass
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbjqcompass.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* Compass datagrid
*
* Compass DataGrid is an ajax-driven data grid that relies on server-side
* code for its data. Rather than manipulating an existing table or breaking
* it down into multiple pages, Compass DataGrid takes an empty table and
* populates it by connecting to a server-side url via ajax. As users interact
* with the grid, the grid talks with the server-side script letting it know
* what the user is requesting. The server-side script then provides JSON
* encoded data for the plugin to update the table. This is a Chisimba PHP
* wrapper for Compass.
*
* @author Derek Keats
* @package jqcompass
*
*/
class jqcompasshelper extends object
{

    /**
    *
    * Intialiser for the jqcompass database connector
    * @access public
    *
    */
    public function init()
    {
        //nothing now
    }

    /**
    *
    * Load the compass CSS stylesheet
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadCss()
    {
        $css = $this->getResourceUri("compassdatagrid.css", "jqcompass");
        $script = " <link rel=\"stylesheet\" href=\"$css\" type=\"text/css\" media=\"screen\" />";
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the compass jQuery plugin javascript file
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadJs()
    {
        $objLive = $this->getObject('jquery', 'jquery');
        $objLive->loadLiveQueryPlugin();
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("jquery.compassdatagrid.min.js", "jqcompass")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the jqcompas jQuery plugin ready function that is the business end
    *
    * @access public
    * @return TRUE
     * @access public
    *
    */
    public function loadReadyFunction()
    {
        $targetUrl = str_replace('&amp;', '&', $targetUrl);
        $this->appendArrayVar('headerParams', $this->readyFunction);
        return TRUE;
    }

    /**
    *
    * Some examples of $arrayParams are
    *         $arrayParams = array('images' => 'images/',
    *             'url' => 'getdata.php')
    *
    * @param string array $arrayParams An array of param => value pairs
    * @param string $browseTable The css class of the table for the grid
    * @access public
    *
    */
    public function buildReadyFunction($arrayParams, $browseTable='browseTable')
    {
        $ret = '<script type="text/javascript">';
        $ret .= "\n     jQuery(document).ready(function() {\n";
        $ret .= "          jQuery(\".$browseTable\").compassDatagrid({\n";
        $entries = count($arrayParams);
        $counter = 0;
        foreach ($arrayParams as $key=>$value) {
            $counter++;
            $ret .= "               $key : '$value'";
            if ($counter < $entries) {
                $ret .= ",\n";
            } else {
                $ret .= "\n";
            }
        }
        $ret .= "          });\n     });\n</script>";
        $this->readyFunction = $ret;
    }

    /**
     * Method to build the base table that will be replaced in the
     * output with the data. The columns should correspond between the
     * base table and the actual data.
     *
     * @param integer $tableCols The number of colums to
     * @param string $browseTable The class for the table
     * @return string The table HTML
     * @access public
     *
     */
    public function buildBaseTable($tableCols=4, $browseTable='browseTable')
    {
        $ret = "<table class=\"$browseTable\">\n    <tbody>\n\n";
        $count = 1;
        while ($count <= $tableCols) {
            $ret .= "        <td></td>\n";
            $count++;
        }
        $ret .= "\n\n    </tbody>\n</table>";
        return $ret;
    }

    /**
    * Add a div to activate the resizer
    *
    * @return string The rendered div
    * @access public
    * 
    */
    public function addResizer()
    {
        return '<div id="ctResizer"></div>';
    }

}
?>