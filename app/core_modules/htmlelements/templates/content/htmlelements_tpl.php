<script language="JavaScript" src="core_modules/htmlelements/resources/tabbedbox.js"></script>

<?php
//echo $strElements;
		$this->leftNav = $this->getObject('layer','htmlelements');
		$this->leftNav->id = "leftnav";
		$this->leftNav->str=$left;
		echo $this->leftNav->addToLayer();
		
$this->rightNav = $this->getObject('layer','htmlelements');
$this->rightNav->id = "rightnav";
$this->rightNav->str = $right;
echo $this->rightNav->addToLayer();
		
		$this->contentNav = $this->getObject('layer','htmlelements');
$this->contentNav->id = "content";
$this->contentNav->str = $content;
echo $this->contentNav->addToLayer();

		
		$this->footerNav = $this->getObject('layer','htmlelements');
		$this->footerNav->id = "footer";
		$this->footerNav->str = $bottom;
		echo $this->footerNav->addToLayer();
		


	
?>