<?PHP

require_once 'PHPUnit2/Framework/TestCase.php';
require_once '../classes/core/engine_class_inc.php';

class engineTest extends PHPUnit2_Framework_TestCase
{
    private $eng;
    protected $security = array();
    private $session;

    protected function setUp()
    {
        $this->security[] = 'kewl_entry_point_run';
        $this->eng = new engine;
    }

    //test that the security is in place...
    public function testSecurityString()
    {
        $this->assertNotNull($this->security);
    }

    //test to set the session
    public function testSetSession()
    {
        $this->session = $this->eng->setSession();
        $this->assertNotNull($this->session);
    }

    //test for loadclass
    public function testLoadClass()
    {
        $this->assertNotNull($this->eng->loadClass());
    }

    //test that the db Object gets instantiated
    public function testGetDbObj()
    {
        $this->assertNotNull($this->eng->getDbObj());
    }

    //test to get content page
    public function testGetContent()
    {
        $this->assertNotNull($this->eng->getContent());
    }










}