<?php
/**
 * Ohloh API Client
 *
 * Ohloh has an API that makes use of a REST interface to deliver statistics about projects, people and code
 * This is a simple Object Orientated client class that will allow PHP developers to make use of that API
 * to gain valuable information about themselves and their projects.
 * 
 * NOTE: The Ohloh API is beta state software, and by proxy so is this class. Please read the license text below for warranty
 * (HINT: There are no warranties).
 * 
 * This is Kudoware on the Ohloh site... ;) Please include a link to www.ohloh.net on your web pages!
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @version    CVS: $Id$
 * @package    ohloh
 * @subpackage apiclient
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 Paul Scott
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://http://www.ohloh.net/api/getting_started
 */
class ohlohapi extends object
{
    /**
     * API REST URL
     *
     * @var    string
     * @access public
     */
    public $url;
    
    /**
     * Project ID
     *
     * @var integer
     * @access public
     */
    public $projectID;
    
    /**
     * Ohloh user ID
     *
     * @var    string
     * @access public
     */
    public $userID;
    
    /**
     * User Email address
     *
     * @var    string
     * @access public
     */
    public $userEmail;
    
    /**
     * Ohloh Account collection URL
     *
     * @var    string
     * @access public
     */
    public $account_collectionURL = 'http://www.ohloh.net/accounts.xml';
    
    /**
     * Ohloh Account base URL
     *
     * @var    string
     * @access public
     */
    public $baseurl;
    
    /**
     * Required badge HTML
     *
     * @var    string
     * @access private
     */
    private $badge;
    
    /**
     * API Version
     *
     * @param  integer $version
     * @access public
     */
    private $version = 1;
    
    /**
     * Proxy server connection information
     *
     * @var    array
     * @access private
     */
    private $proxyArr = NULL;
    
    /**
     * API key
     *
     * @var    string
     * @access protected
     */
    protected $apiKey; 
    
    /**
     * Standard init function
     *
     */
    public function init()
    {
        
    }
    
    /**
     * Setup
     *
     * @param  string $apikey
     * @access public
     */
    public function setup($apikey, $projectid = NULL, $userid = NULL, $proxyArray = NULL, $useremail = NULL)
    {
        // populate the badge HTML
        $this->badge = '<a href ="http://www.ohloh.net">
                        <img src="http://www.ohloh.net/images/badges/mini.gif" width="80" height="15" />
                        </a>';
        // set the API Key
        $this->apiKey = $apikey;
        $this->projectID = $projectid;
        $this->userID = $userid;
        // User email must be md5'ed
        $this->userEmail = md5($useremail);
        $this->proxyArr = $proxyArray;
        $this->setupBaseURL();
    }
    
    /**
     * You must do three things to receive an XML-formatted response:
     *    1. Append a .xml extension to the basic URL. For example, instead of http://www.ohloh.net/projects/1, 
     *       which returns an HTML page, you would request http://www.ohloh.net/projects/1.xml. 
     *    2. Provide your API Key as an HTTP parameter. Your request will be forbidden without a valid api_key. 
     *    3. Provide the API version as an HTTP parameter. Only v=1 is supported.
     * 
     * @access public
     * @return NULL
     */
    public function setupBaseURL()
    {
        $this->baseurl = 'http://www.ohloh.net/projects/'.$this->projectID.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return;
    }
    
    /**
     * Get the default project (if passed to __construct()) information
     *
     * @return object $project simplexmlObject 
     * @access public
     */
    public function getDefaultProjectInfo()
    {
        // use cURL to grab the project base info
        // There should also be an alternative method for querying this, but for now cURL is easy and Just Works (TM)
        $info = $this->_curlRequest($this->baseurl);
        $infoObject = simplexml_load_string($info);
        if($infoObject->status != 'success')
        {
            return FALSE;
        }
        else {
            $project = $infoObject->result->project;
            return $project;
        }
    }
    
    /**
     * Get Project Information for a specific project by its Project ID
     *
     * @param  integer $projectId The project ID
     * @return object $project simplexmlObject
     * @access public
     */
    public function getProjectInfo($projectId)
    {
        $url = 'http://www.ohloh.net/projects/'.$projectId.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Method to get a list of all projects
     *
     * @return object SimpleXMLObject
     * @access public
     */
    public function getAllProjects()
    {
        $url = 'http://www.ohloh.net/projects.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Account queries
     * 
     * An Account represents an Ohloh member. Some Account data is private, 
     * and cannot be accessed through the Ohloh API.
     */
    
    /**
     * Gets information about a single account by its account ID
     *
     * @param integer $accountId
     * @return object SimpleXMLObject
     * @access public
     */
    public function getSingleAccount($accountId)
    {
        $url = 'http://www.ohloh.net/accounts/'.$accountId.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        $info = $this->_curlRequest($url);
        $infoObj = simplexml_load_string($info);
        if($infoObj->status != 'success')
        {
            return FALSE;
        }
        else {
            $account = $infoObj->result->account;
            return $account;
        }
    }
    
    /**
     * Gets account information by an email address
     * 
     * You need to know the email address of the person that you
     * are querying!
     *
     * @param string $email
     * @return object SimpleXMLObject
     * @access public
     */
    public function getAccountByEmail($email)
    {
        $email = md5($email);
        $url = 'http://www.ohloh.net/accounts/'.$email.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        $info = $this->_curlRequest($url);
        $infoObj = simplexml_load_string($info);
        if($infoObj->status != 'success')
        {
            return FALSE;
        }
        else {
            $account = $infoObj->result->account;
            return $account;
        }
    }
    
    /**
     * Size fact methods
     * 
     * The results cannot be paginated or filtered. Results are sorted chronologically.
     */
    
    /**
     * Size facts - unspecified analysis
     * 
     * A SizeFact is a pre-computed collection of statistics about Project source code. 
     * It provides monthly running totals of lines of code, commits, and developer effort. 
     * SizeFacts contain the running totals of ActivityFacts. 
     * A SizeFact is derived from lower-level statistics contained in an Analysis. 
     * SizeFacts are updated whenever a Project is re-analyzed. 
     * SizeFacts are availabled only after Ohloh has downloaded and analyzed the project source code.
     * 
     * @access public
     * @return object SimpleXMLObject
     */
    public function sizeFactsNoAnalysis()
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/analyses/latest/size_facts.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Size facts - specified analysis
     * 
     * A SizeFact is a pre-computed collection of statistics about Project source code. 
     * It provides monthly running totals of lines of code, commits, and developer effort. 
     * SizeFacts contain the running totals of ActivityFacts. 
     * A SizeFact is derived from lower-level statistics contained in an Analysis. 
     * SizeFacts are updated whenever a Project is re-analyzed. 
     * SizeFacts are availabled only after Ohloh has downloaded and analyzed the project source code.
     * 
     * @access public
     * @param integer $analysisID Analysis ID
     * @return object SimpleXMLObject
     */
    public function sizeFacts($analysisID)
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/analyses/'.$analysisID.'/size_facts.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    
    /**
     * Analysis
     * 
     * An Analysis is a pre-computed collection of statistics about Project source code and contributors.
     * 
     * An individual Analysis never changes. When a Project's source code is modified, a completely new Analysis is generated 
     * for that Project. Eventually, old analyses are deleted from the database. 
     * Therefore, you should always obtain the ID of the best current analysis from the project 
     * record before requesting an analysis.
     * 
     * The Analysis object described here contains only a few top-level metrics. 
     * Detailed, historical metrics are contained within child objects of an Analysis, 
     * which will be exposed through the API at a later date.
     */
    
    /**
     * Analysis with no analysis ID
     * 
     * A shortcut to the current best Analysis for a single Project 
     * can be made by substituting 'latest' for the analysis ID
     *
     * @return object $analysis simplexml object
     * @access public
     */
    public function getAnalysisLatest()
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/analyses/latest.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Analysis with no analysis ID
     * 
     * A shortcut to the current best Analysis for a single Project 
     * can be made by substituting 'latest' for the analysis ID
     *
     * @param  integer $analysisId Analysis ID 
     * @return object $analysis simplexml object
     * @access public
     */
    public function getAnalysisById($analysisId)
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/analyses/'.$analysisId.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    
    /**
     * ActivityFacts
     * 
     * An ActivityFact is a pre-computed collection of statistics about Project source code. 
     * It summarizes changes to lines of code, commits, and contributors in a single month.
     * 
     * SizeFacts contain the running totals of ActivityFacts.
     * An ActivityFact is derived from lower-level statistics contained in an Analysis. 
     * ActivityFacts are updated whenever a Project is re-analyzed.
     * 
     * ActivityFacts are availabled only after Ohloh has downloaded and analyzed the project source code.
     */
    
    /**
     * Get activity facts
     *
     * @return simpleXMLObject
     * @access public
     */
    public function getActivityFactsNoId()
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/analyses/latest/activity_facts.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get activity facts by ID
     *
     * @param  integer $analysisId Analysis ID
     * @return object simpleXMLObject
     */
    public function getActivityFactsById($analysisId)
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/analyses/'.$analysisId.'/activity_facts.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * ContributorFact
     * 
     * A ContributorFact contains a selection of high-level statistics about a person who commited source code to a Project. 
     * One ContributorFact record exists for each contributor.
     * 
     * A ContributorFact is part of an Analysis, and is derived from lower-level statistics contained within the Analysis.
     * A new ContributorFact is created for each Project contributor whenever a new Analysis is created for the Project.
     * 
     * ContributorFacts only exist after Ohloh has downloaded and analyzed the project source code.
     */
    
    /**
     * To get a single ContributorFact based on the latest Analysis for a Project
     *
     * @param $userid The user (contributor) ID
     * @access public
     */
    public function contributorFactById($userid = NULL)
    {
        if($userid === NULL)
        {
            $url = 'http://www.ohloh.net/projects/'.$this->projectID.
                   '/contributors/'.$this->userID.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        }
        else {
            $url = 'http://www.ohloh.net/projects/'.$this->projectID.
                   '/contributors/'.$userid.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        }
        return $this->_process($url);
    }
    
    /**
     * To get a list of all ContributorFacts based on the lastest Analysis for a Project
     * 
     * @access public
     */
    public function contributorFactProject()
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.'/contributors.xml?api_key='
               .$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get contributor facts by ID
     *
     * @param  integer $id User ID
     * @return object simpleXMLObject
     * @access public
     */
    public function contributorLanguageFactById($id)
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.
               '/contributors/'.$id.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
        
    }
    
    /**
     * Get all project enlistments
     *
     * @return object SimpleXMLObject
     * @access public
     */
    public function getProjectEnlistments()
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.'enlistments.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get a single enlistment by enlistment ID
     *
     * @param  integer $enlistmentId
     * @return object simpleXMLObject
     * @access public
     */
    public function getSingleEnlistment($enlistmentId)
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.'enlistment/'.
               $enlistmentId.'.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get project Factoids
     *
     * @return object SimpleXMLObject
     * @access public
     */
    public function getProjectFactoids()
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.'factoids.xml?api_key='.$this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get a single factoid by its ID
     *
     * @param  integer $id
     * @return object simpleXMLObject
     * @access public
     */
    public function getFactoidById($id)
    {
        $url = 'http://www.ohloh.net/projects/'.$this->projectID.'factoids/'.$id.'.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get recieved kudos
     *
     * @param  integer $account the user account
     * @return object simpleXMLObject
     * @access public
     */
    public function getRecievedKudos($account)
    {
        $url = 'http://www.ohloh.net/accounts/'.$account.'/kudos.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get the kudos that you have sent
     *
     * @param integer $account user account id
     * @return object simpleXMLObject
     * @access public
     */
    public function getSentKudos($account)
    {
        $url = 'http://www.ohloh.net/accounts/'.$account.'/kudos/sent.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get the primary language by ID
     *
     * @param  integer $id
     * @return object simpleXMLObject
     * @access public
     */
    public function getLanguageById($id)
    {
        $url = 'http://www.ohloh.net/languages/'.$id.'.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get all languages on Ohloh
     *
     * @return object simpleXMLObject
     * @access public
     */
    public function getLanguages()
    {
        $url = 'http://www.ohloh.net/languages.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get a stack by its ID
     *
     * @param integer $userid account userid
     * @param integer $stackid stack id
     * @return object simpleXMLObject
     * @access public
     */
    public function getStackById($userid, $stackid)
    {
        $url = 'http://www.ohloh.net/accounts/'.$userid.'/stacks/'.$stackid.'.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get a users stack by userid
     *
     * @param integer $userid account userid
     * @return object simpleXMLObject
     * @access public
     */
    public function getStackByAccount($userid)
    {
        $url = 'http://www.ohloh.net/accounts/'.$userid.'/stacks/default.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Get all stacks associated with a project
     *
     * @param integer $projectid project ID
     * @return object simpleXMLObject
     * @access public
     */
    public function getStacksByProject($projectid)
    {
        $url = 'http://www.ohloh.net/projects/'.$projectid.'/stacks.xml?api_key='.
               $this->apiKey.'&v='.$this->version;
        return $this->_process($url);
    }
    
    /**
     * Method to process and return data from REST Service
     *
     * @param string $url
     * @return object $data returned data
     * @access private
     */
    private function _process($url)
    {
        $info = $this->_curlRequest($url);
        $infoObj = simplexml_load_string($info);
        if($infoObj->status != 'success')
        {
            return FALSE;
        }
        else {
            $data = $infoObj->result;
            return $data;
        }
    }
    
    /**
     * cURL Request method
     *
     * @param string $url The request URL built from properties.
     * @return string $code the return string from Ohloh
     * @access private
      */
    private function _curlRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($this->proxyArr) && $this->proxyArr['proxy_protocol'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyArr['proxy_host'].":".$this->proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyArr['proxy_user'].":".$this->proxyArr['proxy_pass']);
        }
        $code = curl_exec($ch);
        curl_close($ch);
        return $code;
    }
}
?>