<?php
//include the PHPUnit test code
require_once 'PHPUnit2/Framework/TestCase.php';

//Set up the class to do the tests
class arrayTest extends PHPUnit2_Framework_TestCase
{
    /**
     * Method to do the test
     *
     *@example
     * public function testNewArrayIsEmpty()
     * {
     *   // Create the Array fixture.
     *   $fixture = Array();
     *
     *   // Assert that the size of the Array fixture is 0.
     *   $this->assertEquals(0, sizeof($fixture));
     * }
     */

    public function testArray()
    {
        //create the fixture
        $fixture = array();
        //assert that the fixture is NULL
        $this->assertEquals(0, $fixture);
    }

    public function testArrayNotEmpty()
    { 
        //create the fixture
        $fixture = array();
        $fixture[] = "Element";
        $this->assertEquals(1,$fixture);
    }

}
?>
