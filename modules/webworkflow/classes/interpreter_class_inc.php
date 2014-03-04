<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * This class will interpret the workflow instructions to produce the URI endpoint document
 * Features include syntax checking, error catching and warnings.
 *
 * e.g. (workflow language)
 *
 *<workflow>
 *   <open>http://www.google.com</open>
 *   <input name="q">Straw Berry</input>
 *   <click name="btnG"></click>
 *</workflow>
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Charl Mert <charl.mert@gmail.com>
 *
 */

class interpreter extends object
{

    /**
    * openTagData - an array of collected links for every <OPEN> tag
    *
    * @access private
    * @var array
    */
    protected $openTagData;

    /**
    * inputTagData - an array of collected attributes and cdata for every <INPUT> tag
    *
    * @access private
    * @var array
    */
    protected $inputTagData;

    /**
    * clickTagData - an array of collected attributes for every <CLICK> tag
    *
    * @access private
    * @var array
    */
    protected $clickTagData;

    /**
    * tagSequence - an array of collected tags stored in order of procedural sequence
    *
    * @access private
    * @var array
    */
    protected $tagSequence;

    /**
    * workflowSequence - an array of collected workflow sequences according to tagSequence order.
    *
    * @access private
    * @var array
    */
    protected $workflowSequence;

    /**
    * lastResultDocument - a string containing the last document resulting from a workflow execution
    * This is used to persist documents accross workflows to aid complex workflow execution
    *
    * @access private
    * @var array
    */
    protected $lastResultDocument;

    /**
    * lastOpenLink - a string containing the last URI opened resulting from a previous workflow execution
    * This is used to persist documents accross workflows to aid complex workflow execution
    *
    * @access private
    * @var array
    */
    protected $lastOpenLink;

    /**
    * Class Constructor
    *
    * @access public
    * @return void
    */

    public function init()
    {
        $this->openTagData = array();
        $this->inputTagData = array();
        $this->clickTagData = array();
        $this->tagSequence = array();
        $this->workflowSequence = array();
        $this->lastResultDocument = '';
        $this->lastOpenLink = '';
        
        //$this->objCurl = $this->getObject('curlwrapper', 'utilities');

        // Load Config Object
        $objConfig = $this->getObject('altconfig', 'config');

        $this->objCurl = new curlwrapper_($objConfig);
        //$this->objCurl = new curlcmdwrapper_($objConfig);
    }

    /**
     * public method to open a workflow and execute it.
     *
     * @param string $workflow The XML workflow string
     * @return void
     * @access public
     */
    public function run($workflow)
    {

        /*TODO: Strengthening / Defining Web Workflow Patterns Here:
            *      - Current Constraints:
            *          1. Conformed to HTML/XHTML and possibly other TEXT Markups
            *          2. Javascript Actions Omitted (For initial tests only) 4 impl See SpiderMonkey http://www.mozilla.org/js/spidermonkey/
            *          3. ActiveX and Flash / Embeded objects ommitted
            *
            *      - Roadmap:
            *          1. Web User Interface Benchmarking / Optimization using Web Workflows.
            *             e.g. Testing workflows against a website for readable benchmarks cases
            *
            *      -Algo:
            *          <open>(Validated URI)</open> must appear first
            *          Any Work Flow can follow here (See Below)
            *
            *      Workflow Patterns (Experimental):
            *      //Simplest Cases
            *
            *      [swf0]- 0 Straight "GET" type form submit: (Simplest Form Submit)
            *              |<open><input><input * n>|
            *
            *      [swf1]- 1 anchor click to new page: (Simplest Anchor Click Case)
            *              <open><click type="anchor">
            *
            *      [swf2]- 1 click -> form submit: (Simplest Form Submit Case)
            *              <open><input ... text><input ... checkbox><click name="submitBtnName">
            *
            *      //Combinations of the Simple Cases
            *      [wf3]-1 anchor click to new page -> 1 click -> form submit:
            *              <open><click type="anchor"><input><click name="submitBtnName">
            *              = [swf1] + [swf2]
            *
            *      [wf4]-N anchor clicks to get to a certain page -> 1 click -> form submit:
            *              <open> (<click type="anchor"> * N) <input><click name="submitBtnName">
            *              = N * [swf1] + [swf2]
            *
            *      [wf5]-1 click -> form submit -> N anchor clicks to get to a certain page:
            *              <open><click type="anchor"><input><click name="submitBtnName">
            *              = [swf2] + N * [swf1]
            */

        //Curl Workflow gets processed once a <COMMIT> tag is hit
        if ($this->parseWorkflow($workflow)) {
            //var_dump($this->openTagData);
            //var_dump($this->inputTagData);
            //var_dump($this->clickTagData);
            //var_dump($this->tagSequence);
            //var_dump($this->workflowSequence); exit;
    
            log_debug("\nWorkflow Sequence:\n" . var_export($this->workflowSequence, true) . "\n");

            //echo "\nWorkflow Sequence:\n" . var_export($this->workflowSequence, true) . "\n";
            //exit;
            
            //Executing the aquired workflows
            foreach ($this->workflowSequence as $workflowSignature) {
                switch ($workflowSignature) {
                    case 'WF_SIGN_0':
                        $this->lastResultDocument = $this->runSimpleGetWorkflow($this->openTagData, $this->inputTagData, $this->clickTagData);
                    break;
                    case 'WF_SIGN_1':
                        $this->lastResultDocument = $this->runSimpleAnchorClickWorkflow($this->openTagData, $this->inputTagData, $this->clickTagData);
                    break;
                    case 'WF_SIGN_2':
                        $this->lastResultDocument = $this->runSimpleFormSubmitWorkflow($this->openTagData, $this->inputTagData, $this->clickTagData);
                    break;
                }
            }

            //Closing Session once all requests have been handled (Helps to preserve sessions)
            $this->objCurl->closeCurl();
            
            return $this->lastResultDocument;
        }
        
    }

    /**
     * This method will read the workflow and store syntatical information
     * @param string $workflow - The workflow source
     * @return boolean TRUE or FALSE based on weather or not the $workflow passes the Web Workflow syntax checking.
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function parseWorkflow($workflow) {
        //$elems = simplexml_load_string($workflow);
		$workflow = str_replace('&amp;', '&', $workflow);
		$workflow = str_replace('&', '&amp;', $workflow);
		
        $xml = new SimpleXMLElement($workflow);

        //Grabbing all of the elements under "workflow"
        $elems = $xml->xpath('/workflow/*');

        $openTagCounter = 0;
        $inputTagCounter = 0;
        $clickTagCounter = 0;

        $workflowSignature = '';

        foreach ($elems as $elem){
            //tag name:
            $tagName = $elem->getName();

            //Adding to the tag sequence
            array_push($this->tagSequence, $tagName);
            
            //Applying workflow signatures
            //WF_0 - Simple Get Request
            if (strtolower($tagName) == 'commit'
             && strtolower($this->tagSequence[count($this->tagSequence) - 2]) == 'input') {
                $workflowSignature = 'WF_SIGN_0';
                array_push($this->workflowSequence, $workflowSignature);
             }

            
            //WF_1 - Simple Anchor Click
            if (strtolower($tagName) == 'click'
             && strtolower($this->tagSequence[count($this->tagSequence) - 2]) == 'open') {
                $workflowSignature = 'WF_SIGN_1';
                array_push($this->workflowSequence, $workflowSignature);
             }
             
            //WF_2 - Simple Form Submit
            if (strtolower($tagName) == 'click'
             && strtolower($this->tagSequence[count($this->tagSequence) - 2]) == 'input') {
                $workflowSignature = 'WF_SIGN_2';
                array_push($this->workflowSequence, $workflowSignature);
             }
            
            //tag contents:
            if (isset($elem[0])) {
                $tagData = $elem[0];
            }

            //Catching <OPEN> tag data
            if (strtolower($tagName) == 'open') {
                //CHECK: is_url($tagData);
                //WARNING: if false
                $this->openTagData[$openTagCounter]['link'] = $tagData;
                $openTagCounter++;
            }

            //Catching <INPUT> tag data
            if (strtolower($tagName) == 'input') {
                $this->inputTagData[$inputTagCounter]['text'] = $tagData;
                
                //tag attributes:
                $attrs = $elem->attributes();
                if (!empty($attrs)){
                    foreach ($attrs as $attr => $value) {
                        //CHECK: $attr != 'text'
                        //WARNING: if false
                        $this->inputTagData[$inputTagCounter][$attr] = $value;
                    }
                }

                $inputTagCounter++;
            }

            //Catching <CLICK> tag data
            if (strtolower($tagName) == 'click') {
                //$this->clickTagData[$clickTagCounter]['text'] = $tagData;

                //tag attributes:
                $attrs = $elem->attributes();
                if (!empty($attrs)){
                    foreach ($attrs as $attr => $value) {
                        //CHECK: $attr != 'text'
                        //WARNING: if false
                        $this->clickTagData[$clickTagCounter][$attr] = $value;
                    }
                }

                if ($tagData != ''){
                    $this->clickTagData[$clickTagCounter]['text'] = $tagData;
                }

                $clickTagCounter++;
            }

            //Curl Workflow gets processed once a <COMMIT> tag is hit
            if (strtolower($tagName) == 'commit') {
                return TRUE;
            }
            
        }
        log_debug('Error Parsing Workflow: No Commit Tag Specified');
        return FALSE; //no commit tag found
    }


    /**
     * This method will interpret, execute and return a document based on the swf0
     *      - Simple Workflow 0
     *      [swf0]- Straight "GET" form submit
     *              <open><input ... text><input ... checkbox><click name="submitBtnName">
     *
     *      Description: open link, fill in form, submit form
     *
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function runSimpleGetWorkflow($openTagData, $inputTagData, $clickTagData) {

        //Setting the indexes
        $openExCounter = 0;
        $inputExCounter = 0;
        $clickExCounter = 0;

        //var_dump($this->lastResultDocument); exit;

        $targetLink = $this->openTagData[$openExCounter]['link'];

        $postArgs = '&';

        //Adding All Inputs
        foreach ($this->inputTagData as $inputTagData) {
            //Gathering the text input
            $inputName = $inputTagData['name'];
            $inputText = $inputTagData['text'];

            $postArgs .= $inputName . '=' . $inputText . '&';
        }
        $postArgs = substr($postArgs, 0, strlen($postArgs) - 1);
        //$postArgs = urlencode($postArgs);

        //Getting the first line of the form to strip attributes
        $lines = explode("\n", $parentForm);

        $targetUrl = $targetLink . urlencode($postArgs);
        $targetUrlNonEnc = $targetLink . $postArgs;
        
        //var_dump($targetUrl); exit;
        log_debug("Curl Simple Form Submit Workflow Target : $targetUrlNonEnc \nurlencoded: $targetUrl  \nPost Data : " . $postArgs);
        //echo "$targetUrl";
        //exit;

        //TODO: Check when really to use encoded url's and when not to
        //Using non encoded
        $this->objCurl->initializeCurl($targetUrlNonEnc);
        $this->lastOpenLink = $targetUrl;

        //Submitting the form based on the form details gathered above
        $resultDoc = $this->objCurl->sendPostData();
        //TODO: RETURNS AFTER FIRST COMMIT, CHANGE TO INCLUDE MULTIPLE COMMITS PER WORKFLOW

        $config = array('output-xhtml'=>true,
                        'wrap' => 0,
                        'wrap-attributes' => true
                        );

        $tidy = tidy_parse_string($resultDoc, $config);
        $tidy->cleanRepair();
        $resultDoc = $tidy;

        return $resultDoc;
    }



    /**
     * This method will interpret, execute and return a document based on the swf2
     *      - Simple Workflow 2
     *      [swf2]- 1 click -> form submit: (Simplest Form Submit Case)
     *              <open><input ... text><input ... checkbox><click name="submitBtnName">
     *
     *      Description: open link, fill in form, submit form
     *
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function runSimpleFormSubmitWorkflow($openTagData, $inputTagData, $clickTagData) {

        //Setting the indexes
        $openExCounter = 0;
        $inputExCounter = 0;
        $clickExCounter = 0;

        //var_dump($this->lastResultDocument); exit;
        
        //Getting the initial open document if there isn't already one in the resultCache
        if ($this->lastResultDocument == '') {
            //The curl request to get the doc at the first <open> link gets sent and the openCounter is ++'d
            $targetLink = $this->openTagData[$openExCounter]['link'];

            if ($this->lastOpenLink != $targetLink) {
                $this->objCurl->initializeCurl($targetLink);
            }

            $this->lastOpenLink = $targetLink;
            
            $resultDoc = $this->objCurl->sendPostData();
            if ($resultDoc == '') {
                die("Fatal Error: Host returned nothing '{$this->openTagData[$openExCounter]['link']}'\n");
            }
        } else {
            $resultDoc = $this->lastResultDocument;
            if ($this->lastOpenLink != '') {
                $targetLink = $this->lastOpenLink;
            } else {
                die ('Fatal Error: No Last Open Link for an existing lastResultDoc');
            }
        }

        //Getting the form details based on the <click> tag's name/id attr:
        //TODO: Add form query methods based on submit button TEXT and ID
        $tagName = strval($this->clickTagData[$clickExCounter]['name']);

        //TODO: Provision Click Types 'anchor vs button'
        //CHECK: click_type
        //FORK: button=parentForm, anchor=getUrl();

        $parentForm = $this->getParentForm($tagName, $resultDoc);

        //Gathering Info to submit form
        $postArgs = $this->getPostString($parentForm);

        if ($postArgs != '') {
            $postArgs .= '&';
        }
        
        //Adding All Inputs
        foreach ($this->inputTagData as $inputTagData) {
            //Gathering the text input
            $inputName = $inputTagData['name'];
            $inputText = $inputTagData['text'];

            $postArgs .= $inputName . '=' . $inputText . '&';
        }
        $postArgs = substr($postArgs, 0, strlen($postArgs) - 1);
        //$postArgs = urlencode($postArgs);

        //Getting the first line of the form to strip attributes
        $lines = explode("\n", $parentForm);

        $attrs = $this->getAttributes('form', $lines[0]);
        if (empty($attrs)){
            die ('Error Submitting Form: Form has no tags, couldn\'t determine action');
        }

        //Getting the uri parts to build second relative target
        $uriParts = parse_url($targetLink);
        if ($attrs['action'][0] == '/'){
            $uriParts['path'] = '';
        }
        //Stripping the scriptname
        $uriParts['path'] = str_replace(end(explode('/', $uriParts['path'])), '', $uriParts['path']);
        $targetUrl = $uriParts['scheme'] . '://'
                    . $uriParts['host']
                    . $uriParts['path']
                    . $attrs['action'];

        //For debugging purposes only, for printing non urlencoded post args
        $targetUrlNonEnc = $targetUrl;

        //var_dump($uriParts);
        //var_dump($attrs['action']); exit;
        //Checking if <form> method is POST then do:
        if (strtolower($attrs['method']) != 'post'){
            if (strpos('?', $attrs['action']) !== false) {
                $targetUrl .= '&' . urlencode($postArgs);
            } else {
                $targetUrl .= '?' . urlencode($postArgs);
            }

            //Same conditional just removed urlencode for post args
            if (strpos('?', $attrs['action']) !== false) {
                $targetUrlNonEnc .= '&' . $postArgs;
            } else {
                $targetUrlNonEnc .= '?' . $postArgs;
            }

            $postArgs = false;
        }

        //var_dump($targetUrl); exit;
        log_debug("Curl Simple Form Submit Workflow Target : $targetUrlNonEnc \nurlencoded: $targetUrl  \nPost Data : " . $postArgs);
        //echo "$targetUrl";
        //exit;

        //TODO: Revisit Curl HTTP Sessions and Check Code Below
        if ($this->lastOpenLink != $targetUrl) {
            //Re Initializing Curl
            $this->objCurl->closeCurl();
            $this->objCurl->initializeCurl($targetUrl);
        }

        $this->lastOpenLink = $targetUrl;

        //Submitting the form based on the form details gathered above
        $resultDoc = $this->objCurl->sendPostData($postArgs);
        //TODO: RETURNS AFTER FIRST COMMIT, CHANGE TO INCLUDE MULTIPLE COMMITS PER WORKFLOW

        $config = array('output-xhtml'=>true,
                        'wrap' => 0,
                        'wrap-attributes' => true
                        );

        $tidy = tidy_parse_string($resultDoc, $config);
        $tidy->cleanRepair();
        $resultDoc = $tidy;

        return $resultDoc;
    }

    /**
     * This method will interpret, execute and return a document based on the swf2
     *      - Simple Workflow 1
     *      [swf1]- 1 click -> form submit: (Simplest Form Submit Case)
     *              <open><click type="anchor">Link Text</click>
     *
     *      Description: open link, click another link
     *
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function runSimpleAnchorClickWorkflow($openTagData, $inputTagData, $clickTagData) {

        //Setting the indexes
        $openExCounter = 0;
        $inputExCounter = 0;
        $clickExCounter = 0;
        
        //Getting the initial open document if there isn't already one in the resultCache
        if ($this->lastResultDocument == '') {
            //The curl request to get the doc at the first <open> link gets sent and the openCounter is ++'d
            $targetLink = $this->openTagData[$openExCounter]['link'];

            //To help properly preserve sessions
            if ($this->lastOpenLink != $targetLink) {
                $this->objCurl->initializeCurl($targetLink);
            }
            
            $this->lastOpenLink = $targetLink;
            
            $resultDoc = $this->objCurl->sendPostData();
            if ($resultDoc == '') {
                die("Fatal Error: Host returned nothing '{$this->openTagData[$openExCounter]['link']}'\n");
            }
        } else {
            $resultDoc = $this->lastResultDocument;
            if ($this->lastOpenLink != '') {
                $targetLink = $this->lastOpenLink;
            } else {
                die ('Fatal Error: No Last Open Link for an existing lastResultDoc');
            }
        }

        if ($targetLink == '') {
            die("Error: Must specify a target link in the <open></open> tags\n");
        }

        //$this->objCurl->setOpt('CURLOPT_FOLLOWLOCATION', 1);
        log_debug("Curl Simple Anchor Click Workflow Init: " . $targetLink);
        $resultDoc = $this->objCurl->sendPostData();
        $this->lastResultDocument = $resultDoc;
        if ($resultDoc == '') {
            die("Fatal Error: Host returned nothing '{$this->openTagData[$openExCounter]['link']}'\n");
        }

        //Error Catching
        //TODO: Need to catch proper HTTP Protocol Errors like 404 here
        if (preg_match('/.*404.*Not.*Found.*/isU', $resultDoc)) {
            die("Error: Http Error 404 (File Not Found) URL: '$targetLink'\n");
        }

        //Getting the target link based on the href="" attribute of the anchor
        //TODO: Add anchor query methods based on anchor TEXT and ID ... other attribute matches
        $tagText = strval($this->clickTagData[$clickExCounter]['text']);

        $anchorTag = $this->getAnchorTag($tagText, $resultDoc);

        //Error Halt: Couldn't find any anchor tag for the text $tagText
        if ($anchorTag == '') {
            die("Fatal Error: Couldn't find any anchor tag for the text '$tagText'\n");
        }
        
        $attrs = $this->getAttributes('a', $anchorTag);
        $targetUrl = $attrs['href'];
        
        //Resolving Relativity if no host in targetUrl
        if (!preg_match('/.*\:.*/i', $targetUrl)) {
            //Getting the uri parts to build second relative target
            $uriParts = parse_url($targetLink);
            //Stripping the scriptname
            $uriParts['path'] = str_replace(end(explode('/', $uriParts['path'])), '', $uriParts['path']);
            $targetUrl =  $uriParts['scheme'] . '://'
                        . $uriParts['host']
                        . $uriParts['path']
                        . $attrs['href'];
        }
        
        log_debug("Curl Simple Anchor Click Workflow Target : " . $targetUrl);
        $this->lastOpenLink = $targetUrl;
        
        //echo "$targetUrl";
        //exit;
        
        //Submitting the form based on the form details gathered above
        $resultDoc = $this->objCurl->sendPostData();
        //TODO: RETURNS AFTER FIRST COMMIT, CHANGE TO INCLUDE MULTIPLE COMMITS PER WORKFLOW
        
        $config = array('output-xhtml'=>true,
                        'wrap' => 0,
                        'wrap-attributes' => true
                        );
        
        $tidy = tidy_parse_string($resultDoc, $config);
        $tidy->cleanRepair();
        $resultDoc = $tidy;
        $this->lastResultDocument = $resultDoc;
        
        return $resultDoc;
    }


    /**
     * This method returns the anchor tag based on the TEXT
     * @param string $anchorText - The link text
     * //TODO: input can be a valid xquery expression

     * @param string $htmlSource - The document string to search
     * @return string - Returns the full anchor tag line
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function getAnchorTag($anchorText, $htmlSource) {
        $config = array('output-xhtml'=>true,
                        'wrap' => 0,
                        'wrap-attributes' => true
                        );

        $tidy = tidy_parse_string($htmlSource, $config);
        $tidy->cleanRepair();
        $tidy = preg_replace('/(<\/)(.*?)(\>)/i', "$0\n", $tidy);
        
        //echo $tidy; exit;

        $result = preg_match_all('/\<a.*\<\/a>/isU', $tidy, $matches);
        $matches = $matches[0];
        
        foreach ($matches as $anchor) {
            //Making sure every tag is on it's own line (Makes it easier to grab attrs)
            //var_dump($anchor);
            if (preg_match('/.*'.trim($anchorText).'.*/i', strval($anchor))) {
                //Trimming the string properley here
                $anchor = preg_replace('/.*?<a/isU', '<a', $anchor);
                //var_dump($anchor); exit;
                return $anchor;
            }
        }

        log_debug("Couldn't find an anchor tag for '$anchorText'");
        return FALSE;
    }


    /**
     * This method returns the parent forms details if the form contains the given
     * @param string element id or name
     * @param string $htmlSource - The document string to search
     * @return string - Returns the full form from start to </form> lines
     * //TODO: input can be a valid xquery expression
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function getParentForm($btnName, $htmlSource) {
        $forms = array();
        $retForm = array();

        $config = array('output-xhtml'=>true,
                        'wrap' => 0,
                        'wrap-attributes' => true
                        );

        $tidy = tidy_parse_string($htmlSource, $config);
        $tidy->cleanRepair();

        $result = preg_match_all('/\<form.*\<\/form>/isU', $tidy, $matches);
        
        foreach ($matches as $form) {
            $form = $form[0];
            //Making sure every tag is on it's own line (Makes it easier to grab attrs)
            $form = preg_replace('/>/i', ">\n", $form);

            if (preg_match("/.*$btnName.*/i", $form)) {
                return $form;
            }
        }

        log_debug("Couldn't get Parent Form for button with name/id = '$btnName'");
        return FALSE;
    }


    /**
     * This method returns a constructed query string based on the given forms
     * hidden inputs.
     * @param $form The html form from start to end <form> tag
     * @return string postString. This string will be ready to use in a curl request as the POST param.
     * @author: Charl Mert <charl.mert@gmail.com>
     */
    function getPostString($form) {
        //Fetching the hidden input values to add to the POST/GET params
        $hres = preg_match_all('/.*input.*type.*hidden.*/i', $form, $inputs);
        $postStr = '';
        
        if (!empty($inputs)) {

            $inputs = $inputs[0];
            foreach ($inputs as $input) {
                $attrs = $this->getAttributes('input', $input);
                if (!empty($attrs)) {
                    $postStr .= $attrs['name'] . '=' . $attrs['value'] . '&';
                }
            }
            $postStr = substr($postStr, 0, strlen($postStr) - 1);
        }
        return $postStr;
    }


    /**
     * This method returns an array of attributes for the given xml tags
     */
    function getAttributes($element_name, $xml) {
        if ($xml == false) {
            log_debug("Couldn't Retrieve Attributes for '$element_name' in a NULL document");
            return false;
        }
        // Grab the string of attributes inside an element tag.

        $found = preg_match('#<'.$element_name.
                '\s+([^>]+(?:"|\'))\s?/?>#',
                $xml, $matches);

        if ($found == 1) {
            $attribute_array = array();
            $attribute_string = $matches[1];
            // Match attribute-name attribute-value pairs.

            $found = preg_match_all(
                    '#([^\s=]+)\s*=\s*(\'[^<\']*\'|"[^<"]*")#',
                    $attribute_string, $matches, PREG_SET_ORDER);

            if ($found != 0) {
                // Create an associative array that matches attribute
                // names to attribute values.
                foreach ($matches as $attribute) {
                    $attribute_array[$attribute[1]] = str_replace('"', '', $attribute[2]);
                    $attribute_array[$attribute[1]] = str_replace("'", '', $attribute_array[$attribute[1]]);
                }
                return $attribute_array;
            }
        }
        // Attributes either weren't found, or couldn't be extracted
        // by the regular expression.

        log_debug("Couldn't Retrieve Attributes for '$element_name'");
        return false;
    }

}





















/**
* CUTTING FROM code_modules/utilities/curlwrapper_class_inc.php CLASS
* TODO: to be merged
*
* Curl is a tool for transferring files with URL syntax
*
* This class is a wrapper for PHP's CURL functions integrated with
* Chisimba's Proxy Configurations. Developers can simply instantiate
* this class and request the page they want.
*
* @category  Chisimba
* @package   utilities
* @author Tohir Solomons
* @author Derek Keats
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: curlwrapper_class_inc.php 11250 2008-11-02 11:32:56Z charlvn $
* @link      http://avoir.uwc.ac.za
* Example:
*   $objCurl = $this->getObject('curl', 'utilities');
*   $objCurl->initializeCurl($url);
*   echo $objCurl->getData();
*   $objCurl->closeCurl();
* Example:
*    echo $objCurl->exec('http://ws.geonames.org/search?name_equals=Walvisbaai&style=full');
*/

class curlwrapper_ extends object
{
    /**
    * @var array $proxyInfo Array Containing Proxy Details
    * @access private
    */
    private $proxyInfo;

    public $ch;
    public $options = array();

    /**
    * Constructor
    */
    /*
    public function init()
    {
        //TODO: reopen when merge complete
        //$this->setupProxy();
    }
    */

    public function curlwrapper_($objConfig) {
        $this->objConfig = $objConfig;
        $this->setupProxy();
    }

    /**
    *
    * Method to extract the proxy settings from the chisimba settings
    * and prepare them for use in curl.
    *
    */
    public function setupProxy()
    {
        // Load Config Object
        $objConfig = $this->objConfig;
        // Get Proxy String
        $proxy = $objConfig->getProxy();
        // Remove http:// from beginning of string
        $proxy =  preg_replace('%\Ahttp://%i', '', $proxy);
        // Create Empty Array
        $this->proxyInfo = array('username'=>'','password'=>'','server'=>'','port'=>'',);
        // Check if string has @, indicator of username/password and server/port
        if (preg_match('/@/i', $proxy)) {
            // Split string into username and password
            preg_match_all('/(?P<userinfo>.*)@(?P<serverinfo>.*)/i', $proxy, $result, PREG_PATTERN_ORDER);
            // If it has user information, perform further split
            if (isset($result['userinfo'][0])) {
                // Split at : to get username and password
                $userInfo = explode(':', $result['userinfo'][0]);
                // Record username if it exists
                $this->proxyInfo['username'] = isset($userInfo[0]) ? $userInfo[0] : '';
                // Record password if it exists
                $this->proxyInfo['password'] = isset($userInfo[1]) ? $userInfo[1] : '';
            }
            // If it has server information, perform further split
            if (isset($result['serverinfo'][0])) {
                // Split at : to get server and port
                $serverInfo = explode(':', $result['serverinfo'][0]);
                // Record server if it exists
                $this->proxyInfo['server'] = isset($serverInfo[0]) ? $serverInfo[0] : '';
                // Record port if it exists
                $this->proxyInfo['port'] = isset($serverInfo[1]) ? $serverInfo[1] : '';
            }
        // Else only has server and port details
        } else {
            // Split at : to get server and port
            $serverInfo = explode(':', $proxy);
            // Record server if it exists
            $this->proxyInfo['server'] = isset($serverInfo[0]) ? $serverInfo[0] : '';
            // Record port if it exists
            $this->proxyInfo['port'] = isset($serverInfo[1]) ? $serverInfo[1] : '';
        }
    }

    public function initializeCurl($url)
    {
        log_debug("\n\nCURL INITIALIZED: $url \n\n");
        // Setup URL for Curl
        $this->ch = curl_init($url);
        //$this->setupProxy();
    }

    public function closeCurl()
    {
        // Close the CURL
        curl_close($this->ch);
    }

    /**
    * Set a curl option.
    *
    * @link http://www.php.net/curl_setopt
    * @param mixed $theOption One of the valid CURLOPT defines.
    * @param mixed $theValue the value of the curl option.
    *
    */
    public function setopt($theOption, $theValue)
    {
        curl_setopt($this->ch, $theOption, $theValue) ;
        $this->options[$theOption] = $theValue ;
    }

    public function setProxy()
    {
        // Add Server Proxy if it exists
        if ($this->proxyInfo['server'] != '') {
            $this->setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
        }
        // Add Port Proxy if it exists
        if ($this->proxyInfo['port'] != '') {
            $this->setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
        }
        // Add Username for Proxy if it exists
        if ($this->proxyInfo['username'] != '') {
            $userNamePassword = $this->proxyInfo['username'];
            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
            }
            $this->setopt ($this->ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }
    }

    /**
     *
     * Make sure all the options are set first
     *
     */
    public function getUrl()
    {
        // Get the page
        //curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        //curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $ch = curl_init($url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
    * Method to get contents of a page using POST
    * This method follows the location for when the server issues a redirect
    * @param string $url URL of the Page
    * @return string contents of the page
    */
    public function sendPostData($postargs=FALSE)
    {
        // Setup URL for Curl
        if ($this->ch == null || $this->ch == '') {
            log_debug ("\nCurl Handle NOT initialized!\n");
            die ("\nCurl Handle NOT initialized!\n");
        }

        // More Curl settings
        /*
        curl_setopt($this->ch, CURLOPT_HEADER, TRUE); //Needed to follow 302 redirects
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        */

        /*
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER , true);     // return web page
        curl_setopt($this->ch, CURLOPT_HEADER         , true);   // return headers
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION , true);     // follow redirects
        curl_setopt($this->ch, CURLOPT_ENCODING       , "");       // handle all encodings

        curl_setopt($this->ch, CURLOPT_USERAGENT      , "spider"); // who am i
        curl_setopt($this->ch, CURLOPT_AUTOREFERER    , true);     // set referer on redirect
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT , 120);     // timeout on connect

        curl_setopt($this->ch, CURLOPT_TIMEOUT        , 120);      // timeout on response
        curl_setopt($this->ch, CURLOPT_MAXREDIRS      , 10);       // stop after 10 redirects
        */

        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';

        $options = array(
            //CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => 1,     // return web page
            CURLOPT_HEADER          => 1,     // return headers
            CURLOPT_FOLLOWLOCATION  => 1,     // follow redirects
            CURLOPT_COOKIESESSION   => 1,     // Enable Session Cookies
            CURLOPT_ENCODING        => "",       // handle all encodings

            CURLOPT_USERAGENT       => $useragent, // who am i
            CURLOPT_AUTOREFERER     => true,     // set referer on redirect
            //CURLOPT_CONNECTTIMEOUT  => 120,      // timeout on connect

            //CURLOPT_TIMEOUT         => 120,      // timeout on response
            CURLOPT_MAXREDIRS       => 10,       // stop after 10 redirects
            CURLOPT_COOKIEFILE      => '/var/www/fresh/usrfiles/webworkflow/cookies.txt',
            CURLOPT_COOKIEJAR       => '/var/www/fresh/usrfiles/webworkflow/cookies.txt'
        );

        curl_setopt_array($this->ch, $options );

        $uriParts = parse_url($url);
        //Stripping the scriptname

		log_debug($uriParts['host']);

        if ($uriParts['host'] != 'localhost' &&
            $uriParts['host'] != '127.0.0.1' ) {
            // Add Server Proxy if it exists
            if ($this->proxyInfo['server'] != '') {
                curl_setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
            }

            // Add Port Proxy if it exists
            if ($this->proxyInfo['port'] != '') {
                curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
            }

            // Add Username for Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword = $this->proxyInfo['username'];

                // Add Password Proxy if it exists
                if ($this->proxyInfo['username'] != '') {
                    $userNamePassword .= ':'.$this->proxyInfo['password'];
                }

                curl_setopt ($this->ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
            }
        }
        //*/

        log_debug("\n\n" . 'Curl->SendData() Post Data : ' . $postargs . "\n\n");
        if($postargs !== FALSE){
            curl_setopt ($this->ch, CURLOPT_POST, TRUE);
            curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $postargs);
        }

        // Get the page
        $data = curl_exec ($this->ch);

        $err     = curl_errno( $this->ch );
        $errmsg  = curl_error( $this->ch );
        $header  = curl_getinfo( $this->ch );

        if ($err != ''){
            log_debug('Curl Error: ' . $err);
            log_debug('Curl Error Message: ' . $errmsg);
        }

        log_debug('Curl Header: ' . var_export($header, true));
        //*/

        // Return Data
        return $data;
    }

    public function sendData($url, $postargs=FALSE)
    {
        $this->ch = curl_init($url);
        //$this->setProxy();
        if($postargs !== FALSE){
            curl_setopt ($this->ch, CURLOPT_POST, TRUE);
            curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $postargs);
        }
        if($this->username !== FALSE && $this->password !== FALSE) {
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->userName.':'.$this->password);
        }
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->headers != ''){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        $response = curl_exec($this->ch);
        $this->responseInfo=curl_getinfo($this->ch);
        curl_close($this->ch);
        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;
            }
        }else{
            return FALSE;
        }
    }


    private function process($url,$postargs=FALSE)
    {
        $ch = curl_init($url);

        if($postargs !== FALSE){
            curl_setopt ($ch, CURLOPT_POST, TRUE);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }

        if($this->username !== FALSE && $this->password !== FALSE)
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->userName.':'.$this->password);

        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($ch);

        $this->responseInfo=curl_getinfo($ch);
        curl_close($ch);


        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;
            }
        }else{
            return FALSE;
        }
    }


    /**
    * Method to transfer/get contents of a page
    * @param string $url URL of the Page
    * @return string contents of the page
    */
    public function exec($url)
    {
        // Setup URL for Curl
        $ch = curl_init($url);

        // More Curl settings
        curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        // Add Server Proxy if it exists
        if ($this->proxyInfo['server'] != '') {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
        }

        // Add Port Proxy if it exists
        if ($this->proxyInfo['port'] != '') {
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
        }

        // Add Username for Proxy if it exists
        if ($this->proxyInfo['username'] != '') {
            $userNamePassword = $this->proxyInfo['username'];

            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
            }

            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }

        // Get the page
        $data = curl_exec ($ch);

        // Close the CURL
        curl_close($ch);

        // Return Data
        return $data;
    }
}









/**
* CUTTING FROM code_modules/utilities/curlwrapper_class_inc.php CLASS
* TODO: to be merged
*
* Curl is a tool for transferring files with URL syntax
*
* This class is a wrapper for PHP's CURL functions integrated with
* Chisimba's Proxy Configurations. Developers can simply instantiate
* this class and request the page they want.
*
* @category  Chisimba
* @package   utilities
* @author Tohir Solomons
* @author Derek Keats
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: curlwrapper_class_inc.php 11250 2008-11-02 11:32:56Z charlvn $
* @link      http://avoir.uwc.ac.za
* Example:
*   $objCurl = $this->getObject('curl', 'utilities');
*   $objCurl->initializeCurl($url);
*   echo $objCurl->getData();
*   $objCurl->closeCurl();
* Example:
*    echo $objCurl->exec('http://ws.geonames.org/search?name_equals=Walvisbaai&style=full');
*/

class curlcmdwrapper_ extends object
{
    /**
    * @var array $proxyInfo Array Containing Proxy Details
    * @access private
    */
    private $proxyInfo;

    public $ch;
    public $options = array();

    /**
    * Constructor
    */
    /*
    public function init()
    {
        //TODO: reopen when merge complete
        //$this->setupProxy();
    }
    */

    public function curlcmdwrapper_($objConfig) {
        $this->objConfig = $objConfig;
        $this->setupProxy();
    }

    /**
    *
    * Method to extract the proxy settings from the chisimba settings
    * and prepare them for use in curl.
    *
    */
    public function setupProxy()
    {
        // Load Config Object
        $objConfig = $this->objConfig;
        // Get Proxy String
        $proxy = $objConfig->getProxy();
        // Remove http:// from beginning of string
        $proxy =  preg_replace('%\Ahttp://%i', '', $proxy);
        // Create Empty Array
        $this->proxyInfo = array('username'=>'','password'=>'','server'=>'','port'=>'',);
        // Check if string has @, indicator of username/password and server/port
        if (preg_match('/@/i', $proxy)) {
            // Split string into username and password
            preg_match_all('/(?P<userinfo>.*)@(?P<serverinfo>.*)/i', $proxy, $result, PREG_PATTERN_ORDER);
            // If it has user information, perform further split
            if (isset($result['userinfo'][0])) {
                // Split at : to get username and password
                $userInfo = explode(':', $result['userinfo'][0]);
                // Record username if it exists
                $this->proxyInfo['username'] = isset($userInfo[0]) ? $userInfo[0] : '';
                // Record password if it exists
                $this->proxyInfo['password'] = isset($userInfo[1]) ? $userInfo[1] : '';
            }
            // If it has server information, perform further split
            if (isset($result['serverinfo'][0])) {
                // Split at : to get server and port
                $serverInfo = explode(':', $result['serverinfo'][0]);
                // Record server if it exists
                $this->proxyInfo['server'] = isset($serverInfo[0]) ? $serverInfo[0] : '';
                // Record port if it exists
                $this->proxyInfo['port'] = isset($serverInfo[1]) ? $serverInfo[1] : '';
            }
        // Else only has server and port details
        } else {
            // Split at : to get server and port
            $serverInfo = explode(':', $proxy);
            // Record server if it exists
            $this->proxyInfo['server'] = isset($serverInfo[0]) ? $serverInfo[0] : '';
            // Record port if it exists
            $this->proxyInfo['port'] = isset($serverInfo[1]) ? $serverInfo[1] : '';
        }
    }

    public function initializeCurl($url)
    {
        log_debug("\n\nCURL CMD INITIALIZED: $url \n\n");
        // Setup URL for Curl
        $this->url = $url;
        //$this->setupProxy();
		$this->createCache();
    }

    public function closeCurl()
    {
        // Close the CURL
    }

    /**
    * Set a curl option.
    *
    * @link http://www.php.net/curl_setopt
    * @param mixed $theOption One of the valid CURLOPT defines.
    * @param mixed $theValue the value of the curl option.
    *
    */
    public function setopt($theOption, $theValue)
    {
        curl_setopt($this->ch, $theOption, $theValue) ;
        $this->options[$theOption] = $theValue ;
    }

    public function setProxy()
    {
        // Add Server Proxy if it exists
        if ($this->proxyInfo['server'] != '') {
            $this->setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
        }
        // Add Port Proxy if it exists
        if ($this->proxyInfo['port'] != '') {
            $this->setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
        }
        // Add Username for Proxy if it exists
        if ($this->proxyInfo['username'] != '') {
            $userNamePassword = $this->proxyInfo['username'];
            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
            }
            $this->setopt ($this->ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }
    }

    /**
     *
     * Make sure all the options are set first
     *
     */
    public function getUrl()
    {
        // Get the page
        //curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        //curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $ch = curl_init($url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
    * Method to get contents of a page using POST
    * This method follows the location for when the server issues a redirect
    * @param string $url URL of the Page
    * @return string contents of the page
    */
    public function sendPostData($postargs=FALSE)
    {

        // More Curl settings
        /*
        curl_setopt($this->ch, CURLOPT_HEADER, TRUE); //Needed to follow 302 redirects
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        */

        /*
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER , true);     // return web page
        curl_setopt($this->ch, CURLOPT_HEADER         , true);   // return headers
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION , true);     // follow redirects
        curl_setopt($this->ch, CURLOPT_ENCODING       , "");       // handle all encodings

        curl_setopt($this->ch, CURLOPT_USERAGENT      , "spider"); // who am i
        curl_setopt($this->ch, CURLOPT_AUTOREFERER    , true);     // set referer on redirect
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT , 120);     // timeout on connect

        curl_setopt($this->ch, CURLOPT_TIMEOUT        , 120);      // timeout on response
        curl_setopt($this->ch, CURLOPT_MAXREDIRS      , 10);       // stop after 10 redirects
        */





		/*
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';

        $options = array(
            //CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => 1,     // return web page
            CURLOPT_HEADER          => 1,     // return headers
            CURLOPT_FOLLOWLOCATION  => 1,     // follow redirects
            CURLOPT_COOKIESESSION   => 1,     // Enable Session Cookies
            CURLOPT_ENCODING        => "",       // handle all encodings

            CURLOPT_USERAGENT       => $useragent, // who am i
            CURLOPT_AUTOREFERER     => true,     // set referer on redirect
            //CURLOPT_CONNECTTIMEOUT  => 120,      // timeout on connect

            //CURLOPT_TIMEOUT         => 120,      // timeout on response
            CURLOPT_MAXREDIRS       => 10,       // stop after 10 redirects
            CURLOPT_COOKIEFILE      => '/var/www/fresh/usrfiles/webworkflow/cookies.txt',
            CURLOPT_COOKIEJAR       => '/var/www/fresh/usrfiles/webworkflow/cookies.txt'
        );

        curl_setopt_array($this->ch, $options );

        $uriParts = parse_url($url);
        //Stripping the scriptname

		log_debug($uriParts['host']);

        if ($uriParts['host'] != 'localhost' &&
            $uriParts['host'] != '127.0.0.1' ) {
            // Add Server Proxy if it exists
            if ($this->proxyInfo['server'] != '') {
                curl_setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
            }

            // Add Port Proxy if it exists
            if ($this->proxyInfo['port'] != '') {
                curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
            }

            // Add Username for Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword = $this->proxyInfo['username'];

                // Add Password Proxy if it exists
                if ($this->proxyInfo['username'] != '') {
                    $userNamePassword .= ':'.$this->proxyInfo['password'];
                }

                curl_setopt ($this->ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
            }
        }
        //*/

        log_debug("\n\n" . 'CurlCMD->SendData() Post Data : ' . $postargs . "\n\n");
		
		$cmdPost = '';
        if($postargs !== FALSE){
			$postStr = str_replace('&amp;', '&', $postargs);
			$pParts = explode('&', $postStr);
			foreach ($pParts as $prt) {
				$cmdPost .= ' -d '.$prt.' ';
			}
        }

        // Get the page

        $basePath = $this->objConfig->getcontentBasePath()."webworkflows/";
		$tmpFileName = $basePath . 'searchoutput_' . date('Ymd-hms'). '.html';
		//Executing Server Side curl executable
		$exec_str = "curl -o \"$tmpFileName\" -x http://cache.uwc.ac.za:8080 $cmdPost " . '"' . $this->url . '"';

		//echo $exec_str . "\n";

		exec($exec_str);
		$data = file_get_contents($tmpFileName);

        // Return Data
        return $data;
    }

    /**
     * Method to make sure the output cache exists
     *
     * @param $methods An array of full method prototype declarations: e.g. "function someMethod ()"[0]
     * @access public
     * @return HTML
     */
    public function createCache() 
    {

        $this->basePath = $this->objConfig->getcontentBasePath()."webworkflows/";
        
        //Ensuring the base directory exists
        if(!file_exists($this->basePath))
        {
            mkdir($this->basePath, 0777, true);
        }

        return TRUE;
    }




    public function sendData($url, $postargs=FALSE)
    {
        $this->ch = curl_init($url);
        //$this->setProxy();
        if($postargs !== FALSE){
            curl_setopt ($this->ch, CURLOPT_POST, TRUE);
            curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $postargs);
        }
        if($this->username !== FALSE && $this->password !== FALSE) {
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->userName.':'.$this->password);
        }
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        if ($this->headers != ''){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        $response = curl_exec($this->ch);
        $this->responseInfo=curl_getinfo($this->ch);
        curl_close($this->ch);
        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;
            }
        }else{
            return FALSE;
        }
    }


    private function process($url,$postargs=FALSE)
    {
        $ch = curl_init($url);

        if($postargs !== FALSE){
            curl_setopt ($ch, CURLOPT_POST, TRUE);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }

        if($this->username !== FALSE && $this->password !== FALSE)
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->userName.':'.$this->password);

        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_NOBODY, 0);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($ch);

        $this->responseInfo=curl_getinfo($ch);
        curl_close($ch);


        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;
            }
        }else{
            return FALSE;
        }
    }


    /**
    * Method to transfer/get contents of a page
    * @param string $url URL of the Page
    * @return string contents of the page
    */
    public function exec($url)
    {
        // Setup URL for Curl
        $ch = curl_init($url);

        // More Curl settings
        curl_setopt($this->ch, CURLOPT_HEADER, FALSE);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

        // Add Server Proxy if it exists
        if ($this->proxyInfo['server'] != '') {
            curl_setopt($this->ch, CURLOPT_PROXY, $this->proxyInfo['server']);
        }

        // Add Port Proxy if it exists
        if ($this->proxyInfo['port'] != '') {
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $this->proxyInfo['port']);
        }

        // Add Username for Proxy if it exists
        if ($this->proxyInfo['username'] != '') {
            $userNamePassword = $this->proxyInfo['username'];

            // Add Password Proxy if it exists
            if ($this->proxyInfo['username'] != '') {
                $userNamePassword .= ':'.$this->proxyInfo['password'];
            }

            curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $userNamePassword);
        }

        // Get the page
        $data = curl_exec ($ch);

        // Close the CURL
        curl_close($ch);

        // Return Data
        return $data;
    }
}


?>
