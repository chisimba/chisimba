<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Utilitie Class for the Flag System
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*/

    class flagutils extends object
    {

        /**
        * The user object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

        /**
        * The language object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;

        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objFlagEmail = & $this->getObject('dbflagemail', 'cmsadmin');
                $this->_objFlagOption = & $this->getObject('dbflagoptions', 'cmsadmin');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objSysConf = $this->getObject('dbsysconfig', 'sysconfig');
                $this->_objConf = $this->getObject('altconfig', 'config');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to email an email
         *
         * @param string $title Title of the announcement
         * @param string $message Message of the announceme nt
         * @param array $recipients List of Recipients (array of email addresses);
         */
        private function sendEmail($title, $message, $recipients)
        {
            $recipients = array_unique($recipients);

            $objMailer = $this->getObject('mailer', 'mail');
            $objMailer->setValue('from', $this->_objUser->email());
            $objMailer->setValue('fromName', $this->_objUser->fullname());
            $objMailer->setValue('subject', $title);
            $objMailer->setValue('bcc', $recipients);
            $objMailer->setValue('body', $message);

            return $objMailer->send(TRUE);
        }


        /**
         * Method to send email alerts when content is flagged.
         * @access public
         * @return boolean True on success, False on failure
         */
        public function sendEmailAlerts($contentId, $optionId)
        {
            $arrEmails = $this->_objFlagEmail->getAll();
            
            $subject = $this->_objLanguage->languageText('mod_cmsadmin_flag_email_subject', 'cmsadmin');
            $reason = $this->_objFlagOption->getOption($optionId);
            $subject .= ' ' . $reason['text'];
            
            $siteUrl = $this->_objConf->getsiteRoot();
            
            foreach ($arrEmails as $mail) {
                $message = 'Dear ' . $mail['name'] . "\na Content item has been flagged";
                $message .= "\nReason: $reason[text]";
                $message .= "\nPlease open this url in your browser and review it's contents:\n";
                $message .= "\n{$siteUrl}?module=cms&action=showfulltext&id={$contentId}";
                
                $result = $this->sendEmail($subject, $message, array($mail['email']));

                return $result;
                
            }

            return TRUE;
        }




	}

?>
