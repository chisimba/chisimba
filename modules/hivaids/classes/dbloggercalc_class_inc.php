<?php
/**
* dbLoggerCalc class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbLoggerCalc class contains calculations performed on the statistical data contained in the logger table
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbLoggerCalc extends dbtable
{
    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_logger');
        $this->table = 'tbl_logger';
    }

    /**
    * Method to get the total number of hits on the site
    *
    * @access public
    * @return integer
    */
    public function getTotalHits($track, $by = 'all')
    {
        $sql = "SELECT count(*) as cnt FROM {$this->table} l";

        if($track != 'everyone'){
            $sql .= ", tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

            if($by != 'all'){
                $sql .= "AND staff_student = '{$by}' ";
            }
        }

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['cnt'];
        }
        return 0;
    }

    /**
    * Method to get the total number of hits on the site by a given user
    *
    * @access public
    * @param string $userid
    * @return integer
    */
    public function getTotalHitsByUser($userid = '')
    {
        $sql = "SELECT count(*) as cnt FROM {$this->table} l, tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

        if(!empty($userid)){
            $sql .= "AND hu.user_id = '{$userid}'";
        }

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['cnt'];
        }
        return 0;
    }

    /**
    * Method to get the total number of hits on the site by a given group of users
    *
    * @access public
    * @param string $group The group type eg gender
    * @param string $subgroup The group eg males
    * @return integer
    */
    public function getTotalHitsByGroup($by = 'all', $group = '', $subgroup = '')
    {
        $sql = "SELECT count(*) as cnt FROM {$this->table} l, tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

        if(!empty($group) && !empty($subgroup)){
            $sql .= "AND {$group} = '{$subgroup}' ";
        }
        if($by != 'all'){
            $sql .= "AND staff_student = '{$by}'";
        }

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['cnt'];
        }
        return 0;
    }

    /**
    * Method to get the total number of unique visitors on the site - by IP address or by userid if for registered users ($track != everyone)
    * If $view is students then only unique students are displayed.
    *
    * @access public
    * @return integer
    */
    public function getTotalUniqueVisitors($track, $by)
    {
        $sql = "SELECT DISTINCT(ipaddress), count(*) as cnt FROM {$this->table} l";

        if($track != 'everyone'){
            $sql .= ", tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

            if($by != 'all'){
                $sql .= "AND staff_student = '{$by}' ";
            }
            $sql .= "GROUP BY hu.user_id";
        }else{
            $sql .= " GROUP BY ipaddress";
        }

        $data = $this->getArray($sql);
        if(!empty($data)){
            $cnt = count($data);
            return $cnt;
        }
        return 0;
    }

    /**
    * Method to get the total number of unique visitors on the site - by IP address
    *
    * @access public
    * @return integer
    */
    public function getTotalUniqueVisitorsByGroup($by = 'all', $group = '', $subgroup = '')
    {
        $sql = "SELECT DISTINCT(ipaddress), count(*) as cnt FROM {$this->table} l, tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

        if(!empty($group) && !empty($subgroup)){
            $sql .= "AND {$group} = '{$subgroup}' ";
        }
        if($by != 'all'){
            $sql .= "AND staff_student = '{$by}' ";
        }
        $sql .= "GROUP BY hu.user_id";

        $data = $this->getArray($sql);
        if(!empty($data)){
            $cnt = count($data);
            return $cnt;
        }
        return 0;
    }

    /**
    * Method to get the number of hits and visitors per module and action
    *
    * @access public
    * @return array
    */
    public function getModuleHitsByUser($userid)
    {
        $sql = "SELECT module, ipaddress, eventparamvalue,
            count(module) as cnt, count(ipaddress) as ip_cnt, count(eventparamvalue) as act_cnt
            FROM {$this->table} l, tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

        if(!empty($userid)){
            $sql .= "AND hu.user_id = '{$userid}' ";
        }

        $sql .= "GROUP BY module, eventparamvalue, ipaddress";

        $data = $this->getArray($sql);

        if(!empty($data)){
            // Split data by module
            // Find unique actions - remove additional parameters
            // Count IPs for each action / group of actions
            $arrModules = $this->organiseModuleData($data);
            return $arrModules;
        }
        return array();
    }

    /**
    * Method to get the number of hits and visitors per module and action
    *
    * @access public
    * @return array
    */
    public function getModuleHitsByGroup($by = 'all', $group = '', $subgroup = '')
    {
        $sql = "SELECT module, hu.user_id, eventparamvalue,
            count(module) as cnt, count(ipaddress) as ip_cnt, count(eventparamvalue) as act_cnt
            FROM {$this->table} l, tbl_hivaids_users hu, tbl_users u
            WHERE l.userid = hu.user_id AND hu.user_id = u.userid ";

        if(!empty($group) && !empty($subgroup)){
            $sql .= "AND {$group} = '{$subgroup}' ";
        }
        if($by != 'all'){
            $sql .= "AND staff_student = '{$by}' ";
        }
        $sql .= "GROUP BY module, eventparamvalue, hu.user_id";

        $data = $this->getArray($sql);

        if(!empty($data)){
            // Split data by module
            // Find unique actions - remove additional parameters
            // Count IPs for each action / group of actions
            $arrModules = $this->organiseModuleData($data, 'user_id');
            return $arrModules;
        }
        return array();
    }

    /**
    * Method to get the number of hits and visitors per module and action
    *
    * @access public
    * @return array
    */
    public function getModuleHits()
    {
        $sql = "SELECT module, ipaddress, eventparamvalue,
            count(module) as cnt, count(ipaddress) as ip_cnt, count(eventparamvalue) as act_cnt
            FROM {$this->table} t GROUP BY module, eventparamvalue, ipaddress";

        $data = $this->getArray($sql);

        if(!empty($data)){
            // Split data by module
            // Find unique actions - remove additional parameters
            // Count IPs for each action / group of actions
            $arrModules = $this->organiseModuleData($data);
            return $arrModules;
        }
        return array();
    }

    /**
    * Method to organise the module hits and visitors in a usable array for display.
    *
    * @access public
    * @return array
    */
    public function organiseModuleData($data, $userType = 'ipaddress')
    {
        if(!empty($data)){
            // Split data by module
            // Find unique actions - remove additional parameters
            // Count IPs for each action / group of actions
            $arrModules = array();
            foreach($data as $item){

                $action = '';
                if(!is_null($item['eventparamvalue']) && !empty($item['eventparamvalue'])){
                    $arrAction = explode('&', $item['eventparamvalue']);

                    if(!empty($arrAction)){
                        foreach($arrAction as $val){
                            $pos = strpos($val, 'action=');
                            $action = ($pos == 0 && $pos !== FALSE) ? substr($val, 7) : '';
                        }
                    }
                }

                $arrModules[$item['module']][$action]['params'][] = $item['eventparamvalue'];
                $arrModules[$item['module']][$action]['hits'] = isset($arrModules[$item['module']][$action]['hits']) ? $arrModules[$item['module']][$action]['hits'] + $item['cnt'] : $item['cnt'];

                $arrModules[$item['module']][$action]['users'][$item[$userType]] = isset($arrModules[$item['module']][$action]['users'][$item[$userType]]) ? $arrModules[$item['module']][$action]['users'][$item[$userType]] + $item['cnt'] : $item['cnt'];
            }
            return $arrModules;
        }
        return array();
    }

    /**
    * Method to get all the forum categories and associated statistics
    *
    * @access public
    * @return array
    */
    public function getForumCategories($by = 'all')
    {
        $sql = "SELECT forum_id, forum_name, t.views, t.replies
            FROM tbl_forum_topic t, tbl_forum f
            WHERE f.id = t.forum_id
            ORDER BY forum_name";

        $data = $this->getArray($sql);
        $arrCats = $this->organiseForumData($data);

        return $arrCats;
    }

    /**
    * Method to organise the module hits and visitors in a usable array for display.
    *
    * @access public
    * @return array
    */
    public function organiseForumData($categories = '')
    {
        $arrCats = array();
        if(!empty($categories)){
            foreach($categories as $item){
                $arrCats[$item['forum_id']]['name'] = $item['forum_name'];
                $arrCats[$item['forum_id']]['views'] = isset($arrCats[$item['forum_id']]['views']) ? $arrCats[$item['forum_id']]['views'] + $item['views'] : $item['views'];
                $arrCats[$item['forum_id']]['replies'] = isset($arrCats[$item['forum_id']]['replies']) ? $arrCats[$item['forum_id']]['replies'] + $item['replies'] : $item['replies'];
                $arrCats[$item['forum_id']]['topics'] = isset($arrCats[$item['forum_id']]['topics']) ? $arrCats[$item['forum_id']]['topics'] + 1 : 1;
            }
        }
        return $arrCats;
    }

    /**
    * Method to get all the forum categories and associated statistics
    *
    * @access public
    * @return array
    */
    public function getForumTopics($catId, $by = 'all')
    {
        $sql = "SELECT f.forum_name, t.id as topicid, t.views, t.replies, pt.post_title
            FROM tbl_forum f, tbl_forum_topic t, tbl_forum_post p, tbl_forum_post_text pt
            WHERE t.forum_id = '{$catId}' AND f.id = t.forum_id
            AND t.first_post = p.id AND p.post_parent = '0' AND pt.post_id = p.id
            ORDER BY pt.post_title";

        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to get all the views of each category
    *
    * @access public
    * @return array
    */
    public function getForumCatViews($by = 'all', $type = 'cat', $num = 21)
    {
        $sql = "SELECT l.eventparamvalue, hu.user_id, hu.course, hu.study_year, hu.language, u.sex FROM tbl_logger l, tbl_users u, tbl_hivaids_users hu
                WHERE u.userid = hu.user_id AND u.userid = l.userid
                AND module = 'hivaidsforum' AND eventparamvalue LIKE 'action=show{$type}&%' ";

        if($by != 'all'){
            $sql .= "AND hu.staff_student = '{$by}'";
        }

        $data = $this->getArray($sql);

        $arrViews = $this->organiseViewData($data, $num);
        return $arrViews;
    }

    /**
    * Method to organise the module hits and visitors in a usable array for display.
    *
    * @access public
    * @return array
    */
    public function organiseViewData($views = '', $num = 21)
    {
        $arrViews = array();
        if(!empty($views)){
            foreach($views as $item){
                $catId = substr($item['eventparamvalue'], $num);
                $arrViews[$catId]['total'] = isset($arrViews[$catId]['total']) ? $arrViews[$catId]['total']+1 : 1;
                $arrViews[$catId]['rows'] = isset($arrViews[$catId]['language'][$item['language']]) ? $arrViews[$catId]['rows'] : $arrViews[$catId]['rows']+1;
                $arrViews[$catId]['rows'] = isset($arrViews[$catId]['course'][$item['course']]) ? $arrViews[$catId]['rows'] : $arrViews[$catId]['rows']+1;
                $arrViews[$catId]['rows'] = isset($arrViews[$catId]['sex'][$item['sex']]) ? $arrViews[$catId]['rows'] : $arrViews[$catId]['rows']+1;
                $arrViews[$catId]['rows'] = isset($arrViews[$catId]['study_year'][$item['study_year']]) ? $arrViews[$catId]['rows'] : $arrViews[$catId]['rows']+1;

                $arrViews[$catId]['language'][$item['language']] = isset($arrViews[$catId]['language'][$item['language']]) ? $arrViews[$catId]['language'][$item['language']] + 1 : 1;
                $arrViews[$catId]['course'][$item['course']] = isset($arrViews[$catId]['course'][$item['course']]) ? $arrViews[$catId]['course'][$item['course']] + 1 : 1;
                $arrViews[$catId]['sex'][$item['sex']] = isset($arrViews[$catId]['sex'][$item['sex']]) ? $arrViews[$catId]['sex'][$item['sex']] + 1 : 1;
                $arrViews[$catId]['study_year'][$item['study_year']] = isset($arrViews[$catId]['study_year'][$item['study_year']]) ? $arrViews[$catId]['study_year'][$item['study_year']] + 1 : 1;

            }
        }
        return $arrViews;
    }

    /**
     * Method to get the participation of students in the forums
     *
     * @access public
     * @return array $data
     */
    public function getStudentParticipation()
    {
        // Fetch the categories and topics
        $sql = "SELECT t.forum_id, f.forum_name, p.topic_id, pt.post_title
            FROM tbl_forum f, tbl_forum_topic t, tbl_forum_post p, tbl_forum_post_text pt
            WHERE f.id=t.forum_id AND p.id=pt.post_id AND p.topic_id = t.id
            AND t.first_post = p.id AND p.post_parent = '0'";

        $catData = $this->getArray($sql);

        $arrCat = array();
        if(!empty($catData)){
            foreach($catData as $item){
                $arrCat[$item['topic_id']]['cat'] = $item['forum_name'];
                $arrCat[$item['topic_id']]['topic'] = $item['post_title'];
            }
        }

        // Fetch the views by topic
        $sql = "SELECT count(*) as cnt, userid, eventparamvalue FROM tbl_logger l
            WHERE module='hivaidsforum' AND eventparamvalue LIKE 'action=showtopic&%'
            GROUP BY userid, eventparamvalue";

        $viewData = $this->getArray($sql);

        $arrView = array();
        if(!empty($viewData)){
            foreach($viewData as $item){
                $topicId = substr($item['eventparamvalue'], 25);
                $arrView[$item['userid']][$topicId] = isset($arrView[$item['userid']][$topicId]) ? $arrView[$item['userid']][$topicId] + $item['cnt'] : $item['cnt'];
            }
        }

        // Fetch the user information and posted replies
        $sql = "SELECT p.topic_id, t.post_title, t.wordcount, t.userid, t.datelastupdated, u.username, u.staffnumber
            FROM tbl_forum_post p, tbl_forum_post_text t, tbl_users u, tbl_hivaids_users hu
            WHERE p.id=t.post_id AND u.userid=t.userid
            AND u.userid=hu.user_id AND hu.staff_student = 'student'";

        $userData = $this->getArray($sql);

        $data = array();
        if(!empty($userData)){
            foreach($userData as $item){
                $views = 0;
                $data[$item['userid']]['username'] = $item['username'];
                $data[$item['userid']]['staffnumber'] = $item['staffnumber'];
                $data[$item['userid']]['topics'][$item['topic_id']]['category'] = $arrCat[$item['topic_id']]['cat'];
                $data[$item['userid']]['topics'][$item['topic_id']]['topic'] = $arrCat[$item['topic_id']]['topic'];
                $data[$item['userid']]['topics'][$item['topic_id']]['replies'] = isset($data[$item['userid']]['topics'][$item['topic_id']]['replies']) ? $data[$item['userid']]['topics'][$item['topic_id']]['replies'] + 1 : 1;
                if($data[$item['userid']]['topics'][$item['topic_id']]['replies'] > 0) $views = 1;
                $data[$item['userid']]['topics'][$item['topic_id']]['views'] = isset($arrView[$item['userid']][$item['topic_id']]) ? $arrView[$item['userid']][$item['topic_id']] : $views;
            }
        }

        return $data;
    }

    /**
    * Method to clear the logged info
    *
    * @access public
    * @return array
    */
    public function clearLogger()
    {
        $sql = "DELETE FROM tbl_logger";

        $this->getArray($sql);
    }

    /**
     * Method to get the logged data for the cms
     *
     * @access public
     * @return array $data
     */
    public function getCmsLog()
    {
        $data = $this->getLoggedData('cms');
        $log = array();

        if(!empty($data)){
            foreach ($data as $item){
                // Get the eventparamvalue, if it's null then its the cms home page, else extract the section id
                $event = $item['eventparamvalue'];
                $sectionId = 'home';
                $pageId = 'home';
                $sectionType = 'home';
                if(is_null($event) || empty($event)){
                    $sectionType = 'home';
                }else{
                    // get the action - show section or show content
                    $pos = strpos($event, 'sectionid');
                    $len = strpos($event, '&id=');
                    $action = substr($event, 7, $len-7);
                    $sectionId = substr($event, $pos+10);

                    switch($action){
                        case 'showsection':
                            $sectionType = 'section';
                            break;
                        case 'showfulltext':
                        case 'showcontent':
                            $sectionType = 'page';
                            $pageId = substr($event, $len+4, $pos-$len-5);
                            break;
                        default:
                            // not sure about the action here
                            // so find the section id and the page id the long way round
                            if($len > $pos){
                                $sectionId = substr($event, $pos+10, $len-$pos-10);
                                $pageId = substr($event, $len+4);
                            }else{
                                $pageId = substr($event, $len+4, $pos-$len-5);
                            }
                            if($pageId == $sectionId){
                                $sectionType = 'section';
                            }else{
                                $sectionType = 'page';
                            }
                    }
                }
                $log[$sectionId][$pageId][] = $item;
            }
        }

        return $log;
    }

    /**
     * Method to get the discussion forum logged data
     *
     * @access public
     * @return array $data
     */
    public function getDFLog()
    {
        $objDbForum = $this->getObject('dbhivforum', 'hivaidsforum');

        $data = $this->getLoggedData('hivaidsforum');

        $log = array();
        $arrReply = array();

        if(!empty($data)){
            // Get the default category
            $defaultId = $objDbForum->getDefaultCatID();
            foreach ($data as $item){
                // Get the eventparamvalue, if it's null then its the default forum / category
                $event = $item['eventparamvalue'];
                $referrer = $item['referrer'];
                $catId = '';
                $topicId = '';
                $action = '';
                $ignore = FALSE;

                if(is_null($event) || empty($event)){
                    $catId = $defaultId;
                    $topicId = 'home';
                }else{
                    // get the action - show topic or show category or show reply
                    $pos = strpos($event, '&');
                    if($pos === false){
                        $action = substr($event, 7);
                    }else{
                        $action = substr($event, 7, $pos-7);
                    }

                    switch($action){
                        case 'showcat':
                            $catId = substr($event, $pos+7);
                            $topicId = 'home';
                            break;

                        case 'showtopic':
                            $topicId = substr($event, $pos+9);
                            // check referrer for category id
                            $ref = strpos($referrer, 'catId=');
                            if($ref === false){
                                // get from DB
                                $catId = $objDbForum->getCategoryForTopic($topicId);
                            }else{
                                $catId = substr($referrer, $ref+6);
                            }
                            break;

                        case 'showreply':
                            if($pos !== false){
                                $ref = strpos($event, 'parent_id=');
                                $parentId = substr($event, $ref+10);
                                $parent = $objDbForum->getPostParent($parentId);
                                $topicId = $parent['topic_id'];
                            }else{
                            // Check referrer for topic id
                                $ref = strpos($referrer, 'topicId=');
                                if($ref === false){
                                    if(isset($arrReply[$item['userid']]['topic']) && !empty($arrReply[$item['userid']]['topic'])){
                                        $topicId = $arrReply[$item['userid']]['topic'];
                                    }else{
                                        $topicId = $prevTopId;
                                    }
                                }else{
                                    $topicId = substr($referrer, $ref+8);
                                }
                            }
                            // get category id from DB
                            $catId = $objDbForum->getCategoryForTopic($topicId);
                            $arrReply[$item['userid']]['topic'] = $topicId;
                            $arrReply[$item['userid']]['cat'] = $catId;
                            $ignore = TRUE; // Only show hit if post is saved.
                            break;

                        case 'showpost':
                            if($pos !== false){
                                $ref = strpos($event, 'postId=');
                                $postId = substr($event, $ref+7);
                                $post = $objDbForum->getPost($postId);
                                $topicId = $post['topic_id'];
                            }else{
                                $ref = strpos($referrer, 'topicId=');
                                if($ref === false){
                                    $ignore = TRUE;
                                }
                                $topicId = substr($referrer, $ref+8);
                            }
                            // get category id from DB
                            $catId = $objDbForum->getCategoryForTopic($topicId);
                            break;

                        case 'savepost':
                            $topicId = $arrReply[$item['userid']]['topic'];
                            $catId = $arrReply[$item['userid']]['cat'];
                            $item['posts'] = 1;
                            break;

                        case '':
                        default:
                            $ignore = TRUE;
                    }
                }
                if(!$ignore){
                    $log[$catId][$topicId][] = $item;
                    $prevCatId = $catId;
                    $prevTopId = $topicId;
                }
            }
        }
        return $log;
    }

    /**
     * Method to get the podcast logged data
     *
     * @access public
     * @return array $data
     */
    public function getPodcastLog()
    {
        $data = $this->getLoggedData('podcast');

        $log = array();

        if(!empty($data)){
            foreach ($data as $item){
                // Get the eventparamvalue, if it's null then its the default forum / category
                $event = $item['eventparamvalue'];
                $action = '';
                $podId = 'none';

                if(is_null($event) || empty($event) || $event == 'action='){
                    $action = 'home';
                }else{
                    // get the action - show topic or show category or show reply
                    $pos = strpos($event, '&');
                    if($pos == false){
                        $action = substr($event, 7);
                    }else{
                        $action = substr($event, 7, $pos-7);
                    }

                    switch($action){
                        case 'playpodcast':
                            $len = strpos($event, '&id=');
                            $podId = substr($event, $len+4);
                            break;
                    }
                }
                $log[$action][$podId][] = $item;
            }
        }
        return $log;
    }

    /**
     * Method to get the hivaids logged data - video list, links page, registration
     *
     * @access public
     * @return array $data
     */
    public function getHIVLog()
    {
        $data = $this->getLoggedData('hivaids');

        $log = array();

        if(!empty($data)){
            foreach ($data as $item){
                // Get the eventparamvalue, if it's null then its the default forum / category
                $event = $item['eventparamvalue'];
                $action = '';

                if(is_null($event) || empty($event) || $event == 'action='){
                }else{
                    // get the action - show topic or show category or show reply
                    $pos = strpos($event, '&');
                    if($pos == false){
                        $action = substr($event, 7);
                    }else{
                        $action = substr($event, 7, $pos-7);
                    }

                    switch($action){
                        case 'viewlinks':
                        case 'playyourmoves':
                        case 'showregister':
                        case 'videolist':
                            $log[$action][] = $item;
                    }
                }
            }
        }
        return $log;
    }

    /**
     * Method to get the photogallery logged data
     *
     * @access public
     * @return array $data
     */
    public function getPhotoLog()
    {
        $data = $this->getLoggedData('photogallery');

        $log = array();

        if(!empty($data)){
            foreach ($data as $item){
                // Get the eventparamvalue, if it's null then its the default forum / category
                $event = $item['eventparamvalue'];
                $action = '';

                if(is_null($event) || empty($event) || $event == 'action='){
                }else{
                    // get the action - show topic or show category or show reply
                    $pos = strpos($event, '&');
                    if($pos == false){
                        $action = substr($event, 7);
                    }else{
                        $action = substr($event, 7, $pos-7);
                    }

                    switch($action){
                        case 'viewlinks':
                        case 'playyourmoves':
                        case 'showregister':
                        case 'videolist':
                            $log[$action][] = $item;
                    }
                }
            }
        }
        return $log;
    }


    /**
     * Method to get the logged data for a given module
     *
     * @access private
     * @param string $module
     * @return array $data
     */
    private function getLoggedData($module = 'hiviads')
    {
        $sql = "SELECT *, l.datecreated as log_date FROM tbl_logger l
            LEFT JOIN tbl_hivaids_users hu ON l.userid = hu.user_id
            LEFT JOIN tbl_users u ON hu.user_id = u.userid
            WHERE l.module = '{$module}'
            ORDER BY l.puid";

        $data = $this->getArray($sql);
        return $data;
    }
}
?>