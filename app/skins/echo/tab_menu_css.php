<?php header("Content-type: text/css");  ?>
<?php include('stylesheet_info.php');  ?>

/*******************************************************************************
*  BEGIN: Tab CSS                                                              *
*******************************************************************************/

#header {
    float: left;
    width: 100%;
    line-height: normal;
    z-index: 100;
    background: #FFFFFF url("../_common/images/tabs/tabbackground.gif")
      repeat-x bottom;
      <!--#DAE0D2-->
}

#header ul {
    margin: 0;
    padding: 0;
    list-style: none;
    padding: 2px 10px 0;
}

#header li {
    float: left;
    margin: 0;
    padding: 0;
    background: url("../_common/images/tabs/tabright.gif")
      no-repeat right top;
}

#header li a:hover {
    color: <?php echo($dropdownHoverFontColor); ?>;
}

#header a {
    float: left;
    display: block;
    background: url("../_common/images/tabs/tableft.gif")
      no-repeat left top;
    padding: 5px 15px 4px 6px;
    text-decoration: none;
    font: bold <?php echo $dropdownFontSize; ?> <?php echo $dropdownFontFamily; ?>;
    font-size: 9pt;
    color: <?php echo $dropdownLinkColor; ?>;
}

#header #current {
    background-image: url("../_common/images/tabs/tabrighton.gif");
    color: <?php echo($dropdownSelected); ?>;
}

#header #current a {
    background-image: url("../_common/images/tabs/tablefton.gif");
    padding-bottom: 5px;
    color: <?php echo($dropdownSelected); ?>;
}

<!-- For IE5/Mac -->

/* Commented Backslash Hack
     hides rule from IE5-Mac \*/
#header a {float:none;}
/* End IE5-Mac hack */


/*******************************************************************************
*  END: Tab CSS                                                                *
*******************************************************************************/