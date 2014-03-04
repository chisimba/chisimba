<?php
//$this->loadClass('layer', 'htmlelements');
//archive template
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objUi = $this->getObject('blogui');
$middleColumn = NULL;
// left hand blocks
$leftCol = ' '; //$objUi->leftBlocks($userid);
$bconf = $objUi->doConfig($userid);
if (is_array($bconf[0])) {
    if (isset($bconf[0]['leftblocks'])) {
        $leftblocks = $bconf[0]['leftblocks'];
    } else {
        $leftblocks = NULL;
    }
    if (isset($bconf[0]['rightblocks'])) {
        $rightblocks = $bconf[0]['rightblocks'];
    } else {
        $rightblocks = NULL;
    }
}
if (is_array($leftblocks) && is_array($rightblocks)) {
    $blocks = array_merge($leftblocks, $rightblocks);
} elseif (isset($leftblocks)) {
    $blocks = $leftblocks;
} elseif (isset($rightblocks)) {
    $blocks = $rightblocks;
}
$blocks = array_unique($blocks);
// right side blocks
$rightSideColumn = NULL; //$objUi->rightBlocks($userid, NULL);
$cssLayout->setNumColumns(3);
//initiate objects
$scripts = '<script type="text/javascript" src="chisimba_modules/yahoolib/resources/yahoo/yahoo.js"></script>

	<script type="text/javascript" src="chisimba_modules/yahoolib/resources/event/event.js"></script>
	<script type="text/javascript" src="chisimba_modules/yahoolib/resources/dom/dom.js"></script>
	<script type="text/javascript" src="chisimba_modules/yahoolib/resources/dragdrop/dragdrop.js"></script>
	<script type="text/javascript" src="chisimba_modules/blog/resources/DDPlayer.js"></script>
	<script type="text/javascript" language="Javascript"> 
	
YAHOO.example.DDApp = function() {
    var slots = [];
    var players = [];
    var Event = YAHOO.util.Event;
    var DDM = YAHOO.util.DDM;
    return {

        remove: function() {
            players[4].removeFromGroup("bottomslots");
        },

        init: function() {
            
            // slots
            slots[0] = new YAHOO.util.DDTarget("l1", "leftslots");
            slots[1] = new YAHOO.util.DDTarget("l2", "leftslots");
            slots[2] = new YAHOO.util.DDTarget("l3", "leftslots");
            slots[3] = new YAHOO.util.DDTarget("l4", "leftslots");
            slots[4] = new YAHOO.util.DDTarget("l5", "leftslots");
            slots[5] = new YAHOO.util.DDTarget("l6", "leftslots");
            slots[6] = new YAHOO.util.DDTarget("l7", "leftslots");
            slots[7] = new YAHOO.util.DDTarget("l8", "leftslots");
            slots[8] = new YAHOO.util.DDTarget("l9", "leftslots");
            slots[9] = new YAHOO.util.DDTarget("l10", "leftslots");
            slots[10] = new YAHOO.util.DDTarget("l11", "leftslots");
            slots[11] = new YAHOO.util.DDTarget("l12", "leftslots");
            slots[12] = new YAHOO.util.DDTarget("l13", "leftslots");
            slots[13] = new YAHOO.util.DDTarget("l14", "leftslots");
            slots[14] = new YAHOO.util.DDTarget("l15", "leftslots");
            
            slots[15] = new YAHOO.util.DDTarget("r1", "rightslots");
            slots[16] = new YAHOO.util.DDTarget("r2", "rightslots");
            slots[17] = new YAHOO.util.DDTarget("r3", "rightslots");
            slots[18] = new YAHOO.util.DDTarget("r4", "rightslots");
            slots[19] = new YAHOO.util.DDTarget("r5", "rightslots");
            slots[20] = new YAHOO.util.DDTarget("r6", "rightslots");
            slots[21] = new YAHOO.util.DDTarget("r7", "rightslots");
            slots[22] = new YAHOO.util.DDTarget("r8", "rightslots");
            slots[23] = new YAHOO.util.DDTarget("r9", "rightslots");
            slots[24] = new YAHOO.util.DDTarget("r10", "rightslots");
            slots[25] = new YAHOO.util.DDTarget("r11", "rightslots");
            slots[25] = new YAHOO.util.DDTarget("r12", "rightslots");
            slots[26] = new YAHOO.util.DDTarget("r13", "rightslots");
            slots[27] = new YAHOO.util.DDTarget("r14", "rightslots");
            slots[28] = new YAHOO.util.DDTarget("r15", "rightslots");
            
            // players
            players[0] = new YAHOO.example.DDPlayer("usermenu", "leftslots");
            players[0].addToGroup("rightslots");
            players[1] = new YAHOO.example.DDPlayer("profiles", "leftslots");
            players[1].addToGroup("rightslots");
            players[2] = new YAHOO.example.DDPlayer("adminsection", "leftslots");
            players[2].addToGroup("rightslots");
            players[3] = new YAHOO.example.DDPlayer("loginbox", "leftslots");
            players[3].addToGroup("rightslots");
            players[4] = new YAHOO.example.DDPlayer("feeds", "leftslots");
            players[4].addToGroup("rightslots");
            players[5] = new YAHOO.example.DDPlayer("bloglinks", "leftslots");
            players[5].addToGroup("rightslots");
            players[6] = new YAHOO.example.DDPlayer("blogroll", "leftslots");
            players[6].addToGroup("rightslots");
            players[7] = new YAHOO.example.DDPlayer("blogpages", "leftslots");
            players[7].addToGroup("rightslots");
            players[8] = new YAHOO.example.DDPlayer("blogslink", "leftslots");
            players[8].addToGroup("rightslots");
            players[9] = new YAHOO.example.DDPlayer("archivebox", "leftslots");
            players[9].addToGroup("rightslots");
            players[10] = new YAHOO.example.DDPlayer("blogtagcloud", "leftslots");
            players[10].addToGroup("rightslots");
            players[11] = new YAHOO.example.DDPlayer("catsmenu", "leftslots");
            players[11].addToGroup("rightslots");
            players[12] = new YAHOO.example.DDPlayer("searchbox", "leftslots");
            players[12].addToGroup("rightslots");
            players[13] = new YAHOO.example.DDPlayer("quickcats", "leftslots");
            players[13].addToGroup("rightslots");
            players[14] = new YAHOO.example.DDPlayer("quickpost", "leftslots");
            players[14].addToGroup("rightslots");
            
            
            //players[1] = new YAHOO.example.DDPlayer("", "leftslots");
            //players[2] = new YAHOO.example.DDPlayer("pb1", "leftslots");
            //players[3] = new YAHOO.example.DDPlayer("pb2", "leftslots");
            //players[4] = new YAHOO.example.DDPlayer("pboth1", "leftslots");
            //players[4].addToGroup("rightslots");
            //players[5] = new YAHOO.example.DDPlayer("pboth2", "leftslots");
            //players[5].addToGroup("rightslots");

            // DDM.mode = document.getElementById("ddmode").selectedIndex;
        }

    };
} ();

YAHOO.util.Event.addListener(window, "load", YAHOO.example.DDApp.init);
YAHOO.util.Event.addListener("removeButton", "click", YAHOO.example.DDApp.remove);

</script>';
$this->appendArrayVar('headerParams', $scripts);
$middleColumn.= '
<style type="text/css">
    .slot { border:2px solid #aaaaaa; background-color:#dddddd; color:#666666; text-align:center; position: absolute; width:60px; height:60px; }
    .player { border:2px solid #bbbbbb; color:#eeeeee; text-align:center; position: absolute; width:60px; height:60px; }
    .target { border:2px solid #574188; background-color:#cccccc; text-align:center; position: absolute; width:60px; height:60px; }

    #l1 { left: 90px; top: 150px; }
    #l2 { left: 90px; top: 200px; }
    #l3 { left: 90px; top: 250px; }
    #l4 { left: 90px; top: 300px; }
    #l5 { left: 90px; top: 350px; }
    #l6 { left: 90px; top: 400px; }
    #l7 { left: 90px; top: 450px; }
    #l8 { left: 90px; top: 500px; }
    #l9 { left: 90px; top: 550px; }
    #l10 { left: 90px; top: 600px; }
    #l11 { left: 90px; top: 650px; }
    #l12 { left: 90px; top: 700px; }
    #l13 { left: 90px; top: 750px; }
    #l14 { left: 90px; top: 800px; }
    #l15 { left: 90px; top: 850px; }
    
    #r1 { right: 90px; top: 150px; }
    #r2 { right: 90px; top: 200px; }
    #r3 { right: 90px; top: 250px; }
    #r4 { right: 90px; top: 300px; }
    #r5 { right: 90px; top: 350px; }
    #r6 { right: 90px; top: 400px; }
    #r7 { right: 90px; top: 450px; }
    #r8 { right: 90px; top: 500px; }
    #r9 { right: 90px; top: 550px; }
    #r10 { right: 90px; top: 600px; }
    #r11 { right: 90px; top: 650px; }
    #r12 { right: 90px; top: 700px; }
    #r13 { right: 90px; top: 750px; }
    #r14 { right: 90px; top: 800px; }
    #r15 { right: 90px; top: 850px; }
    
  //  #pt1 { background-color:#7E695E; left: 164px; top: 350px; }
  //  #pt2 { background-color:#7E695E; left: 164px; top: 430px; }
  //  #pb1 { background-color:#416153; left: 275px; top: 350px; }
  //  #pb2 { background-color:#416153; left: 275px; top: 430px; }
  //  #pboth1 { background-color:#552E37; left: 386px; top: 350px; }
  //  #pboth2 { background-color:#552E37; left: 386px; top: 430px; }
</style>

<!-- div class="slot" id="t1" >1</div>
<div class="slot" id="t2" >2</div>
<div class="slot" id="b1" >3</div>
<div class="slot" id="b2" >4</div>
<div class="slot" id="b3" >5</div>
<div class="slot" id="b4" >6</div> -->

<!-- div class="player" id="pt1" >1</div>
<div class="player" id="pt2" >2</div>
<div class="player" id="pb1" >3</div>
<div class="player" id="pb2" >4</div>
<div class="player" id="pboth1" >5</div>
<div class="player" id="pboth2" >6</div> -->';
// 11 blocks on each side, in case user wants em all in one column...
foreach($blocks as $block) {
    $middleColumn.= '<div id="' . $block . '" class="featurebox">' . $block . '
	</div>';
}
$l = 1;
for ($l = 1; $l < 15; $l++) {
    $leftCol.= '<div class="slot" id="l' . $l . '" >' . $l . '</div><br />';
}
$r = 1;
for ($r = 1; $r < 15; $r++) {
    $rightSideColumn.= '<div class="slot" id="r' . $r . '" >' . $r . '</div><br />';
}
$objBlogUi = $this->getObject('blogui');
$bconf = $objBlogUi->doConfig($userid);
$leftblocks = $bconf[0]['leftblocks'];
$rightblocks = $bconf[0]['rightblocks'];
$rb = count($rightblocks);


// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    $cssLayout->setMiddleColumnContent($middleColumn);
    $cssLayout->setLeftColumnContent($leftCol);
    $cssLayout->setRightColumnContent($rightSideColumn);
    echo $cssLayout->show();
}
?>