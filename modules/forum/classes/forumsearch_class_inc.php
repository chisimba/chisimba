<?php

/**
* Class to control searching in the discussion forum
* @author Tohir Solomons
*/
class forumsearch extends dbtable
{
    /**
    * @var string $defaultForum The Default Selected Forum on the search drop down
    */
    var $defaultForum = 'all';
    
    /**
    * @var string $searchTerm The Default Search text
    */
    var $searchTerm = '';
    
    /**
    * @var array $ignoreWords List of words to ignore. Not implemented yet.
    */
    var $ignoreWords;
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_forum_post_text');
        
        
        // List of Search Terms to ignore - Incomplete list
        $this->ignoreWords = array ('and', 'for', 'in');
        
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        
        $this->objForum =& $this->getObject('dbforum');
        
        // Get Context Code Settings
        $this->contextObject =& $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
        
        // If not in context, set code to be 'root' called 'Lobby'
        $this->contextTitle = $this->contextObject->getTitle();
        if ($this->contextCode == ''){
            $this->contextCode = 'root';
            $this->contextTitle = 'Lobby';
        }
        
        $this->objLanguage =& $this->getObject('language', 'language'); 
    }
    
    /**
    * Method to show the search form
    * @param boolean $showInFieldset Flag whether to show the form in a fieldset or not.
    */
    function show($showInFieldset=TRUE)
    {
        $allForums = $this->objForum->showAllForums($this->contextCode);
        
        $dropdown = new dropdown('forum');
        $dropdown->addOption('all', $this->objLanguage->languageText('mod_forum_allforums', 'forum', 'All Forums'));
        
        foreach ($allForums as $forum)
        {
            $dropdown->addOption($forum['forum_id'], $forum['forum_name']);
        }
        
        $dropdown->setSelected($this->defaultForum);
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_forum_searchforums', 'forum', 'Search Forums'));
        
        $form = new form('searchforums', 'index.php');
        $form->method = 'GET';
        
        $label = new label($this->objLanguage->languageText('mod_forum_searchfor', 'forum', 'Search For').':', 'input_term');
        
        $searchFor = new textinput('term');
        $searchFor->value = htmlentities(stripslashes($this->searchTerm));
        $searchFor->size = 70;
        
        $button = new button ('submitform');
        $button->cssClass = 'search';
        $button->value = $this->objLanguage->languageText('word_search', 'forum', 'Search');
        $button->setToSubmit();
        
        $hiddenItems = '';
        
        $hiddenInput = new hiddeninput('module', 'forum');
        $hiddenItems .= $hiddenInput->show();
        
        $hiddenInput = new hiddeninput('action', 'searchforum');
        $hiddenItems .= $hiddenInput->show();
        
        $form->addToForm($hiddenItems.'<p align="center">'.$label->show().' '.$searchFor->show().' in '.$dropdown->show().' '.$button->show().'</p>');
        
        if ($showInFieldset) {
            $fieldset->addContent($form->show());
            return $fieldset->show();
        } else {
            return $form->show();
        }
    }
    
    /**
    * Method to search the forums
    * @param string $terms Terms to search for
    * @param string $forum Record Id of the forum to search in, or 'all' for all forums in current context.
    */
    function searchForum($terms, $forum='all')
    {
        $searchTerms = $this->prepareTerm($terms);
        $forumClause = $this->prepareForumClause($forum);
        
        $SQL = 'SELECT tbl_forum_post_text.*, topic_id FROM tbl_forum_post_text
        INNER JOIN tbl_forum_post ON (tbl_forum_post.id = tbl_forum_post_text.post_id ) 
        INNER JOIN tbl_forum_topic ON (tbl_forum_topic.id = tbl_forum_post.topic_id) 
        INNER JOIN tbl_forum ON (tbl_forum.id = tbl_forum_topic.forum_id) 
        WHERE '.$searchTerms. ' AND '.$forumClause;
        
        $results = $this->getArray($SQL);
        
        return $this->prepareResults($results, $terms);
    }
    
    /**
    * Method to analyse the search results
    * 
    * It takes the results, determines number of matches, throws out 'bad' results (like html), and orders results according to matches
    * @param array $results List of Results from database
    * @param string $terms List of terms searched for
    */
    function prepareResults($results, $terms)
    {
        $terms = preg_replace('/\\W/', ' ', $terms);  // Replace non characters with space
        $terms = preg_replace('/\\s{2,}/', ' ', $terms); // Replace two or more spaces with one.
        $terms = strip_tags($terms); // strip tags - prevents match in html tags
        $terms = trim($terms); // remove white space
        $searchTerms = explode(' ', $terms);  // Explode term into array, split by space
        
        $resultsArray = array(); // array containing number of words
        $searchResult = array(); // array containing post details
        
        $objTrimstr = $this->getObject('trimstr', 'strings'); // Load Trim String Class
        $objHighlighter = $this->getObject('highlight', 'strings'); // Load Trim String Class
        $objHighlighter->replacement = '<strong class="confirm">{keyword}</strong>';
        
        // Loop Through Results
        foreach ($results as $result)
        {
            $wordCount = 0;
            
            $text = strip_tags($result['post_text']); // strip tags - prevents match in html tags
            $title = strip_tags($result['post_title']); // strip tags - prevents match in html tags
            
            // Chop off text if too long
            $postText = $objTrimstr->strTrim(strip_tags($result['post_text']), 300);
            $postTitle = $result['post_title'];
            
            foreach($searchTerms as $term) // loop through terms to get number of times word appears
            {
                $wordCount += substr_count(strtolower($text), strtolower(trim($term))); // convert to lower case for match
                $wordCount += substr_count(strtolower($title), strtolower(trim($term))); // convert to lower case for match
                
                $postText = $objHighlighter->show($postText, $term);
                $postTitle = $objHighlighter->show($postTitle, $term);
            }
            
            // Only add to list if number of words is greater than 0
            if ($wordCount > 0) {
                $resultsArray[$result['id']] = $wordCount;
                $searchResult[$result['id']] = array('topic_id'=>$result['topic_id'], 'post_id'=>$result['post_id'], 'post_title'=>$postTitle, 'post_text'=>$postText);
            }
        }
        
        asort($resultsArray, SORT_NUMERIC); // Sort Items
        $resultsArray = array_reverse($resultsArray); // Reverse for Descending Order
        
        
        if (count($resultsArray) > 0) {
            $return = '<ol>';
            
            foreach ($resultsArray as $id=>$wordCount)
            {
                $link = new link ($this->uri(array('action'=>'viewtopic', 'id'=>$searchResult[$id]['topic_id'], 'post'=>$searchResult[$id]['post_id'])));
                $link->link = $searchResult[$id]['post_title'];
                $return .= '<li><p>'.$link->show().' ('.$wordCount.' Matches) <br /><br />'.$searchResult[$id]['post_text'].'</p></li>';
            }
            
            
            $return .= '</ol>';
        } else {
            $return = '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_forum_nosearchresultsfor', 'forum', 'No Results found for').' '.stripslashes($terms).'</div>';
        }
        
        return $return;
    }
    
    /**
    * Method to split up a search term and put it in a 'WHERE' clause
    * @param string $term
    * @return string finished term
    */
    function prepareTerm($terms)
    {
        $terms = preg_replace('/\\W/', ' ', $terms);  // Replace non characters with space
        $terms = preg_replace('/\\s{2,}/', ' ', $terms); // Replace two or more spaces with one.
        $terms = strip_tags($terms); // strip tags - prevents match in html tags
        $terms = trim($terms); // remove white space
        $searchTerms = explode(' ', $terms);  // Explode term into array, split by space
        
        $str = '(';
        $title = '(';
        $count = 0;
        $or = '';
        
        foreach ($searchTerms as $searchItem)
        {
            $str .= $or.'( post_text LIKE "%'.trim($searchItem).'%") ';
            $title .= $or.'( post_title LIKE "%'.trim($searchItem).'%") ';
            $or = ' OR ';
        }
        
        $str .= ')';
        $title .= ')';
        
        return '('.$str.' OR '.$title.')';
    }
    
    /**
    * Method to prepare the forum WHERE clause
    * @param string $forum
    * @return string finished clause
    */
    function prepareForumClause($forum)
    {
        if ($forum == 'all') {
            $where = 'forum_context = "'.$this->contextCode.'"';
        } else {
            $where = 'forum_id="'.$forum.'"';
        }
        
        return $where;
    }
    
        /**
    * Method to search the forums with no HTML attached
    * @param string $terms Terms to search for
    * @param string $forum Record Id of the forum to search in, or 'all' for all forums in current context.
    */
    function searchForumNoHTML($terms, $forum='all')
    {
        $searchTerms = $this->prepareTerm($terms);
        $forumClause = $this->prepareForumClause($forum);
        
        $SQL = 'SELECT tbl_forum_post_text.*, topic_id FROM tbl_forum_post_text
        INNER JOIN tbl_forum_post ON (tbl_forum_post.id = tbl_forum_post_text.post_id ) 
        INNER JOIN tbl_forum_topic ON (tbl_forum_topic.id = tbl_forum_post.topic_id) 
        INNER JOIN tbl_forum ON (tbl_forum.id = tbl_forum_topic.forum_id) 
        WHERE '.$searchTerms. ' AND '.$forumClause;
        
        $results = $this->getArray($SQL);
        
        return $results;
    }

}

?>