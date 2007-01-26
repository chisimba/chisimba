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
        $objConfig=&$this->newObject('altconfig','config');
        $siteRoot=$objConfig->getsiteRoot();
        //$siteRootPath = "http://".$_SERVER['HTTP_HOST']."/nextgen/";
        //$this->setSiteRootPath($siteRoot);
        $this->context = $context;
        $this->toolbarSet = 'advanced';
        
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
    * Method to display the WYSIWYG Editor
    */
    function show()
    {
        return $this->showFCKEditor();
    }
    
    /**
    * Method to show the FCKEditor
    * @return string
    */
    function showFCKEditor()
    {
		require_once($this->getResourceUri('fckeditor_2.3.2/fckeditor.php', 'htmlelements'));
        
        $objConfig = & $this->newObject('altconfig', 'config');

        $sitePath = pathinfo($_SERVER['PHP_SELF']);
        $sBasePath = $sitePath['dirname'];
		
		if (substr($sBasePath, -1, 1) != '/') {
			$sBasePath .= '/';
		}
		
		$sBasePath .= 'core_modules/htmlelements/resources/fckeditor_2.3.2/';
      
        $oFCKeditor = new FCKeditor($this->name) ;
        $oFCKeditor->BasePath = $sBasePath ;
        $oFCKeditor->Width= $this->width ;
		$oFCKeditor->Height=$this->height;
        $oFCKeditor->ToolbarSet=$this->toolbarSet;
        $oFCKeditor->SiteRoot=$objConfig->getsiteRoot();
        $oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/silver/' ;     
        $oFCKeditor->Config['CustomConfigurationsPath'] = $sBasePath . 'kngconfig.js'  ;
        
        if ($this->context) {
            $oFCKeditor->Context = 'Yes';
        } else {
            $oFCKeditor->Context = 'No';
        }
       
        $oFCKeditor->Value = $this->value;
        
        //$this->setVar('pageSuppressXML', TRUE);
        
        $this->showFCKEditorWakeupJS();
        
        return '<span onmouseover="wakeUpFireFoxFckeditor(\''.$this->name.'\');">'.$oFCKeditor->CreateHtml().'</span>';
    }
    
    /**
     * Method to load JS to fix FCKEditor refusing to focus
     * @author Tohir Solomons
     *
     * Taken from: http://www.tohir.co.za/2006/06/fckeditor-doesnt-want-to-focus-in.html
     */
    function showFCKEditorWakeupJS()
    {
        $this->appendArrayVar('headerParams', '
<script type="text/javascript">
    function wakeUpFireFoxFckeditor(fckEditorInstance)
    {
        try
        {
            var oEditor = FCKeditorAPI.GetInstance(fckEditorInstance);
            try
            {
                oEditor.MakeEditable();
            }
                catch (e) {}
            //oEditor.Focus();
        }
            catch (e) {}
    }
</script>');
    }
    
    /**
    * Method to show the tinyMCE Editor
    * @return string
    */
    function showTinyMCE()
    {      
    	$str = '';
    	$str =$this->getJavaScripts();
    	$str .='<form name="imgform"><input type="hidden" name="hiddentimg"/></form>';
    	$str .='<textarea id="'.$this->name.'" name="'.$this->name.'" rows="'.$this->rows.'" cols="'.$this->cols.'" style="width: 100%">'.$this->value.'</textarea>';
    	return   $str;
    }
    
    /**
    * Method to set the toolbar set to basic 
    * meaning that only the basic commands are available of the editor
    */
    function setBasicToolBar(){
        $this->toolbarSet = 'simple';
    }
    
    /**
    * Method to toolbar set to default 
    */
    function setDefaultToolBarSet(){
         $this->toolbarSet = 'advanced';
    }
    
    /**
    * Method to toolbar set to default without the save button
    */
    function setDefaultToolBarSetWithoutSave(){
         $this->toolbarSet = 'DefaultWithoutSave';
    }
    
    /**
    * Method to get the javascript files
    * @return string
    */
    public function getJavaScripts()
    {
    	$str = '
    			<script language="javascript" type="text/javascript" src="core_modules/htmlelements/resources/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    			
    			
				<script language="javascript" type="text/javascript">
				
					tinyMCE.init({
						mode : "textareas",
						theme : "'.$this->toolbarSet.'",
						plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable",
						theme_advanced_buttons1_add_before : "save,newdocument,separator",
						theme_advanced_buttons1_add : "fontselect,fontsizeselect",
						theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
						theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
						theme_advanced_buttons3_add_before : "tablecontrols,separator",
						theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
						theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_path_location : "bottom",
						content_css : "example_full.css",
					    plugin_insertdate_dateFormat : "%Y-%m-%d",
					    plugin_insertdate_timeFormat : "%H:%M:%S",
						extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
						external_link_list_url : "example_link_list.js",
						external_image_list_url : "example_image_list.js",
						flash_external_list_url : "example_flash_list.js",
						file_browser_callback : "fileBrowserCallBack",
						theme_advanced_resize_horizontal : false,
						theme_advanced_resizing : true
					});
				
					function fileBrowserCallBack(field_name, url, type, win) {
						// This is where you insert your custom filebrowser logic
						//alert("Example of filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);
						mywindow = window.open ("'.$this->uri(array('action' => 'showmedia'), 'mediamanager').'",  "imagewindow","location=1,status=1,scrollbars=0,  width=200,height=200");  mywindow.moveTo(0,0);
						
						//alert(mywindow.document.forms[0].hideme.value);
						// Insert new URL, this would normaly be done in a popup
						win.document.forms[0].elements[hide'.$this->name.'].value = "'.$this->uri(array('action' => 'list'), 'mediamanager').'";
					}
				</script>
					';
    	$this->appendArrayVar('headerParams', $str);
    	//return $str;
    }
 }

?>
