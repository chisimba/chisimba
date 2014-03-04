<?php
//suppress everything
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressSkin', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('SUPPRESS_PROTOTYPE', true);
$this->setVar('SUPPRESS_JQUERY', true);

$this->objWashout = $this->getObject ( 'washout', 'utilities' );
$this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
$this->objViewer = $this->getObject('viewer');

$adhead1 = $this->objSysConfig->getValue ( 'adhead1', 'brandmonday' );
$adhead2 = $this->objSysConfig->getValue ( 'adhead2', 'brandmonday' );
$fbhead = $this->objSysConfig->getValue ( 'fbhead', 'brandmonday' );
$fbtext = $this->objSysConfig->getValue ( 'fbtext', 'brandmonday' );
$adtext1 = $this->objSysConfig->getValue ( 'adtext1', 'brandmonday' );
$adtext2 = $this->objSysConfig->getValue ( 'adtext2', 'brandmonday' );
$abouthead = $this->objSysConfig->getValue ( 'abouthead', 'brandmonday' );
$tweetThisHead = $this->objLanguage->languageText ( "mod_brandmonday_tweetthis", "brandmonday" );
        
$objExtJS = $this->getObject('extjs','htmlelements');
$objExtJS->show();
$this->appendArrayVar('headerParams', '
        	<script type="text/javascript">	        		
        		var baseUri = "'.$this->objConfig->getsiteRoot().'index.php";
        		var poweredHead = "'.$this->objSysConfig->getValue ( 'chishead', 'brandmonday' ).'";
        		var adhead1 = "'.$adhead1.'";
        		var adhead2 = "'.$adhead2.'";
        		var abouthead = "'.$abouthead.'";
        		var fhead = "'.$fbhead.'";
        		var tweetThisHead = "'.$tweetThisHead.'";
        		
        		
        	</script>');

//$ext =$this->getJavaScriptFile('statusBar.js', 'twitterizer');
//$ext .=$this->getJavaScriptFile('SearchField.js', 'twitterizer');

$ext =$this->getJavaScriptFile('functions.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('brandplus.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('awards.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('brandminus.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('mentions.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('west.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('middlePanel.js', 'brandmonday');
$ext .=$this->getJavaScriptFile('interface.js', 'brandmonday');
$this->appendArrayVar('headerParams', $ext);
$ext .= '
<style type="text/css">
.tagcloud {
	font: tahoma;
	padding:10px 10px 10px 10px;
	color:#555;
}
.tagcloud a{

	//color:#555;
	text-decoration:none;
}

.bestserv {
padding:10px 10px 10px 10px;
}
.search-item {
    font:normal 12px tahoma, arial, helvetica, sans-serif;
    padding:3px 10px 3px 10px;
    border:1px solid #fff;
    border-bottom:1px solid #eeeeee;
    white-space:normal;
    color:#555;
    height:65px;
}
.search-item h3 {
    display:block;
    position:relative;
    font:inherit;
    font-weight:bold;
    color:#222;
}

.search-item img {
    float: left;
    font-weight:normal;
    margin:0 7px 0 3px;    
    display:block;
    
}

.search-item a span{
	
}

.header{
	font: bold 18px tahoma, arial, helvetica, sans-serif;
	padding:10px 10px 10px 10px;
	margin-left:300px;
}

.search-item span {
    float: right;
    font-weight:normal;
    margin:0 0 5px 5px;
    width:100px;
    display:block;
    clear:none;
}

        #search-results a {
            color: #385F95;
            font:bold 11px tahoma, arial, helvetica, sans-serif;
            text-decoration:none;
        }
        #search-results a:hover {
            text-decoration:underline;
        }
        #search-results .search-item {
            padding:5px;
        }
        #search-results p {
            margin:3px !important;
        }
        #search-results {
            border-bottom:1px solid #ddd;
            margin: 0 1px;
            height:300px;
            overflow:auto;
        }
        #search-results .x-toolbar {
            border:0 none;
        }
        
        /* StatusBar */

.x-statusbar .x-status-text {
    height: 21px;
    line-height: 21px;
    padding: 0 4px;
    cursor: default;
}
.x-statusbar .x-status-busy {
    padding-left: 25px !important;
    background: transparent url(../images/loading.gif) no-repeat 3px 3px;
}
.x-statusbar .x-status-text-panel {
    border-top: 1px solid #99BBE8;
    border-right: 1px solid #fff;
    border-bottom: 1px solid #fff;
    border-left: 1px solid #99BBE8;
    padding: 2px 8px 2px 5px;
}

/* StatusBar word processor example styles */

#word-status .x-status-text {
    color: #777;
}
#word-status .x-status-text-panel .spacer {
    width: 60px;
    font-size:0;
    line-height:0;
}
#word-status .x-status-busy {
    padding-left: 25px !important;
    background: transparent url(../images/saving.gif) no-repeat 3px 3px;
}
#word-status .x-status-saved {
    padding-left: 25px !important;
    background: transparent url(../images/saved.png) no-repeat 3px 3px;
}

/* StatusBar form validation example styles */

.x-statusbar .x-status-error {
    color: #C33;
    cursor: pointer;
    padding-left: 25px !important;
    background: transparent url(../images/exclamation.gif) no-repeat 3px 3px;
}
.x-statusbar .x-status-valid {
    padding-left: 25px !important;
    background: transparent url(../images/accept.png) no-repeat 3px 3px;
}
.x-status-error-list {
    font: 11px tahoma,arial,verdana,sans-serif;
    position: absolute;
    z-index: 9999;
    border: 1px solid #C33;
    background: #ffa;
    padding: 5px 10px;
    color: #999;
}
.x-status-error-list li {
    cursor: pointer;
    list-style: disc;
    margin-left: 10px;
}
.x-status-error-list li a {
    color: #15428B;
    text-decoration: none;
}
.x-status-error-list li a:hover {
    text-decoration: underline;
}

.west {
	font: 11px tahoma,arial,verdana,sans-serif;
	padding:5px 5px 5px 5px;
}
    </style>';
$this->appendArrayVar('headerParams', $ext);
?>


<div id="west" class="x-hide-display">
        <p>Hi. I'm the west panel.</p>
    </div>
   
    <div id="props-panel" class="x-hide-display" style="width:200px;height:200px;overflow:hidden;">

    </div>
    <div id="south" class="x-hide-display">
        <p>Powered By Chisimba</p>
    </div>
    
    
    
    <div id="poweredby" class="west">   
    <p>
    <?php 
    	echo $this->objWashout->parseText($this->objSysConfig->getValue ( 'chistext', 'brandmonday' ));      
    ?>
    </p>
    </div>
    
    <div id="feeds" class="west">   
    <p>
    <?php 
    	echo $this->objViewer->rssBlock(false);
     ?>
    </p>
    </div>
    
        
     <div id="disclaimer" class="west">   
    <p>
    <?php 
    	echo  $this->objWashout->parseText($this->objLanguage->languageText("mod_brandmonday_disclaimertext", "brandmonday"));
     ?>
    </p>
    </div>
    
     <div id="ad1" class="west">   
    <p>
    <?php 	echo $this->objWashout->parseText($adtext1);       ?>
    </p>
    </div>
    
     <div id="ad2" class="west">   
    <p>
    <?php  	echo $this->objWashout->parseText($adtext2) ?>
    </p>
    </div>
    
     <div id="ad3" class="west">   
    <p>
    <?php  	echo   $this->objWashout->parseText($fbtext)   ?>
    </p>
    </div>
    
    <div id="about" class="west">   
    <p>
    <?php  	echo   $this->objViewer->aboutBlock(false);   ?>
    </p>
    </div>
    
     <div id="tweetthis" class="west">   
    <p>
    <?php  	echo   $this->objViewer->tweetThisBox(false);   ?>
    </p>
    </div>  
</div>
