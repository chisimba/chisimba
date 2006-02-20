<?php
$objConfig =& $this->getObject('config', 'config');
$siteRoot = $objConfig->siteRoot();
?>
<script type="text/javascript">
__KNG__ = '<?php echo $siteRoot; ?>';
</script>
<?php

/**
* textare class to use to make textarea inputs.
* 
* @package htmlTextarea
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version $Id: 
* @author Wesley Nitsckie 
* @example 
* @todo -c HTML Editor that will extend this object
*/
//require_once("htmlbase_class_inc.php");
include("modules/htmlelements/resources/fckeditor/fckeditor.php") ;
class htmlarea extends object
 {
     /**
    * 
    * @var string $siteRootPath: The path to the site
    */
    var $siteRootPath;
     /**
    * 
    * @var string $cols: The number of columns the textare will have
    */
    var $cols;
    /**
    * 
    * @var string $rows: The number of rows the textare will have
    */
    var $rows;
    
    /**
    * 
    * @var string $label: The label of the editor
    */
    var $label;
    
    /**
    * 
    * @var string $cssClass: The style sheet class
    */
    var $cssClass;
    
    /**
    * 
    * @var string $height: The height of the editor
    */
    var $height;
    
    /**
    * 
    * @var string $width: The width of the editor 
    */
    var $width;
    /**
    * 
    * @var string $toolbarSet: The toolbarSet of the editor : either Default or Basic
    */
    var $toolbarSet;

    /**
    * @var boolean $context Are we in a context aware mode.
    */
    var $context;
    
    /**
    * Method to establish the default values
    */
    function init($name=null,$value=null,$rows=4,$cols=50,$context=false)
     {
        $this->height = '400px';
        $this->width = '600px';
        $this->toolbarSet='Default';
        $this->name=$name;
        $this->value=$value;
        $this->rows=$rows;
        $this->cols=$cols;
        $this->css='textarea';
        //$this->_objConfig =& $this->getObject('config', 'config');
        //$siteRootPath = $this->_objConfig->siteRootPath();
        $objConfig=&$this->newObject('config','config');
        $siteRoot=$objConfig->siteRoot();
        //$siteRootPath = "http://".$_SERVER['HTTP_HOST']."/nextgen/";
        //$this->setSiteRootPath($siteRoot);
        $this->context = $context;
    }

    /**
    * function to set the root path
    * 
    * @var string $siteRootPath: The site path
    */
    function setSiteRootPath($siteRootPath)
    {
        $this->siteRootPath = $siteRootPath;
    }
    
    /**
    * function to set the value of one of the properties of this class
    * 
    * @var string $name: The name of the textare
    */
    function setName($name)
    {
        $this->name=$name;
    }
    /**
    * function to set the amount of rows 
    * @var string $Rows: The number of rows of the textare
    * 
    */
    function setRows($rows)
    {
        $this->rows=$rows;
    }
    /**
    * function to set the amount of cols 
    * @var string $cols: The number of cols of the textare
    * 
    */
    function setColumns($cols)
    {
        $this->cols=$cols;
    }
    
    /**
    * function to set the content
    * @var string $content: The content of the textare
    */
    function setContent($value=null)
    {
        $this->value=$value;
    }
     /**
    * Method to show the textarea
    * @return string The formatted link
    * @deprecated
    */
    function show_OLD()
    {
        $str = '<script type="text/javascript">';
           $str .= '_editor_url = "'.$this->siteRootPath.'modules/htmlelements/resources/htmlarea/";';
           $str .= '_editor_lang = "en";';
        $str .= '_site_root_path = "'.$this->siteRootPath.'";';
        $str .= '</script>';
        $str .= '<script type="text/javascript" src="'.$this->siteRootPath.'modules/htmlelements/resources/htmlarea/htmlarea.js"></script>';
        //$str .= '<script type="text/javascript" defer="1">';
        //$str .= 'HTMLArea.replace("'.$this->name.'");';
        //$str .= '</script>';
        $str .= '<textarea name="'.$this->name.'"';
        $str .= ' id="'.$this->name.'"';
        if($this->cssClass){
            $str.=' class="'.$this->cssClass.'">';
        }
        /*
        * if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
        }
        if ($this->extra) {
            $str .= ' id="' . $this->extra . '"';
        }
        */
        if($this->rows){
            $str.=' rows="'.$this->rows.'"';
        }
        if($this->cols){
            $str.=' cols="'.$this->cols.'"';
        }
        $str.='>';
        $str.=$this->value;
        $str.='</textarea>';
        
        return $str;
    }
    
    
    /*****
    ** NEW EDITOR
    ****/
    
    function show(){      
        $objConfig = & $this->newObject('config', 'config');        
        $sBasePath = $objConfig->siteRoot().'modules/htmlelements/resources/fckeditor/';
        global $Config;
        $Config['UserFilesPath'] = $objConfig->contentBasePath();
        $oFCKeditor = new FCKeditor($this->name, $objConfig->siteRoot(), $this->context?'Yes':'No') ;
        $oFCKeditor->BasePath = $sBasePath ;
        $oFCKeditor->Width= $this->width ;
		$oFCKeditor->Height=$this->height;
        $oFCKeditor->ToolbarSet=$this->toolbarSet;
        $oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/silver/' ;     
        $oFCKeditor->Config['CustomConfigurationsPath'] = $sBasePath . 'kngconfig.js'  ;
        $oFCKeditor->Value = $this->value;
        return $oFCKeditor->Create() ;
            
    }
    
    /**
    * Method to set the toolbar set to basic 
    * meaning that only the basic commands are available of the editor
    */
    function setBasicToolBar(){
        $this->toolbarSet = 'Basic';
    }
    
    /**
    * Method to toolbar set to default 
    */
    function setDefaultToolBarSet(){
         $this->toolbarSet = 'Default';
    }
    
    /**
    * Method to toolbar set to default without the save button
    */
    function setDefaultToolBarSetWithoutSave(){
         $this->toolbarSet = 'DefaultWithoutSave';
    }
 }

?>