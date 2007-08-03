<?php

/**
 * File containing the class for creating the link to the help
 * 
 * The class creates an icon which links to the help for the module and opens in a pop-up window.
 * 
 * PHP version 5
 * 
 * 
 * @category  Chisimba
 * @package   help
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 University of the Western Cape
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
* Class for displaying a help icon for a module. The icon opens the help in a pop up window.
* 
* @author Derek Keats
* @author Megan Watson - updated 13/09/2006 porting to 5ive 
*/
class helplink extends object  {

    /**
     * Variable containing the calling module - the module for which the help file is generated.
     * @var    unknown
     * @access private
     */
    private $rootModule=Null;
    
    /**
     * Constructor function for the class. Function sets up global variables and objects used within the class.
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        $this->objHelpIcon = $this->newObject('geticon','htmlelements');
        $this->rootModule = $this->getParam('module', Null);
    }

    /**
    * Method to display the help icon.
    *
    * @access public
    * @param string $helpid 
    * @param string $module The name of the module for which the help file is generated.
    * @return string html The html to display the icon.
    */
    public function show($helpid, $module = NULL)
    { 
        if(!is_null($module) && !empty($module)){
            $this->rootModule = $module;
        }
        
        $objSkin = & $this->getObject('skin','skin');
        $this->objHelpIcon->setModuleIcon('help');
        //Popup window
        //$this->objPop=&new windowpop;
        $this->objPop=& $this->getObject('windowpop','htmlelements');
        $location=$this->uri(array(
          'helpid'=>$helpid,
          'rootModule'=>$this->rootModule, 
          'module'=>'help'));
        $this->objPop->set('window_name','help');
        $this->objPop->set('location',$location);
        $this->objPop->set('linktext', $this->objHelpIcon->show());
        $this->objPop->set('width','400');
        $this->objPop->set('height','400');
        $this->objPop->set('left','300');
        $this->objPop->set('top','300');
        $this->objPop->set('scrollbars', 'auto');
//        echo $this->objPop->putJs(); // you only need to do this once per page
        return $this->objPop->show();
        
    } 
} 

?>