<?PHP

//include the PHPUnit test code
require_once 'PHPUnit2/Framework/TestCase.php';

//Set up the class to do the tests
class DatabaseTests extends PHPUnit2_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = $GLOBALS['kewl_entry_point_run'] = true;
    }
    public function testDBConnIsValid()
    {
        $this->assertEquals(true, $this->connection);

    }
}