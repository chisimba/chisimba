<?php

/**
* HTML control class to create multiple tabbed boxes using the layers class.
* The style sheet class is >box<.
*
* UPDATE: This class now uses the tabcontent object instead, to have uniformity with the CSS
*
* @abstract
* @package tabs
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Prince Mbekwa
* @example
* $objElement =new tabpane(100,500);

* $objElement->addTab(array('name'=>'Second','url'=>'http://localhost','content' => $check.$radio.$calendar));
* $objElement->addTab(array('name'=>'First','url'=>'http://localhost','content' => $form));
* $objElement->addTab(array('name'=>'Third','url'=>'http://localhost','content' => $tab,'height' => '300','width' => '600'));

*
*/
class tabpane extends object
{
	/**
	 * Width Adjustment
	 * @var $width string : width of all the tabs
	*/
	var $width='98%';
    
    /**
     * Height Adjustment
     * @var $height string :  the height all the tabs
    */
    var $height;


	/**
	* Constuctor
	*/

	function init()
	{
        $this->objTabContent = $this->newObject('tabcontent');
    }

	/**
	* Method that addes a tab
	* @param $properties array : Can hold the following values
	* name string
	* content string
	*/
    function addTab($properties=NULL,$css='winclassic-tab-style-sheet')
    {
		if (is_array($properties)) {
			$link =null;
			if (isset($properties['name'])) {
				$label = $properties['name'];
				if(isset($properties['content'])) {
					$content=$properties['content'];
                } else {
                    $content = '';
                }

			}
            
            $this->objTabContent->addTab($label, $content);
		}
	}


	/**
	* Method to show the tabs
	* @return $str string
	*/
	function show()
    {
		// Set Width
        if ($this->width != '') {
            $this->objTabContent->width = $this->width;
        }
        
        // Set Height
        if ($this->height != '') {
            $this->objTabContent->height = $this->height;
        }
        
        return $this->objTabContent->show();
	}
}
?>