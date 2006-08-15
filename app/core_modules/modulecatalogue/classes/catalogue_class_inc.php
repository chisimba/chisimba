<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building the catalogue navigation for module catalogue.
*
* The class builds a css style navigation menu
*
* @author Nic Appleby
* @copyright (c)2006 UWC
* @version $Id
* @package modulecatalogue
*/

class catalogue extends object {

	/**
	 * Nodes of navigation list
	 *
	 * @var array $nodes
	 */
	protected $nodes = array();

	/**
    * Method to construct the class.
    */
    public function init()
    {
   		try {
    		$this->nodes = array();
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();

		}
    }

    /**
     * Method to add content to the navigation list
     *
     * @param array $nodes An array containing key for category
     */
    public function addNodes($nodes) {
    	try {
    		if (is_array($nodes)) {
    			foreach($nodes as $node) {
    				$this->addNodes($node);
    			}
    		} else {
    			array_push($this->nodes,$nodes);
    		}
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();
        }
    }

    /**
     * Method to reset the nodelist
     */
    public function clearNodes() {
    	try {
    		$this->nodes = array();
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();
		}
    }

    /**
     * Method to display the navigation menu
     *
     * @param string $activeNode
     * @return string
     */
    public function show($activeNode = null) {
    	try {
    		$un = $this->getParam('uninstall');
    		$str = '<ul id="nav-secondary">';
    		$cssClass = '';
    		//loop through the nodes
    		foreach($this->nodes as $node) {
				if(strtolower($node) == strtolower($activeNode)) {
					$cssClass = ' class="active" ';
				}
				$name = ucwords($node);
				if ($un) {
					$str .="<li $cssClass><a href='{$this->uri(array('action'=>'list','cat'=>$node,'uninstall'=>'1'),'modulecatalogue')}'>{$name}</a></li>";
				} else {
					$str .="<li $cssClass><a href='{$this->uri(array('action'=>'list','cat'=>$node),'modulecatalogue')}'>{$name}</a></li>";
				}
				//reset the cssclass
				$cssClass = '';
    		}
    		$str .='</ul>';
    		return $str;
    	} catch (customException $e) {
			echo customException::cleanUp($e);
    		exit();
		}
    }
}