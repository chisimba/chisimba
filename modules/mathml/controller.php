<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the mathml stuff
*
* @author Paul Scott
* @package mathml
*
* @version $Id: controller.php 5223 2006-12-23 02:19:06Z tohir $
* @copyright 2005 UWC
*
*/
class mathml extends controller
{

    /**
    * @var string $action The action parameter from the querystring 
    */
    public $action;
    public $sym;
    public $maths;
    public $ml;

    /**
    * Standard constructor method 
    */
    public function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        //get the mathml parser class
        $this->objMaths = $this->getObject("mathmlparser","mathml");
        $this->objMathImg = $this->getObject("mathimg","mathml");
        
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * Standard dispatch method 
    */
    public function dispatch()
    {
        switch ($this->action) {
            case null:
            case "test":
                $expression = $this->getParam('expression');
                If(!isset($expression))
                {
            		$expression = " 5 < a S(f)(t)=a_{0}+sum{n=1}{+infty}{a_{n} cos(n omega t)+b_{n} sin(n omega t)}";
            		//$expression = "x+y=z";
            		//$expression = "int_-1^1 sqrt(1-x^2) = pi/2";
                }
				
                $ar = $this->objMaths->mathmlreturn($expression);
                // Parse the MathML
                
                $image = $this->objMathImg->render($expression);
                
				$this->setVarByRef('ar', $ar);
				$this->setVarByRef('image', $image);
                return "main_tpl.php";
                break;
			case 'render':
				$formula = $this->getParam('formula','');
				$this->setVar('str',$this->objMaths->mathmlreturn($formula));
				$this->setPageTemplate('xml_tpl.php');
				return "xml_tpl.php";
        }
    }
}