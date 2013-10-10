<?php
/**
 *
 * Interface to the jQuery JsTree plugin
 
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
* Interface helper for the jQuery tree plugin
*
* @author Wesley Nitsckie
* @package jqtress
*
*/
class jstreehelper extends object
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
    public function loadTree()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("lib/jquery.js", "jstree")
          . '" type="text/javascript"></script>';
          
           $script .= '<script language="javascript" src="'
          . $this->getResourceUri("jquery.treeview.js", "jstree")
          . '" type="text/javascript"></script>';
          
          $script .= '<script language="javascript" src="'
          . $this->getResourceUri("jquery.treeview.async.js", "jstree")
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
        $css = $this->getResourceUri("jquery.treeview.css", "jstree");
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
    public function loadReadyFunction($targetUrl, $whichDiv='tree')
    {
        $targetUrl = str_replace('&amp;', '&', $targetUrl);
        $script = '<script type="text/javascript">
            jQuery(document).ready(
                function() {
                    jQuery("#' . $whichDiv . '").treeview({
                        url: \'' . $targetUrl . '\'
                    });
                }
            );
            </script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }
    
    /**
    *
    * Demo method for the getting the json 
    * need for displaying the tree structure
    *
    * @access public
    * @return string
    */
    public function  getDemoData()
    {
      
      if ($this->getParam('root') == 'source')
      {
          $treeData = '[
                {
                    "text": "1. Pre Lunch (120 min)",
                    "expanded": true,
                    "classes": "important",
                    "children":
                    [
                        {
                            "text": "1.1 The State of the Powerdome (30 min)"
                        },
                        {
                            "text": "1.2 The Future of jQuery (30 min)"
                        },
                        {
                            "text": "1.2 jQuery UI - A step to richnessy (60 min)"
                        }
                    ]
                },
                {
                    "text": "2. Lunch  (60 min)"
                },
                {
                    "text": "3. After Lunch  (120+ min)",
                    "children":
                    [
                        {
                            "text": "3.1 jQuery Calendar Success Story (20 min)"
                        },
                        {
                            "text": "3.2 jQuery and Ruby Web Frameworks (20 min)"
                        },
                        {
                            "text": "3.3 Hey, I Can Do That! (20 min)"
                        },
                        {
                            "text": "3.4 Taconite and Form (20 min)"
                        },
                        {
                            "text": "3.5 Server-side JavaScript with jQuery and AOLserver (20 min)"
                        },
                        {
                            "text": "3.6 The Onion: How to add features without adding features (20 min)",
                            "id": "36",
                            "hasChildren": true
                        },
                        {
                            "text": "3.7 Visualizations with JavaScript and Canvas (20 min)"
                        },
                        {
                            "text": "3.8 ActiveDOM (20 min)"
                        },
                        {
                            "text": "3.8 Growing jQuery (20 min)"
                        }
                    ]
                }
            ]';
        }else{
            $treeData = '
                    [
                    {
                        "text": "1. Review of existing structures",
                        "expanded": true,
                        "children":
                        [
                            {
                                "text": "1.1 jQuery core"
                            },
                            {
                                "text": "1.2 metaplugins"
                            }
                        ]
                    },
                    {
                        "text": "2. Wrapper plugins"
                    },
                    {
                        "text": "3. Summary"
                    },
                    {
                        "text": "4. Questions and answers"
                    }
                    
                ]
            ';
            
        }
        
        return $treeData;
        
    }

}
?>
