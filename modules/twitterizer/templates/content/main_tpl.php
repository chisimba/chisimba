<?php
//suppress everything
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressSkin', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('SUPPRESS_PROTOTYPE', true);
$this->setVar('SUPPRESS_JQUERY', true);

$objExtJS = $this->getObject('extjs','htmlelements');
$objExtJS->show();
$this->appendArrayVar('headerParams', '
        	<script type="text/javascript">	        		
        		var baseUri = "'.$this->objConfig->getsiteRoot().'index.php";
        		var terms = "'.$this->objSysConfig->getValue('trackterms', 'twitterizer').'"
        	</script>');

$ext =$this->getJavaScriptFile('statusBar.js', 'twitterizer');
$ext .=$this->getJavaScriptFile('SearchField.js', 'twitterizer');

$ext .=$this->getJavaScriptFile('functions.js', 'twitterizer');
$ext .=$this->getJavaScriptFile('west.js', 'twitterizer');
$ext .=$this->getJavaScriptFile('middle.js', 'twitterizer');
$ext .=$this->getJavaScriptFile('interface.js', 'twitterizer');
$this->appendArrayVar('headerParams', $ext);
$ext .= '
<style type="text/css">
.search-item {
    font:normal 13px tahoma, arial, helvetica, sans-serif;
    padding:3px 10px 3px 10px;
    border:1px solid #fff;
    border-bottom:1px solid #eeeeee;
    white-space:normal;
    color:#555;
    height:60px;
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
    
    
</div>
