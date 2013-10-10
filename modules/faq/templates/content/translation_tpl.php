<?php
$this->setLayoutTemplate('faq2_layout_tpl.php');
// Show the heading.
$objHeading =& $this->getObject('htmlheading','htmlelements');
$objHeading->type=4;
$objHeading->str="&nbsp;";


//check permissions
$permissions=$this->faq2Tools->checkPermissions();

//creat add faq entry link
$objHeading->str.=$objLanguage->languageText("faq2_category","faq2");
$objHeading->str.="&nbsp;&raquo;&nbsp;".$category[0]['categoryname'];

   $prev = "&nbsp;&raquo;&nbsp;<a href=\"" .
    	               $previous
    		. "\">";
    		$icon = $this->getObject('geticon','htmlelements');
    		$icon->setIcon('prev');
    		$icon->alt = "Previous";
    		$icon->align=false;
    		$prev .= $icon->show();
    		$prev .= "</a>";
$objHeading->str.=$prev;


//display heading
echo $objHeading->show();
//display page css layout
echo $display;
    // Load classes.
	$this->loadHTMLElement("form");
	$this->loadHTMLElement("textinput");
	$this->loadHTMLElement("dropdown");
	$this->loadHTMLElement("button");
	$this->loadHTMLElement("link");
    $this->loadHTMLElement("hiddeninput");
    $this->loadHTMLElement("label");
//
    //Use to check for admin user:
    $isAdmin = $this->objUser->isAdmin();

    //Use to check for lecturer in context:
    $isLecturer = false;
    if($contextId != 'root'){
        $userPKId=$this->objUser->PKId($this->objUser->userId());
        $objGroups=$this->getObject('groupAdminModel','groupadmin');
        $groupid=$objGroups->getLeafId(array($contextId,'Lecturers'));
        if($objGroups->isGroupMember( $userPKId, $groupid )){
            $isLecturer = true;
        }
    }
//
    // Display error string if neccessary.
	if ($error != "") {
		echo "<span class=\"error\">";
		echo $error;
		echo "</span>";
	    echo "<br/>";
	}

    // Add an entry if not displaying "All Categories".
//	if ($categoryId != "All Categories") {
       if ($permissions) {

		
            // Add an entry.
    		$transLink = "<a href=\"" .
    	               $this->uri(array(
    		    		'module'=>'faq2',
    		   			'action'=>'translate',
    					'id'=>$element["id"],
                                        'catd'=>$element['categoryid']
    		))
    		. "\">";
    		
    		$transLink.=$objLanguage->languageText("mod_faq2_translatefaq",'faq2');
    		$transLink .= "</a>";

        } else {
			
            $transLink = NULL;
        }

	

/*	if (!empty($list)) {
		// List the questions as href links to link to the main body of the FAQ.
		$index = 1;
	    // show using an ordered list
	    echo '<ol>';
	  	foreach ($list as $element) {
			echo "<li><a href=\"#".$index."\">";
			echo /*$index . " : " . */ nl2br($element["question"]);
			/*echo "</a></li>";
			$index++;
		}
	    echo '</ol>';
		echo "<br/>";
*/	/*}*/

    // List the questions and answers.
	$index = 1;
	$found = false;
  	foreach ($list as $element) {
         
         
         
         
         //create translate link
         if ($permissions) {

		
            // Add an entry.
    		$transLink = "<a href=\"" .
    	               $this->uri(array(
    		    		'module'=>'faq2',
    		   			'action'=>'translate',
    					'id'=>$element["id"]
                                    
    		))
    		. "\">";
    		
    		$transLink.=$objLanguage->languageText("mod_faq2_translatefaq",'faq2');
    		$transLink .= "</a>";

        } else {
			
            $transLink = NULL;
        }

        // Anchor tag for link to top of page.
		echo "<a id=\"".$index."\"></a>";
		$found = true;
?>
		<!--<div style="background-color: #008080; padding:5px;">-->
		<!--<div style="background-color: #000080; padding:5px;">-->
        <div class="wrapperDarkBkg">
<?php
		echo "<b>" . $index . ": " . "</b>" . nl2br($element["question"]);
?>
        
<!--<div style="background-color: #FFFFFF; padding:5px;">-->
        <div class="wrapperLightBkg">
<?php
	  	echo "<p>";
		echo "<b>" . "</b>" . nl2br($element["answer"]);
	  	echo "</p>";
		//echo $objLanguage->languageText("faq_postedby") . " : " . $objUser->fullname($element["userId"]) . "&nbsp;" . $element["dateLastUpdated"] . "<br/>";
		echo "&nbsp;";
?>
		</div>
		<!--<div style="background-color: #FFFFFF; padding:5px;">-->
        <div class="wrapperLightBkg">
<?php         
	  	
                $languageCodes = & $this->newObject('languagecode','language');
                $language=$languageCodes->getLanguage($element['language']);
                //get entrylicense
               $entrylicense =  $this->objFaqEntries->getLicenseCode($element['entryid']);
               
               $entries=$this->objFaqEntries->getCatEntry($element['entryid'],$element['language']);
               $count=count($entries);
               
               
                //echo $entrylicense.$['entryid'].pop;
                echo "<p>"; 
		echo "<b>".$objLanguage->languageText("mod_faq2_createdby","faq2").":</b>&nbsp;" .$this->objUser->fullname($element["userid"]). "<b>&nbsp;".$objLanguage->languageText("mod_faq2_on","faq2").":</b>" .
                nl2br($this->objDate->formatDate($element["datelastupdated"])). "<b>&nbsp;&nbsp</b>" .
                $this->objLicense->show($entrylicense[0]['licenseid'])."<b>".$objLanguage->languageText("mod_faq2_madein","faq2").
                ":</b>&nbsp;".$language;
                //."&nbsp;".$transLink;
                
                
                if($count!=0)
                echo "&nbsp;<b>".$objLanguage->languageText("mod_faq2_alsoavailablein","faq2")."</b>";
                //create links for other languages
                foreach($entries as $entry)
               {
                   $entrylanguage=$languageCodes->getLanguage($entry['language']);
                  $langLink = "<a href=\"" .
    	               $this->uri(array(
    		    		'module'=>'faq2',
    		   			'action'=>'translation',
    					'id'=>$entry["id"]
                                    
    		))
    		. "\">";
                  
                $langLink.=$entrylanguage."</a>";
                echo "&nbsp;".$langLink."&nbsp;";
               }
         
	  	echo "</p>";
		//echo $objLanguage->languageText("faq_postedby") . " : " . $objUser->fullname($element["userId"]) . "&nbsp;" . $element["dateLastUpdated"] . "<br/>";
		echo "&nbsp;";
                
        if ($permissions) {
            // Edit an entry.
    		$icon = $this->getObject('geticon','htmlelements');
    		$icon->setIcon('edit');
    		$icon->alt = "Edit";
    		$icon->align=false;
                 //since we are editing a translation of the original faq, set translation to true
                $translation=1;
    		echo "<a href=\"" .
                    $this->uri(array(
    		    		'module'=>'faq2',
    		   			'action'=>'edit',
    					'translation'=>$translation,
                                        'category'=>$categoryid,
    					'id' => $element["id"]
    		))
    		. "\">".$icon->show()."</a>";
    		echo "&nbsp;";
            // Delete an entry.
            $objConfirm=&$this->newObject('confirm','utilities');
    		$icon = $this->getObject('geticon','htmlelements');
    		$icon->setIcon('delete');
    		$icon->alt = "Delete";
    		$icon->align=false;
            $objConfirm->setConfirm(
                $icon->show(),
            	$this->uri(array(
            	    'module'=>'faq2',
			'translation'=>1,
            		'action'=>'deletefaqentryconfirm',
            		'catid'=>$categoryid,
            		'id'=>$element["entryid"]
            	)),
                $objLanguage->languageText('faq2_suredelete'));
            echo $objConfirm->show();
        }
?>
		</div>
		</div>
		<!--</div>-->
<?php
        echo "<br/>";
        echo "<br/>";
		$index++;
  	}
    // If no entries then display message.
	if (!$found) {
		echo "<div align=\"left\"class=\"noRecordsMessages\">" . $norecords . "</div>";
	}

	
     

