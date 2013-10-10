<?php
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Manageviews_essay
* @package essay
* @author Paul Mungai, Jeremy O'Connor
* @copyright (c) 2009, 2010 Avoir
* @version $Id: $
*/

class manageviews_essay extends object
{
    /**
    * Initialization method.
    */
    public function init()
    {
        $this->dbessays = $this->getObject('dbessays');
        $this->dbtopic = $this->getObject('dbessay_topics');
        $this->dbbook = $this->getObject('dbessay_book');
        $this->objUser = $this->getObject('user','security');
        $this->userId=$this->objUser->userId();
        $this->objContext = $this->getObject('dbcontext','context');
        if($this->objContext->isInContext()){
            $this->contextcode=$this->objContext->getContextCode();
            $this->context=$this->objContext->getTitle();
            $incontext=TRUE;
        }else{
            $incontext=FALSE;
        }
    }

    /**
    * Method to get booked and submitted essays for a student.
    * @param string $contextCode The context code
    * @return array The student's essays
    **/
    public function getStudentEssays($contextCode = NULL)
    {
        // get student booked essays
        if (empty($contextCode)) {
            $data = $this->dbbook->getBooking("WHERE context = '{$this->contextcode}'
            AND studentid='{$this->userId}'");
    	} else {
            $data = $this->dbbook->getBooking("WHERE context='{$contextCode}'
            AND studentid='{$this->userId}'");
    	}
        if (!empty($data)) {
            foreach ($data as $key=>$item) {
                // get essay info: topic, num
                $essay = $this->dbessays->getEssay($item['essayid'], 'id, topic');
                $data[$key]['essay'] = $essay[0]['topic'];
                $topic = $this->dbtopic->getTopic($item['topicid'], 'name, closing_date, bypass');
                $data[$key]['name'] = $topic[0]['name'];
                $data[$key]['date'] = $topic[0]['closing_date'];
                $data[$key]['bypass'] = $topic[0]['bypass']?'YES':'NO';
                if (empty($item['studentfileid'])) {
                    $data[$key]['mark'] = 'submit';
                } else {
                    $data[$key]['mark'] = $item['mark'];
                }
            }
        }
        return $data;
     }
}
?>