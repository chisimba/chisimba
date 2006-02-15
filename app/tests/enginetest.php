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
        $observer = $this->getMock('_dbconfig','config');
    }

    //test that the security is in place...
    public function testSecurityString()
    {
        $this->assertNotNull($this->security);
    }

    //test for loadclass
 //   public function testLoadClass()
   // {
     //   $this->assertNotNull($this->eng->loadClass($observer));
    //}

    //test for run
    public function testRun()
    {
        $this->assertNotNull($this->eng->run());
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

    //test to get layout content page
    public function testGetLayoutContent()
    {
        $this->assertNotNull($this->eng->getLayoutTemplate());
    }

    //test to  setLayoutTemplate
    public function testSetLayoutTemplate()
    {
        $this->assertNotNull($this->eng-> setLayoutTemplate());
    }

    //test to getPageTemplate
    public function testGetPageTemplate()
    {
        $this->assertNotNull($this->eng->getPageTemplate());
    }

    //test to setPageTemplate
    public function testSetPageTemplate()
    {
        $this->assertNotNull($this->eng->setPageTemplate());
    }

    //test for newObject
    public function testNewObject()
    {
        $this->assertNotNull($this->eng->newObject());
    }

    //test for getObject
    public function testGetObject()
    {
        $this->assertNotNull($this->eng->getObject());
    }

    //test for getVar
    public function testGetVar()
    {
        $this->assertNotNull($this->eng->getVar());
    }

    //test for setVar
    public function testSetVar()
    {
        $this->assertNotNull($this->eng->setVar());
    }

    //test for getVarByRef
    public function testGetVarByRef()
    {
        $this->assertNotNull($this->eng->getVarByRef());
    }

    //test for setVarByRef
    public function testSetVarByRef()
    {
        $this->assertNotNull($this->eng->setVarByRef());
    }

    //test for appendArrayVar
    public function testAppendArrayVar()
    {
        $this->assertNotNull($this->eng->appendArrayVar());
    }

    //test for getParam
    public function testGetParam()
    {
        $this->assertNotNull($this->eng->getParam());
    }

    //test for getArrayParam
    public function testGetArrayParam()
    {
        $this->assertNotNull($this->eng->getArrayParam());
    }

    //test to set the session
    public function testSetSession()
    {
        $this->session = $this->eng->setSession();
        $this->assertNotNull($this->session);
    }

    //test to get the session
    public function testGetSession()
    {
        $this->session = $this->eng->getSession();
        $this->assertNotNull($this->session);
    }

    //test to unset the session
    public function testUnsetSession()
    {
        $this->session = $this->eng->unsetSession();
        $this->assertNull($this->session);
    }

    //test to start the session
    public function testSessionStart()
    {
        $this->session = $this->eng->sessionStart();
        $this->assertNotNull($this->session);
    }

    //test for error message
    public function testSetErrorMessage()
    {
        $this->assertNotNull($this->eng->setErrorMessage());
    }

    public function testAddMessage()
    {
        $this->assertNotNull($this->eng->addMessage());
    }

    public function testNextAction()
    {
        $this->assertNotNull($this->eng->nextAction());
    }

    public function testUri()
    {
        $this->assertNotNull($this->eng->uri());
    }

    public function testGetResourceUri()
    {
        $this->assertNotNull($this->eng->getResourceUri());
    }

    public function testGetJavaScriptFile()
    {
        $this->assertNotNull($this->eng->getJavaScriptFile());
    }

    public function testPutMessages()
    {
        $this->assertNotNull($this->eng->putMessages());
    }
}
?>