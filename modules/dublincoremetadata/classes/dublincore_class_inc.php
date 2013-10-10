<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * The class dublincoremetadata that manages
 * the Dublin Core Metadata
 * @package dublincoremetedata
 * @category context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie
 * @author Megan Watson
 */
class dublincore extends dbTable
{

    /**
    * @var array $elements;
    */
    var $elements;
    /**
    * @var  object $objLanguage
    */
    var $objLanguage;
     /**
     *@var object $objUser : The user Object
     */
     var $objUser;

     /**
     *@var object $objDBContext : The dbcontext Object
     */
     var $objDBContext;

     /**
    *The dispatch method that kick starts the module
    */
    function init()
    {
        parent::init('tbl_dublincoremetadata');
        $this->table = 'tbl_dublincoremetadata';
        $this->elements= array();
        $this->objUser=&$this->newObject('user','security');
        $this->objLanguage=& $this->getObject('language', 'language');
        $this->objDBContext = & $this->getObject('dbcontext', 'context');
    }

    /*
    *Method that sets the elements array
    *@param array $arrElements The array of elements
    *@return null
    *@access public
    */
    function setElements($arrElements)
    {
        foreach ($arrElements as $arr)
        {
            $this->elements[$arr] = $arrElements[$arr];
        }
    }

   /**
   *Method to show the metadata tags . This will most
   * often be show in the content when a page is loaded
   *@return string $str
   */
    function show($id=NULL){
        $str = '';
        if(isset($id)){
            $line = $this->getRow('id',$id);
            $str = '<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />';

            foreach($line as $arr)
            {
                $str .= '<meta name="DC.$arr" content="$line[$arr]" />\n';
            }
        }
        return $str;
    }

    /**
    * Method that generates metadata tags
    */
    function getMetadataTags($id)
    {
        $line = $this->getRow('id',$id);
        if(is_array($line)){
            $keys = array_keys($line);
            $str = '<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />';
            foreach($keys as  $arr)
            {
                if($arr=='id') continue;
                $str .= '
                <meta name="'.$arr.'"  content="'.$line[$arr].'" />';
                 // print $line[$arr] .'<br>';
            }
            return $str;
        }
    }

    /**
    * Method to get the input boxes
    */
    function getInputs($id=NULL, $mode=NULL)
    {
        //$this->loadClass('textinput','htmlelements');
        $textarea = & $this->newObject('textinput','htmlelements');
        $textarea->size = '40';
        $textarea->value = '';
        $textarea->setId(null);


        $table = & $this->newObject('htmltable','htmlelements');
        $table->width = '75%';

        $objButton=new button('save');
        $objButton->setToSubmit();
        $objButton->setValue($this->objLanguage->languageText("mod_contextadmin_save"));

        //title
        $title = $textarea;
        $title->name = 'title';
       // $title->setValue($arr['title']);
        $title->label = $this->objLanguage->languageText("word_title");

        //subject
        $subject = $textarea;
        $subject->name = 'subject';
        $subject->label = $this->objLanguage->languageText("mod_dublin_subject");

        //description
        $description = $textarea;
        $description->name = 'description';
        $description->label = $this->objLanguage->languageText("mod_dublin_description");

        //source
        $source = $textarea;
        $source->name = 'source';
        $source->label = $this->objLanguage->languageText("mod_dublin_source");

          //sourceURL
        $sourceurl = $textarea;
        $sourceurl->name = 'sourceurl';
        $sourceurl->label = $this->objLanguage->languageText("mod_dublin_sourceurl");


        //type
        $type = $textarea;
        $type->name = 'type';
        $type->label = $this->objLanguage->languageText("mod_dublin_type");

        //relationship
        $relationship = $textarea;
        $relationship->name = 'relationship';
        $relationship->label = $this->objLanguage->languageText("mod_dublin_relationship");

        //coverage
        $coverage = $textarea;
        $coverage->name = 'coverage';
        $coverage->label = $this->objLanguage->languageText("mod_dublin_coverage");

        //creator
        $creator = $textarea;
        $creator->name = 'creator';
        $creator->label = $this->objLanguage->languageText("mod_dublin_creator");

        //publisher
        $publisher = $textarea;
        $publisher->name = 'publisher';
        $publisher->label = $this->objLanguage->languageText("mod_dublin_publisher");

        //contributor
        $contributor = $textarea;
        $contributor->name = 'contributor';
        $contributor->label = $this->objLanguage->languageText("mod_dublin_contributor");

        //rights
        $rights = $textarea;
        $rights->name = 'rights';
        $rights->label = $this->objLanguage->languageText("mod_dublin_rights");

        //relationship
        $date = $textarea;
        $date->name = 'date';
        $date->label = $this->objLanguage->languageText("mod_dublin_date");

        //format
        $format = $textarea;
        $format->name = 'format';
        $format->label = $this->objLanguage->languageText("mod_dublin_format");

        //relationship
        $relationship = $textarea;
        $relationship->name = 'relationship';
        $relationship->label = $this->objLanguage->languageText("mod_dublin_relationship");

        //identifier
        $identifier = $textarea;
        $identifier->name = 'identifier';
        $identifier->label = $this->objLanguage->languageText("mod_dublin_identifier");

        //language
        $language = $textarea;
        $language->name = 'language';
        $language->label = $this->objLanguage->languageText("mod_dublin_language");

        //audience
        $audience = $textarea;
        $audience->name = 'audience';
        $audience->label = $this->objLanguage->languageText("mod_dublin_audience");

        if($mode=='edit' && isset($id)){
            $line = $this->getRow('id',$id);
            $title->value = $line['dc_title'];
            $subject->value = $line['dc_subject'];
            $description->value = $line['dc_description'];
            $source->value = $line['dc_source'];
            $sourceurl->value = $line['dc_sourceurl'];
            $type->value = $line['dc_type'];
            $relationship->value = $line['dc_relationship'];
            $coverage->value = $line['dc_coverage'];
            $creator->value = $line['dc_creator'];
            $publisher->value = $line['dc_publisher'];
            $rights->value = $line['dc_rights'];
            $date->value = $line['dc_date'];
            $format->value = $line['dc_format'];
            $identifier->value = $line['dc_identifier'];
            $language->value = $line['dc_language'];
            $audience->value = $line['dc_audience'];
        }

        $table->addRow(array($title->label,$title->show()),'odd');
        $table->addRow(array($subject->label,$subject->show()),'even');
        $table->addRow(array($description->label,$description->show()),'odd');
        $table->addRow(array($source->label,$source->show()),'even');
        $table->addRow(array($type->label,$type->show()),'odd');
        $table->addRow(array($relationship->label,$relationship->show()),'even');
        $table->addRow(array($coverage->label,$coverage->show()),'odd');
        $table->addRow(array($creator->label,$creator->show()),'even');
        $table->addRow(array($publisher->label,$publisher->show()),'odd');
        $table->addRow(array($rights->label,$rights->show()),'even');
        $table->addRow(array($date->label,$date->show()),'odd');
        $table->addRow(array($format->label,$format->show()),'even');
        $table->addRow(array($identifier->label,$identifier->show()),'odd');
        $table->addRow(array($language->label,$language->show()),'even');
        $table->addRow(array($audience->label,$audience->show()),'odd');
        $table->addRow(array($sourceurl->label,$sourceurl->show()),'even');
        return '<div id="dublincore">'.$table->show().'</div>';
    }

    /**
    * Method to insert the metadata into the database
    * @param string $nodeId The node id
    */
    function insertMetaData($id = NULL,$params = NULL){
        if(!isset($params))
        {
            $params = array(
                'dc_title' => $this->getParam('nodetitle'),
                'dc_subject' => $this->getParam('subject'),
                'dc_description' => $this->getParam('description'),
                'dc_source' => $this->getParam('source'),
                'dc_sourceurl' => $this->getParam('sourceurl'),
                'dc_type' => $this->getParam('type'),
                'dc_relationship' => $this->getParam('relationship'),
                'dc_coverage' => $this->getParam('coverage'),
                'dc_creator' =>$this->objUser->fullname(),
                'dc_publisher' => $this->getParam('publisher'),
                'dc_rights' => $this->getParam('rights'),
                'dc_date' => date("Y-m-d H:i:s"),
                'dc_format' => $this->getParam('format'),
                'dc_identifier' => $this->getParam('identifier'),
                'dc_language' => $this->getParam('language'),
                'dc_audience' => $this->getParam('audience')

            );
        }
       $params['dc_creator'] = $this->objUser->fullname();
       $params['dc_date'] = date("Y-m-d H:i:s");

         //update if the id exist
        if($this->valueExists('id', $id)){
           $lastId = $this->update('id', $id, $params);

        } else {
           // $params['id'] = $id;
            $lastId = $this->insert($params);

         }
        return $lastId;
    }

    /**
    * Method to initialize the elements
    */
    function initElements(){
        $arr = array('title', 'subject', 'description', 'source',  'sourceurl', 'type', 'relationship', 'coverage', 'creator', 'publisher', 'rights', 'date', 'format', 'identifier', 'language', 'audience');
        $this->setElements($arr);
    }

    /**
    * Method to insert metadata
    * @author Megan Watson
    * @param array $fields The fields to update
    * @param string $id The id of the metadata to update
    */
    function addMetaData($fields, $id = NULL)
    {
        if(isset($id) && !empty($id)){
            $fields['datestamp'] = $this->now();
            $fields['updated'] = $this->now();
            $this->update('id', $id, $fields);
        }else{
            $fields['enterdate'] = $this->now();
            $fields['datestamp'] = $this->now();
            $fields['updated'] = $this->now();
            $fields['deleted'] = 0;
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
    * Method to retrieve metadata
    * @author Megan Watson
    * @param string $id The id of the metadata to retrieve
    */
    function getMetaData($id)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE id = '$id'";

        $data = $this->getArray($sql);

        return $data;
    }

    /**
    * Method to delete an item's metadata
    * @author Megan Watson
    * @param string $id The id of the metadata to delete
    */
    function deleteMetaData($id)
    {
        $field = array();
        $field['deleted'] = 'true';

        $this->update('id', $id, $field);
    }
}
?>