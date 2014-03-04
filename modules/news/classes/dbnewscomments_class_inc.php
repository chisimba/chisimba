<?php

class dbnewscomments extends dbtable
{

    public function init()
    {
        parent::init('tbl_news_storycomments');
		$this->objUser = $this->getObject('user', 'security');
		$this->objLanguage = $this->getObject('language', 'language');
    }
    
    public function addComment($storyId, $name, $email, $comment)
	{
        return $this->insert(array(
				'storyid'=>$storyId,
				'fullname'=>$name, 
				'email'=>$email, 
				'comment'=>strip_tags($comment), 
				'commentdate'=>date('Y-m-d'), 
				'creatorid' => '1',
				'datecreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
			));
	}
    
    public function deleteStoryComments($storyid)
    {
        return $this->delete('storyid', $storyid);
    }
    
    public function getStoryComments($storyId)
    {
        
        
        $comments = $this->getStoryCommentsSQL($storyId);
        
        if (count($comments) == 0) {
            return '';
        } else {
            
            $this->loadClass('htmlheading', 'htmlelements');
            $header = new htmlheading();
            $header->type = 3;
            $header->str = $this->objLanguage->languageText('mod_news_usercommentsonstory', 'news', 'User Comments on this Story');
            
            $commentStr = $header->show().'<br />';
            
            $objGravatar = $this->getObject('getgravatar', 'gravatar');
            $objGravatar->avatarSize = 40;
            $objGravatar->cssClass = 'storyimage';
            
            $table = $this->newObject('htmltable', 'htmlelements');
            
            foreach ($comments as $comment)
            {
                $table->startRow();
                $table->addCell($objGravatar->show($comment['email']));
                
                $table->addCell('<strong>'.$comment['fullname'].'</strong> - <em>'.$comment['commentdate'].'</em><br />'.$comment['comment'].'<br />');
                
                $table->endRow();
                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();
            }
            
            return $commentStr.$table->show().'<br />';
        }
    }
    
    public function getStoryCommentsSQL($storyId)
    {
        return $this->getAll(' WHERE storyid=\''.$storyId.'\' ORDER BY commentdate DESC, datecreated DESC');
    }
    
    public function commentsForm($id)
    {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');

        $form = new form ('addcomment', $this->uri(array('action'=>'savecomment')));

        $table = $this->newObject('htmltable', 'htmlelements');

        $table->startRow();
        $table->addCell('Your Name');

        $name = new textinput ('name');
        $name->size = 50;

        $table->addCell($name->show());
        $table->endRow();
        
        $table->startRow();
        $table->addCell('Email');

        $email = new textinput ('email');
        $email->size = 50;

        $table->addCell($email->show());
        $table->endRow();

        $table->startRow();
        $table->addCell('Comments');

        $comments = new textarea ('comments');
        $comments->size = 50;

        $table->addCell($comments->show());
        $table->endRow();

        $table->startRow();
        $table->addCell('&nbsp;');

        $comments = new button ('savecomment', 'Submit Comment');
        $comments->setToSubmit();

        $table->addCell($comments->show());
        $table->endRow();

        $form->addToForm('<h3>'.$this->objLanguage->languageText('mod_news_commentonstory', 'news', 'Comment on this Story').':</h3>'.$table->show());

        $hiddeninput = new hiddeninput('id', $id);
        $form->addToForm($hiddeninput->show());

        return $form->show();
    
    }


}
?>