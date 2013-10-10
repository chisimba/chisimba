<?php

class dbnewskeywords extends dbtable
{

    public function init()
    {
        parent::init('tbl_news_keywords');
		$this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    public function addStoryKeywords($storyId, $keywords)
	{
		if (!is_array($keywords)) {
			
		}
		
		$this->clearStoryKeywords($storyId);
		
		if (count($keywords) > 0) {
			foreach ($keywords as $keyword)
			{
				$keyword = trim(stripslashes($keyword));
				
				if ($keyword != '') {
					$this->addKeyword($storyId, $keyword);
				}
			}
		}
	}
	
	private function addKeyword($storyId, $keyword)
	{
		return $this->insert(array(
				'storyid'=>$storyId, 
				'keyword'=>$keyword, 
				'creatorid' => $this->objUser->userId(),
				'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
			));
	}
	
	public function clearStoryKeywords($storyId)
	{
		return $this->delete('storyid', $storyId);
	}
	
	public function getAjaxKeywords($start)
	{
		return $this->getAll(' WHERE keyword LIKE \''.$start.'%\' GROUP BY keyword ORDER BY keyword');
	}
	
	public function getKeywordCloud($period=NULL)
	{
		$sql = 'SELECT keyword, count(keyword) AS keywordcount FROM tbl_news_keywords GROUP BY keyword ORDER BY keyword';
		
		$results = $this->getArray($sql);
		
		if (count($results) > 0) {
			$tagArray = array();
			foreach ($results as $result)
			{
				$tagArray[] = array('name'=>$result['keyword'], 'url'=>$this->uri(array('action'=>'viewbykeyword', 'id'=>$result['keyword'])), 'weight'=>$result['keywordcount']*2, 'time'=>'');
			}
			
			$tagCloud = $this->newObject('tagcloud', 'utilities');
			return $tagCloud->buildCloud($tagArray);
			
		} else {
			return NULL;
		}
	}
	
	public function getStoryKeywords($storyId)
	{
		$sql = 'SELECT keyword FROM tbl_news_keywords WHERE storyid=\''.$storyId.'\' ORDER BY keyword';
		
		$results = $this->getArray($sql);
        
        // Convert to Single Array
        if (count($results) == 0) {
            return $results;
        } else {
            $newArray = array();
            
            foreach ($results as $result)
            {
                $newArray[] = $result['keyword'];
            }
            
            return $newArray;
        }
	}
    
    public function getStoryKeywordsBlock($storyId)
    {
        //$sql = 'SELECT keyword, count(keyword) as keywordcount FROM tbl_news_keywords WHERE keyword = (SELECT keyword FROM tbl_news_keywords WHERE storyid=\''.$storyId.'\' ORDER BY keyword) GROUP BY keyword ORDER BY keywordcount';
        
        $sql = 'SELECT keyword, count( keyword ) AS keywordcount FROM tbl_news_keywords WHERE keyword IN (SELECT keyword FROM tbl_news_keywords WHERE storyid = \''.$storyId.'\') GROUP BY keyword';
        
        $results = $this->getArray($sql);
        
        if (count($results) == 0) {
            return '';
        } else {
            
            $str = '<h4>'.$this->objLanguage->languageText('word_timelines', 'word', 'Timelines').'</h4><ul>';
            
            foreach ($results as $result)
            {
                $link = new link ($this->uri(array('action'=>'viewbykeyword', 'id'=>$result['keyword'])));
                $link->link = $result['keyword'];
                
                $str .= '<li>'.$link->show().'</li>';
            }
            
            $str .= '</ul>';
            
            return $str;
        }
        
    }
    
    public function getLastNewsStoryDate($keyword)
    {
        $sql = 'SELECT storydate FROM tbl_news_stories INNER JOIN tbl_news_keywords ON (tbl_news_keywords.storyid = tbl_news_stories.id AND tbl_news_keywords.keyword=\''.$keyword.'\')ORDER BY storydate DESC LIMIT 1';
        
        $results = $this->getArray($sql);
        
        
        if (count($results) == 0) {
            return date('F j Y');
        } else {
            $date = explode('-', $results[0]['storydate']);
            $objDateTime = $this->getObject('dateandtime', 'utilities');
            return $objDateTime->monthFull($date[1]).' '.$date[2].' '.$date[0];
        }
    }

}
?>