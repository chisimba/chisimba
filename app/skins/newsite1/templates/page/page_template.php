<?php
    // Get Header that goes into every skin
    require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader.php');

    if (!isset($pageSuppressToolbar)) {
        // get toolbar object
        $menu = $this->getObject('menu','sanord-toolbar');
        // $menu = $this->getObject('sanordmenu','sanordmenu');
        $toolbar = $menu->show();
        // get any header params or body onload parameters for objects on the toolbar
        // $menu->getParams(&$headerParams, &$bodyOnLoad);
    }
    // Set the page title
    if (!isset($pageTitle)) {
        $pageTitle = $objConfig->getSiteName();
    }
?>

<head>

    <title>
        <?php echo $pageTitle; ?>
    </title>


<?php

    if (!isset($pageSuppressSkin)){
        echo $objSkin->putSkinCssLinks();

        if (!isset($pageSuppressToolbar)) {
            echo '
                <!--[if lte IE 6]>
                <style type="text/css">
                    body { behavior:url("skins/_common/js/ADxMenu_prof.htc"); }
                </style>
                <![endif]-->
                ';
        }
    }
    echo $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);

    //Only show the dropdown on pages with toolbar to prevent it appearing in popups
    if (!isset($pageSuppressToolbar)){
    ?>
    <script src="core_modules/htmlelements/resources/jquery/jquery.easing.1.3.js" type="text/javascript"></script>
    <script src="core_modules/htmlelements/resources/jquery/jquery.slideviewer.1.1.js" type="text/javascript"></script>
    <?php
    }
?>


    <link rel="stylesheet" type="text/css" href="skins/sanord.com/includes/tigra_2.0.1/menu.css">
    <script language="JavaScript" src="skins/sanord.com/includes/tigra_2.0.1/menu.js"></script>

 <script language="JavaScript">
        var MENU_ITEMS = [
            ['Home', '?module=cms', null,
                //['SIAMC SANORD','?module=cms&action=showsection&id=gen22Srv38Nme10_6390_1223365894&sectionid=gen22Srv38Nme10_6390_1223365894',null],
                //['SANORD Activities','?module=cms&action=showsection&id=gen22Srv38Nme10_4825_1215600644&sectionid=gen22Srv38Nme10_4825_1215600644',null],
               // ['Research Groups','?module=cms&action=showsection&id=gen22Srv38Nme10_5805_1214398614&sectionid=gen22Srv38Nme10_5805_1214398614',null],
               // ['News and Activities','?module=cms&action=showsection&id=gen22Srv38Nme10_1452_1215599912&sectionid=gen22Srv38Nme10_1452_1215599912',null],//['Press Releases','?module=cms&action=showsection&pageid=gen22Srv38Nme10_9321_1215600107&id=gen22Srv38Nme10_1452_1215599912&sectionid=gen22Srv38Nme10_1452_1215599912',null],
                //],
            ],
            ['SAIAMC',null, null,
               // ['Council Members','?module=cms&action=showsection&id=gen22Srv38Nme10_6390_1223365894&pageid=gen22Srv38Nme10_1202_1223375306&sectionid=gen22Srv38Nme10_6390_1223365894', null],
                //['Board Members', '?module=cms&action=showsection&id=gen22Srv38Nme10_6390_1223365894&pageid=gen22Srv38Nme10_7257_1223375968&sectionid=gen22Srv38Nme10_6390_1223365894',null],
               // ['Nomination Committee','?module=cms&action=showsection&id=gen22Srv38Nme10_6390_1223365894&pageid=gen22Srv38Nme10_4897_1223376049&sectionid=gen22Srv38Nme10_6390_1223365894',null],

            ],
            ['HySa', null,null,

            ],
            ['ACER', null, null,
                //['Scholarships','http://sanord.uwc.ac.za/index.php?module=cms&action=showsection&id=gen22Srv38Nme10_1452_1215599912&pageid=gen22Srv38Nme10_5003_1237969994&sectionid=gen22Srv38Nme10_1452_1215599912', null],
               // ['Masters Programmes','?module=cms&action=showsection&id=gen22Srv38Nme10_1452_1215599912&pageid=gen22Srv38Nme10_1788_1242296149&sectionid=gen22Srv38Nme10_1452_1215599912', null],
             //   ['PhD Programmes','?module=cms&action=showsection&pageid=gen22Srv38Nme10_6998_1221146087&id=gen22Srv38Nme10_1452_1215599912&sectionid=gen22Srv38Nme10_1452_1215599912', null],
              //  ['Exchange Programmes','?module=cms&action=showsection&pageid=gen22Srv38Nme10_2572_1217602199&id=gen22Srv38Nme10_1452_1215599912&sectionid=gen22Srv38Nme10_1452_1215599912', null],
               // ['Summer/ Winter Schools','?module=cms&action=showsection&pageid=gen22Srv38Nme10_5667_1221149121&id=gen22Srv38Nme10_1452_1215599912&sectionid=gen22Srv38Nme10_1452_1215599912', null],

            ],

         ['Contact', null, null,            

               ];


 //['Logout', null, null,            

               ];

            <?
    $this->objUser =  $this->getObject('user', 'security');

    if ($this->objUser->isLoggedIn()) {


    ?>
            ['Logout','?module=security&action=logoff',null],
    <? }

?>
        ];

    </script>

    <script language="JavaScript" src="skins/sanord.com/includes/tigra_2.0.1/menu_tpl.js"></script>

</head>

<?php


    if (isSet($bodyParams)) {
        echo "<body " . $bodyParams . ">";
    } else {
        echo '<body class="'.$this->getParam('module', 'cms').'">';
    }

    if (!isset($pageSuppressContainer)) {
        echo '<div id="container">';
    }

    if (!isset($pageSuppressBanner)) {
    ?>
<div id="headerwrapper">
    <div id="header">
        <h1 id="sitename"><span><?php echo $objConfig->getsiteName();?></span></h1>
        <?php
                 /*Add Logout link if user is logged in
                $this->objUser =  $this->getObject('user', 'security');
                $logOutStr='';
                if ($this->objUser->isLoggedIn()) {
                    $this->loadClass('link', 'htmlelements');
                    $link = $this->uri(array('action'=>'logoff'), 'security');
                    $link->link=$objLanguage->languageText("word_logout");
                    //$logOutStr .= $this->prepareItem('Logout', $link);
                    echo $link->link;
                }*/

            if(!isset($pageSuppressSearch)){
                //  echo $objSkin->siteSearchBox();
            }

        ?>
    </div>
    <?php
        if (!isset($pageSuppressToolbar)) {
        ?>

    <div id="java_menu">

        <div id="sanord_menubar">
            <script type = "text/javascript">
                <!--
                new menu (MENU_ITEMS, MENU_TPL);
                -->
            </script>
        </div>
    </div>

        <?
            //  echo $toolbar;
        }
    }
?>
    <div style="float:right;margin:0 0 ; /**background-color: red;**/ " id="search">
        <?echo $objSkin->siteSearchBox(); ?>

    </div>
    <span id="captureStartContent">
    <?php
        // Get content
        echo $this->getLayoutContent();
    ?>
    <span id="captureEndContent">
    <?php
        // Render the footer
        if (!isset($suppressFooter)) {
            // Create the bottom template area
            $this->footerNav = & $this->newObject('layer', 'htmlelements');
            $this->footerNav->id = 'footer';
            $this->footerNav->cssClass='';
            $this->footerNav->position='';
            $this->footerNav->str = "";
                /*if (isset($footerStr)) {
                    $this->footerNav->str = $footerStr;
                } else if ($objUser->isLoggedIn()) {
                    $this->loadClass('link', 'htmlelements');
                    $link = new link ($this->URI(array('action'=>'logoff'),'security'));
                    $link->link=$objLanguage->languageText("word_logout");
                    $str=$objLanguage->languageText("mod_context_loggedinas", 'context').' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
                    $this->footerNav->str = $str;
                }*/
            echo $this->footerNav->show();
        }


        //Render the container closing div
        if (!isset($pageSuppressContainer)) { ?>
</div>
<?php
}
$this->putMessages();

/*
<!---
Google analytics code

Please note that if you adapt this skin for your own
site, you must change the code from UA-1632289-2 to your
own code or it will not work.

--->
*/
$pageCode = $this->getParam('module','cms')
. "::" . $this->getParam('action', NULL);
?>
</span>
</span>
<script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
    var pageTracker = _gat._getTracker("UA-1632289-2");
    pageTracker._initData();
    pageTracker._trackPageview('<?php echo $pageCode?>');
</script>
<?php
    // apture.com script and snapshot script
    $curHost = $_SERVER['HTTP_HOST'];
    if ($curHost == "www.dkeats.com") {
        $act = $this->getparam('action', NULL);
        $disableapture = $this->getparam('disableapture', FALSE);
        //die("Offline momentarily: back in < 2 minutes" . $act . "++++" . $disableapture);
        if ($act !== "edit"
            && $act !=="add"
            && $act !== "blogpost"
            && $disableapture !=="true") {
        ?>
<!--apture.com script-->
<script id='aptureScript' type="text/javascript" src="http://www.apture.com/js/apture.js?siteToken=64jIwXF" charset='utf-8'></script>
<!--snap.com script-->
<script type="text/javascript" src="http://shots.snap.com/ss/6123827ab5edd5046f8a8d2608b8c0f0/snap_shots.js"></script>
<?php
}
}
?>
<script type="text/javascript">
    //<[CDATA[
    (function() {
        var links = document.getElementsByTagName('a');
        var query = '?';
        for(var i = 0; i < links.length; i++) {
            if(links[i].href.indexOf('#disqus_thread') >= 0) {
                query += 'url' + i + '=' + encodeURIComponent(links[i].href) + '&';
            }
        }
        document.write('<script type="text/javascript" src="http://disqus.com/forums/dkeats/get_num_replies.js' + query + '"></' + 'script>');
    })();
    //]]>
</script>
</body>
</html>
