<?php

class dbrtt extends dbtable {

    function init() {
        parent::init('tbl_news_stories');
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objWashout=$this->getObject('washout','utilities');
    }

    function getPostDemoContent() {
        $objTrim = $this->getObject('trimstr', 'strings');
        $storyid = $this->objDbSysconfig->getValue('POST_DEMO_STORY_ID', 'rtt');
        $data = $this->getStory($storyid);
        $content = '';

        $content = '
          
            
            ' . $this->objWashout->parseText($data['storytext']) . '
            
            <br/>
              ';

        return $content;
    }



    function getDemoContent() {
        $objTrim = $this->getObject('trimstr', 'strings');
        $storyid = $this->objDbSysconfig->getValue('DEMO_STORY_ID', 'rtt');
        $data = $this->getStory($storyid);
        $content = '';

        $content = '


            ' . $this->objWashout->parseText($data['storytext']) . '

            <br/>
              ';

        return $content;
    }
    public function getDownloadsStory() {
        $objTrim = $this->getObject('trimstr', 'strings');
        $storyid = $this->objDbSysconfig->getValue('DOWNLOAD_STORY_ID', 'rtt');
        
        $data = $this->getStory($storyid);
      
        $content = '';
        $content = $this->objWashout->parseText($data['storytext']) . '<br/>';

        return $content;
    }

    public function getStory($id) {
        $data = $this->getRow('id', $id);
        return $data;
    }

}

?>
