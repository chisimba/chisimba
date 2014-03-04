<?php
	
	$linkAdd = '';
    //if( $this->isValid( 'add' ) ){
	    // Show the add link
	    $iconAdd = $this->getObject('geticon','htmlelements');
	    $iconAdd->setIcon('add');
	    $iconAdd->alt = $objLanguage->languageText("mod_readinglist_add", 'readinglist');
	    $iconAdd->align=false;
	    $objLink =& $this->getObject('link','htmlelements');
	    $objLink->link($this->uri(array(
	                'module'=>'readinglist',
	            'action'=>'add',
	        )));

         $objLink->link =  $iconAdd->show();
	     $linkAdd = $objLink->show();
	//}

    // Show the heading
    $objHeading =& $this->getObject('htmlheading','htmlelements');
    $objHeading->type=1;
    $objHeading->str =$objLanguage->languageText("mod_readinglist_heading", 'readinglist')." ".$contextTitle
    .'&nbsp;&nbsp;&nbsp;'.$linkAdd;
    echo $objHeading->show();
    echo "<br/>";
    // Create a table object
    $table =& $this->newObject("htmltable","htmlelements");
    $table->border = 0;
    $table->cellspacing='12';
    $table->cellpadding='12';
    $table->width = "100%";
    // Add the table heading.
    $table->startRow();
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_author",'readinglist')."</b>");
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_title",'readinglist')."</b>");
    //$table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_publisher",'readinglist')."</b>");
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_year",'readinglist')."</b>");
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_link",'readinglist')."</b>");
    //$table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_publication",'readinglist')."</b>");
    
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_action",'readinglist')."</b>");
    $table->endRow();
    //echo $objUser->fullname ();
    // Step through the list of books.
    $class = 'even';
    foreach ($list as $item) {
        $linkList = $this->objDbReadingList_links->getByItem($item['id']);
        $class = ($class == 'odd') ? 'even':'odd';
    // Display each field for book.author
        $table->startRow();
        $table->addCell($item['author'], "", NULL, NULL, $class, '');
        $table->addCell($item['title'], "", NULL, NULL, $class, '');
        //$table->addCell($item['publisher'], "", NULL, NULL, $class, '');
        $table->addCell($item['publishingyear'], "", NULL, NULL, $class, '');
        
        //echo $item['link'];
        if($linkList){
    		$link2 = "<a href = '".$linkList[0]['link']."'>".$linkList[0]['link']."</a>";
        }else{
            $link2 = '';
        }
//		$link2 = "<a href = '".$item['link']."'>".$item['link']."</a>";

        //$table->addCell($item['link'], "", NULL, NULL, $class, '');
        $table->addCell($link2, "", NULL, NULL, $class, '');
        //$table->addCell($item['publication'], "", NULL, NULL, $class, '');

	
		
		// Show the Additional link
        $iconAdditional = $this->getObject('geticon','htmlelements');
        $iconAdditional->setIcon('view');
        $iconAdditional->alt = $objLanguage->languageText("mod_readinglist_additionals",'readinglist');
        $iconAdditional->align=false;
        $objLink =& $this->getObject("link","htmlelements");
        $objLink->link($this->uri(array(
                    'module'=>'readinglist',
                'action'=>'additionals',
                'id' => $item["id"]
            )));
            //if( $this->isValid( 'edit' ))
              $objLink->link = $iconAdditional->show();
        $linkAdd = $objLink->show();
        
        // Show the edit link
        $iconEdit = $this->getObject('geticon','htmlelements');
        $iconEdit->setIcon('edit');
        $iconEdit->alt = $objLanguage->languageText("mod_readinglist_edit",'readinglist');
        $iconEdit->align=false;
        $objLink =& $this->getObject("link","htmlelements");
        $objLink->link($this->uri(array(
                    'module'=>'readinglist',
                'action'=>'edit',
                'id' => $item["id"]
            )));
            //if( $this->isValid( 'edit' ))
              $objLink->link = $iconEdit->show();
        $linkEdit = $objLink->show();
        
        
        // Show the Google scholar link
        $iconScholar = $this->getObject('geticon','htmlelements');
        $iconScholar->setIcon('search');
        $iconScholar->alt = $objLanguage->languageText("mod_readinglist_scholar",'readinglist');
        //$iconScholar->align=false;
        $objScholar =& $this->newObject('windowpop','htmlelements');
        $windowScholar = $this->uri(array(
                'action'=>'schgoogle'
            ));
            $objScholar->set('location', $windowScholar); 
		    $objScholar->set('linktext',$iconScholar->show());   
            $objScholar->set('width','500'); 
            $objScholar->set('height','340');
            $objScholar->set('left','100');
            $objScholar->set('top','100');
            $objScholar->set('scrollbars','yes');
        
            $objScholar->link = $iconScholar->show();
        $linkScholar = $objScholar->show();
        

        // Show the delete link
        $iconDelete = $this->getObject('geticon','htmlelements');
        $iconDelete->setIcon('delete');
        $iconDelete->alt = $objLanguage->languageText("mod_readinglist_delete",'readinglist');
        $iconDelete->align=false;

        $objConfirm =& $this->getObject("link","htmlelements");

        $objConfirm=&$this->newObject('confirm','utilities');
            $objConfirm->setConfirm(
                $iconDelete->show(),
                $this->uri(array(
                        'module'=>'readinglist',
                    'action'=>'deleteConfirm',
                    'id'=>$item["id"]
                )),
            $objLanguage->languageText('mod_readinglist_suredelete','readinglist'));
			
            //echo $objConfirm->show();
        $table->addCell($linkAdd.$linkEdit.$linkScholar. $objConfirm->show(), "", NULL, NULL, $class, '');
        $table->endRow();

    }
    echo $table->show();
?>