<?php

class genreflectionclass extends reflectionClass
{
	/**
	 * @var string class name
	 */
	public $classname = null;

	/**
	 * @var string
	 */
	public $fullDescription = "";

	/**
	 * @var string
	 */
	public $smallDescription = "";

	/**
	 * @var IPReflectionMethod[]
	 */
	public $methods = Array();

	/**
	 * @var IPReflectionProperty[]
	 */
	public $properties = Array();

	/**
	 * @var string
	 */
	public $extends;

	/**
	 * @var string
	 */
	private $comment = null;


	/**
	 * Constructor
	 *
	 * sets the class name and calls the constructor of the reflectionClass
	 *
	 * @param string The class name
	 * @return void
	 */
	public function __construct($classname)
	{
		$this->classname = $classname;
		try {
			parent::__construct($classname);
		}
		catch (Exception $e)
		{
			exit;
		}

		$this->parseComment();
	}

	/**
	 * Method to get an array of all the methods in a class
	 *
	 * @param boolean If the method should also return protected functions
	 * @param boolean If the method should also return private functions
	 * @return array genreflectionmethod
	 */
	public function getMethods($alsoProtected = true, $alsoPrivate = true)
	{
		$ar = parent::getMethods();
		foreach($ar as $method)
		{
			$m = new genreflectionmethod($this->classname, $method->name);
			if((!$m->isPrivate() || $alsoPrivate) && (!$m->isProtected() || $alsoProtected) && ($m->getDeclaringClass()->name == $this->classname))
			{
				$this->methods[$method->name] = $m;
			}
		}
		ksort($this->methods);
		return $this->methods;
	}

	/**
	 * Gets an array of all the properties in a class
	 *
	 * @param boolean If the method should also return protected properties
	 * @param boolean If the method should also return private properties
	 * @return array genreflectionproperty
	 */
	public function getProperties($alsoProtected=true,$alsoPrivate=true)
	{
		$ar = parent::getProperties();
		$this->properties = array();
		foreach($ar as $property)
		{
			if((!$property->isPrivate() || $alsoPrivate) && (!$property->isProtected() || $alsoProtected))
			{
				try{
					$p = new genreflectionproperty($this->classname, $property->getName());
					$this->properties[$property->name]=$p;
				}
				catch(ReflectionException $exception)
				{
					echo "Property Error: ".$property->name."<br>\n";
				}
			}
		}
		ksort($this->properties);
		return $this->properties;
	}

	/**
	 * Get the annotations
	 *
	 * @param $annotationName String the annotation name
	 * @param $annotationClass String the annotation class
	 * @return void
	 */
	public function getAnnotation($annotationName, $annotationClass = null)
	{
		return genphpdoc::getAnnotation($this->comment, $annotationName, $annotationClass);
	}

	/**
	 * Gets all the usefull information from the comments
	 *
	 * @param void
	 * @return void
	 */
	private function parseComment()
	{
		$this->comment = $this->getDocComment();
		new genreflectioncommentparser($this->comment, $this);
	}
}
?>