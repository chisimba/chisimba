<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
}
/**
 * Class mentorfuncs which contains
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @copyright 2010 Wits University
 * @license http://opensource.org/licenses/lgpl-2.1.php
 * @package mentors
 */
class mentorfuncs extends dbTable
{
   /** @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;

   /** @var object $objUser: The user class of the security module
    * @access public
    */
   public $objUser;

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
        $this->objDbWiki = $this->getObject('dbwiki', 'wiki');
    }
	
    /**
    * Method to get the user id of the mentors of a given user
    *
    * @access public
    * @param string $userId: The users Id
    * @return array $resultArr The user id of the users mentors.
    */
    public function getMentors($userId)
    {
	//Get the users mentor
        $resultArr = array();
	$result = $this->objDbTripleStore->getTriples(array('predicate'=>'isMentorOf', 'object'=>$userId), array('subject'));
        if(is_array($result) && count($result) > 0){
            foreach($result as $res){
                $resultArr[] = $res['subject'];
            }
        }
        return $resultArr;
    }

    /**
    * Method to get the user id of the students of a given user
    *
    * @access public
    * @param string $userId: The users Id
    * @return array $studentId The user id of the users mentors.
    */
    public function getStudents($userId)
    {
	//Get the users mentor
        $resultArr = array();
	$result = $this->objDbTripleStore->getTriples(array('predicate'=>'isMentorOf', 'subject'=>$userId), array('object'));
        if(is_array($result) && count($result) > 0){
            foreach($result as $res){
                $resultArr[] = $res['object'];
            }
        }

        return $resultArr;
    }

    /**
    * Method to create a metor/student relationship
    *
    * @access public
    * @param string $mentorId: The user id of the users mentor
    * @return bool
    */
    public function addMentor($mentorId, $userId)
    {
	//Check if relationship exists
        $results = $this->objDbTripleStore->getTriples(array('predicate'=>'isMentorOf', 'object'=>$userId), array('userId'));
        if(is_array($result) && count($result)>0){
            $return = 'Already mentoring user';
	} else {
           $return = $this->objDbTripleStore->insert($mentorId, 'isMentorOf', $userId);
	}
	return $return;

    }

    /**
    * Method to create a metor/student relationship
    *
    * @access public
    * @param string $mentorId: The user id of the users mentor
    * @return bool
    */
    public function addStudent($mentorId, $userId)
    {
	//Check if relationship exists
        $results = $this->objDbTripleStore->getTriples(array('predicate'=>'isStudentOf', 'object'=>$mentorId), array('userId'));
        if(is_array($result) && count($result)>0){
            $return = 'Already being mentored by user';
	} else {
            $return = $this->objDbTripleStore->insert($userId, 'isStudentOf', $mentorId);
	}
	return $return;

    }

    /**
    * Method to create a metor/student relationship
    *
    * @access public
    * @param string $mentorId: The user id of the users mentor
    * @return bool
    */
    public function delete($mentorId, $userId)
    {
	//Check if relationship exists
        $results = $this->objDbTripleStore->getTriples(array('subject'=>$mentorId, 'predicate'=>'isMentorOf', 'object'=>$userId), array('id'));
        if(is_array($results) && count($results)>0){
            foreach($results as $res){
                $return = $this->objDbTripleStore->delete($res['id']);
            }
	} else {
            $return = 'No relationship';
	}
	return $return;
    }

    /**
     * Method to check whether the wiki exists and create it if it doesn't
     *
     * @access public
     * @return string $wikiId The id of the mentors wiki
     */
    public function createMentorsWiki()
    {
        //Check if wiki exists
        parent::init('tbl_wiki_wikis');
        $sql = "WHERE wiki_name = 'mentors'";
        $data = $this->getAll($sql);
        if(is_array($data) && count($data)>0){
            $wikiId = $data[0]['id'];
        } else {
            $wikiId = $this->objDbWiki->addWiki('mentors', $this->objLanguage->languageText('mod_mentors_wikidesc', 'mentors'), '1');
        }
        return $wikiId;
    }

}
?>