<?php
require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/Extensions/TestSetup.php';

class DatabaseTestSetup extends PHPUnit2_Extensions_TestSetup
{
    protected $connection = NULL;

    protected function setUp()
    {
        $this->connection = new PDO(
          'mysql:host=localhost;dbname=nextgen',
          'root',
          ''
        );
    }

    protected function tearDown()
    {
        $this->connection = NULL;
    }

    public static function suite()
    {
        return new DatabaseTestSetup(
          new PHPUnit2_Framework_TestSuite('DatabaseTests')
        );
    }
}
?>