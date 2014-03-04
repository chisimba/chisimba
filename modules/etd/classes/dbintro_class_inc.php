<?php
/**
* dbIntro class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbIntro class for managing the data in the tbl_etd_intro table.
* @author Megan Watson
* @copyright (c) 2004 UWC
* @version 0.2
*/

class dbIntro extends dbtable
{
    /**
    * Constructor method
    *
    * @access public
    */
    public function init()
    {
        parent::init('tbl_etd_intro');
        $this->table = 'tbl_etd_intro';
    }

    /**
    * Method to insert a new introduction into the database.
    *
    * @access public
    * @param string $userId The user Id for the creator.
    * @param string $id The Id for the introduction entry.
    * @return string The row id
    */
    public function addIntro($userId, $id = NULL)
    {
        $fields = array();
        $fields['language'] = $this->getParam('language', 'en');
        $fields['content_text'] = $this->getParam('introduction');
        $fields['updated'] = $this->now();
        if(!empty($id)){
            $fields['modifierid'] = $userId;
            $id = $this->update('id', $id, $fields);
        }else{
            $fields['content_type'] = 'introduction';
            $fields['creatorid'] = $userId;
            $fields['datecreated'] = $this->now();
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to insert a new piece of content into the database.
    *
    * @access public
    * @param string $content The content to insert / update
    * @param string $type The content type - intro, footer, etc
    * @param string $userId The user Id for the creator.
    * @param string $id The row id of the entry
    * @return string The row id
    */
    public function addContent($content, $type, $userId, $id)
    {
        $fields = array();
        $fields['content_text'] = $content;
        $fields['updated'] = $this->now();
        if(!empty($id)){
            $fields['modifierid'] = $userId;
            $id = $this->update('id', $id, $fields);
        }else{
            $fields['content_type'] = $type;
            $fields['creatorid'] = $userId;
            $fields['datecreated'] = $this->now();
            $id = $this->insert($fields);
        }
        if($type == 'footer'){
            $this->setSession('footerStr', $content);
        }
        return $id;
    }

    /**
    * Method to get the introduction by language.
    *
    * @access public
    * @param string $lang The language given.
    */
    public function getIntro($lang = 'en')
    {
        $sql = "SELECT * FROM {$this->table} WHERE content_type = 'introduction'";
        if(!empty($lang)){
            $sql .= " AND language = '$lang' ";
        }
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0];
        }
        
        if($lang != 'en'){
            return $this->getIntro('en');
        }
        
        return '';
    }
    
    /**
    * Method to get the content by type
    *
    * @access public
    * @param string $type
    * @return array $data The content data
    */
    public function getContent($type = 'footer', $lang = NULL)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE content_type = '$type'";
            
        if(!empty($lang)){
            $sql .= " AND language = '$lang' ";
        }
        
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0];
        }
        return '';
    }
    
    /**
    * Method to get the introduction text. Parsed.
    *
    * @access public
    * @return string
    */
    public function getParsedIntro()
    {
        $lang = ''; $text = '';
        $data = $this->getIntro($lang);
        if(isset($data['content_text'])){
            $text = $data['content_text'];
        }
        return $this->parseIntro($text);
    }
    
    /**
    * Method to parse the introduction text for keywords.
    *
    * @access public
    * @param string $text The text to be parsed
    * @return string
    */
    public function parseIntro($text = '')
    {
        $objConfig = $this->getObject('altconfig', 'config');
        $institution = $objConfig->getinstitutionName();
        $shortname = $objConfig->getinstitutionShortName();
        
        if(empty($text)){
            $objLanguage = $this->getObject('language', 'language');
            return $objLanguage->code2Txt('mod_etd_welcomeintro', 'etd', array('institution' => $institution, 'shortname' => $shortname));
        }
        
        $text = str_replace('[-institution-]', $institution, $text);
        $text = str_replace('[-shortname-]', $shortname, $text);
        return $text;
    }

    /**
    * Method to delete an introduction entry.
    *
    * @access public
    * @param string $id The row id of the entry
    */
    public function deleteIntro($id)
    {
        $this->delete('id', $id);
    }
    
    /**
    * Method to display the footer and store the string in session 
    *
    * @access public
    * @return string The footer content
    */
    public function showFooter()
    {
        $footer = $this->getSession('footerStr');
        
        if(isset($footer) && !empty($footer)){
            return $footer;
        }
        $footer = $this->getContent('footer');
        $footerStr = isset($footer['content_text']) ? $footer['content_text'] : '';
        $this->setSession('footerStr', $footerStr);
        return $footerStr;
    }
    
    /**
    * Method to display the faq
    *
    * @access public
    * @return string The faq content
    */
    public function showFaq()
    {
        $faq = $this->getContent('faq');
        $faqStr = isset($faq['content_text']) ? $faq['content_text'] : '';
        return $faqStr;
    }
}
?>