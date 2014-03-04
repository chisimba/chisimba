<?php
//Path to resizeable side square image
$sqrImgPath = "core_modules/ext/resources/ext-3.0-rc2/resources/images/default/sizer/square.gif";
$style = '
<style type="text/css">

#learningcontentmenu ul  {list-style-type:none; margin-left: -21px;}

#learningcontentmenu li a {
   color: #4a4949;
   background-color: #e1e1e1;
   width: 250px;
   display: block;
   padding: 3px 8px;
   margin-bottom: 3px;
   }
   
#learningcontentmenu li a:hover {
   color: #fff;
   background-color: #4d4d4d;
   width: 250px;
   display: block;
   }
</style>
';

//Ext stuff
//Common Javascript for the example
$extjs_example = '<script language="JavaScript" src="'.$this->getResourceUri('example.js','learningcontent').'" type="text/javascript"></script>';
//Common Styles for the example
$ext_example = '<link rel="stylesheet" href="'.$this->getResourceUri('example.css', 'learningcontent').'" type="text/css" />';
$this->appendArrayVar('headerParams', $ext_example);
$this->appendArrayVar('bodyParams', $extjs_example);
$objExtJs = $this->getObject('extjs', 'ext');
$objExtJs->show();

//$ext .= $this->getJavaScriptFile('resizeme.js', 'learningcontent');

//Append stylesheet
$ext = Null;
$ext .= $style;
$this->appendArrayVar('headerParams', $ext);

$objFile = $this->getObject('dbfile', 'filemanager');
$objHead = $this->newObject('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$this->loadClass("htmltable", 'htmlelements');

//Add link back to the chapter list on the middle links

$middle = '';
$prvpage = $this->objContentOrder->getPrevPageId($this->contextCode, $currentChapter, $pagelft);
//Check if first page in the chapter
if($prvpage!=Null){
$prevLeftValue = $pagelft-2;
$nextpage = $this->objContentOrder->getNextPageId($this->contextCode, $currentChapter, $prevLeftValue);

$link = new link ($this->uri(array("action"=>"showcontextchapters","chapterid"=>$currentChapter, 'prevpageid'=>$nextpage), $module));
$link->link = '&#171; '.$this->objLanguage->languageText('mod_learningcontent_backchapter','learningcontent');
$middle .= $link->show();

$middle .= ' <br /> ';
}
//A link to adding a page    
$addLink = new link ($this->uri(array('action'=>'addpage', 'id'=>$page['id'], 'context'=>$this->contextCode, 'chapter'=>$page['chapterid'])));
$addLink->link = $this->objLanguage->languageText('mod_learningcontent_addcontextpages','learningcontent');

$addScormLink = new link ($this->uri(array('action'=>'addscormpage', 'id'=>$page['id'], 'context'=>$this->contextCode, 'chapter'=>$page['chapterid'])));
$addScormLink->link = $this->objLanguage->languageText('mod_learningcontent_addcontextscormpages','learningcontent');

//A link to editing a page
$editLink = new link ($this->uri(array('action'=>'editpage', 'id'=>$page['id'], 'context'=>$this->contextCode)));
$editLink->link = $this->objLanguage->languageText('mod_learningcontent_editcontextpages','learningcontent');

if (($page['rght'] - $page['lft'] - 1) == 0) {
    $deleteLink = new link ($this->uri(array('action'=>'deletepage', 'id'=>$page['id'], 'context'=>$this->contextCode)));
} else {
    $deleteLink = new link ("javascript:alert('".$this->objLanguage->languageText('mod_learningcontent_pagecannotbedeleteduntil','learningcontent').".');");
}
$deleteLink->link = $this->objLanguage->languageText('mod_learningcontent_delcontextpages','learningcontent');

$list = array();

if ($this->isValid('addpage')) {
    $list[] = $addLink->show();
    $list[] = $addScormLink->show();
}

if ($this->isValid('editpage')) {
    $list[] = $editLink->show();
}

if ($this->isValid('deletepage')) {
    $list[] = $deleteLink->show();
}

if (count($list) == 0) {
    $middle .= '&nbsp;';
} else {
    $middle .= '';
    $divider = '';
    
    foreach ($list as $item)
    {
        $middle .= $divider.$item;
        $divider = ' / ';
    }
}

if ($this->isValid('movepageup')) {
    
    $middle .= '<br />';
    
    if ($isFirstPageOnLevel) {
        $middle .= '<span style="color:grey;" title="'.$this->objLanguage->languageText('mod_learningcontent_isfirstpageonlevel','learningcontent').'">'.$this->objLanguage->languageText('mod_learningcontent_movepageup','learningcontent').'</span>';
    } else {
        $link = new link($this->uri(array('action'=>'movepageup', 'id'=>$page['id'])));
        $link->link = $this->objLanguage->languageText('mod_learningcontent_movepageup','learningcontent');
        $middle .= $link->show();
    }
    
    $middle .= ' / ';
    
    if ($isLastPageOnLevel) {
        $middle .= '<span style="color:grey;" title="'.$this->objLanguage->languageText('mod_learningcontent_islastpageonlevel', 'learningcontent').'">'.$this->objLanguage->languageText('mod_learningcontent_movepagedown','learningcontent').'</span>';
    } else {
        $link = new link($this->uri(array('action'=>'movepagedown', 'id'=>$page['id'])));
        $link->link = $this->objLanguage->languageText('mod_learningcontent_movepagedown','learningcontent');
        $middle .= $link->show();
    }
}

$table = $this->newObject('htmltable', 'htmlelements');
//$table->border='1';
$table->startRow();
$table->addCell($prevPage, '33%', 'top');
$table->addCell($middle, '33%', 'top', 'center');
$table->addCell($nextPage, '33%', 'top', 'right');
$table->endRow();

$table2 = $this->newObject('htmltable', 'htmlelements');
//$table->border='1';
$table2->startRow();
$table2->addCell($prevPage, '33%', 'top');
$table2->addCell('&nbsp;', '33%', 'top', 'center');
$table2->addCell($nextPage, '33%', 'top', 'right');
$table2->endRow();

$topTable = $this->newObject('htmltable', 'htmlelements');
//$topTable->border='1';
$topTable->startRow();
$topTable->addCell($prevPage, '50%', 'top');
$topTable->addCell($nextPage, '50%', 'top', 'right');
$topTable->endRow();


$this->loadClass('link', 'htmlelements');

$this->setVar('pageTitle', htmlentities($this->objContext->getTitle().' - '.$page['menutitle']));

if (trim($page['headerscripts']) != '') {
    
    // Explode into array
    $scripts = explode(',', $page['headerscripts']);
    
    // Loop through array
    foreach ($scripts as $script)
    {
        // Check if valid
        if (trim($script) != '') {
            
            // Get Path
            $fileInfo = $objFile->getFilePath($script);
            
            // If Valid
            if ($fileInfo != FALSE) {
                
                // Check if Script or CSS, and display
                if (substr($fileInfo, -2, 2) == 'js') {
                    $this->appendArrayVar('headerParams', '<script type="text/javascript" src="'.$fileInfo.'"></script>');
                } else {
                    $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$fileInfo.'"');
                }
            }
        }
    }
    
    //$this->appendArrayVar('headerParams', $page['headerscripts']);


}


$objWashout = $this->getObject('washout', 'utilities');

$content = $objWashout->parseText($page['pagecontent']);
//Table to hold content pictures and formula in one row but separate columns
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='center' border='0'";
$objTable->cellspacing = '12';
$objTable->startRow();
$objTable->addCell($content, '50%', 'top', 'left');

//Get the browser in use
$browsers = array("firefox", "msie", "opera", "chrome", "safari",
                    "mozilla", "seamonkey",    "konqueror", "netscape",
                    "gecko", "navigator", "mosaic", "lynx", "amaya",
                    "omniweb", "avant", "camino", "flock", "aol");

$this->Agent = strtolower($_SERVER['HTTP_USER_AGENT']);
foreach($browsers as $browser)
{
    if (preg_match("#($browser)[/ ]?([0-9.]*)#", $this->Agent, $match))
    {
        $Name = $match[1] ;
        $Version = $match[2] ;
        break ;
    }
} 
//Get the name of each pagepicture
$pagepicture = $page['pagepicture'];
if(!empty($pagepicture)){
 $hpictures = explode(',',$page['pagepicture']);
 $hpics = "<div id='learningcontentmenu'><ul>";
 foreach($hpictures as $hpicture){
  if(!empty($hpicture)){
   $picname = $this->objFiles->getFileName($hpicture);
   $pictype = $this->objFiles->getType($hpicture);
   $picid = $hpicture;
   $picdesc = $this->objFiles->getFileInfo($picid);
   if(empty($picdesc['filedescription'])){
    $picdesc = $picname;
   }else{
    $picdesc = $picdesc['filedescription'];    
   }

   $picid = $hpicture;

   $objIcon->setIcon($pictype, $type = 'gif', $iconfolder='icons/filetypes/');
   $objIcon->alt = $picdesc;
   $objIcon->title = $this->objLanguage->languageText('mod_learningcontent_clicktoview','learningcontent');
   $picdesc = $objIcon->show()." ".$picdesc;
//   if($Name=='firefox'){
   if($Name){
    $imageDesc = $this->objFiles->getFileInfo($picid);
    $imgPath = "usrfiles/";
    $resize = $this->objDynamicBlocks->createResizeableImageCSS($picid, $sqrImgPath);
    $resize .= $this->objDynamicBlocks->createResizeableImageJS($picid,'viewPicture');
    $this->appendArrayVar('headerParams', $resize);
    $filePath = $imgPath.$imageDesc['path'];
    $fileName = $imageDesc['filename'];
    $customImgStr = '<img id="custom'.$picid.'" src="'.$filePath.'" style="position:absolute;left:0;top:0;"/>
<div style="padding:8px;border:1px solid #ffffff;background:#ffffff;width:150px;text-align:center;"><button id="showMe'.$picid.'">'.$picdesc.'</button></div>';
    $picViewLink = new link ($this->uri(array('action'=>'viewpageimage', 'id'=>$page['id'], 'imageId'=>$picid)));
    $picViewLink->link = $picdesc;
    //$hpics .= "<li>".$picViewLink->show()."</li>";
    $hpics .= "<li>".$customImgStr."</li>";
   }else{
    $link = $this->uri(array('action' => 'imagewindowpopup', 'imageId' => $picid));
    // Load the window popup class
    $objPop = $this->newObject('windowpop', 'htmlelements');
    $objPop->set('location', $link);
    $objPop->set('linktext', $picdesc);
    $objPop->set('window_name','picture');
    $objPop->set('width','1024');
    $objPop->set('height','768');
    $objPop->set('left','100');
    $objPop->set('top','100');
    $objPop->set('resizable','yes');
    $objPop->set('scrollbars','yes');   
    $hpics .= "<li>".$objPop->show()."</li>";
   }
  }
 }
  $hpics .= "</ul></div>";
  if(!empty($hpics)){
   $objPHead = $this->newObject('htmlheading', 'htmlelements');
   $wordPicture = $this->objLanguage->languageText('mod_learningcontent_picture','learningcontent');
   $objPHead->type = 2;
   $objPHead->str = $wordPicture;  
   $hpics = $objPHead->show()."<p>".$hpics."</p>";
   $objTable->addCell($hpics, '25%', 'top', 'left');
  }
}
//Get the formula id if any
$pageformula = $page['pageformula'];
//Get the name of each headerscripts
if(!empty($pageformula)){
 $hformulas = explode(',',$page['pageformula']);
 $hformula = "<div id='learningcontentmenu'><ul>";
 foreach($hformulas as $fmla){
  if(!empty($fmla)){
   $fmlaname = $this->objFiles->getFileName($fmla);
   $fmlatype = $this->objFiles->getType($fmla);
   $fmlaid = $fmla;
   $fmladesc = $this->objFiles->getFileInfo($fmlaid);
   if(empty($fmladesc['filedescription'])){
    $fmladesc = $fmlaname;
   }else{
    $fmladesc = $fmladesc['filedescription'];
   }
   $objIcon->setIcon($fmlatype, $type = 'gif', $iconfolder='icons/filetypes/');
   $objIcon->alt = $this->objLanguage->languageText('mod_learningcontent_clicktoview','learningcontent');
   $objIcon->title = $fmladesc;
   $fmladesc = $objIcon->show()." ".$fmladesc;
   
//   if($Name=='firefox'){
   if($Name){
    $imageDesc = $this->objFiles->getFileInfo($fmlaid);
    $imgPath = "usrfiles/";
    $filePath = $imgPath.$imageDesc['path'];
    $fileName = $imageDesc['filename'];
    $resize = $this->objDynamicBlocks->createResizeableImageCSS($fmlaid, $sqrImgPath);
    $resize .= $this->objDynamicBlocks->createResizeableImageJS($fmlaid,'viewFormula');
    $this->appendArrayVar('headerParams', $resize);
    $customImgStr = '<img id="custom'.$fmlaid.'" src="'.$filePath.'" style="position:absolute;left:0;top:0;"/>
<div style="padding:8px;border:1px solid #ffffff;background:#ffffff;text-align:center;"><button id="showMe'.$fmlaid.'">'.$fmladesc.'</button></div>';
    $fmlaViewLink = new link ($this->uri(array('action'=>'viewpageimage', 'id'=>$page['id'], 'imageId'=>$fmlaid)));
    $fmlaViewLink->link = $fmladesc;
    //$hformula .= "<li>".$fmlaViewLink->show()."</li>";
    $hformula .= "<li>".$customImgStr."</li>";
   }else{
    $fmlalink = $this->uri(array('action' => 'imagewindowpopup', 'imageId' => $fmlaid));
    //Load the window popup class
    $objPop = $this->newObject('windowpop', 'htmlelements');
    $objPop->set('location', $fmlalink);
    $objPop->set('linktext', $fmladesc);
    $objPop->set('window_name','formula');
    $objPop->set('width','1024');
    $objPop->set('height','768');
    $objPop->set('left','100');
    $objPop->set('top','100');
    $objPop->set('resizable','yes');
    $objPop->set('scrollbars','yes');
    $hformula .= "<li>".$objPop->show()."</li>";
   }
  }  
 }
 $hformula .= "</ol></div>";
 if(!empty($hformula)){
  $objFHead = $this->newObject('htmlheading', 'htmlelements');
  $wordFormula = $this->objLanguage->languageText('mod_learningcontent_formula','learningcontent');
  $objFHead->type = 2;
  $objFHead->str = $wordFormula; 
  $hformula = $objFHead->show()."<p>".$hformula."</p>";
  $objTable->addCell($hformula, '25%', 'top', 'left');
 }
}

//$content = $content.$hpics.$hformula;
$content = $objTable->show();

$form = "";

if (count($chapters) > 1 && $this->isValid('movetochapter')) {
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('dropdown', 'htmlelements');
    $this->loadClass('hiddeninput', 'htmlelements');
    $this->loadClass('button', 'htmlelements');
    $this->loadClass('label', 'htmlelements');

    $form = new form ('movetochapter', $this->uri(array('action'=>'movetochapter')));
    $hiddenInput = new hiddeninput('id', $page['id']);

    $dropdown = new dropdown('chapter');
    foreach ($chapters as $chapterItem)
    {
        $dropdown->addOption($chapterItem['chapterid'], $chapterItem['chaptertitle']);
    }
    $dropdown->setSelected($page['chapterid']);
    
    $label = new label ($this->objLanguage->languageText('mod_learningcontent_movepagetoanotherchapter','learningcontent').': ', 'input_chapter');
    
    $button = new button ('movepage', $this->objLanguage->languageText('mod_learningcontent_move','learningcontent'));
    $button->setToSubmit();
    
    $form->addToForm($hiddenInput->show().$label->show().$dropdown->show().' '.$button->show());
    
    $form = $form->show();
    
}

if ($this->isValid('addpage')) {
  $objTabs = $this->newObject('tabcontent', 'htmlelements');
  $objTabs->setWidth('98%');
  $objTabs->addTab("Lecturer View",$topTable->show().$content.'<hr />'.$table->show().$form);
  $objTabs->addTab("Student View",$topTable->show().$content.'<hr />'.$table2->show());
  echo $objTabs->show();
}
else {
  echo $topTable->show().$content.'<hr />'.$table->show().$form;
}

//Check if comments are allowed for this course
$showcomment = $this->objContext->getField('showcomment', $contextCode = NULL);
if($showcomment==1)
{
	$head = $this->objLanguage->languageText('mod_learningcontent_word_comment','learningcontent');
	$objHead->type = 1;
	$objHead->str = $head;
	echo '<br/>'.$objHead->show().'<br/>';

	$commentpost = $this->objContextComments->getPageComments($currentPage);
	if (count($commentpost) < 1)
	{
		
		echo $this->objLanguage->languageText('mod_learningcontent_nocomment','learningcontent').'<br/>';
	}
	else{
		$cnt = 0;
		$oddcolor = $this->objSysConfig->getValue('learningcontent_ODD', 'learningcontent');
		$evencolor = $this->objSysConfig->getValue('learningcontent_EVEN', 'learningcontent');

		foreach($commentpost as $comment)
		{
			$objOutput = '<strong>'.$this->objUser->fullname($comment['userid']).'</strong><br/>';
			$objOutput .= '<i>'.$comment['datecreated'].'</i><br/>';
			$objOutput .= $comment['comment'];

			if($cnt%2 == 0)
				{
					echo '<div class="colorbox '.$evencolor.'box">'.$objOutput.'</div>';
				}
			else
				{
					echo '<div class="colorbox '.$oddcolor.'box">'.$objOutput.'</div>';
				}
			$cnt++;
		}
	}
	$this->loadClass('textarea', 'htmlelements');
	$cform = new form('learningcontent', $this->uri(array('action' => 'addcomment', 'pageid' => $currentPage)));
	
	//start a fieldset
	$cfieldset = $this->getObject('fieldset', 'htmlelements');
	$ct = $this->newObject('htmltable', 'htmlelements');
	$ct->cellpadding = 5;

	//Text
	$ct->startRow();
	$ctvlabel = new label($this->objLanguage->languageText('mod_learningcontent_writecomment', 'learningcontent').':','input_cvalue');
	$ct->addCell($ctvlabel->show());
	$ct->endRow();

	//Textarea
	$ct->startRow();
	$ctv = new textarea('comment', '', 8, 70);
	$ct->addCell($ctv->show());
	$ct->endRow();
	//end off the form and add the button
	$this->objconvButton = new button($this->objLanguage->languageText('mod_learningcontent_submitcomment', 'learningcontent'));
	$this->objconvButton->setValue($this->objLanguage->languageText('mod_learningcontent_submitcomment', 'learningcontent'));
	$this->objconvButton->setToSubmit();
	$cfieldset->addContent($ct->show());
	$cform->addToForm($cfieldset->show());
	$cform->addToForm($this->objconvButton->show());
	echo '<br/>'.$cform->show();
}

if (!empty($imageId)) {
    $alertBox = $this->getObject('alertbox', 'learningcontent');
    $alertBox->putJs();
    echo "<script type='text/javascript'>
 var browser=navigator.appName;
 var b_version=parseFloat(b_version);
 if(browser=='Microsoft Internet Explorer'){
 }else{
	 jQuery.facebox(function() {
	  jQuery.get('" . str_replace('&amp;', '&', $this->uri(array(
        'action' => 'getimageurl','imageId'=>$imageId
    ))) . "', function(data) {
	    jQuery.facebox(data);
	  })
	 })
 }
</script>";
}
//Check if any image record exists where startime and endtime are within the page startime and endtime -- Can be done later
$startdate = explode (" ", '2010-05-06 20:03:35');
$endate = explode (" ", '2010-05-06 20:03:49');

$startime = explode (":", $startdate['1']);
$endtime = explode (":", $endate['1']);
echo "startdate = ".$startime[1]." newenddate = ".$endtime[1]."<br />";
//Check if its the same hour
if ($startime[0] == $endtime[0]){
  //Check if its the same minute
  if ($startime[1] == $endtime[1]){
    //Calculate a random seconds value thats greater than startime and less than endtime
    
  }else{
    //If endtime[1] is greater than startime[1]
    //Generate random value between the two
    $min1 = rand($startime[1], $endtime[1]);
    $min2 = rand($startime[1], $endtime[1]);
    //Swap if min2 is less than min1
    if ($min2 < $min1) {
      $min3 = $min2;
      $min2 = $min1;
      $min1 = $min3;
    }    
    if ( $min1 == $min2 ){
      //Check if min1 is same as $startime[1]
      if ( $min1 == $startime[1] ){
       //Generate a random value that is between $startime[2] and 60
       if($startime[2]<=45){
        $sec1 = $startime[2]+1;
        $sec1 = rand($sec1, 60);

        do { $sec1 = rand($sec1, 60); } while ( $sec1 > 57 );

        $newstartdate = $startdate['0']." ".$startime[0].":".$min1.":".$sec1;
        $sec2 = $sec1+1;
        $sec2 = rand($sec2, 60);
        $newenddate = $startdate['0']." ".$startime[0].":".$min1.":".$sec1;

       }
      } elseif ( $min1 == $endtime[1] ){
        if ($endtime[2]>=20){
        //Get the start datetime
        $sec1 = $endtime[2] - 10;
        $sec1 = rand(0,$sec1);
        $newstartdate = $endate['0']." ".$endtime[0].":".$min1.":".$sec1;
        if($sec1 < 56){
          $sec1a = $sec1 + 1;
          $sec2 = rand($sec1a, 60);
          $newenddate = $endate['0']." ".$endtime[0].":".$min1.":".$sec2;
          } else {
           $sec2 = rand($sec1, 60);
           $newenddate = $endate['0']." ".$endtime[0].":".$min1.":".$sec2;
          }
        }
      } 
    } else {
     //Do nothing for now if not on the same hour :( Too much work
    }
  }
}
echo "newstartdate = ".$newstartdate." newenddate = ".$newenddate;
?>
