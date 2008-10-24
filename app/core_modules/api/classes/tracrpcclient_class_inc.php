<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * XML-RPC Client class
 *
 * @author Paul Scott
 * @copyright GPL
 * @package packages
 * @version 0.1
 */
class tracrpcclient extends object
{
    /**
     * Language Object
     *
     * @var object
     */
    public $objLanguage;

    /**
     * Config object
     *
     * @var object
     */
    public $objConfig;

    /**
     * Sysconfig object
     *
     * @var object
     */
    public $sysConfig;
    
    /**
     * URI of trac instance
     *
     * @var string
     */
    public $tracURL;
    
    /**
     * Trac server part
     *
     * @var string
     */
    public $tracServ;

    /**
     * Standard init function
     *
     * @param void
     * @return void
     */
    public function init()
    {
        if(!function_exists('xml_rpc_se'))
        {
            //require_once($this->getPearResource('XML/RPC.php'));
        }
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->tracURL = $this->objSysConfig->getValue('trac_url', 'api');
        $this->tracServ = $this->objSysConfig->getValue('trac_server', 'api');
    }
    
    /**
     * Method to get a specific wiki page in HTML
     *
     * @param string $pagename
     * @return string HTML
     */
    public function grabTracWikiPageHTML($pagename)
    {
        $msg = new XML_RPC_Message('wiki.getPageHTML', array(new XML_RPC_Value($pagename, "string")));
        $cli = new XML_RPC_Client($this->tracURL, $this->tracServ);
        $cli->setDebug(0);
        // send the request message
        $resp = $cli->send($msg);
        if (!$resp)
        {
            throw new customException($this->objLanguage->languageText("mod_filters_commserr", "filters").": ".$cli->errstr);
            exit;
        }
        if (!$resp->faultCode())
        {
            $val = $resp->value();
            $val = XML_RPC_decode($val);
            if(is_array($val))
            {
                return $val['faultString'];
            }
            else {
                return $val;
            }
        }
        else
        {
            /*
            * Display problems that have been gracefully caught and
            * reported by the xmlrpc server class.
            */
            throw new customException($this->objLanguage->languageText("mod_filters_faultcode", "filters").
                                      ": ".$resp->faultCode() . $this->objLanguage->languageText("mod_filters_faultreason", "filters").
                                      ": ".$resp->faultString()
                                      );
        }
    }
    
    /**
     * Method to get a list of search filters from trac
     *
     * @return array of filters that can be used in search queries
     */
    public function getSearchFilters()
    {
        
    }
    
    /**
     * Method to perform a search against the trac rpc interface
     *
     * @param string $query
     * @param array  $filters
     * @return array list of tuples in the form of (href, title, date, author, excerpt)
     */
    public function performSearch($query='', $filters=array())
    {
        
    }
    
    /**
     * Ticket milestones
     */
    
    /**
     * Method to get a list of all ticket milestone names.
     *
     * @return array
     */
    public function getAllTicketMilestones()
    {
        
    }
    
    /**
     * Method to Get a ticket milestone.
     *
     * @param string $name
     * @return struct (multidim array for those less educated)
     */
    public function getTicketMilestoneByName($name)
    {
        
    }
    
    /**
     * ticket.severity - Interface to ticket severity.
     */
    
    /**
     * Get a list of all ticket severity names.
     * 
     * @return array
     */
    public function getAllTicketSeverities()
    {
        
    }
    
    /**
     * Get a ticket severity.
     *
     * @param string $name
     * @return string
     */
    public function getTicketSeverity($name)
    {
        
    }
    
    /**
     * ticket.type - Interface to ticket type.
     */
    
    /**
     * Method to Get a list of all ticket type names.
     *
     * @return array
     */
    public function getAllTicketTypes()
    {
        
    }
    
    /**
     * Get a ticket type.
     *
     * @param string $name
     * @return string
     */
    public function getTicketType($name)
    {
        
    }
    
    /**
     * ticket.resolution - Interface to ticket resolution.
     */
    
    /**
     * Get a list of all ticket resolution names.
     * 
     * @return array
     */
    public function getAllResolutionNames()
    {
        
    }
    
    /**
     * Get a ticket resolution.
     *
     * @param string $tktname
     * @return string
     */
    public function getTicketResolution($tktname)
    {
        
    }
    
    /**
     * ticket.priority - Interface to ticket priority.
     */
    
    /**
     * Get a list of all ticket priority names.
     *
     * @return array
     */
    public function getAllTicketPriorities()
    {
        
    }
    
    /**
     * Get a ticket priority.
     *
     * @param string $name
     * @return string
     */
    public function getTicketPriority($name)
    {
        
    }
    
    /**
     * ticket.component - Interface to ticket component objects.
     */
    
    /**
     * Get a list of all ticket component names.
     * 
     * @return array
     */
    public function getAllTicketComponents()
    {
        
    }
    
    /**
     * Get a ticket component.
     *
     * @param string $name
     * @return struct
     */
    public function getTicketComponent($name)
    {
        
    }
    
    /**
     * ticket.version - Interface to ticket version objects.
     */
    
    /**
     * Get a list of all ticket version names.
     *
     * @return array
     */
    public function getTicketVersionNames()
    {
        
    }
    
    /**
     * Get a ticket version.
     *
     * @param string $name
     * @return struct
     */
    public function getTicketVersion($name)
    {
        
    }
    
    /**
     * ticket - An interface to Trac's ticketing system.
     */
    
    /**
     * Perform a ticket query, returning a list of ticket ID's
     *
     * @param unknown_type $querystr
     */
    public function lookupTickets($querystr = "status!=closed")
    {
        
    }
    
    /**
     * Returns a list of IDs of tickets that have changed since timestamp.
     *
     * @param dateTime.iso8601 since $datetime
     * @return array
     */
    public function getChangesSinceDate($datetime)
    {
        
    }
    
    /**
     * Returns the actions that can be performed on the ticket.
     *
     * @param integer $id
     * @return array
     */
    public function getActions($id)
    {
        
    }
    
    /**
     * Fetch a ticket. Returns [id, time_created, time_changed, attributes].
     *
     * @param integer $id
     * @return array
     */
    public function getTicket($id)
    {
        
    }
    
    /**
     * Return the changelog as a list of tuples of the form (time, author, field, oldvalue, newvalue, permanent). 
     * While the other tuple elements are quite self-explanatory, the permanent flag is used to distinguish 
     * collateral changes that are not yet immutable (like attachments, currently).
     *
     * @param integer $id
     * @param integer $when
     * @return struct
     */
    public function getTicketChangelog($id, $when)
    {
        
    }
    
    /**
     * Lists attachments for a given ticket. 
     * Returns (filename, description, size, time, author) for each attachment.
     *
     * @param integer $id
     * @return array
     */
    public function listTicketAttachments($id)
    {
        
    }
    
    /**
     * returns the content of an attachment.
     *
     * @param string ticket id $id
     * @param string $filename
     * @return base64 encoded string
     */
    public function getTicketAttachment($id, $filename)
    {
        
    }
    
    /**
     * ticket.status - Interface to ticket status.
     */
    
    /**
     * Get a list of all ticket status names.
     *
     * @return array
     */
    public function getAllTicketStatusNames()
    {
        
    }
    
    /**
     * Get a ticket status.
     *
     * @param string $name
     * @return string
     */
    public function getTicketStatus($name)
    {
        
    }
}

?>
