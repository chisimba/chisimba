<?php
//header("Content-type: image/png");
$head = '<script src="'.$this->getResourceUri("mscross119.js").'" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $head);

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 2
$cssLayout->setNumColumns(2);
$leftMenu = NULL;
$leftCol = NULL;
$middleColumn = NULL;
$mainmap = ''; //<div style="width: 800px; height: 600px; border: 50px;" id="map_tag" ></div>';
$middleColumn .= <<<EOT
   <div style="width: 800px; height: 600px; border: 50px;" id="map_tag" ></div>
   <div style="width: 190px; height: 400px;" id="ref_tag"></div>
   <script type="text/javascript">
     //<![CDATA[
     myMap1 = new msMap( document.getElementById('map_tag'), 'standardRight');
     myMap1.setCgi( "$mapservcgi" );
     myMap1.setFullExtent( $bounds );
     myMap1.setMapFile( "$mapfile" );
     myMap1.setLayers( "$layers" );
	 myMap1.setMode('map');
	 
	 myMap2 = new msMap( document.getElementById("ref_tag") );
	 myMap2.setActionNone();
	 myMap2.setFullExtent($bounds);
	 myMap2.setMapFile("$mapfile");
	 myMap2.setLayers("$layers");
	 
	 myMap1.setReferenceMap(myMap2);

	 myMap1.redraw();  myMap2.redraw();
	 
	//chgLayers();


	/* function chgLayers()
	{
  		var list = "$layers";
  		var objForm = document.forms[0];
  		for(i=0; i<document.forms[0].length; i++)
  		{
    		if( objForm.elements["layer[" + i + "]"].checked )
    		{
      			list = list + objForm.elements["layer[" + i + "]"].value + " ";
    		}
  		}
  		myMap1.setLayers( list );
  		myMap1.redraw();
	}
     myMap1.redraw(); */
     //]]>
   </script>
EOT;

$leftCol .= $objSideBar->show();

//$maptab = $this->newObject('htmltable', 'htmlelements');
//$maptab->cellpadding = 3;
//$maptab->startRow();
//$maptab->addCell($mainmap);
//$maptab->addCell($refmap);
//$maptab->endRow();

//echo $mainmap, $refmap;
$middleColumn .= $mainmap; //$maptab->show();
//$middleColumn .= header("Content-type: image/png");
//$middleColumn .= "<img src=".$themap.">";

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
echo $cssLayout->show();
?>