<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;


$middleColumn .= $this->raceOps->digiPtMap();

/* '
     <div id="map"></div>
    <ul id="controlToggle">
        <li>
            <input type="radio" name="type" value="none" id="noneToggle"
                   onclick="toggleControl(this);" checked="checked" />
            <label for="noneToggle">navigate</label>
        </li>
        <li>
            <input type="radio" name="type" value="point" id="pointToggle" onclick="toggleControl(this);" />
            <label for="pointToggle">draw point</label>
        </li>
        <li>
            <input type="radio" name="type" value="line" id="lineToggle" onclick="toggleControl(this);" />
            <label for="lineToggle">draw line</label>
        </li>
    </ul>
    <p>Check the box to draw points.  Uncheck to navigate normally.</p>
    <textarea id="geojson" cols="80" rows="30"></textarea>
    ';
*/

$leftColumn .= $this->leftMenu->show();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
