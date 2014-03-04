<?php

class dbfaqtags extends dbtable {

    public function init() {
        parent::init('tbl_faq_tags');
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext=$this->getObject('dbcontext','context');
        $this->contextCode=$this->objContext->getContextCode();
    }



    public function prepArrayForTagCloud($array) {
        $finalArray = array();

        if (count($array) > 0) {
            foreach ($array as $item) {
                $finalArray[$item['tag']] = $item;
            }
        }

        return $finalArray;
    }

    public function getLastLimitTags($limit=50) {
        $contextCode = $this->contextCode;
        if ($contextCode == NULL || $contextCode == "") {
            $contextCode = 'root';
        }
        $sql = 'SELECT tbl_faq_tags.* , (SELECT count(tag) FROM tbl_faq_tags AS tags2 WHERE tbl_faq_tags.tag = tags2.tag) AS tagcount
FROM tbl_faq_tags,tbl_faq_entries  where tbl_faq_tags.faqid=tbl_faq_entries.id  and tbl_faq_entries.contextid= "'. $contextCode .'" GROUP BY tbl_faq_tags.tag ORDER BY tbl_faq_tags.puid DESC ';
        if (isset($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }

        return $this->prepArrayForTagCloud($this->getArray($sql));
    }
    /**
     * Method to take all the existing tags, and build them into a Tag Cloud
     * @return string Tag Cloud
     */
    public function getTagCloud() {
        // Get All Tags
        $tags = $this->getLastLimitTags();

        // Check that there are tags
        if (count($tags) == 0) {
            return '<div class="noRecordsMessage">Tag Cloud Goes Here</div>';
        } else {
            // Load Object
            $objTagCloud = $this->newObject('tagcloud', 'utilities');

            // Loop through tags
            foreach ($tags as $tag) {
                // Link to File
                $uri = $this->uri(array('action'=>'tag', 'tag'=>$tag['tag']));

                // Add Tag
                $objTagCloud->addElement($tag['tag'], $uri, $tag['tagcount']*6, strtotime('-1 day'));
            }

            // Return Tag Cloud
            return $objTagCloud->biuldAll();
        }
    }


    public function addFaqTags($faqId, $tags) {
        $tags = explode(',', $tags);

        //$this->clearFaqTags($faqId);

        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $tag = trim(stripslashes($tag));

                if ($tag != '') {
                    $this->addTag($faqId, $tag);
                }
            }
        }
    }


    public function updateFaqTags($faqId, $tags) {
        $tags = explode(',', $tags);

        $this->clearFaqTags($faqId);

        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $tag = trim(stripslashes($tag));

                if ($tag != '') {
                    $this->addTag($faqId, $tag);
                }
            }
        }
    }

    private function addTag($faqId, $tag) {
        return $this->insert(array(
                'faqid'=>$faqId,
                'tag'=>$tag,
                'creatorid' => $this->objUser->userId(),
               
                'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }

    public function getFaqTags($faqId) {
        $results = $this->getAll(' WHERE faqid=\''.$faqId.'\'');

        if (count($results) == 0) {
            return '';
        } else {
            $returnArray = array();

            foreach ($results as $result) {
                $returnArray[] = $result['tag'];
            }

            return $returnArray;
        }
    }

    public function clearFaqTags($faqId) {
        return $this->delete('faqid', $faqId);
    }

}
?>