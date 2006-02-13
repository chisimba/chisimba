<?php
//include the PHPUnit test code
require_once 'PHPUnit2/Framework/TestCase.php';

//Set up the class to do the tests
class ExampleTest extends PHPUnit2_Framework_TestCase
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

    public function testExample()
    {
        //create the fixture
        $fixture = NULL;
        //assert that the fixture is NULL
        $this->assertEquals(NULL, $fixture);
    }

}
?>