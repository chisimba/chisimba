<?php
class football_manager extends controller 
{
	function init()
	{
		$this->message = "";
		$this->objDb = $this->getObject("db");
		$this->objMenu = $this->getObject("nav");
	}

	function dispatch($action)
	{
		switch ($action)
	   	{
			case 'menu':    $title = $this->getParam("menutitle");
                                        $this->setVarByRef("title",$title);
					return "home_tpl.php";
			case 'submitform': return $this->evaluateForm();
			case 'add':    $title="Add Players";
                                        $this->setVarByRef("title",$title);
                                        return "addplayers_tpl.php";

			case 'view':    $title="View Players";
                                        $this->setVarByRef("title",$title);
                                        return "viewplayers_tpl.php";
			case 'search': return 'search_tpl.php';
			case 'result':	return 'result_tpl.php';
			default:$title="Footballer Management System";
				$this->setVarByRef("title",$title); 
				return "home_tpl.php";
		}
	}

	function evaluateForm() {

		//--Defining parameters from form
		$fn = $this->getParam("firstname");
		$ln = $this->getParam("lastname");
		$age = $this->getParam("age");
		$pos = $this->getParam("position");
		$fee = $this->getParam("transferfee");
		$other = $this->getParam("other");
		$status = $this->getParam("status");

	       //-----Writing to the table/DB...
	       $this->objDb->addInfo($fn,$ln,$age,$pos,$fee,$other,$status);
               $this->message = "Information saved successfully.";
               return "home_tpl.php";
	}
}
?>
