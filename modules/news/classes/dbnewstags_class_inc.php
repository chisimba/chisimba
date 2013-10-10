<?php

class dbnewstags extends dbtable
{

    public function init()
    {
        parent::init('tbl_news_tags');
		$this->objUser = $this->getObject('user', 'security');
    }
    
    public function addStoryTags($storyId, $tags)
	{
		$tags = explode(',', $tags);
		
		$this->clearStoryTags($storyId);
		
		if (count($tags) > 0) {
			foreach ($tags as $tag)
			{
				$tag = trim(stripslashes($tag));
				
				if ($tag != '') {
					$this->addTag($storyId, $tag);
				}
			}
		}
	}
	
	private function addTag($storyId, $tag)
	{
		return $this->insert(array(
				'storyid'=>$storyId, 
				'tag'=>$tag, 
				'creatorid' => $this->objUser->userId(),
				'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
			));
	}
    
    public function getStoryTags($storyId)
    {
        $results = $this->getAll(' WHERE storyid=\''.$storyId.'\'');
        
        if (count($results) == 0) {
            return '';
        } else {
            $returnArray = array();
            
            foreach ($results as $result)
            {
                $returnArray[] = $result['tag'];
            }
            
            return $returnArray;
        }
    }
	
	public function clearStoryTags($storyId)
	{
		return $this->delete('storyid', $storyId);
	}

}
?>