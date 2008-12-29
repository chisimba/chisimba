<?php
/* -------------------- imap class ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

//do the check for the IMAP C extension
if(!extension_loaded("imap"))
{
    //die("This module requires the IMAP C Extension to be loaded, please consult your PHP manual or system administrator to enable it!");
    log_debug("This module requires the IMAP C Extension to be loaded, please consult your PHP manual or system administrator to enable it!");
}

/**
 * This is a full(ish) implementation of the C-client IMAP extension for PHP5 and above.
 * It is written in native PHP5.
 * Most of the IMAP functions are available, except some of the aliases, these were left out due to imminemt deprecation
 * by the PHP team. This class allows you to send and receive mail by one of 3 protocols:
 * <pre>
 * <li> POP 3 </li>
 * <li> NNTP </li>
 * <li> or IMAP </li>
 * </pre>
 *
 * The class incorporates functionality that can be used to easily create a webmail application
 *
 * @author    Paul Scott
 * @package   webmail
 * @copyright AVOIR
 * @category  Chisimba
 *            
 */
class imap //extends object
{
    /**
     * Property to hold the server that you want to connect to (NNTP, POP, IMAP)
     *
     * @var string
     */
    public $server;

    /**
     * The user (mail user)
     *
     * @var string
     */
    public $user;

    /**
     * Password used for server authentication
     *
     * @var string
     */
    public $pass;

    /**
     * The protocol that you would like to connect with (IMAP, NNTP, POP...)
     *
     * @var string
     */
    public $protocol;

    /**
     * The name of your mailbox (INBOX, INBOX.paul, INBOX.Sent...)
     *
     * @var string
     */
    public $mailbox;

    /**
     * The port at which you will connect to the server
     * <pre>
     * <li>POP = 110 </li>
     * <li>IMAP = 143 </li>
     * </pre>
     *
     * @var unknown_type
     */
    public $port;

    /**
     * An overview of your mail box
     *
     * @var object
     */
    public $overview;

    /**
     * IMAP Server generated alerts (Mailbox full etc)
     *
     * @var array
     */
    public $alerts;

    /**
     * Default DSN for the IMAP server
     * Should be in the form of imap://user:pass@server/mailbox
     *
     * @var string | array
     */
    public $imapdsn = array(
    'imapserver'  => false,
    'imapuser' => false,
    'imappass' => false,
    'imapprotocol' => false,
    'imapport'     => false,
    'imapmailbox' => false,
    );

    /**
     * The IMAP connection resource stream
     *
     * @var object
     */
    private $conn;

    /**
     * Mail headers for the connection
     *
     * @var StdObject
     */
    private $headers;

    /**
     * Number of emails gained from the current overview
     *
     * @var integer
     */
    private $numEmails;

    /**
     * All of the mail headers from the current overview
     *
     * @var StdObject
     */
    private $mailHeader;

    /**
     * The full current DSN
     *
     * @var string
     */
    private $currdsn;

    /**
     * Standard init method
     *
     * @param void
     * @return void
     */
    public function init()
    {
    }

    /**
     * Factory method to instantiate and build the class(es)
     *
     * @param string $dsn
     * @return array $alerts on alert | void
     * @access public
     */
    public function factory($dsn)
    {
        $this->setconn($dsn);
        $this->connect();
        if($this->alerts)
        {
            return $this->alerts;
        }
        else {
            return TRUE;
        }
    }

    /**
     * Method to set the connection and parse the DSN
     *
     * @param string $dsn
     * @access private
     * @return void
     */
    private function setconn($dsn)
    {
        $this->currdsn = $dsn;
        $conarr = $this->parseDSN($dsn);
        //print_r($conarr);
        $this->server = $conarr['imapserver'];
        $this->user = $conarr['imapuser'];
        $this->pass = $conarr['imappass'];
        $this->protocol = $conarr['imapprotocol'];
        $this->mailbox = $conarr['imapmailbox'];
        $this->port = $conarr['imapport'];
    }

    /**
     * Method to connect to the server
     *
     * @return array alerts | bool
     * @param void
     * @access private
     */
    private function connect()
    {
        $this->conn = @imap_open("{".$this->server. ":" . $this->port . "/" . $this->protocol . "}" . $this->mailbox, $this->user, $this->pass);
        if(!$this->conn)
        {
            throw new customException("Could not connect to " . $this->protocol . " Server at " . $this->server . " on port " . $this->port . " using mailbox " . $this->mailbox);
        }
        else {
            //get any alerts (like mailbox full or something)
            $this->alerts = imap_alerts();
            //the alerts will return false on no alerts
            if ($this->alerts == FALSE)
            {
                return TRUE;
            }
            else {
                return $this->alerts;
            }
        }
    }

    /**
     * Method to create an RFC822 formatted email address eg Paul Scott <pscott@uwc.ac.za>
     *
     * @access public
     * @param string $user - pscott
     * @param string $domain - uwc.ac.za
     * @param string $name - Paul Scott
     * @return email address
     */
    public function setAddress($user, $domain, $name)
    {
        return imap_rfc822_write_address($user, $domain, $name);
    }

    /**
     * Method to check the status of a mailbox
     * Will return info like:
     * Number of unread mails
     * Number of new mails
     * Number of recent mails
     * number of total messages
     * Size in bytes
     * Date
     * Driver used
     * Mailbox used
     * etc...
     *
     * @access public
     * @param void
     * @return stdObject
     */
    public function checkMailboxStatus()
    {
        $check = imap_mailboxmsginfo($this->conn);
        return $check;
    }

    /**
     * Management function to get the ACL's listed on the server
     *
     * @param void
     * @access public
     * @return stdObject
     */
    public function getACL()
    {
        return @imap_getacl($this->conn, $this->mailbox);
    }

    /**
     * Public method to ping the server in order to keep the connection alive
     * If the server has disconnected, it will reconnect, else return the connection true
     *
     * @access public
     * @param void
     * @return unknown
     */
    public function pingServer()
    {
        if(!(imap_ping($this->conn)))
        {
            return $this->connect();
        }
        else {
            return TRUE;
        }
    }

    /**
     * Management function to list the mailboxes on the server
     *
     * @param void
     * @return stdObject
     * @access public
     */
    public function listMailBoxes()
    {
        $list = imap_getmailboxes($this->conn, "{$this->server}", "*");
        if (is_array($list)) {
               return $list;
            //foreach ($list as $key => $val) {
               //echo "($key) ";
               //echo imap_utf7_decode($val->name) . ",";
               //echo "'" . $val->delimiter . "',";
               //echo $val->attributes . "<br />\n";
           //}
        } else {
               return FALSE;
        }
    }

    /**
     * Method to get the quota limits asssociated with the mailbox and user
     * This is not implemented on the test IMAP server, so it is untested
     * Please could someone test this method on a fully implemented IMAP server and let me know?
     *
     * @access public
     * @param void
     * @return stdObject
     */
    public function getQuotas()
    {
        $quota_values = @imap_get_quotaroot($this->conn, $this->mailbox);
        if (is_array($quota_values)) {
               return $quota_values;
        }
        else {
            return FALSE;
        }
        //    $storage = $quota_values['STORAGE'];
           //    echo "STORAGE usage level is: " .  $storage['usage'];
           //    echo "STORAGE limit level is: " .  $storage['limit'];

           //    $message = $quota_values['MESSAGE'];
           //    echo "MESSAGE usage level is: " .  $message['usage'];
           //    echo "MESSAGE limit is: " .  $message['limit'];

    }

    /**
     * Method to get the headers from the current mailbox stream
     *
     * @access public
     * @param void
     * @return stdClass object
     */
    public function getHeaders()
    {
        $this->headers = imap_headers($this->conn);
        return $this->headers;
    }

    /**
     * Method to glean slightly more information about the headers
     * of a PARTICULAR message.
     *
     * @access public
     * @param integer $messageNum - UID of the message
     * @return stdClass
     */
    public function getHeaderInfo($messageNum)
    {
        $this->mailHeader = @imap_headerinfo($this->conn, $messageNum);
        $headers = $this->mailHeader;
        return $headers;
        /*
        $from = $this->mailHeader->fromaddress;
        $subject = strip_tags($this->mailHeader->subject);
        $date = @$this->mailHeader->date;
        */
    }

    /**
     * Method to count the number of mails in the mailbox
     *
     * @param void
     * @access public
     * @return integer
     */
    public function numMails()
    {
        return sizeof($this->headers);
    }

    /**
     * Method to check the mailbox. Will return a stdObject of all the mail headers with size, date, to, from, etc
     * as well as the message flags such as read, seen, replied, deleted etc
     *
     * @access public
     * @param void
     * @return stddClass object
     */
    public function checkMbox()
    {
        $this->overview = imap_check($this->conn);
        $nummsgs = $this->overview->Nmsgs;
        $overview = imap_fetch_overview($this->conn,"1:$nummsgs",0);
        return $overview;
    }

    /**
     * Method to retrieve a specific message according to the message number (UID)
     *
     * @access public
     * @param integer $messageNum
     * @return array + array of attachments
     */
    public function getMessage($messageNum)
    {
        //fetch the structure
        $struct = imap_fetchstructure($this->conn, $messageNum);
        if(isset($struct->parts))
        {
            $parts = $struct->parts;
        }
        else {
            $parts = NULL;
        }
        $i = 0;
        if (!$parts) {
            // Simple message, only 1 piece
            $attachment = array();
            // No attachments
            $content = @imap_body($this->conn, $messageNum);
        } else {
            // Complicated message, multiple parts
            $endwhile = false;
            // Stack while parsing message
            $stack = array();
            // Content of message
            $content = "";
            // Attachments
            $attachment = array();
            while (!$endwhile) {
                if (!isset($parts[$i])) {
                    if (count($stack) > 0) {
                        $parts = $stack[count($stack)-1]["p"];
                        $i    = $stack[count($stack)-1]["i"] + 1;
                        array_pop($stack);
                    } else {
                        $endwhile = true;
                    }
                }

                if (!$endwhile) {
                    // Create message part first (example '1.2.3')
                    $partstring = "";
                    foreach ($stack as $s) {
                        $partstring .= ($s["i"]+1) . ".";
                    }
                    $partstring .= ($i+1);
                    if(!isset($parts[$i]->disposition))
                    {
                        $parts[$i]->disposition = NULL;
                    }
                    // Attachment
                    if (strtoupper($parts[$i]->disposition) == "ATTACHMENT") {
                        //var_dump($parts[$i]->dparameters[0]->value);

                        $at = $parts[$i]->parameters;
                        //var_dump($at);
                        //echo "<hr><br />";
                        if(is_array($at))
                        {
                            if(is_object($at[0]))
                            {
                                $filename = $at[0]->value;
                                //echo "<h1>" . $filename . "</h1><br>";
                            }

                        }
                        if(isset($parts[$i]->dparameters[0]))
                        {
                            $filename = $parts[$i]->dparameters[0]->value;
                            //echo "<h1>" . $filename . "</h1>";
                        }

                        else {

                            if(!empty($parts[$i]->parameters->value))
                            {
                                $filename = $parts[$i]->parameters->value;
                            }
                            else {
                                $filename = NULL;
                                break;
                            }
                        }
                        $filedata = imap_fetchbody($this->conn, $messageNum, $partstring);
                        $attachment[] = array("filename" => $filename,
                        "filedata" => $filedata);
                        // Message
                    } elseif (strtoupper($parts[$i]->subtype) == "PLAIN") {
                        $content .= imap_fetchbody($this->conn, $messageNum, $partstring);
                    }
                }

                if (isset($parts[$i]->parts)) {
                    $stack[] = array("p" => $parts, "i" => $i);
                    $parts = $parts[$i]->parts;
                    $i = 0;
                } else {
                    $i++;
                }
            }
        }

        $messagearr = array($content, $attachment);
        return $messagearr;
    }

    /**
     * Method to parse the DSN
     *
     * @access private
     * @param string $dsn
     * @return void
     */
    private function parseDSN($dsn)
    {
        $parsed = $this->imapdsn;
        $arr = NULL;
        if (is_array($dsn)) {
            $dsn = array_merge($parsed, $dsn);
            return $dsn;
        }
        //find the protocol
        if (($pos = strpos($dsn, '://')) !== false) {
            $str = substr($dsn, 0, $pos);
            $dsn = substr($dsn, $pos + 3);
        } else {
            $str = $dsn;
            $dsn = null;
        }
        if (preg_match('|^(.+?)\((.*?)\)$|', $str, $arr)) {
            $parsed['imapprotocol']  = $arr[1];
            $parsed['imapprotocol'] = !$arr[2] ? $arr[1] : $arr[2];
        } else {
            $parsed['imapprotocol']  = $str;
            $parsed['imapprotocol'] = $str;
        }

        if (!count($dsn)) {
            return $parsed;
        }
        // Get (if found): username and password
        if (($at = strrpos($dsn,'@')) !== false) {
            $str = substr($dsn, 0, $at);
            $dsn = substr($dsn, $at + 1);
            if (($pos = strpos($str, ':')) !== false) {
                $parsed['imapuser'] = rawurldecode(substr($str, 0, $pos));
                $parsed['imappass'] = rawurldecode(substr($str, $pos + 1));
            } else {
                $parsed['imapuser'] = rawurldecode($str);
            }
        }

        //server
        if (($col = strrpos($dsn,':')) !== false) {
            $strcol = substr($dsn, 0, $col);
            $dsn = substr($dsn, $col + 1);
            if (($pos = strpos($strcol, '/')) !== false) {
                $parsed['imapserver'] = rawurldecode(substr($strcol, 0, $pos));
            } else {
                $parsed['imapserver'] = rawurldecode($strcol);
            }
        }

        //now we are left with the port and mailbox so we can just explode the string and clobber the arrays together
        $pm = explode("/",$dsn);
        $parsed['imapport'] = $pm[0];
        $parsed['imapmailbox'] = $pm[1];
        $dsn = NULL;

        return $parsed;
    }

    /**
     * Method to close the connection
     *
     * @access public
     * @return bool
     */
    public function close()
    {
        @imap_close($this->conn);
        return TRUE;
    }

    /**
     * Method to populate the Inbox, Sentitems and Trash
     * It will return a multidimensional array of the message id's that
     * can be used to retrieve the message bodies.
     *
     * @access public
     * @param stdClass object of the mailbox
     * @return array
     */
    public function populateFolders($mailbox)
    {
        $numUnread = 0;
        $inbox = array();
        $trash = array();
        $sentitems = array();
        //iterate through the messages and populate the "Folders"
        foreach ($mailbox as $messages)
        {
            if($messages->seen == 0)
            {
                $numUnread++;
            }
            //populate the inbox
            if ($messages->deleted != 1)
            {
                $inbox[] .= $messages->uid;
            }
            //populate the trash (deleted items)
            if ($messages->deleted == 1)
            {
                $trash[] .= $messages->uid;
            }
            //populate the sent items
            if ($messages->answered == 1)
            {
                $sentitems[] .= $messages->uid;
            }
        }

        //create a multidimensional array of the information
        $folders = array('unread' => $numUnread, 'inbox' => $inbox, 'trash' => $trash, 'sentitems' => $sentitems);
        return $folders;

    }

    /*public function populateMsgList($folderarray)
    {
        foreach ($folderarray['inbox'] as $msgId)
        {
            $messages .= $this->getHeaderInfo($msgId);
        }
        return $messages;
    }
*/

    /**
     * Method to completely delete and expunge a message from the mailbox
     *
     * @param integer $messageid
     */
    public function delMsg($messageid)
    {
        //make sure that the messageid is a true integer
        intval(trim($messageid));
        @imap_delete($this->conn, $messageid);
        @imap_expunge($this->conn);
    }

    /**
     * Method to close the connection and destruct the class
     *
     * @access public
     * @return bool
     */
    public function __destruct()
    {
        @imap_close($this->conn);
        return TRUE;
    }
}
?>