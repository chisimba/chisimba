<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   help
 * @author    Megan Watson <mwatson@uwc.ac.za>
 * @copyright 2007 Megan Watson
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/* -------------------- help class extends controller ----------------*/

/**
* Class for providing a service to other modules that want to 
* display help
* 
* @author Derek Keats
* @author Megan Watson - updated 13/09/2006 porting to 5ive 
*/
class helplink extends object  {

    /**
     * Description for private
     * @var    unknown
     * @access private
     */
    private $rootModule=Null;
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        $this->objHelpIcon = $this->newObject('geticon','htmlelements');
        $this->rootModule = $this->getParam("module", Null);
    }

    /**
    * *The standard dispatch method for the module. The dispatch() method must 
    * return the name of a page body template which will render the module 
    * output (for more details see Modules and templating)
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