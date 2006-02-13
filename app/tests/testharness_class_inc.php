<?php
/**
 * This is a Test harness file that all unit tests will be added to
 * This file will be run very regularly via cron
 * @author Paul Scott
 */
require_once "PHPUnit2/Framework/TestSuite.php";

class testHarness extends PHPUnit2_Framework_TestSuite
{
	private $seen = array();
	private $parent;

	public function __construct()
	{
		$this->parent = parent::__construct();
		foreach(get_declared_classes() as $class)
		{
			$this->seen[$class] = 1;
		}
	}

	public function register($file)
	{
		require_once($file);

		foreach( get_declared_classes() as $class)
		{
			if(array_key_exists($class, $this->seen))
			{
				continue;
			}

			$this->seen[$class] = 1;

			//The Zend Engine lower cases class names, so we look for testcase
			if(substr($class, -8, 8) == 'test')
			{
				print "adding $class\n";
				$this->addTestSuite($class);
			}
		}
	}
}
?>
