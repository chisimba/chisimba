<?php
//include the PHPUnit test code
require_once 'PHPUnit2/Framework/TestCase.php';
require_once '../modules/config/classes/altconfig_class_inc.php';

//Set up the class to do the tests
class configTest extends PHPUnit2_Framework_TestCase
{
   	protected $fixture;

    public function setUp()
    {
        //create the fixture
        $fixture = new altconfig();
        
    }
    //test for instance of fixture	
    public function testFixture()
    {
    	//assert that the fixture is NULL
        return $this->assertEquals(true, $this->fixture);
    }
    //test for write config
    protected function testwriteConfig()
    {
    	return $this->assertNotNull($this->fixture->writeConfig('test','XML'));
    }
    //test for read properties
    public function testreadProperties()
    {
    	return $this->assertNotNull($this->fixture->readProperties('XML'));
    }
    //test for write properties
    public function testwriteProperties()
    {
    	return $this->assertNotNull($this->fixture->writeProperties('test','XML'));
    }
    //TEST FOR INSERT PARAMS
    public function testinsertParam()
    {
     	return $this->assertNotNull($this->fixture->insertParam('test', 'Test_module', 'Test_value',false));
    }
    //test for get params
    public function testgetParam()
    {
    	return $this->assertNotNull($this->fixture->getParam('Test_value', 'Test_module'));
    }
    //test for get sitename
    public function testgetSiteName()
    {
    	return $this->assertNotNull($this->fixture->getSiteName());
    	
    }
    //test for set sitename
   public function testsetSiteName()
   {
   	return $this->assertNotNull($this->fixture->setSiteName("test"));
   	
   }
   //test for getinstitutionshortname
   public function getinstitutionShortName()
   {
   		return $this->assertNotNull($this->fixture->getinstitutionShortNam());
   }

}
?>