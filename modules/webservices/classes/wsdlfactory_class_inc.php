<?php
class wsdlfactory extends object
{
	public $properties;
	public $methods;

	public function init()
	{
		$this->loadClass('genreflectionclass');
		$this->loadClass('genreflectioncommentparser');
		$this->loadClass('genreflectionproperty');
		$this->loadClass('genreflectionmethod');
		$this->loadClass('wshelper');
		$this->loadClass('tagregistration');
		$this->loadClass('xsltemplate');
		$this->loadClass('wsdlstruct');
		$this->loadClass('genxmlschema');

	}

	public function reflect($class)
	{
		$rel = new genreflectionclass($class);
		$this->properties = $rel->getProperties();
		$this->methods = $rel->getMethods();
		return $rel;
	}




}

?>