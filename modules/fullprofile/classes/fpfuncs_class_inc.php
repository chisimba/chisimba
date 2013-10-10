<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
/**
 * Class fpfuncs which contains functions of the  full profile module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright 2010 Wits University
 * @license http://opensource.org/licenses/lgpl-2.1.php
 * @package fullprofile
 */
class fpfuncs extends dbTable
{
   /** @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

   /** @var object $objUser: The user class of the security module
    * @access public
    */
   public $objUser;

   /** @var object $objDbTripleStore: The db triplestore class of the triplestore module
    * @access public
    */
   public $objDbTripleStore;

   /** @var object $objDbActivity: The db activity class of the activitystreamer module
    * @access public
    */
   public $objDbActivity;

   /**
    * @var string $userId: The user id of the current logged in user
    * @access public
    */
    public $userId;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // system classes
        $this->objLanguage = $this->getObject('language','language');
        $this->objUser = $this->getObject('user','security');
        $this->userId = $this->objUser->userId();
	$this->objDbTripleStore = $this->getObject('dbtriplestore', 'triplestore');
        $this->objDbActivity = $this->getObject('activitydb', 'activitystreamer');
        $this->objUserContext = $this->getObject('usercontext', 'context');
        $this->objDbGroups = $this->getObject('dbgroups', 'interestgroups');
        $this->objDbFoaf = $this->getObject('dbfoaf', 'foaf');
    }

    /**
    * Method to a users triples
    *
    * @access public
    * @param string $userId: The users Id
    * @param string $groupBy
    * @return array $result An array of the users triples
    */
    public function getTriples($userId, $groupBy = 'predicate')
    {
	//Get the users triples
        $resultArr = array();
	$result = $this->objDbTripleStore->getTriples(array('subject'=>$userId), array(), $groupBy);

        return $result;
    }

    /**
     * Method to get a users site activity from the activity streamer
     *
     * @param string $userId The id of the user
     * @param int $limit The number of entries to retrieve
     * @return array $activityArr An array of the users latest activity on the site
     */
    public function getActivity($userId, $limit = "25")
    {
        $filter = "WHERE createdby = {$userId}";
        $activityArr = $this->objDbActivity->getActivities($filter, $limit);

        return $activityArr;
    }

    /*
     * Method to get a users contexts
     *
     * @param string $userId The user id
     * @return array $contextArr An array of all contexts the user belongs to
     */
    public function getContexts($userId)
    {
        // Get all user contents
        $contexts = $this->objUserContext->getUserContext($userId);

        return $contexts;
    }

    /**
     * Method to return a triple as a readable string
     *
     * @param array $triple The triple
     * @return string $output The readable string
     */
    public function tripleToString($triple)
    {
        $output = "";
        switch($triple['predicate']){
            case 'isMentorOf':
                $subject = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$triple['subject'])).'">'.$this->objUser->getSmallUserImage($triple['subject']).$this->objUser->fullname($triple['subject']).'</a>';
                $object = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$triple['object'])).'">'.$this->objUser->getSmallUserImage($triple['object']).$this->objUser->fullname($triple['object']).'</a>';

                $output = $subject.' is mentoring '.$object;
                break;

            case 'isStudentOf':
                $subject = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$triple['subject'])).'">'.$this->objUser->getSmallUserImage($triple['subject']).$this->objUser->fullname($triple['subject']).'</a>';
                $object = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$triple['object'])).'">'.$this->objUser->getSmallUserImage($triple['object']).$this->objUser->fullname($triple['object']).'</a>';

                $output = $subject.' is being mentored by '.$object;
                break;

            case 'isGroupMember':
                $subject = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$triple['subject'])).'">'.$this->objUser->fullname($triple['subject']).'</a>';
                $object = $this->objDbGroups->getGroupName($triple['object']);

                $output = $subject.' is a member of the '.$object.' group';
                break;

            case 'isGroupAdmin':
                $subject = '<a href="'.$this->uri(array('action'=>'viewprofile', 'userid'=>$triple['subject'])).'">'.$this->objUser->fullname($triple['subject']).'</a>';
                $object = $this->objDbGroups->getGroupName($triple['object']);

                $output = $subject.' is an admin of the '.$object.' group';
                break;

        }

        return $output;
    }

    /**
     * Method to remove a friend
     *
     * @param string $id The id of the entry to be removed
     * @access public
     */
    public function removeFriend($id)
    {
        $result = $this->objDbFoaf->removeFriend($id);

        return $result;
    }

    /**
     * Method to add a friend
     *
     * @param string $userid The id of the user
     * @param string $fuserid The id of the friend
     * @access public
     */
    public function addFriend($userid, $fuserid)
    {
        $friend = array('userid'=>$userid, 'fuserid'=>$fuserid);
        $result = $this->objDbFoaf->insertFriend($friend);
        return $result;
    }

    /**
     *
     */

}

?>