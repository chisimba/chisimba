<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a dynamic form. i.e. the forms created in the Form Builder module (forms)
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
 * @package   filters
 * @author    Charl Mert <cmert@uwc.ac.za>
 * @copyright 2007 Paul Scott & Derek Keats 
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: parse4formbuilder_class_inc.php 11052 2008-10-25 16:04:14Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
     // security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a flv (Flash video file) and render the video in the page, YouTube! style
 *
 * @author    Paul Scott
 * @package   filters
 * @access    public
 * @copyright AVOIR GNU/GPL
 *
 */

class parse4formbuilder extends object
{

    /**
     * The modules object of the modulecatalogue module.
     *
     * @access protected
     * @var object $objModules
     */
    protected $objModules;
    
    /**
     * init
     * 
     * Standard Chisimba init function
     * 
     * @return void  
     * @access public
     */
    function init()
    {
        $this->objModules = $this->getObject('modules', 'modulecatalogue');

        if ($this->objModules->checkIfRegistered('forms')) {
            $this->objForm = $this->getObject('dbforms', 'forms');
        }

        $this->objConfig = $this->getObject('altconfig', 'config');

        $this->loadClass('layer', 'htmlelements');

        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($txt)
    {
        preg_match_all('/\[FORM\](.*)\[\/FORM\]/U', $txt, $results, PREG_PATTERN_ORDER);
        preg_match_all('/\\[FORM:(.*?)\\]/', $txt, $results2, PREG_PATTERN_ORDER);
        
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $videoId = $item;

            if ($this->objForm) {
                $form = $this->objForm->getForm($videoId);
            } else {
                $form = array();
            }

            if (!isset($form['body'])) {
                $form['body'] = '';
            }

            if (!isset($form['id'])) {
                $form['id'] = '';
            }

            $qry_str = $_SERVER['QUERY_STRING'];

            $formMsg = $this->getParam('form_msg', '');

            if($formMsg == '') {
                $formBody = $form['body'];
            } else {
                $objLayer = new layer();
                $objLayer->str = $formMsg;
                $objLayer->id = 'formmessage';
                $formBody = $objLayer->show().$form['body'];
            }

            $replacement = preg_replace('/<\/form>/i', '', $formBody);
            $replacement .= "<input type='hidden' name='form_id' value='$form[id]' />";
            $replacement .= "<input type='hidden' name='qry_str' value='$qry_str' />";
            $replacement .= "</form>";

            $txt = str_replace($results[0][$counter], $replacement, $txt);
            $counter++;
        }
                
        //Get all the ones [FLV: xx=yy] tags (added by Derek 2007 09 23)
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            var_dump($item);
        }
        
        return $txt;
    }
    
    /**
     * 
     * Method to set up the parameter / value pairs for th efilter
     * @access public
     * @return VOID
     * 
     */
    public function setUpPage()
    {
        if (isset($this->objExpar->url)) {
            $this->id = $this->objExpar->url;
        } else {
            $this->id=NULL;
        }
        
        if (isset($this->objExpar->width)) {
            $this->id = $this->objExpar->width;
        } else {
            $this->id=NULL;
        }
        
        
    }
    
}
?>
