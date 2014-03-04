<?php
include("mathmlsymbols_class_inc.php");
include('asciimathphp_class_inc.php');

/**
 * Wrapper class for mathml
 * @author Paul Scott
 */
class mathml //extends object
{
	public function init()
	{
		$this->sym = new mathmlsymbols();
		$this->ascii_math =& new asciimathphp($this->sym->symbols()); 
		
	}
	
	public function mathmlreturn($expr)
	{
		$this->ascii_math->setExpr($expr); 
		$this->ascii_math->genMathML();
		return $this->ascii_math->getMathML();
	}
}

$m = new mathml;
$m->init();
echo $m->mathmlreturn("sum x+y");
?>