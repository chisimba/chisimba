<?php

// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass("iframe", 'htmlelements');
//Paul M. To do -- Correct form action
$form = new form("default",
                $this->uri(array(
                    'module' => 'contextcontent', 'action' => 'movetochapter'
                )));

//Array to contain language items for JS
$arrLang = array();
$arrLang['previous'] = $this->objLanguage->languageText('mod_scorm_previous', 'scorm');
$arrLang['next'] = $this->objLanguage->languageText('mod_scorm_next', 'scorm');
$arrLang['home'] = $this->objLanguage->languageText('mod_scorm_home', 'scorm');
//AJAX to check if selected folder contains scorm
$this->appendArrayVar('headerParams', "<style type='text/css'>
  div.small-box {
  width:250px;
  height:550px;
  border:1px solid grey;
  overflow:scroll;
  font:30px;
  }
  div.big-box {
  width:700px;
  height:550px;
  border:1px solid grey;
  //overflow:scroll;
  font:30px;
  }

</style>
    <script type='text/javascript'>
        // Flag Variable - Update message or not
        var doUpdateMessage = false;
       	var nextPage;
       	var prevPage;        
        // Var Current Entered Code
        var currentCode;

       	//var fpath = jQuery('#rootFolder').attr(\"value\");

        /*        
        // Action to be taken once page has loaded
        jQuery(document).ready(function(){
            jQuery('#div_navigators').bind(\"mouseover\", function() {
                getIframeID(jQuery('#IFRAME_content').attr(\"src\"));
            });
        });
        jQuery(document).ready(function(){
         		jQuery('#div_navigators a').click(function() {
          			getIframeID(this.href);
         			return confirm('You are going to visit: ' + this.href);
      		});
        });
        //jQuery('#rootFolder').attr(\"value\")
        */
       	// prepare the form when the DOM is ready 
        jQuery(document).ready(function(){

      		jQuery('#span_next').click(function() {
      			jQuery('#span_next').html('');
      			getNextPage(nextPage,jQuery('#input_rootfolder').attr(\"value\"));
      			getPrevPage(nextPage,jQuery('#input_rootfolder').attr(\"value\"));
			
      		});
      		jQuery('#span_prev').click(function() {
      			jQuery('#span_next').html('');
      			getNextPage(prevPage,jQuery('#input_rootfolder').attr(\"value\"));
      			getPrevPage(prevPage,jQuery('#input_rootfolder').attr(\"value\"));
 			
	      	});
      		jQuery('#span_nextb').click(function() {
      			jQuery('#span_nextb').html('');
      			getNextPage(nextPage,jQuery('#input_rootfolder').attr(\"value\"));
      			getPrevPage(nextPage,jQuery('#input_rootfolder').attr(\"value\"));			
      		});
      		jQuery('#span_prevb').click(function() {
      			jQuery('#span_nextb').html('');
      			getNextPage(prevPage,jQuery('#input_rootfolder').attr(\"value\"));
      			getPrevPage(prevPage,jQuery('#input_rootfolder').attr(\"value\"));
			
		});

		jQuery('#div_navigators a').click(function() {
			//getIframeID(this.href);
			//getIframeID(fpath);
			//getIframeID(this.href,jQuery('#input_rootfolder').attr(\"value\"));
			getNextPage(this.href,jQuery('#input_rootfolder').attr(\"value\"));
			getPrevPage(this.href,jQuery('#input_rootfolder').attr(\"value\"));
		});
		jQuery('#span_home a').click(function() {
			jQuery('#span_next').html('');
			jQuery('#span_prev').html('');
			getNextPage(this.href,jQuery('#input_rootfolder').attr(\"value\"));
			getPrevPage(this.href,jQuery('#input_rootfolder').attr(\"value\"));
		});
		jQuery('#span_homeb a').click(function() {
			jQuery('#span_next').html('');
			jQuery('#span_prev').html('');
			jQuery('#span_nextb').html('');
			jQuery('#span_prevb').html('');
			getNextPage(this.href,jQuery('#input_rootfolder').attr(\"value\"));
			getPrevPage(this.href,jQuery('#input_rootfolder').attr(\"value\"));
		});

/*
		jQuery('#divContent iframe').load(function() {
			jQuery('#span_prev').html('');
			jQuery('#span_next').html('');
			getNextPage(jQuery('#IFRAME_content').attr(\"src\"),jQuery('#input_rootfolder').attr(\"value\"));
			getPrevPage(jQuery('#IFRAME_content').attr(\"src\"),jQuery('#input_rootfolder').attr(\"value\"));
		});
*/
        });
	function getNextPage(current,fpath)
	{
		var page;
		var folderpath;
		
   // DO Ajax
 		// prepare the form when the DOM is ready 
   jQuery.ajax({
       type: 'GET', 
       url: 'index.php?', 
       data: 'module=scorm&action=getNext&page='+current+'&folderpath='+fpath, 
       success: function(msg){
        nextPage = msg;
        // IF next page exists
        if (msg=='omega') {
            jQuery('#span_next').html('Last Page');
            jQuery('#span_next').addClass('error');
            jQuery('#span_next').removeClass('success');			
            jQuery('#span_nextb').html('Last Page');
            jQuery('#span_nextb').addClass('error');
            jQuery('#span_nextb').removeClass('success');			
        }else{
            jQuery('#span_next').html('<a href=\''+nextPage+'\' target = \'content\' id = \'next\'>" . $arrLang['next'] . "</a>');
            jQuery('#span_next').addClass('success');
            jQuery('#span_next').removeClass('error');
            jQuery('#span_nextb').html('<a href=\''+nextPage+'\' target = \'content\' id = \'next\'>" . $arrLang['next'] . "</a>');
            jQuery('#span_nextb').addClass('success');
            jQuery('#span_nextb').removeClass('error');

   				}
  	                              
   }
 });

	}
	function getPrevPage(current,fpath)
	{
		 var page;
		 var folderpath;
   // DO Ajax
 		// prepare the form when the DOM is ready 
   jQuery.ajax({
    type: 'GET', 
    url: 'index.php?', 
    data: 'module=scorm&action=getPrev&page='+current+'&folderpath='+fpath, 
    success: function(msg){
      prevPage = msg;
       // IF next page exists
       if (msg=='alpha') {
           jQuery('#span_prev').html('First Page');
           jQuery('#span_prev').addClass('error');
           jQuery('#span_prev').removeClass('success');			
           jQuery('#span_prevb').html('First Page');
           jQuery('#span_prevb').addClass('error');
           jQuery('#span_prevb').removeClass('success');			
       }else{
           jQuery('#span_prev').html('<a href=\''+prevPage+'\' target = \'content\' id = \'next\'>" . $arrLang['previous'] . "</a>');
           jQuery('#span_prev').addClass('success');
           jQuery('#span_prev').removeClass('error');
           jQuery('#span_prevb').html('<a href=\''+prevPage+'\' target = \'content\' id = \'next\'>" . $arrLang['previous'] . "</a>');
           jQuery('#span_prevb').addClass('success');
           jQuery('#span_prevb').removeClass('error');

				}
  	                              
  }
 });

	}

	function getIframeID(el,path)
	{
		jQuery('#contextcodemessage').html(el+' '+path);
  jQuery('#contextcodemessage').addClass('success');

	}
    </script>");


if ($mode == 'page') {

    $addLink = new link($this->uri(array('action' => 'addpage', 'id' => $id, 'context' => $this->contextCode, 'chapter' => $currentChapter), 'contextcontent'));
    $addLink->link = $this->objLanguage->languageText('mod_contextcontent_addcontextpages', 'contextcontent');

    $addScormLink = new link($this->uri(array('action' => 'addscormpage', 'id' => $id, 'context' => $this->contextCode, 'chapter' => $currentChapter), 'contextcontent'));
    $addScormLink->link = $this->objLanguage->languageText('mod_contextcontent_addcontextscormpages', 'contextcontent');


    $editLink = new link($this->uri(array('action' => 'editpage', 'id' => $id, 'context' => $this->contextCode), 'contextcontent'));
    $editLink->link = $this->objLanguage->languageText('mod_contextcontent_editcontextpages', 'contextcontent');

    if (($rght - $lft - 1) == 0) {
        $deleteLink = new link($this->uri(array('action' => 'deletepage', 'id' => $id, 'context' => $this->contextCode), 'contextcontent'));
    } else {
        $deleteLink = new link("javascript:alert('" . $this->objLanguage->languageText('mod_contextcontent_pagecannotbedeleteduntil', 'contextcontent') . ".');");
    }
    $deleteLink->link = $this->objLanguage->languageText('mod_contextcontent_delcontextpages', 'contextcontent');

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
        $middle = '&nbsp;';
    } else {
        $middle = '';
        $divider = '';

        foreach ($list as $item) {
            $middle .= $divider . $item;
            $divider = ' / ';
        }
    }
    if (!empty($this->userId)) {
        if ($this->isValid('movepageup')) {

            $middle .= '<br></br>';

            if ($isFirstPageOnLevel) {
                $middle .= '<span style="color:grey;" title="' . $this->objLanguage->languageText('mod_contextcontent_isfirstpageonlevel', 'contextcontent') . '">' . $this->objLanguage->languageText('mod_contextcontent_movepageup', 'contextcontent') . '</span>';
            } else {
                $link = new link($this->uri(array('action' => 'movepageup', 'id' => $id), 'contextcontent'));
                $link->link = $this->objLanguage->languageText('mod_contextcontent_movepageup', 'contextcontent');
                $middle .= $link->show();
            }

            $middle .= ' / ';

            if ($isLastPageOnLevel) {
                $middle .= '<span style="color:grey;" title="' . $this->objLanguage->languageText('mod_contextcontent_islastpageonlevel', 'contextcontent') . '">' . $this->objLanguage->languageText('mod_contextcontent_movepagedown', 'contextcontent') . '</span>';
            } else {
                $link = new link($this->uri(array('action' => 'movepagedown', 'id' => $id), 'contextcontent'));
                $link->link = $this->objLanguage->languageText('mod_contextcontent_movepagedown', 'contextcontent');
                $middle .= $link->show();
            }
        }
    }
}

//Get The API
$getApi = $this->getResourcePath('api.htm', 'scorm');
//get scorm folder id
//$folderId = 'gen5Srv7Nme24_7833_1217613986';

$folder = $this->objFolders->getFolder($folderId);
$filePath = $folder['folderpath'];
$this->setVarByRef('filePath', $filePath);
//Generate the TOC for navigation from imsmanifest.xml
$navigators = $this->objReadXml->readManifest($filePath);
$firstPage = $this->objReadXml->xmlFirstPage($filePath);
//$navigators = $this->objReadXml->treeMenuXML($filePath);
$objTable = new htmltable();
$objTable->width = '950px';
$objTable->height = '100%';
$objTable->attributes = " align='center' border='0'";
$objTable->cellspacing = '5';
$row = array("<b>" . $objLanguage->code2Txt("word_name") . ":</b>");
//$objIframe = new iframe();
//iframe to hold the API
$apiIFrame = '<iframe src="' . $getApi . '" name="API" height=0 width=10 frameborder=0 scrolling=no></iframe>';
//iframe to hold the content
$content = '<iframe id="IFRAME_content" src="' . $firstPage . '" name="content" height=450 width=650 frameborder=0 scrolling=yes></iframe>';
$testNavs = "<div id='divNavs' align = 'center'><span id='span_home'> <a href = '" . $firstPage . "' target = 'content' id = 'home'> Home</a></span>" . " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span id='span_prev'>&nbsp</span>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span id='span_next'>&nbsp</next> </div>";
$testNavsB = "<div id='divNavsb' align = 'center'><span id='span_homeb'> <a href = '" . $firstPage . "' target = 'content' id = 'home'> Home</a></span>" . " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <span id='span_prevb'>&nbsp</span>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span id='span_nextb'>&nbsp</next> </div>";

$objTable->startRow();
$objTable->addCell("&nbsp;");
$objTable->addCell("&nbsp;");
$objTable->addCell("&nbsp;");
$objTable->endRow();

// Spacer
$objTable->startRow();
$objTable->addCell($apiIFrame);
$objTable->addCell('<div id="div_navigators" class="small-box">' . $navigators . "</div>");
$objTable->addCell('<div class="big-box"><br></br>' . $testNavs . "<div id='divContent' align = 'center'>" . $content . "</div>" . $testNavsB. '</div>');
//$objTable->addCell('<div class="big-box">'.'test'."<br /><div id='divContent' align = 'center'>".$content."</div><br />").'</div>';
$objTable->startRow();
$objTable->addCell("&nbsp;");
$objTable->addCell("&nbsp;");
$objTable->addCell(' <span id="contextcodemessage">' . $contextCodeMessage . '</span>');

$objTable->endRow();

if ($mode == 'page') {
    $objTable->startRow();
    $objTable->addCell($prevPage, '33%', 'top');
    $objTable->addCell($middle, '33%', 'top', 'center');
    $objTable->addCell($nextPage, '33%', 'top', 'right');
    $objTable->endRow();
}

if (!empty($this->userId)) {
    if (count($chapters) > 1 && $this->isValid('movetochapter')) {

        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $hiddenInput = new hiddeninput('id', $id);

        $dropdown = new dropdown('chapter');
        foreach ($chapters as $chapterItem) {
            $dropdown->addOption($chapterItem['chapterid'], $chapterItem['chaptertitle']);
        }
        $dropdown->setSelected($chapterId);

        $label = new label($this->objLanguage->languageText('mod_contextcontent_movepagetoanotherchapter', 'contextcontent') . ': ', 'input_chapter');

        $button = new button('movepage', $this->objLanguage->languageText('mod_contextcontent_move', 'contextcontent'));
        $button->setToSubmit();

        $form->addToForm('<br></br>'.$hiddenInput->show() . $label->show() . $dropdown->show() . ' ' . $button->show());
    }
}
$objWashout = $this->getObject('washout', 'utilities');
$form->addToForm($objWashout->parseText($objTable->show()));
echo $form->show();
?>
