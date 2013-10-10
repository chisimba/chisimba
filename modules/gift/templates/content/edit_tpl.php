<?php

	$gifts = $this->objDbGift->getGifts();
	
	// scripts for extjs
    $extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
    $extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
    $extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
    
    $this->appendArrayVar('headerParams', $extbase);
    $this->appendArrayVar('headerParams', $extalljs);
    $this->appendArrayVar('headerParams', $extallcss);
	
	//CSS
	// Create an instance of the css layout class
	$cssLayout = &$this->newObject('csslayout', 'htmlelements');// Set columns to 2
	$cssLayout->setNumColumns(2);
	// get the links on the left
	$form = $this->objHome->getLeftContent($toSelect, $this->getParam('action'), $this->getParam('archived'));
	// links are displayed on the left
	$leftSideColumn = $form;
	$cssLayout->setLeftColumnContent($leftSideColumn);
	
	// Add the table to the centered layer and a message of database functionality
	$rightSideColumn =  '<h1>Gift List</h1><div id="grouping-grid"><br /></div>';
	$cssLayout->setMiddleColumnContent($rightSideColumn);
	

	// Output the content to the page
	echo $cssLayout->show();
	
	
//--------------------------------------EXTTJ------------------------------------------------------------------

	
	$data = "";
	$numberOfRows = $this->objDbGift->getNumberOfGifts();
	 foreach($gifts as $value) {
		


                        $data .= "['".$value['donor']."',";


                       
                        $data .= "'".$value['recipient']."',";
                        
                        $data .= "'".$value['giftname']."',";
                                             
                        $data .= "'".$value['description']."',";
						
						
                        $data .= "'".$value['value']."'";
                        $data .= "]";
						
                        if($value['puid'] != $numberOfRows) {
                            $data .= ",";
                        }
                       
                    }  
	
$title = "Donor";
$recepient = "Recipient";
$giftname ="Gift Name";
$description ="Descriptioon";
$value ="Value";
	
	$mainjs = "/*!
				 * Ext JS Library 3.0.0
				 * Copyright(c) 2006-2009 Ext JS, LLC
				 * licensing@extjs.com
				 * http://www.extjs.com/license
				*/
				Ext.onReady(function(){

				Ext.QuickTips.init();

                var xg = Ext.grid;
					
					// shared reader
                    var reader = new Ext.data.ArrayReader({}, [
                       {name: 'donor'},
                       {name: 'recipient'},
                       {name: 'giftname'},
                       {name: 'description'},
                       {name: 'value'}
                    ]);	
					
				var grid = new xg.GridPanel({
                        store: new Ext.data.GroupingStore({
                            reader: reader,
                            data: xg.Data,
                            sortInfo:{field: 'giftname', direction: \"ASC\"},
                            groupField:'donor'
                        }),	
				columns: [
                            {id:'donor',header: \"".$title."\", width: 200, dataIndex: 'donor'},
                            {header: \"".$recepient."\", width: 150},
                            {header: \"".$giftname."\", width: 120},
                            {header: \"".$description."\", width: 50},
                            {header: \"".$value."\", width: 100}
                            ";
                            $mainjs .="
                        ],
				
				view: new Ext.grid.GroupingView({
                            forceFit:true,
                            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? \"Items\" : \"Item\"]})'
                        }),
						
						frame:false,
                        width: 750,
                        height: 450,
                        collapsible: true,
                        animCollapse: false,
                       
                        renderTo: 'grouping-grid'
				
				});
			});
	
			// Array data for the grids
               Ext.grid.Data = [".$data."];
				
                ";
		
				
			   
 echo "<script type=\"text/javascript\">".$mainjs."</script>";

//-----------------------------------------------END----------------------------------------------- 



?>
