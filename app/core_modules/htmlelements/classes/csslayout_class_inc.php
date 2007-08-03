<?php

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");


/**
* The Css Layout class helps developers to display either two or three column layouts using CSS.
* The layouts are particular to the ones used in the KEWL.Nextgen system and works hand in hand with the stylesheet.
* Any additional layouts implemented in this class should also correspond with the stylesheet
*
* One of the problems with CSS Layouts is that it is difficult to control the height of columns.
* We have overcome this by using javascript. An article on this is available at:
* http://www.sitepoint.com/print/exploring-limits-css-layout
*
* A note on how the layouts look:
* The two column layout has a left side column and a broad middle column
* The three column layout has a left and right side column and a broad middle column
*
* NB! At present there is no accommodation for a two column layout with a broad
* middle column and a right side column
*
*
* @package   cssLayout
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   1
* @author    Tohir Solomons
* @example  
*            $cssLayout =& $this->newObject('csslayout', 'htmlelements');
*            $cssLayout->setNumColumns(3);
*            $cssLayout->setLeftColumnContent('Content in Left Column');
*            $cssLayout->setMiddleColumnContent('Content in Middle Column');
*            $cssLayout->setRightColumnContent('Content in Right Column');
*            echo $cssLayout->show();
*/
class csslayout extends object implements ifhtml
{

    /**
    * @var integer $numColumns: the number of columns the layout should have: either two or three
    */
    public $numColumns;

    /**
    * @var string $leftColumnContent: the contents of the left hand side column
    */
    public $leftColumnContent;

    /**
    * @var string $rightColumnContent: the contents of the right hand side column
    */
    public $rightColumnContent;

    /**
    * @var string $middleColumnContent: the contents of the middle column
    */
    public $middleColumnContent;

    /**
    * Constructor Method for the class
    *
    * This method sets the default number of columns to two, and sets the content of all the columns to have nothing.
    */
    public function init()
    {
        $this->numColumns = 2;
        $this->leftColumnContent = NULL;
        $this->rightColumnContent = NULL;
        $this->middleColumnContent = NULL;
    }

    /**
    * Method to set the number of columns the layout will have.
    *
    * We only cater for two or three column layouts as per the KEWL.Nextgen project.
    * This function first checks that the parameter is either two or three (for the columns) before assigning it to the variable.
    *
    * @param integer $number : The number of
    */
    public function setNumColumns($number)
    {
        if ($number == 2 OR $number == 3) {
            $this->numColumns = $number;
        }
    }

    /**
    * Method to set the content of the left column
    *
    * @param string $content : Content of the left hand side column
    */
    public function setLeftColumnContent($content)
    {
        $this->leftColumnContent = $content;
    }

    /**
    * Method to set the content of the right column
    *
    * @param string $content : Content of the right hand side column
    */
    public function setRightColumnContent($content)
    {
        $this->rightColumnContent = $content;
    }

    /**
    * Method to set the content of the middle column
    *
    * @param string $content : Content of the middle column
    */
    public function setMiddleColumnContent($content)
    {
        $this->middleColumnContent = $content;
    }
    
     /**
    * Method to return the JavaScript that runs when the page loads
    *
    * @access public
    * @return string
    */
    public function bodyOnLoadScript()
    {
        return 'xAddEventListener(window, "resize", adjustLayout, false);
          adjustLayout();';
    }

    /**
    * Method to return the JavaScript that fixes a two column css layout using javascript
    *
    * @access public
    * @return string $fixLayoutScript: the JavaScript that goes in the header
    */
    public function fixTwoColumnLayoutJavascript()
    {
        $fixLayoutScript ='
        <script type="text/javascript">

        function adjustLayout()
        {
             // Get natural heights  
            var cHeight = xHeight("contentcontent");  
            var lHeight = xHeight("leftcontent");  
            var rHeight = xHeight("rightcontent");  
            
            // Find the maximum height  
            var maxHeight =  
            Math.max(cHeight, Math.max(lHeight, rHeight));  
            
            // Assign maximum height to all columns  
            
            xHeight("content", maxHeight);  
            xHeight("left", maxHeight);  
            xHeight("right", maxHeight);  
        }

        </script>';

        return $fixLayoutScript;
    }

    /**
    * Method to return the JavaScript that fixes a three column css layout using javascript
    *
    * @access private
    * @return string  $fixLayoutScript: the JavaScript that goes in the header
    */
    public function fixThreeColumnLayoutJavascript()
    {
        $fixLayoutScript = '
        <script type="text/javascript">

        function adjustLayout()
        {
             // Get natural heights  
            var cHeight = xHeight("contentcontent");  
            var lHeight = xHeight("leftcontent");  
            var rHeight = xHeight("rightcontent");  
            
            // Find the maximum height  
            var maxHeight =  
            Math.max(cHeight, Math.max(lHeight, rHeight));  
            
            // Assign maximum height to all columns  
            
            xHeight("content", maxHeight);  
            xHeight("left", maxHeight);  
            xHeight("right", maxHeight);
        }

        </script>';

        return $fixLayoutScript;
    }

    /**
    * Show method - Method to display the layout
    * This method also places the appropriate javascript in the header
    *
    * @return string $result: the finished layout
    */
    public function show()
    {
        // Depending on the number of columns, load appropriate script to fix the column heights
        if ($this->numColumns == 2) {
            $this->putTwoColumnFixInHeader();
        } else {
            // else, load the three column javascript fix
            $this->putThreeColumnFixInHeader();
        }
		
        // Depending on the number of columns, use approprate css styles.
        if ($this->numColumns == 2) {
            $result = '
<div id="twocolumn">
	<div id="wrapper"> 
		<div id="content"> 
			<div id="contentcontent">
                '.$this->middleColumnContent.'
			</div>
		</div>
	</div>';
            $result .= '
    <div id="left"> 
		<div id="leftcontent">
		  '.$this->leftColumnContent.'
		</div>
	</div>
</div>';
        } else {
            // for a three column layout, first load the right column, then the middle column
            $result = '
<div id="threecolumn">
	<div id="wrapper"> 
		<div id="content"> 
			<div id="contentcontent">
                '.$this->middleColumnContent.'
			</div>
		</div>
	</div>';
            $result .= '
    <div id="left"> 
		<div id="leftcontent">
		  '.$this->leftColumnContent.'
		</div>
	</div>';
            $result .= '
    <div id="right"> 
		<div id="rightcontent">
            '.$this->rightColumnContent.'
		</div>
	</div>
</div>';
        }

        $str = $result;

        return $str;
    }



    /**
    * Method to load place a three column javascript fix into the header of a webpage
    * This method can also be used by other modules that just want to load the javascript fix - e.g. splash screen (prelogin)
    * @access public
    */
    public function putThreeColumnFixInHeader()
    {
        $headerParams=$this->getJavascriptFile('x.js','htmlelements');
        $headerParams .= $this->fixThreeColumnLayoutJavascript();
        $this->appendArrayVar('headerParams',$headerParams);
        $this->appendArrayVar('bodyOnLoad',$this->bodyOnLoadScript());
    }

    /**
    * Method to load place a two column javascript fix into the header of a webpage
    * This method can also be used by other modules that just want to load the javascript fix - e.g. splash screen (prelogin)
    * @access public
    */
    public function putTwoColumnFixInHeader()
    {
        $headerParams=$this->getJavascriptFile('x.js','htmlelements');
        $headerParams .= $this->fixTwoColumnLayoutJavascript();
        $this->appendArrayVar('headerParams',$headerParams);
        $this->appendArrayVar('bodyOnLoad',$this->bodyOnLoadScript());
    }

} // End Class




?>