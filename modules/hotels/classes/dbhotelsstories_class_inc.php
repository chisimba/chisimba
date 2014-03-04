<?php

/**
* Class to store hotel stories
*/
class dbhotelsstories extends dbtable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_hotels_stories');

        $this->objKeywords = $this->getObject('dbhotelskeywords');

        $this->objUser = $this->getObject('user', 'security');
        $this->objDateTime = $this->getObject('dateandtime', 'utilities');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('link', 'htmlelements');
    }

    /**
    * Method to add a hotels story
    * @param string $storyTitle Title of the story
    * @param date $storyDate Date of the Story
    * @param string $storyCategory Category story falls under
    * @param string $storyLocation Place where story took place
    * @param string $storyText News Story
    * @param string $storySource Source of item
    * @param string $storyImage Image relating to story
    * @param array $tags Tags for the sotry
    * @param array $keyTags Key Tags
    * @param date/time $publishDate Date when story should be published
    */
    public function addStory($storyTitle, $storyDate, $storyCategory, $storyLocation, $storyText, $storySource, $storyImage, $tags, $keyTags, $publishDate = NULL, $sticky='N')
    {

        if ($publishDate == NULL) {
            $publishDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }
        
        $userId = $this->objUser->userId();
        
        $data = array(
            'storytitle' => stripslashes($storyTitle),
            'storydate' => $storyDate,
            'storycategory' => $storyCategory,
            'storylocation' => $storyLocation,
            'storytext' => stripslashes($storyText),
            'storysource' => stripslashes($storySource),
            'storyimage' => $storyImage,
            'creatorid' => $userId,
            'storyorder' => $this->getLastCategoryOrder($storyCategory)+1,
            'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            'dateavailable' => $publishDate,
            'sticky' => $sticky,
            );

        $storyId = $this->insert($data);

        if ($storyId != FALSE) {
            $this->objKeywords->addStoryKeywords($storyId, $keyTags);

            $objTags = $this->getObject('dbhotelstags');
            $objTags->addStoryTags($storyId, $tags);
            
            // Call Object
            $objIndexData = $this->getObject('indexdata', 'search');
            
            // Prep Data
            $docId = 'news_stories_'.$storyId;
            $docDate = $storyDate;
            $url = $this->uri(array('action'=>'viewstory', 'id'=>$storyId), 'news');
            $title = stripslashes($storyTitle);
            
            // Remember to add all info you want to be indexed to this field
            $contents = stripslashes($storyTitle).' '.stripslashes($storyText);
            
            // A short overview that gets returned with the search results
            $objTrim = $this->getObject('trimstr', 'strings');
            $teaser = $objTrim->strTrim(strip_tags(stripslashes($storyText)), 300);
            
            $module = 'news';
            
            $additionalSearchIndex = array(
                    'storylocation' => $storyLocation,
                    'storycategory' => $storyCategory,
                    'storydateavailable' => $publishDate,
                );
            
            // Add to Index
            $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents,
            $teaser, $module, $userId, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $additionalSearchIndex);

            return $storyId;
        } else {
            return FALSE;
        }
    }

    /**
    *
    *
    *
    */
    private function getLastCategoryOrder($category)
    {
        $result = $this->getAll('WHERE storycategory = \''.$category.'\' ORDER BY storyorder DESC LIMIT 1');

        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0]['storyorder'];
        }
    }


    /**
    * Method to update a news story
    * 
    * @param string $id Record Id
    * @param string $storyTitle Title of the story
    * @param date $storyDate Date of the Story
    * @param string $storyCategory Category story falls under
    * @param string $storyLocation Place where story took place
    * @param string $storyText News Story
    * @param string $storySource Source of item
    * @param string $storyImage Image relating to story
    * @param array $tags Tags for the sotry
    * @param array $keyTags Key Tags
    * @param date/time $publishDate Date when story should be published
    */
    public function updateStory($id, $storyTitle, $storyDate, $storyCategory, $storyLocation, $storyText, $storySource, $storyImage, $tags, $keyTags, $publishDate, $sticky)
    {
        $userId = $this->objUser->userId();
        
        $data = array(
            'storytitle' => stripslashes($storyTitle),
            'storydate' => $storyDate,
            'storycategory' => $storyCategory,
            'storylocation' => $storyLocation,
            'storytext' => stripslashes($storyText),
            'storysource' => stripslashes($storySource),
            'storyimage' => $storyImage,
            'sticky' => $sticky,
            'modifierid' => $userId,
            'dateavailable' => $publishDate,
            'datemodified' => strftime('%Y-%m-%d %H:%M:%S', mktime())
            );
        
        $result = $this->update('id', $id, $data);
        
        if ($result != FALSE) {
            $this->objKeywords->addStoryKeywords($id, $keyTags);
            
            $objTags = $this->getObject('dbhotelstags');
            $objTags->addStoryTags($id, $tags);
            
            // Call Object
            $objIndexData = $this->getObject('indexdata', 'search');
            
            // Prep Data
            $docId = 'news_stories_'.$id;
            $docDate = $storyDate;
            $url = $this->uri(array('action'=>'viewstory', 'id'=>$id), 'hotels');
            $title = stripslashes($storyTitle);
            
            // Remember to add all info you want to be indexed to this field
            $contents = stripslashes($storyTitle).' '.stripslashes($storyText);
            
            // A short overview that gets returned with the search results
            $objTrim = $this->getObject('trimstr', 'strings');
            $teaser = $objTrim->strTrim(strip_tags(stripslashes($storyText)), 300);
            
            $module = 'hotels';
            
            $additionalSearchIndex = array(
                    'storylocation' => $storyLocation,
                    'storycategory' => $storyCategory,
                    'storydateavailable' => $publishDate,
                );
            
            // Add to Index
            $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents,
            $teaser, $module, $userId, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $additionalSearchIndex);
            
            return $result;
        } else {
            return FALSE;
        }
    }

    public function deleteStory($id)
    {
        if ($id != '') {
            return $this->delete('id', $id);
        }
    }

    public function getNumCategoryStories($category)
    {

        return $this->getRecordCount('WHERE storycategory = \''.$category.'\'');
    }

    /**
    *
    *
    *
    */
    public function generateTimeline()
    {
        return $this->generateTimelineCode($this->getAll('WHERE dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' ORDER BY storydate'));
    }

    /**
    *
    *
    *
    */
    private function generateTimelineCode($stories)
    {
        $str = '<data date-time-format="iso8601">';

        $objTrimString = $this->getObject('trimstr', 'strings');

        if (count($stories) > 0) {
            foreach($stories as $story)
            {
                $str .= '<event start="'.$story['storydate'].'" title="'.htmlentities($story['storytitle']).'">';//image="'.$image.'" // Re add image

                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                $storyLink->link = $this->objLanguage->languageText('mod_hotels_readmore', 'hotels', 'Read More').'...';
                $storyLink->target = '_top';

                $str .= htmlentities($objTrimString->strTrim(strip_tags($story['storytext']), 150, TRUE) . "<br />" . $storyLink->show());
                $str .= "</event>";
            }
        }
        $str .= "</data>";

        return $str;
    }

    /**
    *
    *
    *
    */
    public function getTopStories($limit=3)
    {
        $sql = 'SELECT tbl_hotels_stories.*, categoryname, name as location, filename FROM tbl_hotels_stories
LEFT JOIN tbl_hotels_categories ON (tbl_hotels_stories.storycategory=tbl_hotels_categories.id)
LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation=tbl_geonames.geonameid)
LEFT JOIN tbl_files ON (tbl_hotels_stories.storyimage=tbl_files.id)
WHERE sticky=\'Y\' AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\'
ORDER BY storydate DESC, datecreated DESC LIMIT '.$limit;

        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
    public function getStory($id)
    {
        $sql = 'SELECT tbl_hotels_stories.*, categoryname, name as location, geonameid, filename, tbl_hotels_stories.id as storyid FROM tbl_hotels_stories
INNER JOIN tbl_hotels_categories ON (tbl_hotels_stories.storycategory=tbl_hotels_categories.id)
LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation=tbl_geonames.geonameid)
LEFT JOIN tbl_files ON (tbl_hotels_stories.storyimage=tbl_files.id)
WHERE tbl_hotels_stories.id = \''.$id.'\'';

        $results = $this->getArray($sql);

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }

    /**
    *
    *
    *
    */
    public function getTopStoriesFormatted()
    {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $numTopStoriesValue = $objSysConfig->getValue('NUMFRONTPAGETOPICS', 'hotels');
        
        $stories = $this->getTopStories($numTopStoriesValue);

        if (count($stories) == 0) {
            return array('topstoryids'=>array(), 'stories'=>'');
        } else {
            $output = '';

            $objTrimString = $this->getObject('trimstr', 'strings');
            $objThumbnails = $this->getObject('thumbnails', 'filemanager');

            $storyIds = array();

            foreach ($stories as $story)
            {
                $storyIds[] = $story['id'];

                $output .= '<div class="newsstory">';

                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                $storyLink->link = $story['storytitle'];

                if ($story['storyimage'] != '') {
                    $storyLink->link = '<img class="storyimage" src="'.$objThumbnails->getThumbnail($story['storyimage'], $story['filename']).'" alt="'.$story['storytitle'].'" title="'.$story['storytitle'].'" />';

                    $output .= '<div class="storyimagewrapper">'.$storyLink->show().'</div>';
                }

                $storyLink->link = $story['storytitle'];

                $output .= '<h3>'.$storyLink->show().'</h3>';

                if ($story['location'] != '') {
                    $locationLink = new link ($this->uri(array('action'=>'viewbylocation', 'id'=>$story['storylocation'])));
                    $locationLink->link = $story['location'];
                    //$output .= '[ '.$locationLink->show().'] ';
                    $output .= '[ '.$story['location'].' ] ';
                }

                $output .= $objTrimString->strTrim(strip_tags($story['storytext']), 300, TRUE);

                $storyLink->link = 'Read More...';
                $output .= ' ('.$storyLink->show().')';

                $output .= '</div><br clear="both" />';
            }

            return array('topstoryids'=>$storyIds, 'stories'=>$output);
        }
    }

    /**
    * Method to get the list of other stories, that are top stories
    * @param array $storyIds Record Ids of Top Stories to exclude
    * @return array List of Non Top Stories
    */
    public function getNonTopStories($category, $storyIds, $limit = 5)
    {
        if (!is_array($storyIds)) {
            $storyIds = array($storyIds);
        }

        $where = ' WHERE storycategory=\''.$category.'\' ';

        if (count($storyIds) > 0) {
            $where .= ' AND (';
            $joiner = '';

            foreach ($storyIds as $id)
            {
                $where .= $joiner.' tbl_hotels_stories.id != \''.$id.'\' ';
                $joiner = ' AND ';
            }

            $where .= ')';
        }

        $sql = 'SELECT tbl_hotels_stories.* FROM tbl_hotels_stories '.$where.'
 AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\'
ORDER BY storydate DESC LIMIT '.$limit;

//echo $sql;

        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
    public function getNonTopStoriesFormatted($category, $storyIds)
    {
        $stories = $this->getNonTopStories($category, $storyIds);

        if (count($stories) == 0) {
            return '';
        } else {
            $str = '<ul>';

            foreach ($stories as $story)
            {
                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                $storyLink->link = $story['storytitle'];
                $str .= '<li>'.$storyLink->show().'</li>';
            }

            $str .= '</ul>';

            return $str;
        }
    }

    /**
    *
    *
    *
    */
    public function getRelatedStories($id, $storyDate)
    {
        $storyKeywords = $this->objKeywords->getStoryKeywords($id);

        if (count($storyKeywords) == 0) {
            return $storyKeywords;
        } else {
            $keywordWhere = '(';
            $joiner = '';

            foreach ($storyKeywords as $keyword)
            {
                $keywordWhere .= $joiner.' keyword=\''.$keyword.'\'';
                $joiner = ' OR ';
            }

            $keywordWhere .= ')';

            $sql = 'SELECT tbl_hotels_stories.id, storytitle, storydate, tbl_hotels_stories.datecreated FROM tbl_hotels_stories, tbl_hotels_keywords WHERE (tbl_hotels_stories.id = storyid) AND ('.$keywordWhere.') AND (storydate <= \''.$storyDate.'\') AND (tbl_hotels_stories.id != \''.$id.'\') ORDER BY storydate DESC';
            return $this->getArray($sql);
        }
    }

    /**
    *
    *
    *
    */
    public function getRelatedStoriesFormatted($id, $storyDate, $dateCreated)
    {
        $stories = $this->getRelatedStories($id, $storyDate);

        if (count($stories) == 0) {
            return '';
        } else {
            $str = '<h4>'.$this->objLanguage->languageText('mod_hotels_relatedstories', 'hotels', 'Related Hotels').'</h4><ul>';

            $counter = 0;
            foreach ($stories as $story)
            {
                if ($storyDate == $story['storydate']) {
                    if ($this->objDateTime->sqlToUnixTime($story['datecreated']) < $this->objDateTime->sqlToUnixTime($dateCreated)) {
                        $okToDisplay = TRUE;
                    } else {
                        $okToDisplay = FALSE;
                    }
                } else {
                    $okToDisplay = TRUE;
                }


                if ($okToDisplay) {
                    $counter++;
                    $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                    $storyLink->link = $story['storytitle'];

                    $str .= '<li>'.$storyLink->show().'</li>';
                }
            }

            if ($counter == 0) {
                return '';
            } else {
                return $str.'</ul>';
            }
        }
    }

    /**
    *
    *
    *
    */
    public function getKeywordStories($keyword)
    {
        $sql = 'SELECT tbl_hotels_stories.*, categoryname, name as location, filename FROM tbl_hotels_stories
INNER JOIN tbl_hotels_categories ON (tbl_hotels_stories.storycategory=tbl_hotels_categories.id)
INNER JOIN tbl_hotels_keywords ON (tbl_hotels_stories.id=tbl_hotels_keywords.storyid)
LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation=tbl_geonames.geonameid)
LEFT JOIN tbl_files ON (tbl_hotels_stories.storyimage=tbl_files.id)
WHERE tbl_hotels_keywords.keyword = \''.$keyword.'\' ORDER BY storydate DESC';

        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
    public function getCategoryStories($category, $orderby = 'storydate DESC', $includeFutureStories=FALSE)
    {
        $sql = 'SELECT tbl_hotels_stories.*, name as location, filename FROM tbl_hotels_stories
LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation=tbl_geonames.geonameid)
LEFT JOIN tbl_files ON (tbl_hotels_stories.storyimage=tbl_files.id)
WHERE tbl_hotels_stories.storycategory = \''.$category.'\' ';
        
        
        if ($includeFutureStories == FALSE) {
            $sql .=  ' AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' ';
        }
        
        $sql .= ' ORDER BY '.$orderby;
        
        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
    public function generateKeywordTimeline($keyword)
    {
        $stories = $this->getKeywordStories($keyword);

        return $this->generateTimelineCode($stories);
    }

    /**
    *
    *
    *
    */
    public function getStoriesList()
    {
        $sql = 'SELECT tbl_hotels_stories.*, categoryname, name as location FROM tbl_hotels_stories
INNER JOIN tbl_hotels_categories ON (tbl_hotels_stories.storycategory=tbl_hotels_categories.id)
LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation=tbl_geonames.geonameid)
ORDER BY storydate DESC';

        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
	
    public function getFeedLinks()
    {
        //$str = '<br /><h5>'.$this->objLanguage->languageText('mod_hotels_rssfeeds', 'hotels', 'RSS Feeds').'</h5>';

        //$objIcon = $this->newObject('geticon', 'htmlelements');
        //$objIcon->setIcon('rss');
        //$objIcon->align='absmiddle';
        //$rssIcon = $objIcon->show();

       // $topStoriesFeed = new link ($this->uri(array('action'=>'topstoriesfeed')));
        //$topStoriesFeed->link = $this->objLanguage->languageText('mod_hotels_topstoriesfeed', 'hotels', 'Top Hotels Feed');

        //$str .= '<p>'.$rssIcon.' '.$topStoriesFeed->show().'</p>';

        //return $str;
    }

    /**
    *
    *
    *
    */
    public function generateNewsSmap()
    {
        $items = $this->generateNewsSmapSQL();
        $str = '';

        $objTrimString = $this->getObject('trimstr', 'strings');

        $locations = array();
        foreach ($items as $item)
        {
            if (array_key_exists($item['storylocation'], $locations)) {
                $locations[$item['storylocation']] = $locations[$item['storylocation']] + 1;
            } else {
                $locations[$item['storylocation']] = 1;
            }
        }

        foreach ($locations as $location=>$value)
        {
            if ($value == 1) {
                unset($locations[$location]);
            }
        }

        $groupItems = array();

        foreach ($items as $item)
        {
            $latitude = $item['latitude'];
            $longitude = $item['longitude'];

            if (array_key_exists($item['storylocation'], $locations)) {

                $groupItems[$item['storylocation']]['content'][] = array('id'=>$item['storyid'], 'title'=>$item['storytitle']);
                $groupItems[$item['storylocation']]['latitude'] = $latitude;
                $groupItems[$item['storylocation']]['longitude'] = $longitude;
                $groupItems[$item['storylocation']]['locationname'] = $item['location'];

            } else {
                $str .= 'var point = new GLatLng('.$latitude.','.$longitude.');'."\r\n";
                $content = '<h3>'.$item['location'].': '.$item['storytitle'].'</h3>';

                $content .= $objTrimString->strTrim(($item['storytext']), 150, TRUE);

                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$item['storyid'])));
                $storyLink->link = 'Read More...';

                $link = $storyLink->show();
                $link = str_replace('&amp;', '&', $link);

                $content .= ' ('.$link.')';

                $content = '<div style="width:300px;">'.$content.'</div>';

                $content = stripslashes($content);
                $content = str_replace('"', '\"', $content);

                $content = ereg_replace("[\n\r]", " ", $content);
                //$content = ereg_replace("\t\t+", "\n", $content);

                $str .= 'var marker = createMarker(point,"'.$content.'");'."\r\n";
                $str .= 'map.addOverlay(marker);'."\r\n";
            }
        }

        if (count($groupItems) > 0) {
            foreach ($groupItems as $group)
            {
                $str .= 'var point = new GLatLng('.$group['latitude'].', '.$group['longitude'].');'."\r\n";
                $content = '<h3>'.$group['locationname'].'</h3><ul>';
                foreach ($group['content'] as $item)
                {
                    $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$item['id'])));
                    $storyLink->link = $item['title'];

                    $content .= '<li>'.$storyLink->show().'</li>';
                }

                $content .= '</ul>';

                $content = '<div style="width:300px;">'.$content.'</div>';

                $content = stripslashes($content);
                $content = str_replace('"', '\"', $content);

                $str .= 'var marker = createMarker(point,"'.$content.'");'."\r\n";
                $str .= 'map.addOverlay(marker);'."\r\n";
            }

        }
        
        return $str;
    }
    
    /**
    *
    *
    *
    */
    public function generateNewsSmapSQL()
    {
        $sql = 'SELECT tbl_hotels_stories.*, tbl_hotels_stories.id as storyid, latitude, longitude, name as location FROM tbl_hotels_stories INNER JOIN tbl_geonames ON (tbl_hotels_stories.storylocation = tbl_geonames.geonameid) WHERE dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\'';

        return $this->getArray($sql);
    }
    
    public function getMonthStories($category, $month=NULL, $year=NULL)
    {
        if ($month == NULL || !is_int($month)) {
            $month = date('m');
        }
        
        if ($year == NULL || !is_int($year)) {
            $year = date('Y');
        }
        
        $startPeriod = $year.'-'.$month.'-01';
        $endPeriod = $year.'-'.$month.'-31';
        
        $sql = 'SELECT tbl_hotels_stories.*, categoryname, name as location FROM tbl_hotels_stories
        INNER JOIN tbl_hotels_categories ON (tbl_hotels_stories.storycategory=tbl_hotels_categories.id)
        LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation=tbl_geonames.geonameid)
        WHERE tbl_hotels_stories.storycategory = \''.$category.'\'  AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' 
        AND storydate >= \''.$startPeriod.'\'  AND storydate <= \''.$endPeriod.'\' ORDER BY storydate';
        
        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
    public function getFirstStory($category, $order)
    {
        if (substr_count(strtolower($order), 'desc') == 0) {
            $order .= ', datecreated';
        } else {
            $order .= ', datecreated desc';
        }

        $sql = 'SELECT tbl_hotels_stories.*, tbl_hotels_stories.id as storyid, latitude, longitude FROM tbl_hotels_stories LEFT JOIN tbl_geonames ON (tbl_hotels_stories.storylocation = tbl_geonames.geonameid) WHERE storycategory=\''.$category.'\' AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' ORDER BY '.$order.' LIMIT 1';

        $results = $this->getArray($sql);

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }

    /**
    *
    *
    *
    */
    public function getCategoryTitles($category, $order)
    {
        if (substr_count(strtolower($order), 'desc') == 0) {
            $order .= ', datecreated';
        } else {
            $order .= ', datecreated desc';
        }

        $sql = 'SELECT tbl_hotels_stories.id as storyid, storytitle FROM tbl_hotels_stories WHERE storycategory=\''.$category.'\' AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' ORDER BY '.$order;

        return $this->getArray($sql);
    }

    /**
    *
    *
    *
    */
    public function serializeStoryOrder($categoryId, $categoryOrder)
    {
        $stories = $this->getCategoryTitles($categoryId, $categoryOrder);

        if (count($stories) > 0) {
            $counter = 1;

            foreach ($stories as $story)
            {
                $this->update('id', $story['storyid'], array('storyorder'=>$counter));
                $counter++;
            }
        }
    }

    /**
    *
    *
    *
    */
    public function getLastNewsStoryDate()
    {
        $sql = 'SELECT storydate FROM tbl_hotels_stories ORDER BY storydate DESC LIMIT 1';

        $results = $this->getArray($sql);


        if (count($results) == 0) {
            return date('F j Y');
        } else {
            $date = explode('-', $results[0]['storydate']);
            $objDateTime = $this->getObject('dateandtime', 'utilities');
            return $objDateTime->monthFull($date[1]).' '.$date[2].' '.$date[0];
        }
    }

    /**
    *
    *
    *
    */
    public function moveItemUp($id)
    {
        $item = $this->getStory($id);

        $prevItem = $this->getPreviousItem($id, $item['storycategory']);

        if ($item == FALSE || $prevItem == FALSE) {
            return FALSE;
        } else {
            $this->update('id', $item['id'], array('storyorder' => $prevItem['storyorder']));
            $this->update('id', $prevItem['id'], array('storyorder' => $item['storyorder']));

            return TRUE;
        }
    }

    /**
    *
    *
    *
    */
    public function moveItemDown($id)
    {
        $item = $this->getStory($id);

        $prevItem = $this->getNextItem($id, $item['storycategory']);

        if ($item == FALSE || $prevItem == FALSE) {
            return FALSE;
        } else {
            $this->update('id', $item['id'], array('storyorder' => $prevItem['storyorder']));
            $this->update('id', $prevItem['id'], array('storyorder' => $item['storyorder']));

            return TRUE;
        }
    }

    /**
    *
    *
    *
    */
    public function getPreviousItem($id, $category, $limit=1)
    {
        // Limit of 0 Means All Records
        if ($limit == 0) {
            $limit = 1000000;
        }

        $item = $this->getStory($id);


        if ($item == FALSE) {
            return FALSE;
        } else {
            $item2 = $this->getAll(' WHERE storyorder < '.$item['storyorder'].' AND storycategory =\''.$category.'\' AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' ORDER BY storyorder DESC LIMIT '.$limit);

            if (count($item2) == 0) {
                return FALSE;
            } else {
                if ($limit == 1) {
                    return $item2[0];
                } else {
                    return $item2;
                }
            }
        }
    }

    /**
    *
    *
    *
    */
    public function getNextItem($id, $category, $limit=1)
    {
        // Limit of 0 Means All Records

        if ($limit == 0) {
            $limit = 1000000;
        }

        $item = $this->getStory($id);

        if ($item == FALSE) {
            return FALSE;
        } else {
            $item2 = $this->getAll(' WHERE storyorder > '.$item['storyorder'].' AND storycategory =\''.$category.'\' AND dateavailable <= \''.strftime('%Y-%m-%d %H:%M:%S', mktime()).'\' ORDER BY storyorder LIMIT '.$limit);

            if (count($item2) == 0) {
                return FALSE;
            } else {
                if ($limit == 1) {
                    return $item2[0];
                } else {
                    return $item2;
                }
            }
        }
    }

    /**
    *
    *
    *
    */
    public function serializeCategoryOrder($category, $order)
    {
        $stories = $this->getCategoryStories($category, $order);

        if (count($stories) > 0) {
            $counter = 1;
            foreach ($stories as $story)
            {
                $this->update('id', $story['id'], array('storyorder'=>$counter));
                $counter++;
            }
        }
    }
    
    
    function topStoriesFeed()
    {
        $topStories = $this->getTopStories();
        //print_r($topStories);

        $this->objFeedCreator = $this->getObject('feeder', 'feed');
        $objTrimString = $this->getObject('trimstr', 'strings');
        
        $objUser = $this->getObject('user', 'security');

        $this->objFeedCreator->setupFeed(TRUE, 
									$this->objConfig->getSiteName().' - '
									.$this->objLanguage->languageText('mod_hotels_topstories', 'hotels', 'Top Hotels'), 
									$this->objLanguage->languageText('mod_hotels_summaryoftopstories', 'hotels', 'Summary of Top hotels from').' '
									.$this->objConfig->getSiteName(), 
									$this->objConfig->getsiteRoot(), 
									$this->uri(array('action'=>'topstoriesfeed')));

        foreach ($topStories as $story)
        {
            $title = $story['storytitle'];

            $content = $objTrimString->strTrim(($story['storytext']), 150, TRUE);

            $this->objFeedCreator->addItem($title, $this->uri(array('action'=>'viewstory', 'id'=>$story['id'])), $content, 'here', $objUser->fullName($story['modifierid']));
        }

        return $this->objFeedCreator->output();
    }


}
?>