<?php header("Content-type: text/css"); ?>
<?php include('stylesheet_info.php');  ?>
<?php include('css_not_touching.css');  ?>

/*******************************************************************************
*  BEGIN: layout for normal page elements                                      *
*******************************************************************************/
/* specify the body with other words the look and feel of all 
the pages, their text, font, background etc. */

body {
	color: #000; 
	background: #fff; 
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 90%; 
	text-align: center;
	margin: 30px 0;
}

p  {border-top: 0px;}

/*body { 
		font-size: <?php echo $fontsize; ?>;
		color: <?php echo $fontcolor; ?>;
		font-family: <?php echo($bodyfont); ?>;
		background-color: <?php echo $bodycolor; ?>;
		margin-top: 0px;
		margin-left: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
}

p {
    color: <?php echo $fontcolor; ?>;
	font-family: <?php echo($bodyfont); ?>;
}*/

/* This is the spaces or line breaks on the pages, Chantel made 
this one white because she didn't want it to be vissible. */
/*
Internet Explorer defines HR using COLOR whilst Mozilla and Opera uses BACKGROUND-COLOR
Please keep both elements but have them as the same color
*/
hr {
    color: <?php echo $bordercolor2; ?>;
    background-color: <?php echo $bordercolor2; ?>;
    border: 0;
    height: 0.5px;
}

/* This is the headers of the textareas or for example the welcoming text on the pages. */

h1 {
	font-family: arial, helvetica, sans-serif;
	color: #6A6C76;
	font-size: 1.4em;  
	/*padding: 10px 0; */
	padding-bottom: 10px;
	margin: 0;
}

h2 {
	font-family: arial, helvetica, sans-serif;
	color: #6A6C76;
	font-size: 1.2em;  
	/*padding: 10px 0; */
	padding-bottom: 10px;
	margin: 0;
}

h3 {
	font-family: arial, helvetica, sans-serif;
	color: #6A6C76;
	font-size: 1.0em;  
	/*padding: 10px 0; */
	padding-bottom: 10px;
	margin: 0;
}

/*h1, h2, h3, h4, h5, h6 {
	color: <?php echo $headercolor; ?>; 
	font-family: <?php echo $headerFont; ?>;
}

h1 {
    font-size: 190%;
}

h2 {
    font-size: 170%;
}

h3 {
    font-size: 150%;
}*/

h4 {
    font-size: 130%;
}

h5 {
    font-size: 110%;
}

h6 {
    font-size: 100%;
}

/* This is the specification for the text on the pages.*/
.text {
	color: <?php echo $fontcolor; ?>;
	font-size:100%;
	font-family: <?php echo($bodyfont); ?>;
	font-weight: normal;
	text-align: left;
} 

/* Create a transparent background */
.transparentbg {
	background-color: transparent;
}

/* Create a transparent background with no border */
.transparentbgnb {
	background-color: transparent;
	border: 0;
}

iframe {
    border: 0;
}

/*******************************************************************************
*  END: layout for normal page elements                                        *
*******************************************************************************/

/*******************************************************************************
*  BEGIN: layout for elements used on forms                                    *
*******************************************************************************/

/* specify the form on everypage, we do not want forms with borders in them. */
form {
    margin:0px;
}



/* This specify the buttons */
input.button, button.button  {
	background-repeat: repeat;
	background-color: #C1570A;
	/*background-image: url(images/arty_button1.jpg);*/
	height: auto;
	width: auto;
	font-family: <?php echo($bodyfont); ?>;
	font-size: 100%;
	color: #fff; /*#403A90;*/
	border: none;
	font-style: normal;
	text-align: center;
	font-family: Arial, Helvetica, sans-serif; 
	font-weight: bold;
	white-space: normal;
	background-position: center center;
	vertical-align: middle;
	margin: 3px;
	/*padding: 2px 3px 2px 3px;*/
}

/* Specify the text in the textbox field */
input.text {
	font-weight: normal;
	font-size: 90%;
	color: <?php echo $formfontcolor; ?>;
	font-family: <?php echo($bodyfont); ?>;
}

/*used for error in input textbox */
.inputerror {
	color: white;
	font-family : <?php echo($bodyfont); ?>;
	font-size : 90%;
	background-color: red; 
}

/* specify the textarea in the textareafield. */
textarea {
	color: <?php echo $formfontcolor; ?>;
	font-family : <?php echo($bodyfont); ?>;
	font-size : 90%;
	font-weight : normal;
	background-color: <?php echo($formelementbkg); ?>; 
    border-top-color: <?php echo $formborder; ?>;
	border-right-color: <?php echo $formborder; ?>;
    border-bottom-color: <?php echo $formborder; ?>;
}


select /* specify the selected area in the textbox field. */
{
	font-weight: normal;
	font-size: 90%;
	color: <?php echo $formfontcolor; ?>;
	font-family: <?php echo($bodyfont); ?>;
	background-color: <?php echo($formelementbkg); ?>;
    border-top-color: <?php echo $formborder; ?>;
    border-left-color: <?php echo $formborder; ?>;
	border-bottom-color: <?php echo $formborder; ?>;
	border-right-color: <?php echo $formborder; ?>;
}

/* Width of the drop down to choose a course. Can be made relative to the sides*/
select.coursechooser, select.dropdownOnSideBar, div#leftnav select, div#rightnav select {
	width: <?php echo $columnWidth-20; ?>px;
}


/*******************************************************************************
*  END: layout for elements used on FORMS                                      *
*******************************************************************************/

/*******************************************************************************
*  BEGIN: layout for TABLE elements                                            *
*******************************************************************************/

/* specify the table cell default properties. */
td {
	font-family: <?php echo($bodyfont); ?>; 
	color: <?php echo $fontcolor; ?>; 
    font-size:100%;
}

/* specify the heading of the text inside the tables */
thead, th, td.heading, tr.heading {
	font-family:<?php echo($bodyfont); ?>;
	color: <?php echo($tableHeaderForeColor); ?>;
	font-size:100%;
	background: <?php echo($tableHeaderBkg); ?>;
	font-weight: bolder;
}

/* Specify the background of table cells in odd rows */
td.odd, tr.odd {
	background-color: <?php echo($tableOddRows); ?>;
}

/* Specify the background of table cells in even rows */
td.even, tr.even {
	background-color: <?php echo($tableEvenRows); ?>;
}



/* Specify the background colour of teh table cells for 
   recordset navigation */
.rsnav {
	background-color: <?php echo($containerBkg); ?>;
}



/* Specify the background for tableruler */
td.tbl_ruler, tr.tbl_ruler {
	background-color: <?php echo($tableRuler); ?>;
}


table.tableLightBkg, tr.tableLightBkg, td.tableLightBkg {
    background-color: #FFFFFF;
}



/*******************************************************************************
*  END: layout for table elements                                              *
*******************************************************************************/


/***********************************************************************************************
*  BEGIN: Anchor links for text, buttons                                                       *
***********************************************************************************************/

a, a:link{color: #C1570A; text-decoration: none;}
a:visited{color: #C1570A; text-decoration: none;}
a:hover, a:active{color: #F8AC0A;}

.menubar a, .menubar a:link{color: #fff; text-decoration: none;}
.menubar a:visited{color: #fff; text-decoration: none;}
.menubar a:hover, .menubar a:active{color: #d6d6d6;}



/*A:link {
	background: transparent;
	color: <?php echo $linkColor; ?>;
	text-decoration: underline;
	font-weight : bold;
}

A:visited {
		COLOR: <?php echo $linkColor; ?>;
		TEXT-DECORATION: underline; 
		background: none;
		font-weight : bold;
}

A:hover {
	background: #ffffff;
	color: <?php echo $linkColor; ?>;
	text-decoration: underline;
	font-weight : bold;
}

body of every page
A:visited:hover {
		COLOR: <?php echo $linkColor; ?>;
		TEXT-DECORATION: underline;
		background: #ffffff;
		font-weight : bold;
}

A:active {
	background: none transparent scroll repeat 0% 0%;
	color: <?php echo $linkColor; ?>;
	text-decoration: underline;
	font: bold;
}*/

/* Set of anchor classes for making toolbar buttons and that can
   be used for any buttons A 
*/

A.pseudobutton {
	color: #FFFFFF;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 90%;
	text-decoration: none;
	font-weight: normal;
	background: url(images/buttons/button.jpg);
    padding-left: 5px;
    padding-right: 5px;
}

A.pseudobutton:visited {
	color: #FFFFFF;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 90%;
	text-decoration: none;
	font-weight: normal;
	background: url(images/buttons/button.jpg);
    padding-left: 5px;
    padding-right: 5px;
}
	
A.pseudobutton:hover, A.pseudobutton:visited:hover  {
	color: #ffffff;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 90%;
	text-decoration: none;
	font-weight: normal;
	background: url(images/buttons/button_over.jpg);
    padding-left: 5px;
    padding-right: 5px;
}


a.pseudobutton:active  {
    color : #ffffff;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 90%;
	text-decoration: none;
	font-weight: normal;
    background-image: url(images/buttons/button_over.jpg);
    padding-left: 5px;
    padding-right: 5px;
}

/***********************************************************************************************
*  END: Anchor links for text, buttons                                                         *
***********************************************************************************************/

/*******************************************************************************
*  BEGIN: Accessiblity Tags														   *
*******************************************************************************/

/* Border Color and padding of the <fieldset> */
fieldset {
    border:1px solid <?php echo($bordercolor); ?>;
    margin: 3px;
    padding: 3px;
 }

/*******************************************************************************
*  End: Accessiblity Tags														   *
*******************************************************************************/



/*************************************************************************************************
*  BEGIN: SECTION FOR THREE COLUMN AND TWO COLUMN LAYOUTS *
*************************************************************************************************/

/* the container for all of the divs that make up the layout */
div#container {
   width: 99%;
   margin: 10px auto;
   background-color: <?php echo $containerBkg; ?>;
   border: 1px solid <?php echo($bordercolor); ?>;
   border-bottom-width: 0px;
   line-height: 130%;
}

/* a class with containers background colour that can be applied to other elements, eg. body */
.containerBackground {
    background-color: <?php echo $containerBkg; ?>;
}

/* the div for the top banner */
div#top {   
   
   padding: 0;
   background-color: <?php echo($topdivbkg); ?>;
   border-bottom: 1px solid <?php echo($bordercolor); ?>;
   background-image: url(banners/banner_background.jpg);
}

div#toolbar {
    padding: 0;
    background-color: <?php echo($toolbarBkg); ?>;
    border-bottom: 1px solid <?php echo($bordercolor); ?>;
}

/* the left col area*/
div#leftnav {
   float: left;
   width: 250px;/*<?php echo $columnWidth; ?>px;*/
   font-size: 11px;
}

/* Fix the for the input box on a sidebar */
#leftnav input.prelogin {
    width: <?php echo ($columnWidth-20); ?>px;
}

/* the right col area */
div#rightnav {
   float: right;
   width:<?php echo $rightcolumnWidth; ?>px;
}

/* the left and right col areas */
div#leftnav, div#rightnav {
   /*width: <?php echo $columnWidth; ?>px;*/
   margin : 0 0 1em 0;
   padding: 1em;
   background-color: <?php echo $containerBkg; ?>;  
}


/* 

The content areas:
div#content is for both left and right margins
div#contentHasLeftMenu is for content with a left margin only

*/


div#content, div#contentHasLeftMenu {
   padding: 1em;
   /*background-color: <?php echo $contentBkg; ?>;*/	
}

div#content {
    margin-left: <?php echo $columnWidth+25; ?>px;
   border-left: 1px solid <?php echo($bordercolor); ?>;
   margin-right: <?php echo $columnWidth+25; ?>px;
   border-right: 1px solid <?php echo($bordercolor); ?>;  
  
}

div#contentHasLeftMenu {
    margin-left: <?php echo $columnWidth+150; ?>px;
    border-left: 1px solid #908F8F;
   
   /*border-left: 1px solid <?php echo($bordercolor); ?>;*/
}



/* the footer area of the cols */
div#footer {
   margin: 0;
   padding: 1em;
   background-color: <?php echo $footer; ?>;
   /*border-bottom: 1px solid <?php echo($bordercolor); ?>;*/
   /*border-top: 1px solid <?php echo($bordercolor); ?>;*/
}

.content {
   border-left: 1px solid <?php echo($bordercolor); ?>;
   padding: 1em;
   background-color: <?php echo $contentBkg; ?>;
}

/**************************************************************************************************
*  END: SECTION FOR THREE COLUMN AND TWO COLUMN LAYOUTS *
***************************************************************************************************/

/*******************************************************************************
*  BEGIN: special layout for page elements                                     *
*******************************************************************************/

/* Confirm message that action has occurred successfully */
.confirm, #confirm 
{
    color: #000000;
    background-color: #FFDE39; // A dark yellow
}

/* Use for text that is greyed out or dimmed for some reason */ 
.dim {
	color: #666666;
}

/* Specify certain text that for error messages */
.error 
{
	color: red;
}

.HighLightText
{
	background-color: <?php echo($highlightTextBkg); ?>;
	color: #ffffff;
}

.warning /* Specify certain text that you want people to notice or beware of for example: "Please note that the suggestion box 
         is for suggestions about KEWL". */
{
	color: orange;
    font-weight: bold;
    padding: 2px;
}

/*  This specify the color of the notes or short little descriptive wording, shown on the login page and 
        after you've logged in for example the following text:"Note this is still very experimental and there is 
        no translation for most things". */
.minute 
{
	font-size: 85%;
	color: <?php echo($minuteTextColor); ?>;
}


/* Font of warning to say theres no posts in a forum - at start of forum*/
.noRecordsMessage { 
    font-style: oblique;
    text-align: center;
    font-weight: normal;
    padding-top: 2em;
    padding-bottom: 2em;
    font-size: large;
}

.wrapperLightBkg {
    background-color: <?php echo($wrapperLightBkg); ?>;
    padding: 3px;
}

.wrapperDarkBkg {
    background-color: <?php echo($wrapperDarkBkg); ?>;
    padding: 3px;
}

/*******************************************************************************
*  END: special layout for page elements                                       *
*******************************************************************************/


/*************************************************************************************************
*  BEGIN: Styles to use for HELP                                                                  *
*************************************************************************************************/

body.help-popup {
	    padding: 1em 1em 1em 1em;
		background-image: url(images/helpbg.gif);
}

/**************************************************************************************************
*  END: Styles to use for HELP                                                                    *
**************************************************************************************************/



/******************************************************************************************
*  BEGIN: SECTION FOR THE CHAT															  *
*  It can be used for other layouts as well.                                              *
******************************************************************************************/

/* Chat Meta is for instructions that appear on screen. e.g. user enters the room*/
.chat-meta {
	color: green;
}


/* color for private messages between users in the chat*/
.chat-private {
	color: red;
}
/******************************************************************************************
*  END: SECTION FOR THE CHAT															  *
******************************************************************************************/



/******************************************************************************************
*  MOUSE OVER POPUP																		  *
******************************************************************************************/


/* Default DOM Tooltip Style */
div.domTT {
    border: none;
    margin: 0em 0em 2em 0em;
    padding: 0;
}
div.domTTCaption {
    background: <?php echo $boxBkg2; ?>;
    border: 1px solid <?php echo($bordercolor); ?>;
    border-style: solid solid none solid;
    padding: 0em 1em 0em 1em;
    text-transform: none;
	text-decoration: none;
    font-size: 100%;
    font-weight: bold;
	cursor:hand;
}
div.domTTContent {
     background: transparent;
    border-collapse: collapse;
    border: 1px solid <?php echo($bordercolor); ?>;
	padding: 3px;
	overflow: auto;
	background: <?php echo $bodycolor; ?>;
}



/******************************************************************************************
*  END OF MOUSE OVER POPUP																		  *
******************************************************************************************/


/******************************************************************************************
*  BEGIN: SECTION FOR THE BLOG TEMPLATE (/modules/blog/templates/content/main_tpl.php)    *
*  It can be used for other layouts as well.                                              *
******************************************************************************************/

/*The blog container*/
#blog {
   width: 94%;
}

/*The container for the blog content*/
#blog-content {
    background-color: #FAF5ED;
    border-collapse: collapse;
	border: 1px solid <?php echo $boxBorder; ?>;
	border-left: 1px;
	border-right: 1px;
	padding: 12px;
	text-align:left;
}

/*The footer to appear at the bottom of the page*/
#blog-footer {
    background-color: #FAFAFA;
	color: <?php echo $bordercolor2; ?>;
    border-collapse: collapse;
    border: 1px solid <?php echo $boxBorder; ?>;
	border-bottom: 2px;
	border-left: 1px;
	border-right: 1px;
	padding: 3px;
	font-family: <?php echo($bodyfont); ?>;
	font-size:80%;
	text-align:center;
}

#blheadline { 
    background: <?php echo $boxBkg2; ?>;
    border: 1px solid <?php echo $boxBorder; ?>;
    border-style: solid solid none solid;
	border-left: 1px;
	border-top: 3px;
	border-right: 1px;
    color: #666666;
    padding: 4px;
    text-transform: none;
	font-family:<?php echo($bodyfont); ?>;
    font-size: 110%;
	font-weight:bold;
	text-align:center;
	vertical-align:middle;
}

#bltitle { 
    background-color: #003333;
    border: 1px solid <?php echo $boxBorder; ?>;
    border-style: solid solid none solid;
	border-top: 3px;
	border-left: 3px;
	border-right: 3px;
    color: white;
    padding: 4px;
    text-transform: none;
	font-family:<?php echo($bodyfont); ?>;
    font-size: 110%;
	font-weight:bold;
	text-align:center;
	vertical-align:middle;

}

/* the footer area for the end of the blog */
#blog-outside-footer {
   clear: both;
   margin: 0;
   padding: 1em;
   color: #666666;
   background-color: #CCCCCC;
   border-bottom: 1px solid <?php echo($bordercolor); ?>;
   border-top: 1px solid <?php echo($bordercolor); ?>;
   font-family:<?php echo($bodyfont); ?>;
   font-size: 80%;
}

/* TD for right blog */
.blog-right {
   background-color: #FFFFDF;
   padding: 1em;
   border: 1px solid <?php echo $boxBorder; ?>;
   vertical-align:top;
   font-family:<?php echo($bodyfont); ?>;
   color: #666666;
}

/*The container for the blog comments on odd rows*/
#blog-comment-odd {
    background-color: #FFFFCC;
    border-collapse: collapse;
	border: 1px solid <?php echo $boxBorder; ?>;
    border-bottom: 1px dotted black;
	border-left: 1px;
	border-right: 1px;
	padding: 12px;
	text-align:left;
}

/*The container for the blog comments on even rows*/
#blog-comment-even {
    background-color: #F3F2DA;
    border-collapse: collapse;
	border: 1px solid <?php echo $boxBorder; ?>;
   	border-bottom: 1px dotted black;
	border-left: 1px;
	border-right: 1px;
	padding: 12px;
	padding-left: 22px;
	text-align:left;
}

/*The container for the blog comments on odd rows*/
#blog-comment-label {
    background-color: #6699CC;
    border-collapse: collapse;
	border: 1px <?php echo $boxBorder; ?>;
	border-top: 3px;
    border-style: solid solid dotted solid;
	border-left: 1px;
	border-right: 1px;
	border-bottom: 1px red;
	padding-left: 12px;
	padding-right: 12px;
	padding-top: 2px;
	padding-bottom: 1px;
	text-align:left;
	font-weight:bolder;
}

/*Table row for listing 10 mose recent blogs */
TD.blog-listtitle {
	background-color:  #F1F8F8;
	font-size:10px;
	padding: 0;
	
}

/*Table row for listing 10 mose recent blogger names */
TD.blog-listname {
	background-color:#F3F3F3;
	font-size: 80%;
}

/* specify the heading of the text inside the with a small font */
.small-heading {
	font-family:<?php echo($bodyfont); ?>;
	color: <?php echo $fontcolor; ?>;
	font-size:90%;
	background-color: #CCCCCC;
}

/***********************************************************************************************
*  END: SECTION FOR THE POST BLOG TEMPLATE (/modules/blog/templates/content/main_tpl.php)      *
***********************************************************************************************/

/******************************************************************************************
*  DISCUSSION FORUM																	  *
******************************************************************************************/

.forumContainer {
   width: 95%;
   margin: 0px;
   margin-left: 10px;
   padding: 0px;
   background-color: <?php echo $contentBkg; ?>;
   border: 1px solid <?php echo($bordercolor); ?>;
   line-height: 130%;
}


.forumTangentIndent {
    margin: 10px;
    padding: 5px;
    background-color: #ffffcc;
    border: 1px dotted #800080
}

.forumLeftBar {
   /* default width */
   width: 160px;
   margin: 0;
   margin : 0 0 0 0;
   float: left;
   height: 100%;
   padding: 5px;
}

.forumTopic {
   padding: 5px;
   background-color: <?php echo $footer; ?>;
   border-left: 1px solid <?php echo($bordercolor); ?>;
   margin: 0px;
}

.forumContent {
    margin: 0px;
   border-left: 1px solid <?php echo($bordercolor); ?>;
   border-top: 1px solid <?php echo($bordercolor); ?>;
   padding: 5px;
   background-color: #FFFFFF;
}


/* An alternative container when you don't want to use the left side bar*/
div.topicContainer {
border-top: 1px solid #808080;
border-right: 1px solid #808080;
border-bottom: 1px solid #808080;
}


/******************************************************************************************
*  END OF DISCUSSION FORUM																	  *
******************************************************************************************/


/**************************************************************************************************
*  BEGIN: Styles to use for GROUPADMIN                                                            *
**************************************************************************************************/

#treecontent {
    margin:0;
    margin-left: 12em;
    padding: 1em;
}

#treenav {
    width: 12em;
    overflow: auto;
	float : left;
    padding: 1em;
}

#nodeinfo {
    float : right;
    border:0;
	width: 12em;
	padding: 1em;
}

#nodecontent {
    margin-left: 0;
    margin-right: 15em;
}

#formline {
    padding: 0.5em 0 0.5em 0  ;
}

#formlabel {
    float:left;
    text-align:right;
    width: 13em;
}

#formelement {
    margin-left : 15em;
}


/**************************************************************************************************
*  END: Styles to use for GROUPADMIN                                                               *
**************************************************************************************************/







/***********************************************************************************************
*  BEGIN: Sectoin to use when debugging layers                                                 *
***********************************************************************************************/

debug-layers {
	border-style: dashed;
	border-color:#FF0000;
}

/***********************************************************************************************
*  END: Sectoin to use when debugging layers                                                   *
***********************************************************************************************/


/***********************************************************************************************
*  BEGIN: Styles to use for creating tabbed boxes such as the login one                        *
***********************************************************************************************/

fieldset.tabbox {
	border: 1px solid <?php echo $boxBorder; ?>;
	margin:0;
    margin-bottom: 10px;
	padding:10px;
}
legend.tabbox {
	background-color:<?php echo $boxBkg2; ?>;
	/*border: 1px solid <?php echo $boxBorder; ?>;*/
	color:#000000;
	font-family:Arial, Helvetica, sans-serif;
    font-weight:bold;
    padding-left: 5px;
    padding-right: 5px;
}

fieldset.tabbox ul {
    margin-left: 0px;
    padding-left: 15px;
}


	
/***********************************************************************************************
*  END:   Styles to use for creating tabbed boxes such as the login one                        *
***********************************************************************************************/


/***********************************************************************************************
*  BEGIN: Styles to use for creating multitabbed boxes like the ones used in a context         *
***********************************************************************************************/


div.multibox {
    border: none;
    margin: 0em 0em 2em 0em;
    padding: 0;
}

div.multibox div.multibox-content {
    background: transparent;
    border-collapse: collapse;
    border: 1px solid <?php echo $boxBorder; ?>;
	padding: 3px;
	background: <?php echo $boxBkg; ?>;
}

div.multibox .multitablabel { 
    background: <?php echo $boxBkg2; ?>;
    border: 1px solid <?php echo $boxBorder; ?>;
    border-style: solid solid none solid;
    padding: 0em 1em 0em 1em;
    text-transform: none;
	text-decoration: none;
    display: inline;
    font-size: 1em;
    height: 1em;
	cursor:hand;
}
div.multibox .multitabselected {
	background: <?php echo $boxBkg; ?>;
    border: 1px solid <?php echo $boxBorder; ?>;
    border-style: solid solid none solid;
    padding: 0em 1em 0em 1em;
    text-transform: none;
    display: inline;
    font-size: 1em;
    height: 1em;
	cursor:hand;
	text-decoration: none;
}


/***********************************************************************************************
*  END:   Styles to use for creating multitabbed boxes like the ones used in a context         *
***********************************************************************************************/




/*******************************************************************************
*  BEGIN: layout for display such as used in the viewsource module             *
*******************************************************************************/

/*Display title above display box */
.code-display-title {
	border-color: <?php echo $bordercolor2; ?>;
	background-color: #CCCCCC;
	border-width: medium;
	font-family:<?php echo($bodyfont); ?>;
	text-align:center;
}

/*Display box mainly for source code display*/
.code-display {
	border-color: <?php echo $bordercolor2; ?>;
	border-collapse:collapse;
	border-width:thin;
	font-family:<?php echo($bodyfont); ?>;
	background-color:#F8F8F8;
	text-align:left;
}



/*Display title above display box */
.code-display-footer {
	border-color: #333333;
	background-color: #CCCCCC;
	font-family:<?php echo($bodyfont); ?>;
	text-align:center;
	background-color: #CCCCCC;
   	border-bottom: 1px solid <?php echo($bordercolor); ?>;
}






/*******************************************************************************
*  BEGIN: Internal Email
*******************************************************************************/

.emailNew 
{
	background-color: <?php echo($newEmailBkgColor); ?>;
	color: #000000;
}

.emailNew:link, td.emailNew a
{
	color: #000000;
}

.emailOld
{
	background-color: #FFFFFF;
	color: #000000;
}

.emailInfo
{
	background-color: #FAFAFA;
	color: #000000;
}

/*******************************************************************************
*  End : Internal Mail                    *
*******************************************************************************/


/*******************************************************************************
*  BEGIN: Instant Messaging                    *
*******************************************************************************/

body.instantmessaging {
    background-color: #FF9900;
}

/* Instant Messaging - Hidden Iframe */
#IM {
    display:none;
}

#instantmessaging_main {
    margin:0px;
    padding:10px; 
    border: 0;
    margin-right:61px;
    background-image: url(images/im_bkg.jpg);
    background-repeat: repeat-x;
    background-attachment: fixed ;
    background-position: left top;
}

.instantmessaging_height {
    height: 319px;
    overflow: auto;
}

#instantmessaging_side {
    width: 61px;
    float:right;
    height: 339px;
    margin:0;
    padding:0;
    border:0;
    margin-top:1px;
}

#instantmessaging_footer {
    clear:both;
    margin:0;
    padding:0;
    padding: 15px;
    text-align: center;

}

/*******************************************************************************
*  END: Instant Messaging                      *
*******************************************************************************/



/**********************************************************************************************
* BEGIN: style for two levelled navigation
**********************************************************************************************/


ul.twolevel {
 list-style-image:url(../_common/icons/smallsquares.png);
 margin-top:0px;
 margin-right:0px;
 /*margin-left:-30px;*/
 /*marker-offset: 3px;*/
 /**width:200px;*****/ 
}

ul.twolevelselected {
 list-style-image:url(../_common/icons/smallsquares.png);
 margin-top:0px;
 margin-right:0px;
 /*margin-left:-30px;*/
 padding-right: 0px;
 /**width:200px;*****/ 
}
/***********************************************************************************************
* END: style for two levelled navigation
***********************************************************************************************/


/*** SECTION FOR CODEBLOCKS IN TEXT *****/
.cdblk {
	font-family: "Times New Roman", Times, serif;
	font-size: small;
	font-style: normal;
	background-color: #FFFFFF;
	margin: 10px;
	padding: 4px;
	border: thin dashed #CCCCCC;
	width: 80%;
}



/*******************************************************************************
*  BEGIN: layout for elements used to display old mail   - Mailman                      *
*******************************************************************************/
/*Display box mainly for old mail display*/
.old-mail {
	border-color: <?php echo $bordercolor2; ?>;
	width: 80%;
	border-collapse:collapse;
	border-width:thin;
	font-family:<?php echo($bodyfont); ?>;
	background-color:lightyellow;
	text-align:left;
}
/*******************************************************************************
*  END: layout for elements used to display old mail                           *
*******************************************************************************/

/*******************************************************************************
*  BEGIN: style for steps * 
*******************************************************************************/

#step {
	font-size: 30px;
	font-weight: bold;
	text-align: left;
	color: #666666;
	padding: 10px 0px 20px 80px;
	white-space: nowrap;
	position: relative;
	float: left;
}

.step-on {
	color: #ff9900;
	background: #30559C;
	font-weight: bold;
	font-size: 13px;
	padding: 10px;
	border: 1px solid #cccccc;
	margin-bottom: 2px;
}

.step-off {
  font-size: 13px;
	color: #999999;
	font-weight: bold;
	padding: 10px;
	border: 1px solid #cccccc;
	margin-bottom: 2px;
}

/*******************************************************************************
*  END:  styles for steps
*******************************************************************************/

.newForumContainer {
    margin: 10px;
    border: 1px solid black;
}

.newForumTopic {
    background-color: <?php echo $footer; ?>;
    padding: 5px;
}
.newForumContent {
    clear: both;
    background-color: white;
    border-top: 1px solid black;
    padding: 5px;
}

.forumUserPicture {
    float:left;
}

.forumTopicTitle {
    margin-left: 50px;
}

.smallText {
    font-size: 90%;
}
*******************************************************************************
*
*******************************************************************************/
/*******************************************************************************
*                             NEW LAYOUT STUFF
*******************************************************************************/
/*******************************************************************************
*
*******************************************************************************/

.clear { clear: both; }

.alignright {margin-top: 0; text-align: right; clear: both;}

.small {font-size: .9em;}

.wide {
background: #fff;    /* header and footer colours */
width: 100%; 
font-size: 1.6em;
font-weight: bold;
color: #ffa;
text-align: center;
}

.wide p {margin: 0; padding: 5px; position: relative;}

.top {border-bottom: 1px solid #000;}

.top a {position: absolute; right: 20px; text-align: right; font-weight: bold; font-size: .7em; color: #fff;}

.bottom {/*border-top: 1px solid #000;*/ position: relative;}

.testfloat {width: 50px; border: 2px solid #f90; background: #ffd; font-size: .7em;}

.to-right {float: right;}

.to-left {float: left;}

.ap-test {
position: absolute; 
top: -102px; 
font-size: .6em; 
width: 150px; 
height: 100px; 
border-top: 1px solid #000; 
background: #5b5; 
color: yellow;
}

/* \*/
* html .ap-test {
top: -101px; 
t\op: -102px; 
}
/* */

.ap-left {left: 0;}

.ap-right {right: 0;}

.paddingleft{padding-left:30px; padding-right:5px; padding-top:25px;}
.paddingright{padding-right:25px; padding-left:5px; padding-top:25px;}
.padded{padding:25px 25px 10px 15px;}

.wide-top{
background: #ffffff url(bannerbg.gif);
}

.menubar{
/*margin-left: 20px;
margin-right: 20px;*/
background:url(banners/bgmenu.gif) repeat-x;
background-color: #8ac6da;
padding-bottom: 5px;
}

.menuoptions{
padding:10px 5px 10px 14px;
/*background-color: #000;*/

}

/* for the breadcrumbs */
.breadcrumbs{
padding:10px;
border-bottom: 1px solid #908F8F;
padding-top: 37px; 
/*padding: 5px 5px 0px 14px; */
background-color:#fff;
}

.tabs {
padding:0px;
background-color: #8ac6da;
border-bottom: 1px solid #908F8F;
}

.outer {
overflow: on;
width: 100%;
/*height: 1000px;*/

}

.firsttab {
	float:left;
	width: 188px;
	height: 29px;
	background: url(banners/tabselected.gif) no-repeat;
	margin-left: 5px;
	padding-left: 10px;
	padding-top: 5px;
	margin-bottom: -6px;
}

.tabselected {
	background: url(banners/tabselected.gif) no-repeat;
}

.secondtab {
	width: 188px;
	height: 29px;
	padding-top: 5px;
	padding-left: 10px;
	margin-left: 196px;
	margin-bottom: -6px;
}

.tabunselected {
	background: url(banners/tabunselected.gif) no-repeat;
}

.smalltext{font-size: .8em;}
.smallertext{font-size: .6em;}
.tinytext{font-size: .4em;}

.tinted{background: #EFF0F1;}

/* hide from MacIE \*/ 
* html #hollyhack
  {height: 1%;}
/* end hide */

/*****************************************************************
* Variable-Width redesign stuff
******************************************************************/

.encapsulator {
	width:100%;
	background-color:#fff;
}

.encapsulator2 {
	width:95%;
	background:url(images/bgleft.gif) left repeat-y;
}

.bannerencapsulator {
	background:url(banners/bannermiddle.jpg) repeat-x;
}

.bannerencapsulator2 {
	background:url(banners/bannerleft.jpg) top left no-repeat;
}

.contentencapsulator {
	padding-right:21px;
	background:url(images/bgright.gif) right repeat-y;
}

.contentencapsulator2 {
	padding-left:21px;
}

.bottomborder {
	background:url(banners/bgbottommiddle.gif) repeat-x;
	background-color:#fff;
}

.leftcolencapsulator {
	float:left;
	width:45%;
    height:100%;
	/*max-width:300px;*/
	/*border-right:1px solid #908F8F;*/
	padding: 8px;
}

.middlecolencapsulator {
	margin-left:26%;
	border-left:1px solid #908F8F;
    height:100%;
}


/*****************************************************************
* Style for image drop shadow
******************************************************************/

.img-shadow {
  float:left;
  background: url(images/shadowAlpha.png) no-repeat bottom right !important;
  background: url(images/shadow.gif) no-repeat bottom right;
  margin: 10px 0 0 10px !important;
  margin: 10px 0 0 5px;
  }

.img-shadow img {
  display: block;
  position: relative;
  background-color: #fff;
  border: 1px solid #a9a9a9;
  margin: -6px 6px 6px -6px;
  padding: 4px;
  }
