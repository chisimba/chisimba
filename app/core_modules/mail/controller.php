<?php
/* security check - must be included in all scripts */
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
/* end security check */

/**
* Simple demonstration module
* @author  yourname here
* @package yourpackage here
*/
class mail extends controller
{
    /**
    *
    * Standard init method for KINKY controller
    *
    */
    function init()
    {

    }

    /**
    *
    * Dispatch method
    *
    */
    function dispatch()
    {
        $bodyText = "Test email text";
        $this->setVar('str', 'There is no end user functionality in this module');
        $objMailer = $this->getObject('email', 'mail');
        $objMailer->setValue('to', array('pscott@uwc.ac.za'));
        $objMailer->setValue('from', 'elearning@uwc.ac.za');
        $objMailer->setValue('fromName', 'The E-Learning Team');
        $objMailer->setValue('subject', 'Email in Chisimba');
        $objMailer->setValue('cc', array('joe@soap.com', 'tom@thumbalina.com'));
        $objMailer->setValue('bcc', 'someone@someplace.com');
        $objMailer->setValue('body', $bodyText);
        $objMailer->attach('/var/www/app/config/config_inc.php',
          'config_inc.php');
        $objMailer->attach('/var/www/app/index.php');
        if ($objMailer->send()) {
           echo "success ";
        } else {
           echo "failed";
        }
        return "dump_tpl.php";
    }
}
?>