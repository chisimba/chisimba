<?PHP

/**
 * Class to handle errors within the framework
 *
 * @author Paul Scott
 * @copyright
 */

require_once('lib/pear/PEAR.php');
class errorhandler extends PEAR
{
    public $error;
    public $handleErr;

    public function __construct($errstr, $errno, $object)
    {
        $error = new PEAR_Error($errstr, $errno);

        if (PEAR::isError($object))
        {
            $this->error = $object->getMessage();
        }
    }

    public function handleError()
    {
        return $this->error;
    }

}
?>