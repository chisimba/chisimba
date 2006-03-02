<?PHP
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
require_once "lib/logging.php";

/**
 * Class to encapsulate operations via a SOAP interface.
 * It is highly recommended that you create a derived version
 * of this class for each transactional layer, rather than using it directly.
 *
 * @author Paul Scott
 * @example ./examples/soap.eg.php The example
 * TODO: Finish this class.
 *
 * $Id$
 */

class soapcontroller extends object
{

}
?>