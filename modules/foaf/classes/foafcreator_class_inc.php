<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class foafcreator extends object
{
    /**
     * @var object XML_Tree object containing the FOAF RDF/XML Tree
     */
    public $foaftree = null;
    /**
     * @var array Contains all namespaces in use
     */
    public $namespaces = array();
    /**
     * @var array Contains XML_Tree Child nodes for all FOAF elements
     */
    public $children = array();
    /**
     * @var object XML_Tree object for the FOAF
     */
    public $xml_tree = null;
    /**
     * Standard init method (constructor)
     *
     * @param void
     * @access public
     * @return void
     */
    public function init() 
    {
        $this->_setXmlns();
    }
    /**
     * Check if a property is allowed for the current foaf:Agent
     *
     * @param string $property name of the Property to check. Without a namespace
     * @access public
     * @return boolean
     */
    public function isAllowedForAgent($property) 
    {
        $property = strtolower($property);
        $common = array(
            'name',
            'maker',
            'depiction',
            'fundedby',
            'logo',
            'page',
            'theme',
            'dnachecksum',
            'title',
            'nick',
            'givenname',
            'phone',
            'mbox',
            'mbox_sha1sum',
            'gender',
            'jabberid',
            'aimchatid',
            'icqchatid',
            'yahoochatid',
            'msnchatid',
            'homepage',
            'weblog',
            'made',
            'holdsaccount'
        );
        $person = array(
            'geekcode',
            'interest',
            'firstname',
            'surname',
            'family_name',
            'plan',
            'img',
            'myersbriggs',
            'workplacehomepage',
            'workinfohomepage',
            'schoolhomepage',
            'knows',
            'publications',
            'currentproject',
            'pastproject',
            'based_near'
        );
        $organization = array();
        $group = array(
            'member',
            'membershipclass'
        );
        if (in_array($property, $common) || in_array($property, $ {
            $this->agent
        })) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * method to create new FOAF Agent
     *
     * @param string $agent_type Agent type, this can be Person, Organization, Group, Agent.
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_Organization FOAF Specification - foaf:Organization
     * @link http://xmlns.com/foaf/0.1/#term_Group FOAF Specification - foaf:Group
     * @link http://xmlns.com/foaf/0.1/#term_Person FOAF Specification - foaf:Person
     * @link http://xmlns.com/foaf/0.1/#term_Agent FOAF Specification - foaf:Agent
     */
    public function newAgent($agent_type = 'Person') 
    {
        require_once $this->getResourcePath('Tree.php', 'foaf');
        $this->xml_tree = new XML_Tree;
        $agent_type = strtolower($agent_type);
        $this->agent = $agent_type;
        switch ($agent_type) {
            case 'group':
                $this->foaftree = &$this->xml_tree->addRoot('foaf:Group');
                break;

            case 'organization':
                $this->foaftree = &$this->xml_tree->addRoot('foaf:Organization');
                break;

            case 'agent':
                $this->foaftree = &$this->xml_tree->addRoot('foaf:Agent');
            case 'person':
            default:
                $this->foaftree = &$this->xml_tree->addRoot('foaf:Person');
                break;
        }
    }
    /**
     * Set the foaf:name of the Agent
     *
     * @param string $name Name for the Agent.
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_name FOAF Specification - foaf:name
     * @todo Allow for the xml:lang to be specified
     */
    public function setName($name) 
    {
        $this->children['name'] = &$this->foaftree->addChild('foaf:name', $name);
    }
    /**
     * Add a foaf:depiction element
     *
     * @param string $uri URI For the Depicted image
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_depiction FOAF Specification - foaf:depiction
     */
    public function addDepiction($uri) 
    {
        $this->children['depiction'][] = &$this->foaftree->addChild('foaf:depiction', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * Add a foaf:fundedBy element
     *
     * @param string $uri URI for the funder
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_fundedBy FOAF Specification - foaf:fundedBy
     */
    public function addFundedBy($uri) 
    {
        $this->children['fundedby'][] = &$this->foaftree->addChild('foaf:fundedBy', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * Add a foaf:logo element
     *
     * @param string $uri URI for Logo Image
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_logo FOAF Specification - foaf:logo
     */
    public function addLogo($uri) 
    {
        $this->children['logo'][] = &$this->foaftree->addChild('foaf:logo', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * Add a foaf:page element
     *
     * @param string $document_uri URI for the Document being reference
     * @param string $title Title for the Document
     * @param string $description Description for the Document
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_page FOAF Specification - foaf:page
     */
    public function addPage($document_uri, $title = null, $description = null) 
    {
        $page = &$this->foaftree->addChild('foaf:page');
        $document = &$page->addChild('foaf:Document', '', array(
            'rdf:about' => $document_uri
        ));
        if (!is_null($title)) {
            $document->addChild('dc:title', $title);
        }
        if (!is_null($description)) {
            $document->addChild('dc:description', $description);
        }
        $this->children['page'][] = &$page;
    }
    /**
     * Add a foaf:theme element
     *
     * @param string $uri URI for the Theme
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_theme FOAF Specification - foaf:theme
     */
    public function addTheme($uri) 
    {
        $this->children['theme'][] = &$this->foaftree->addChild('foaf:theme', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * set foaf:title
     *
     * @param string $title foaf:Agents title
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_title FOAF Specification - foaf:title
     */
    public function setTitle($title) 
    {
        $this->children['title'] = &$this->foaftree->addChild('foaf:title', $title);
    }
    /**
     * Add a foaf:nick element
     *
     * @param string $nick foaf:Agents Nickname
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_nick FOAF Specification - foaf:nick
     */
    public function addNick($nick) 
    {
        $this->children['nick'][] = &$this->foaftree->addChild('foaf:nick', $nick);
    }
    /**
     * set foaf:givenname
     *
     * @param string $given_name foaf:Agents Given Name
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_givenname FOAF Specification - foaf:givenname
     */
    public function setGivenName($given_name) 
    {
        $this->children['givenname'] = &$this->foaftree->addChild('foaf:givenname', $given_name);
    }
    /**
     * Add a foaf:phone element
     *
     * @param string $phone foaf:Agents Phone Number
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_phone FOAF Specification - foaf:phone
     */
    public function addPhone($phone) 
    {
        if (substr($phone, 0, 4) != 'tel:') {
            $phone = 'tel:'.$phone;
        }
        $this->children['phone'][] = &$this->foaftree->addChild('foaf:phone', '', array(
            'rdf:resource' => $phone
        ));
    }
    /**
     * Add a foaf:mbox or foaf:mbox_sha1sum element
     *
     * @param string $mbox Mailbox, either a mailto:addr, addr or an sha1 sum of mailto:addr
     * @param boolean $sha1 Whether or not to use foaf:mbox_sha1sum
     * @param boolean $is_sha1_hash Whether or not given $mbox is already an sha1 sum
     * @access public
     * @return void
     * @see XML_FOAF::setMboxSha1Sum
     * @link http://xmlns.com/foaf/0.1/#term_mbox_sha1sum FOAF Specification - foaf:mbox_sha1sum
     * @link http://xmlns.com/foaf/0.1/#term_mbox FOAF Specification - foaf:mbox
     */
    public function addMbox($mbox, $sha1 = false, $is_sha1_hash = false) 
    {
        if (substr($mbox, 0, 7) != 'mailto:' && $is_sha1_hash == false) {
            $mbox = 'mailto:'.$mbox;
        }
        if ($sha1 == true) {
            if ($is_sha1_hash == false) {
                $mbox = sha1($mbox);
            }
            $this->children['mbox_sha1sum'][] = &$this->foaftree->addChild('foaf:mbox_sha1sum', $mbox);
        } else {
            $this->children['mbox'][] = &$this->foaftree->addChild('foaf:mbox', '', array(
                'rdf:resource' => $mbox
            ));
        }
    }
    /**
     * Add a foaf:mbox_sha1sum element
     *
     * @param string $mbox Mailbox, either a mailto:addr, addr or an sha1 sum of mailto:addr
     * @param boolean $is_sha1_hash Whether or not given $mbox is already an sha1 sum
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_mbox_sha1sum FOAF Specification - foaf:mbox_sha1sum
     */
    public function addMboxSha1Sum($mbox, $is_sha1_sum = false) 
    {
        $this->addMbox($mbox, true, $is_sha1_sum);
    }
    /**
     * set foaf:gender
     *
     * @param string $gender foaf:Agents Gender (typically 'male' or 'female')
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_gender FOAF Specification - foaf:gender
     */
    public function setGender($gender) 
    {
        $this->children['gender'] = &$this->foaftree->addChild('foaf:gender', strtolower($gender));
    }
    /**
     * Add a foaf:jabberID element
     *
     * @param string $jabbed_id A Jabber ID
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_jabberID FOAF Specification - foaf:jabberID
     */
    public function addJabberID($jabber_id) 
    {
        $this->children['jabbberid'][] = &$this->foaftree->addChild('foaf:jabberID', $jabber_id);
    }
    /**
     * Add a foaf:aimChatID element
     *
     * @param string $aim_chat_id An AIM Username
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_aimChatID FOAF Specification - foaf:aimChatID
     */
    public function addAimChatID($aim_chat_id) 
    {
        $this->children['aimchatid'][] = &$this->foaftree->addChild('foaf:aimChatID', $aim_chat_id);
    }
    /**
     * Add a foaf:icqChatID element
     *
     * @param string $icq_chat_id An ICQ Number
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_icqChatID FOAF Specification - foaf:icqChatID
     */
    public function addIcqChatID($icq_chat_id) 
    {
        $this->children['icqchatid'][] = &$this->foaftree->addChild('foaf:icqChatID', $icq_chat_id);
    }
    /**
     * Add a foaf:yahooChatID element
     *
     * @param string $yahoo_chat_id A Yahoo! Messenger ID
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_yahooChatID FOAF Specification - foaf:yahooChatID
     */
    public function addYahooChatID($yahoo_chat_id) 
    {
        $this->children['yahoochatid'][] = &$this->foaftree->addChild('foaf:yahooChatID', $yahoo_chat_id);
    }
    /**
     * Add a foaf:msnChatID element
     *
     * @param string $msn_chat_id A MSN Chat username
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_msnChatID FOAF Specification - foaf:msnChatID
     */
    public function addMsnChatID($msn_chat_id) 
    {
        $this->children['msnchatid'][] = &$this->foaftree->addChild('foaf:msnChatID', $msn_chat_id);
    }
    /**
     * Add a foaf:OnlineAccount element
     *
     * @param string $account_name Account Name
     * @param string $account_service_homepage URI to Account Service Homepage
     * @param string $acount_type Account type (e.g http://xmlns.com/foaf/0.1/OnlineChatAccount)
     * @access public
     * @return void
     * @see XML_FOAF::setOnlineChatAccount,XML_FOAF::setMsnChatID,XML_FOAF::setIcqChatID,XML_FOAF::setAimChatID
     * @see XML_FOAF::setYahooChatID,XML_FOAF::setJabberID,XML_FOAF::setOnlineGamingAccount,XML_FOAF::setOnlineEcommerceAccount
     * @link http://xmlns.com/foaf/0.1/#term_accountServiceHomepage FOAF Specification - foaf:accountServiceHomepage
     * @link http://xmlns.com/foaf/0.1/#term_accountName FOAF Specification - foaf:accountName
     * @link http://xmlns.com/foaf/0.1/#term_OnlineAccount FOAF Specification - foaf:OnlineAccount
     * @link http://xmlns.com/foaf/0.1/#term_holdsAccount FOAF Specification - foaf:holdsAccount
     */
    public function addOnlineAccount($account_name, $account_service_homepage = null, $account_type = null) 
    {
        $holds_account = &$this->foaftree->addChild('foaf:holdsAccount');
        $online_account = &$holds_account->addChild('foaf:OnlineAccount');
        $online_account->addChild('foaf:accountName', $account_name);
        if (!is_null($account_service_homepage)) {
            $online_account->addChild('foaf:accountServiceHomepage', '', array(
                'rdf:resource' => $account_service_homepage
            ));
        }
        if (!is_null($account_type)) {
            $online_account->addChild('rdf:type', '', array(
                'rdf:resource' => $account_type
            ));
        }
        $this->children['holdsaccount'][] = &$holds_account;
    }
    /**
     * Add a foaf:OnlineChatAccount element
     *
     * @param string $account_name Account Name
     * @param string $account_service_homepage URI Tto Account Service Homepage
     * @access public
     * @return void
     * @see XML_FOAF::setOnlineAccount,XML_FOAF::setMsnChatID,XML_FOAF::setIcqChatID,XML_FOAF::setAimChatID
     * @see XML_FOAF::setYahooChatID,XML_FOAF::setJabberID,XML_FOAF::setOnlineGamingAccount,XML_FOAF::setOnlineEcommerceAccount
     * @link http://xmlns.com/foaf/0.1/#term_accountServiceHomepage FOAF Specification - foaf:accountServiceHomepage
     * @link http://xmlns.com/foaf/0.1/#term_accountName FOAF Specification - foaf:accountName
     * @link http://xmlns.com/foaf/0.1/#term_OnlineChatAccount FOAF Specification - foaf:OnlineChatAccount
     * @link http://xmlns.com/foaf/0.1/#term_holdsAccount FOAF Specification - foaf:holdsAccount
     */
    public function addOnlineChatAccount($account_name, $account_service_homepage) 
    {
        $holds_account = &$this->foaftree->addChild('foaf:holdsAccount');
        $online_chat_account = &$holds_account->addChild('foaf:OnlineChatAccount');
        $online_chat_account->addChild('foaf:accountName', $account_name);
        if (!is_null($account_service_homepage)) {
            $online_chat_account->addChild('foaf:accountServiceHomepage', '', array(
                'rdf:resource' => $account_service_homepage
            ));
        }
        $this->children['holdsaccount'][] = &$holds_account;
    }
    /**
     * Add a foaf:OnlineGamingAccount element
     *
     * @param string $account_name Account Name
     * @param string $account_service_homepage URI Tto Account Service Homepage
     * @access public
     * @return void
     * @see XML_FOAF::setOnlineAccount,XML_FOAF::setMsnChatID,XML_FOAF::setIcqChatID,XML_FOAF::setAimChatID
     * @see XML_FOAF::setYahooChatID,XML_FOAF::setJabberID,XML_FOAF::setOnlineChatAccount,XML_FOAF::setOnlineEcommerceAccount
     * @link http://xmlns.com/foaf/0.1/#term_accountServiceHomepage FOAF Specification - foaf:accountServiceHomepage
     * @link http://xmlns.com/foaf/0.1/#term_accountName FOAF Specification - foaf:accountName
     * @link http://xmlns.com/foaf/0.1/#term_OnlineChatAccount FOAF Specification - foaf:OnlineChatAccount
     * @link http://xmlns.com/foaf/0.1/#term_holdsAccount FOAF Specification - foaf:holdsAccount
     */
    public function addOnlineGamingAccount($account_name, $account_service_homepage) 
    {
        $holds_account = &$this->foaftree->addChild('foaf:holdsAccount');
        $online_gaming_account = &$holds_account->addChild('foaf:OnlineGamingAccount');
        $online_gaming_account->addChild('foaf:accountName', $account_name);
        if (!is_null($account_service_homepage)) {
            $online_gaming_account->addChild('foaf:accountServiceHomepage', '', array(
                'rdf:resource' => $account_service_homepage
            ));
        }
        $this->children['holdsaccount'][] = &$holds_account;
    }
    /**
     * Add a foaf:OnlineEcommerceAccount element
     *
     * @param string $account_name Account Name
     * @param string $account_service_homepage URI Tto Account Service Homepage
     * @access public
     * @return void
     * @see XML_FOAF::setOnlineAccount,XML_FOAF::setMsnChatID,XML_FOAF::setIcqChatID,XML_FOAF::setAimChatID
     * @see XML_FOAF::setYahooChatID,XML_FOAF::setJabberID,XML_FOAF::setOnlineChatAccount,XML_FOAF::setOnlineGamingAccount
     * @link http://xmlns.com/foaf/0.1/#term_accountServiceHomepage FOAF Specification - foaf:accountServiceHomepage
     * @link http://xmlns.com/foaf/0.1/#term_accountName FOAF Specification - foaf:accountName
     * @link http://xmlns.com/foaf/0.1/#term_OnlineChatAccount FOAF Specification - foaf:OnlineChatAccount
     * @link http://xmlns.com/foaf/0.1/#term_holdsAccount FOAF Specification - foaf:holdsAccount
     */
    public function addOnlineEcommerceAccount($account_name, $account_service_homepage) 
    {
        $holds_account = &$this->foaftree->addChild('foaf:holdsAccount');
        $online_ecommerce_account = &$holds_account->addChild('foaf:OnlineEcommerceAccount');
        $online_ecommerce_account->addChild('foaf:accountName', $account_name);
        if (!is_null($account_service_homepage)) {
            $online_ecommerce_account->addChild('foaf:accountServiceHomepage', '', array(
                'rdf:resource' => $account_service_homepage
            ));
        }
        $this->children['holdsaccount'][] = &$holds_account;
    }
    /**
     * Add a foaf:homepage element
     *
     * @param string $uri URI for the Homepage
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_homepage FOAF Specification - foaf:homepage
     */
    public function addHomepage($uri) 
    {
        $this->children['homepage'][] = &$this->foaftree->addChild('foaf:homepage', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * Add a foaf:weblog element
     *
     * @param string $uri URI for the weblog
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_weblog FOAF Specification - foaf:weblog
     */
    public function addWeblog($uri) 
    {
        $this->children['weblog'][] = &$this->foaftree->addChild('foaf:weblog', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * Add a foaf:made element
     *
     * @param string $uri URI for the thing foaf:Agent made
     * @access public
     * @return void
     * @link http://xmlns.com/foaf/0.1/#term_made
     */
    public function addMade($uri) 
    {
        $this->children['made'][] = &$this->foaftree->addChild('foaf:made', '', array(
            'rdf:resource' => $uri
        ));
    }
    /* foaf:Person */
    /**
     * set foaf:geekcode
     *
     * @param string $geek_code foaf:Agents Geek Code
     * @access public
     * @return boolean
     * @link http://www.joereiss.net/geek/geek.html Geek Code Generator
     * @link http://www.geekcode.com/geek.html Geek Code official website
     * @link http://xmlns.com/foaf/0.1/#term_geekcode FOAF Specification - foaf:geekcode
     */
    public function setGeekcode($geek_code) 
    {
        if ($this->isAllowedForAgent('geekcode')) {
            $this->children['geekcode'] = &$this->foaftree->addChild('foaf:geekcode', $geek_code);
            return true;
        } else {
            return false;
        }
    }
    /**
     * set foaf:firstName
     *
     * @param string $first_name foaf:Agents First Name
     * @access public
     * @return boolean
     * @see XML_FOAF::setGivenName,XML_FOAF::setName
     * @link http://xmlns.com/foaf/0.1/#term_firstName FOAF Specification - foaf:firstName
     */
    public function setFirstName($first_name) 
    {
        if ($this->isAllowedForAgent('firstname')) {
            $this->children['firstname'] = &$this->foaftree->addChild('foaf:firstName', $first_name);
            return true;
        } else {
            return false;
        }
    }
    /**
     * set foaf:surname
     *
     * @param string $surname foaf:Agents Surname
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_surname FOAF Specification - foaf:surname
     */
    public function setSurname($surname) 
    {
        if ($this->isAllowedForAgent('surname')) {
            $this->children['surname'] = &$this->foaftree->addChild('foaf:surname', $surname);
            return true;
        } else {
            return false;
        }
    }
    /**
     * set foaf:familyName
     *
     * @param string $family_name foaf:Agents Family name
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_firstName FOAF Specification - foaf:familyName
     */
    public function setFamilyName($family_name) 
    {
        if ($this->isAllowedForAgent('family_name')) {
            $this->children['familyname'] = &$this->foaftree->addChild('foaf:family_name', $family_name);
            return true;
        } else {
            return false;
        }
    }
    /**
     * set foaf:plan
     *
     * @param string $plan .plan file contents
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_plan FOAF Specification - foaf:plan
     */
    public function setPlan($plan) 
    {
        if ($this->isAllowedForAgent('plan')) {
            $this->children['plan'] = &$this->foaftree->addChild('foaf:plan', $plan);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:img element
     *
     * @param string $uri URI for the img being depicted
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_img FOAF Specification - foaf:img
     */
    public function addImg($uri) 
    {
        if ($this->isAllowedForAgent('img')) {
            $this->children['img'][] = &$this->foaftree->addChild('foaf:img', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:myersBriggs elements
     *
     * @param string $myers_briggs Myers Briggs Personality classification
     * @access public
     * @return boolean
     * @link http://www.teamtechnology.co.uk/tt/t-articl/mb-simpl.htm Myers Briggs - Working out your type
     * @link http://xmlns.com/foaf/0.1/#term_myersBriggs FOAF Specification - foaf:myersBriggs
     */
    public function addMyersBriggs($myers_briggs) 
    {
        if ($this->isAllowedForAgent('myersbriggs')) {
            $this->children['myersbriggs'][] = &$this->foaftree->addChild('foaf:myersBriggs', $myers_briggs);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:workplaceHome element
     *
     * @param string $uri URI for the Workplace Homepage
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_workplaceHomepage FOAF Specification - foaf:workplaceHomepage
     */
    public function addWorkplaceHomepage($uri) 
    {
        if ($this->isAllowedForAgent('workplaceHomepage')) {
            $this->children['workplacehomepage'][] = &$this->foaftree->addChild('foaf:workplaceHomepage', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:workInfoHomepage element
     *
     * @param string $uri URI for Work Information Homepage
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_workInfoHomepage FOAF Specification - foaf:workInfoHomepage
     */
    public function addWorkInfoHomepage($uri) 
    {
        if ($this->isAllowedForAgent('workInfoHomepage')) {
            $this->children['workinfohomepage'][] = &$this->foaftree->addChild('foaf:workInfoHomepage', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:schoolHomepage element
     *
     * @param string $uri URI for School Homepage
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_schoolHomepage FOAF Specification - foaf:schoolHomepage
     */
    public function addSchoolHomepage($uri) 
    {
        if ($this->isAllowedForAgent('schoolHomepage')) {
            $this->childen['schoolhomepage'][] = $this->foaftree->addChild('foaf:schoolHomepage', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:publications elements
     *
     * @param string $uri URI to the publications
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_publications FOAF Specification - foaf:publications
     */
    public function addPublications($uri) 
    {
        if ($this->isAllowedForAgent('publications')) {
            $this->children['publications'][] = &$this->foaftree->addChild('foaf:publications', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:currentProject element
     *
     * @param string $uri URI to a current projects homepage
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_currentProject FOAF Specification - foaf:currentProject
     */
    public function addCurrentProject($uri) 
    {
        if ($this->isAllowedForAgent('currentProject')) {
            $this->children['currentproject'][] = &$this->foaftree->addChild('foaf:currentProject', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * Add a foaf:pastProject element
     *
     * @param string $uri URI to a past projects homepage
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_pastProject FOAF Specification - foaf:pastProject
     */
    public function addPastProject($uri) 
    {
        if ($this->isAllowedForAgent('pastProject')) {
            $this->children['pastproject'][] = &$this->foaftree->addChild('foaf:pastProject', '', array(
                'rdf:resource' => $uri
            ));
            return true;
        } else {
            return false;
        }
    }
    /**
     * set foaf:basedNear
     *
     * @param float $geo_lat Latitute for the geo:Point
     * @param float $geo_long Longitude for the geo:Point
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_based_near FOAF Specification - foaf:basedNear
     * @link http://www.w3.org/2003/01/geo/ An RDF Geo Vocabulary: Point/lat/long/alt
     * @link http://esw.w3.org/topic/GeoInfo GeoInfo Wiki
     * @link http://rdfweb.org/topic/UsingBasedNear Using foaf:based_near
     */
    public function setBasedNear($geo_lat, $geo_long) 
    {
        if ($this->isAllowedForAgent('based_near')) {
            $this->namespaces['geo'] = 'http://www.w3.org/2003/01/geo/wgs84_pos#';
            $based_near = &$this->foaftree->addChild('foaf:based_near');
            $geo_point = &$based_near->addChild('geo:Point', '', array(
                'geo:lat' => $geo_lat,
                'geo:long' => $geo_long
            ));
            $this->children['basednear'][] = &$based_near;
            return true;
        } else {
            return false;
        }
    }
    /* foaf:Person && foaf:Group */
    /**
     * Add a foaf:interest element
     *
     * @param string $uri URI with Info about the Interest
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_interest
     */
    public function addInterest($uri) 
    {
        if ($this->isAllowedForAgent('interest')) {
            $this->children['interest'][] = &$this->foaftree->addChild('foaf:interest', '', array(
                'rdf:resource' => $uri
            ));
        } else {
            return FALSE;
        }
    }
    /* foaf:Group */
    /**
     * Add a foaf:member element
     *
     * @param object $foaf_agent XML_FOAF object (with a foaf:agent set)
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_member FOAF Specification - foaf:member
     */
    public function &addMember(&$foaf_agent) 
    {
        if ($this->isAllowedForAgent('member')) {
            $member = &$this->foaftree->addChild('foaf:member');
            $member->addChild($foaf_agent);
            $this->children['member'][] = &$member;
            return true;
        } else {
            return false;
        }
    }
    /**
     * Set foaf:membershipClass
     *
     * @param mixed $membership_class XML String or XML_Tree/XML_Tree_Node object
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_membershipClass FOAF Specification - foaf:membershipClass
     */
    public function setMembershipClass(&$membership_class) 
    {
        if ($this->isAllowedForAgent('membershipClass')) {
            if (is_string($membership_class)) {
                $membership_tree = new XML_Tree;
                $membership_tree->getTreeFromString($membership_class);
                $this->children['membershipclass'] = &$this->foaftree->addChild($membership_tree);
            } else {
                $this->children['membershipclass'] = &$this->foaftree->addChild($membership_class);
            }
            return true;
        } else {
            return false;
        }
    }
    /* end of Agent only methods */
    /**
     * set rdfs:seeAlso
     *
     * @param string $uri URI for the resource
     * @access public
     * @return boolean
     * @link http://www.w3.org/TR/rdf-schema/#ch_seealso RDF Schema Specification - rdfs:seeAlso
     */
    public function addSeeAlso($uri) 
    {
        $this->children['seealso'][] = &$this->foaftree->addChild('rdfs:seeAlso', '', array(
            'rdf:resource' => $uri
        ));
    }
    /**
     * set a foaf:knows
     *
     * @param object $foaf_agent XML_FOAF Object for the foaf:knows Agent
     * @access public
     * @return boolean
     * @link http://xmlns.com/foaf/0.1/#term_knows FOAF Specification - foaf:knows
     */
    public function addKnows(&$foaf_agent) 
    {
        //var_dump($foaf_agent);
        $this->knows = &$this->foaftree->addChild('foaf:knows');
        $this->knows->addChild($foaf_agent->foaftree);
        return true;
    }
    /**
     * Add an XML_Tree, XML_Tree_Node object or XML String to the FOAF
     *
     * @param mixed $xml_tree XML_Tree, XML_Tree_Node or XML String
     * @access public
     * @return boolean
     */
    public function addChild(&$xml_tree) 
    {
        if (is_array($xml_tree)) {
            if (is_string($xml_tree['xml'])) {
                $tree = new XML_Tree;
                $tree->getTreeFromString($xml_tree['xml']);
                $xml_tree['child']->addChild($tree);
            } else {
                $xml_tree['child']->addChild($xml_tree['xml']);
            }
        } else {
            if (is_string($xml_tree)) {
                $tree = new XML_Tree;
                $tree->getTreeFromString($xml_tree);
                $this->foaftree->addChild($tree);
            } else {
                $this->foaftree->addChild($xml_tree);
            }
        }
    }
    /**
     * Echo the FOAF RDF/XML tree
     *
     * @param boolean $without_rdf Ouput RDF/XML inside <rdf:RDF> root elements
     * @access public
     * @return boolean
     */
    public function dump($without_rdf = false) 
    {
        echo $this->get($without_rdf);
        return true;
    }
    /**
     * Return the FOAF RDF/XML tree
     *
     * @param boolean $without_rdf Return RDF/XML inside <rdf:RDF> root element
     * @access public
     * @return string
     */
    public function toXML($without_rdf = false) 
    {
        if ($without_rdf == false) {
            $foaf = "<rdf:RDF ".$this->_getXmlns() .">\n".$this->foaftree->get() ."\n</rdf:RDF>";
        } else {
            $foaf = $this->foaftree->get();
        }
        require_once 'XML/Beautifier.php';
        $beautifier = new XML_Beautifier();
        $foaf = $beautifier->formatString($foaf);
        return $foaf;
    }
    /**
     * Alias for toXML
     *
     * @param boolean $without_rdf Return RDF/XML inside <rdf:RDF> root element
     * @access public
     * @return string
     */
    public function get($without_rdf = false) 
    {
        return $this->toXML($without_rdf);
    }
    /**
     * Set an XML Namespace
     *
     * @param string $qualifier XML Namespace qualifier
     * @param string $uri XML Namespace URI
     * @access public
     * @return boolean
     */
    public function addXmlns($qualifier, $uri) 
    {
        $this->namespaces[$qualifier] = $uri;
    }
    public function toFile($path, $filename, $data) 
    {
        $fp = @fopen($path."/".$filename, 'wb');
        @fwrite($fp, $data);
        @fclose($fp);
        return TRUE;
    }
    /**
     * Return XML Namespaces as xml attributes
     *
     * @access private
     * @return string
     */
    public function _getXmlns() 
    {
        $namespaces = '';
        foreach($this->namespaces as $qualifier => $uri) {
            $namespaces.= ' xmlns:'.$qualifier.' = "'.$uri.'"';
        }
        return $namespaces;
    }
    public function foafFactory($person, $organization, $knows) 
    {
        //person details
        $firstname = $person['firstname'];
        $surname = $person['surname'];
        $name = $firstname." ".$surname;
        $title = $person['title'];
        $mbox = $person['email'];
        $homepage = $person['homepage'];
        $weblog = $person['weblog'];
        $seealso = $person['seealso'];
        //organization
        $oVar = $organization['name'];
        $oName = $organization['name'];
        $oHomepage = $organization['homepage'];
        //knows
        //@todo
        //construct the foaf
        $this->newAgent('Person');
        $this->setName($name);
        $this->setTitle($title);
        $this->setFirstName($firstname);
        $this->setSurname($surname);
        $this->addMbox($mbox);
        $this->addHomepage($homepage);
        $this->addWeblog($weblog);
        $this->addSeeAlso($seealso);
        //$this->newAgent('Organization');
        
    }
    /**
     * Set default XML Namespaces
     *
     * @access private
     * @return void
     */
    private function _setXmlns() 
    {
        $this->namespaces['foaf'] = "http://xmlns.com/foaf/0.1/";
        $this->namespaces['rdf'] = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
        $this->namespaces['rdfs'] = "http://www.w3.org/2000/01/rdf-schema#";
        $this->namespaces['dc'] = "http://purl.org/dc/elements/1.1/";
    }
}
?>