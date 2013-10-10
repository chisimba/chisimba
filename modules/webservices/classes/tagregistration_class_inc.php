<?php
class tagregistration
{
	public $namespace;
	public $tagName;
	public $function;

	public function __construct($namespace, $tagName, $function)
	{
		$this->namespace 	= $namespace;
		$this->tagName 		= $tagName;
		$this->function 	= $function;
	}

	public function process($node)
	{
		$x = $node;
		eval($this->function);
	}
}
?>