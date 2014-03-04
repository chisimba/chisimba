<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Glossary Terms Table
* This class controls all functionality relating to the tbl_glossary table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package glossary
* @version 1
*/
/**
* This class returns arrays of recordset from the database table 'tbl_glossary'
* This table holds the term, definition and context for a term
*/
class dbGlossary extends dbTable
{

    /**
    * Constructor method to define the table(default)
    */
    public function init()
    {
        parent::init('tbl_glossary');
        $this->objLanguage =& $this->getObject('language', 'language');
    }

    /**
    * Method to fetch all terms in a context.
    * Takes a parameter call context.
    *
    * If no parameter is specified, returns terms for all contexts.
    *
    * This function also returns two additional values called 'urls' and 'seealsos'
    * If they have values, it means they are linked to other terms 'seealsos'
    * or have urls attached.
    *
    * The above was required for the parsing tool.
    *
    * @param string $context: ContextCode to filter records
    * @return array All Terms in the database for a particular context
    */
    public function fetchAllRecords($context=null)
    {
    	$sql = 'SELECT distinct tbl_glossary.id AS item_id, tbl_glossary.term, tbl_glossary.definition ';
        //$sql.= ', tbl_glossary_urls.item_id AS urls, bridge_glossary_seealso.item_id AS seealsos, tbl_glossary_images.item_id AS images ';
        $sql.= 'FROM tbl_glossary ';
        //$sql.= ' LEFT JOIN bridge_glossary_seealso ON ';
        //$sql.= '(tbl_glossary.id = bridge_glossary_seealso.item_id OR ';
        //$sql.= 'tbl_glossary.id = bridge_glossary_seealso.item_id2) ';
        //$sql.= 'LEFT JOIN tbl_glossary_urls ON (tbl_glossary.id = tbl_glossary_urls.item_id) ';
        //$sql.= 'LEFT  JOIN tbl_glossary_images ON ( tbl_glossary.id = tbl_glossary_images.item_id ) ';

        if ($context != '') {
            $sql.= "WHERE tbl_glossary.context = '".$context."' ";
        }

       // $sql.= 'GROUP BY tbl_glossary.id ';
        $sql.= 'ORDER BY tbl_glossary.term';

        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to search for terms in the glossary.
    *
    * This method is also used to list terms by letter.
    * eg. 'List by A' would list all the words starting by A
    *
    * Takes two parameters: search term and context.
    *
    * If no parameter is specified for context, returns terms for all contexts.
    * This function also returns two additional values called 'urls' and 'seealsos'
    * If they have values, it means they are linked to other terms 'seealsos'
    * or have urls attached.
    *
    * @param string $term : Term to search for
    * @param string $context: Contextcode to filter records
    * @return array Terms matching the search
    */
    public function searchGlossaryDB($term, $context=null)
    {

     	$sql = 'SELECT distinct tbl_glossary.id AS item_id, tbl_glossary.term, tbl_glossary.definition ';
        //$sql.= ', tbl_glossary_urls.item_id AS urls, bridge_glossary_seealso.item_id AS seealsos, tbl_glossary_images.item_id AS images ';
        $sql.= 'FROM tbl_glossary ';
        //$sql.= 'LEFT JOIN bridge_glossary_seealso ON ';
        //$sql.= '(tbl_glossary.id = bridge_glossary_seealso.item_id OR ';
        //$sql.= 'tbl_glossary.id = bridge_glossary_seealso.item_id2) ';
        //$sql.= 'LEFT JOIN tbl_glossary_urls ON (tbl_glossary.id = tbl_glossary_urls.item_id) ';
        //$sql.= 'LEFT  JOIN tbl_glossary_images ON ( tbl_glossary.id = tbl_glossary_images.item_id ) ';
        $sql.= "WHERE (tbl_glossary.term LIKE '$term' OR tbl_glossary.term LIKE '".strtolower($term)."'
                        OR tbl_glossary.term LIKE '".strtoupper($term)."') ";

        if ($context != '') {
            $sql.= "AND tbl_glossary.context='".$context."' ";
        }

        //$sql.= 'GROUP BY item_id ';
        $sql.= 'ORDER BY tbl_glossary.term';
        return $this->getArray($sql);
    }

    /**
    * Method to fetch a row from the database
    *
    * @param string $id: ID of the Record
    * @return array All Terms in the database for a particular context
    */
    public function listSingle($id)
    {
        return $this->getRow('id', $id);
    }

    /**
    * Method to list a single term showing whether it has SeeAlsos and Urls
    *
    * Context is required as a security parameter, to prevent URL hacking
    *
    * @param string $id: ID of the Record
    * @param string $context: Contextcode to filter records
    * @return array Term with indicators of whether the terms has urls, images or see alsos.
    */
    public function showFullSingle($id, $context)
    {
        $sql = 'SELECT distinct tbl_glossary.id AS item_id, tbl_glossary.term, tbl_glossary.definition, ';
        $sql.= 'tbl_glossary_urls.item_id AS urls, bridge_glossary_seealso.item_id AS seealsos, tbl_glossary_images.item_id AS images ';
        $sql.= 'FROM tbl_glossary LEFT JOIN bridge_glossary_seealso ON ';
        $sql.= '(tbl_glossary.id = bridge_glossary_seealso.item_id OR ';
        $sql.= 'tbl_glossary.id = bridge_glossary_seealso.item_id2) ';
        $sql.= 'LEFT JOIN tbl_glossary_urls ON (tbl_glossary.id = tbl_glossary_urls.item_id) ';
        $sql.= 'LEFT  JOIN tbl_glossary_images ON ( tbl_glossary.id = tbl_glossary_images.item_id ) ';
        $sql.= "WHERE tbl_glossary.id = '".$id."' ";
        $sql.= "AND tbl_glossary.context='".$context."' ";
        //$sql.= 'GROUP BY tbl_glossary.id ';
        $sql.= 'ORDER BY tbl_glossary.term';

        return $this->getArray($sql);
    }

    /**
    * Method to insert a new record
    *
    * @param string $term:            Term
    * @param string $definition:      Definition
    * @param string $context:         ContextCode
    * @param string $userID:          UserId of person inserting the record
    * @param datetine $dateLastUpdated: Date/Time of entry
    */
    public function insertSingle($term, $definition, $context, $userID, $dateLastUpdated)
    {
        $date = strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated);
        $glossId = $this->insert(array(
                'term'            => $term,
                'definition'      => $definition,
                'context'         => $context,
                'userid'          => $userID,
                'datelastupdated' => $date
            ));
         $indexData = $this->getObject('indexdata','search');
         $docId = "glossary_entry_$glossId";
         $url = $this->uri(array('action'=>'search','term'=>$term),'glossary');
         $title = $this->objLanguage->languageText('mod_glossary_teaser','glossary')." $term";
         $contents = "$term $definition";
         $teaser = $definition;
         $indexData->luceneIndex($docId, $date, $url, $title, $contents, $teaser, "glossary", $userID, null, $context);

        return;
    }

    /**
    * Method to delete a record
    *
    * @param string $id: Id of the record
    */
    public function deleteSingle($id)
    {
        $this->delete('id', $id);
        $indexData = $this->getObject('indexdata','search');
        $docId = "glossary_entry_$id";
        $indexData->removeIndex($docId);
        return;
    }

    /**
    * Method to update a record
    *
    * @param string $id:              Id
    * @param string $term:            Term
    * @param string $definition:      Definition
    * @param string $context:         ContextCode
    * @param string $userID:          UserId of person inserting the record
    * @param datetine $dateLastUpdated: Date/Time of entry
    */
    public function updateSingle($id, $term, $definition, $context, $userID, $dateLastUpdated)
    {
        $date = strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated);
        $this->update('id', $id, array(
                'term'            => $term,
                'definition'      => $definition,
                'context'         => $context,
                'userid'          => $userID,
                'datelastupdated' => $date
            ));
         $indexData = $this->getObject('indexdata','search');
         $docId = "glossary_entry_$id";
         $url = $this->uri(array('action'=>'search','term'=>$term),'glossary');
         $title = $term;
         $contents = "$term $definition";
         $teaser = $this->objLanguage->languageText('mod_glossary_teaser','glossary')." $term";
         $indexData->luceneIndex($docId, $date, $url, $title, $contents, $teaser, "glossary", $userID, null, $context);

        return;
    }

    /**
    * Method to determine whether a record exists
    * Uses a record count to determine this
    *
    * @param string $id
    * @return boolean true or false
    */
    public function recordExists($id)
    {
        return $this->getRecordCount(" WHERE id='".$id."'");
    }

    /**
    * Method to get the number of terms in a context
    * Used for the parses. If no records, dont bother to parse
    *
    * Alternatively, get number of all records if no context is specified
    *
    * @param string $context: Context Code to filter records
    * @return int number of records matching
    */
    public function getNumAllRecords($context=null)
    {
        if ($context != '') {
            $sql = " WHERE tbl_glossary.context='".$context."' ";
        } else {
            $sql = '';
        }

        return $this->getRecordCount($sql);
    }


    /**
    * Method to parse text and replace terms with the mouse over popup
    *
    * Takes two parameters:
    * @param string $text: Text that has to be parsed
    * @param string $context: Context Code of the Context. Only use words of a particular context.
    *
    * @return string $text text with glossary terms replaced by mouse over popups
    */
    public function parse($text, $context)
    {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        // Check if Glossary Parsing is allowed
        $allowParse = $objSysConfig->getValue('ALLOW_PARSE', 'glossary');

        // If not, immediately return text, no further action is required.
        if ($allowParse == '0') {
            return $text;
        }

        // First get a list of terms in the context
        // If there are terms, start parsing, otherwise stop altogether
        if ($this->getNumAllRecords($context) > 0) {
            $getList = $this->fetchAllRecords($context);

            // Send JavaScript to the Header
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('domLib.js','htmlelements'));
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('domTT.js','htmlelements'));
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('domTT_drag.js','htmlelements'));

            // Create JavaScript Style
            $iframeStyle= '
<style type="text/css">
.iframepopup {
    width: 98%;
    height: 200px;
}
</style>';
            // Send to Header
            $this->appendArrayVar('headerParams', $iframeStyle);



            // Need to strip slashes if it comes with slashes
            $text = stripslashes($text);

            $objIcon = $this->getObject('geticon', 'htmlelements');
            $objIcon->setIcon('glossary_link');
            $objIcon->title = ' ';
            $objIcon->alt = ' ';
            $objIcon->extra = ' style="cursor:pointer;" ';

            // Tag to Use to Surround Item
            $tag = 'span';

            // Parse One Word at a time.
            foreach ($getList as $term)
            {

                // ********** START BUILDING OF REPLACEMENT ********** //
                $starttag =  '<'.$tag.' ';
                $endtag = '</'.$tag.'>';
                $onclick = '';
                $onmouseout = '';
                $onmouseover = '';
                $cursor = 'help';
                $icon = NULL;


                // Title
                $title = $term['term']; // Convert to Capital Letters

                // If the term has URLS or relates to other terms or has images
                // Add them to the output
                if ($term['urls'] != '' OR $term['seealsos'] != '' OR $term['images'] != '')
                {
                    $popupUrl = $this->uri(array('module'=>'glossary', 'action'=>'singlepopup', 'id'=>$term['item_id']));

                    $onclick = ' onclick="return makeFalse(domTT_activate(this, event, \'caption\', \''.$title.'\', \'content\', \'<iframe class=iframepopup frameborder=0 src='.$popupUrl.' ></iframe>\', \'type\', \'sticky\', \'classPrefix\', \'domTT\', \'closeLink\'));" ';
                    //style=\"width: 98%; height: 200px;\"
                    $cursor = 'pointer';

                    $icon = $objIcon->show();

                } // End if Term has urls or see alsos

                $onmouseout = ' onmouseout="domTT_mouseout(this, event);"';
                $onmouseover = ' onmouseover="domTT_activate(this, event, '."'".'content'."'".', ';

                $definition = nl2br(htmlentities($term['definition']));
                $definition = str_replace("\n", ' ', $definition);
                $definition = str_replace("\r", ' ', $definition);
                $definition = str_replace("'", "\'", $definition);

                $onmouseover .= "'".$definition."' ";
                $onmouseover .= ", 'trail', true, 'fade', 'in', 'maxWidth', '500');\" ";

                $style = 'style="border-bottom: 1px dotted red; cursor:'.$cursor.'" class="glossaryparse"';

                $matchReplacement = $starttag.$onclick.$onmouseout.$onmouseover.$style.'>'."\\0".$icon.$endtag;

                // ********** END BUILDING OF REPLACEMENT ********** //

                // Word to search for - Allow Spaces to be one or more
                $lookfor = str_replace(' ', '\s+', $term['term']);

                // regexp pattern - case insensitive
                $pattern = '#(?!<.*?)(?!<a)(\\b'.$lookfor.'\\b)(?!<\/a>)(?![^<>]*?>)#i';

                // Replace the Text
                $text = preg_replace($pattern, $matchReplacement, $text );

            } // End foreach keyword

        }// End of If Num Records > 0

        return $text;

    }

    /**
    * Method to Generate an Alphabetical listing to Browse the Glossary
    * It only activates a link, if there are words starting with that letter
    * @param string $contextCode Context words are coming from
    */
    public function getGlossaryAlphaBrowse($contextCode)
    {
        // Get the list of terms in the array
        $wordsList = $this->fetchAllRecords($contextCode);

        // Separate Array for Letters
        $alphaArray = array();

        if(!empty($wordsList)){
        		// Get the First Letter of each word
        		foreach ($wordsList as $word)
        		{
            		$alphaArray[] = strtoupper(substr($word['term'], 0, 1));
        		}
        }

        // Make elements in array unique
        $result = array_unique($alphaArray);

        // String for Final Output
        $output = '';

        // Loop from A to Z
        for($i = 65; $i <= 90; $i++)
        {
            // Convert to Character
            $letter = chr($i);

            // If Letter in Array, Add Link
            if (in_array($letter, $alphaArray)) {
                $url=$this->uri(array('action'=>'viewbyletter','letter'=>$letter),'glossary');
                $output .= '<a href="'.$url.'">'.$letter.'</a> | ';
            } else { // Else just add letter
                $output .= $letter.' | ';
            }

        }

        // Text: List All Words
        $listAllName = $this->objLanguage->languageText('mod_glossary_listAllWords', 'glossary');

        // Add List all words to string
        $output.=' <a href="'.$this->uri(array('action'=>'viewbyletter','letter'=>'listall'),'glossary').'">'.$listAllName.'</a>';

        return $output;
    }



}  #end of class

?>
