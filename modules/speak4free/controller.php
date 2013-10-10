<?php

if(!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end of security

class speak4free extends controller {

    public  $objConfig;

    public  $realtimeManager;

    public  $presentManager;
    /**
     * Constructor
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objViewerUtils = $this->getObject('viewerutils');
        $this->objWashout = $this->getObject('washout', 'utilities');
        $this->storyparser=$this->getObject('storyparser');
        $this->dbtopics=$this->getObject('dbtopics');
        $this->dbgroups=$this->getObject('dbgroups');
        $this->articles=$this->getObject('dbarticles');
        $this->objFiles = $this->getObject('dbspeak4freefiles');
        $this->objTags = $this->getObject('dbspeak4freetags');
        $this->objFileEmbed = $this->getObject('fileembed','filemanager');
    }
    /**
     * Method to override login for certain actions
     * @param <type> $action
     * @return <type>
     */
    public function requiresLogin($action) {
        $required = array('login','storyadmin');


        if (in_array($action, $required)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }



    /**
     * Standard Dispatch Function for Controller
     * @param <type> $action
     * @return <type>
     */
    public function dispatch($action) {

        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->getMethod($action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__'.$action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__'.$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    function __home() {
        return 'home_tpl.php';
    }

    function __viewtopic() {
        $id=$this->getParam('id');
        $this->setVarByRef('id',$id);
        return 'topic_tpl.php';
    }

    function __viewtopics() {
        $parentid=$this->getParam('parentid');
        $this->setVarByRef('parentid',$parentid);
        return 'topics_tpl.php';
    }



    function __admin() {
        return "admin_tpl.php";
    }

    function __viewstory() {
        $category=$this->getParam('category');
        $this->setVarByRef('category',$category);
        $this->setVar('pageSuppressToolbar', TRUE);
        return "story_tpl.php";
    }
    function __viewarticle() {
        $storyid=$this->getParam('storyid');
        $articleid=$this->getParam('articleid');
        $this->setVarByRef('storyid',$storyid);
        $this->setVarByRef('articleid',$articleid);
        $this->setVar('pageSuppressToolbar', TRUE);
        return "article_tpl.php";
    }

    function __storyadmin() {
        return "storylist_tpl.php";
    }

    function __addtopic() {
        $this->setVarByRef('mode','add');
        return "addedittopic_tpl.php";

    }
    function __addarticle() {

        $topicid=$this->getParam('topicid');
        $this->setVarByRef('mode','add');
        $this->setVarByRef('topicid',$topicid);
        return "addeditarticle_tpl.php";

    }
    function __savetopic() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $active=$this->getParam('activefield');
        $topicid=$this->dbtopics->saveTopic($title,$content,$active);
        $this->dbgroups->adduser($topicid);
        return $this->nextAction('storyadmin');
    }
    function __savearticle() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $topicid=$this->getParam('topicid');
        $this->articles->saveArticle($title,$content,$topicid);
        return $this->nextAction('viewtopicarticles',array('topicid'=>$topicid));
    }
    function __updatearticle() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $articleid=$this->getParam('articleid');
        $topicid=$this->getParam('topicid');
        $this->articles->updateArticle($title,$content,$articleid);
        return $this->nextAction('viewtopicarticles',array('topicid'=>$topicid));
    }

    function __updateTopic() {
        $content=$this->getParam('pagecontent');
        $title=$this->getParam('titlefield');
        $topicid=$this->getParam('topicid');
        $active=$this->getParam('activefield');
        $topicid=$this->dbtopics->updateTopic($title,$content,$topicid,$active);
        return $this->nextAction('storyadmin');
    }
    function __addmembertotopic() {
        $topicid=$this->getParam('topicid');
        $userid=$this->getParam('userid');
        $this->dbgroups->adduser($topicid,$userid);
        return $this->nextAction('topicmembers',array('topicid'=>$topicid));
    }
    function __topicmembers() {
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('topicid',$topicid);
        return 'topicmembers_tpl.php';
    }
    function __deletemember() {
        $topicid=$this->getParam('topicid');
        $userid=$this->getParam('userid');
        $this->dbgroups->deleteMember($userid);
        $this->setVarByRef('topicid',$topicid);
        return 'topicmembers_tpl.php';
    }
    function __editTopic() {
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('mode','edit');
        $this->setVarByRef('topicid',$topicid);
        return "addedittopic_tpl.php";
    }

    function __deletetopic() {
        $topicid=$this->getParam('topicid');
        $this->dbtopics->deleteTopic($topicid);
        return $this->nextAction('storyadmin');
    }
    function __deletearticle() {
        $topicid=$this->getParam('articleid');
        $this->articles->deleteArticle($topicid);
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('topicid',$topicid);
        return "articlelist_tpl.php";
    }
    function __editarticle() {
        $articleid=$this->getParam('articleid');
        $this->setVarByRef('mode','edit');
        $topicid=$this->getParam('topicid');

        $this->setVarByRef('topicid',$topicid);
        $this->setVarByRef('articleid',$articleid);
        return "addeditarticle_tpl.php";
    }
    function __viewtopicarticles() {
        $topicid=$this->getParam('topicid');
        $this->setVarByRef('topicid',$topicid);
        return "articlelist_tpl.php";
    }
    function __tempiframe() {
        echo '<pre>';
        print_r($_GET);
    }
    /**
     * Used to do the actual upload
     *
     */
    function __doajaxupload() {

        $generatedid = $this->getParam('id');
        $filename = $this->getParam('filename');
        $id = $this->objFiles->autoCreateTitle();
        $objMkDir = $this->getObject('mkdir', 'files');

        $destinationDir = $this->objConfig->getcontentBasePath().'/speak4free/'.$id;

        $objMkDir->mkdirs($destinationDir);

        @chmod($destinationDir, 0777);

        $objUpload = $this->newObject('upload', 'files');
        $objUpload->permittedTypes = array(
            //video
            'mov',
            'wmv',
            'avi',
            'flv',
            'ogg',
            'mpg',
            'mpeg',
            'mp4',
            //audio
            'mp3'
            );
        $objUpload->overWrite = TRUE;
        $objUpload->uploadFolder = $destinationDir.'/';

        $result = $objUpload->doUpload(TRUE, $id);


        if ($result['success'] == FALSE) {
            $this->objFiles->removeAutoCreatedTitle($id);
            rmdir($this->objConfig->getcontentBasePath().'/speak4free/'.$id);

            $filename = isset($_FILES['fileupload']['name']) ? $_FILES['fileupload']['name'] : '';

            return $this->nextAction('erroriframe', array('message'=>'Unsupported file extension.Only use .mov,.wmv, .avi, .flv, .ogg, .mpg, .mpeg, .mp3', 'file'=>$filename, 'id'=>$generatedid));
        } else {

            $filename = $result['filename'];
            $mimetype = $result['mimetype'];

            $path_parts = $result['storedname'];

            $ext = $path_parts['extension'];


            $file = $this->objConfig->getcontentBasePath().'/speak4free/'.$id.'/'.$id.'.'.$ext;

            if ($ext == 'png') {
                $rename = $this->objConfig->getcontentBasePath().'/speak4free/'.$id.'/'.$id.'.png';

                rename($file, $rename);

                $filename = $path_parts['filename'].'.png';
            }

            if ($ext == 'flv') {
                $rename = $this->objConfig->getcontentBasePath().'/speak4free/'.$id.'/'.$id.'.flv';

                rename($file, $rename);

                $filename = $path_parts['filename'].'.flv';
            }
            if (is_file($file)) {
                @chmod($file, 0777);
            }

            $this->objFiles->updateReadyForConversion($id, $filename, $mimetype);

            $uploadedFiles = $this->getSession('uploadedfiles', array());
            $uploadedFiles[] = $id;
            $this->setSession('uploadedfiles', $uploadedFiles);

            return $this->nextAction('ajaxuploadresults', array('id'=>$generatedid, 'fileid'=>$id, 'filename'=>$filename));
        }
    }


    /**
     * Used to push through upload results for AJAX
     */
    function __ajaxuploadresults() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $fileid = $this->getParam('fileid');
        $this->setVarByRef('fileid', $fileid);

        $filename = $this->getParam('filename');
        $this->setVarByRef('filename', $filename);

        return 'ajaxuploadresults_tpl.php';
    }

    function __upload() {
        return 'upload_tpl.php';
    }
    function __erroriframe() {
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $id = $this->getParam('id');
        $this->setVarByRef('id', $id);

        $message = $this->getParam('message');
        $this->setVarByRef('message', $message);

        return 'erroriframe_tpl.php';
    }

    function __ajaxprocess() {
        $this->setPageTemplate(NULL);

        $id = $this->getParam('id');

        $file = $this->objFiles->getFile($id);

        if ($file == FALSE) {
            return $this->nextAction('home', array('error'=>'norecord'));
        }

        // Set Filename as title in this process
        // Based on the filename, it might make it easier for users to complete the name
        $file['title'] = $file['filename'];


        $this->setVarByRef('file', $file);
        $this->setVarByRef('tags', $tags);

        $this->setVar('mode', 'add');

        return 'process_tpl.php';
    }
    function __updatedetails() {
        $id = $this->getParam('id');
        $title = $this->getParam('title');
        $description = $this->getParam('description');
        $tags = explode(',', $this->getParam('tags'));
        $newTags = array();

        // Create an Array to store problems
        $problems = array();
        // Check that username is available
        if ($title == '') {
            $problems[] = 'emptytitle';
            $title=$id;

        }
        //
        // Clean up Spaces
        foreach ($tags as $tag) {
            $newTags[] = trim($tag);
        }

        $tags =  array_unique($newTags);
        $license = $this->getParam('creativecommons');

        $this->objFiles->updateFileDetails($id, $title, $description, $license);
        $this->objTags->addTags($id, $tags);

        $file = $this->objFiles->getFile($id);
        $tags = $this->objTags->getTagsAsArray($id);
      
        $file['tags'] = $tags;
       

        //$this->_prepareDataForSearch($file);

        if (count($problems) > 0) {
            $this->setVar('mode', 'addfixup');
            $this->setVarByRef('problems', $problems);
            return 'process_tpl.php';
        }else {
            return $this->nextAction('view', array('id'=>$id, 'message'=>'infoupdated'));
        }
    }
/**
     * Method to view the details of a presentation
     *
     */
    function __view()
    {
        $id = $this->getParam('id');

        $file = $this->objFiles->getFile($id);

        if ($file == FALSE) {
            return $this->nextAction('home', array('error'=>'norecord'));
        }


        $tags = $this->objTags->getTags($id);

        $this->setVarByRef('file', $file);
        $this->setVarByRef('tags', $tags);

        $this->setVar('pageTitle', $this->objConfig->getSiteName().' - '.$file['title']);


        return 'view_tpl.php';
    }


    function __poems(){
        return "poemshome_tpl.php";
    }
}
?>