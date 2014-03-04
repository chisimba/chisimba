<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object contains forms export methods for processing and sending data via
 * email, pdf, csv and other reporting mechanisms
 *
 * @package forms
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert
 */

class export extends object
{
    /**
    * The User object
    *
    * @access private
    * @var object
    */
    protected $_objUser;

    /**
    * The config object
    *
    * @access private
    * @var object
    */
    protected $_objConfig;

    /**
    * The blocks object
    *
    * @access private
    * @var object
    */
    protected $_objBlocks;

    /**
    * Feature box object
    *
    * @var object
    */
    public $objFeatureBox;

    /**
    * The security object
    *
    * @access public
    * @var object
    */
    public $_objSecurity;

    /**
    * Class Constructor
    *
    * @access public
    * @return void
    */
    public function init()
    {
        try {
            $this->_objQuery =  $this->newObject('jquery', 'jquery');
            $this->_objConfig =$this->newObject('altconfig', 'config');
            $this->_objSysConfig =$this->newObject('dbsysconfig', 'sysconfig');
            $this->objSkin =$this->newObject('skin', 'skin');
            $this->_objUser =$this->newObject('user', 'security');
            // $this->_objUserModel =$this->newObject('useradmin_model','security');
            $this->objLanguage =$this->newObject('language', 'language');
            $this->_objContext =$this->newObject('dbcontext', 'context');
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->objModule=&$this->getObject('modules','modulecatalogue');
            $this->objDateTime = $this->getObject('dateandtime', 'utilities');

            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('radio', 'htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $this->loadClass('hiddeninput', 'htmlelements');
            $this->loadClass('textarea','htmlelements');
            $this->loadClass('htmltable','htmlelements');
            $this->loadClass('layer', 'htmlelements');

        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }

    /**
     * Method to send an email message
     *
     * @param string $title Title of the announcement
     * @param string $message Message of the announceme nt
     * @param array $recipients List of Recipients (array of email addresses);
     */
    private function sendEmail($to, $from, $subject, $message, $recipients = '')
    {

        $objMailer = $this->getObject('mailer', 'mail');
        $objMailer->setValue('from', $from);
        $objMailer->setValue('to', $to);
        $objMailer->setValue('subject', $subject);

        if ($recipients != ''){
            $recipients = array_unique($recipients);
            $objMailer->setValue('cc', $recipients);
        }

        $objMailer->setValue('body', $message);

        $objMailer->send(TRUE);
    }



}

?>