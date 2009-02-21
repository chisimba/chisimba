<?php header("Content-type: text/css");  ?>
<?php include('stylesheet_info.php');  ?>
/* 
 *      Horizontal, top-2-bottom menu
 *      Copyright Aleksandar Vacic, www.aplus.co.yu, some rights reserved http://creativecommons.org/licenses/by-sa/2.0/
 */

/*      ------  Basic style ------      */

#menu {
    display: block;
    padding-left: 10px;
    z-index: 100;
}

#menu ul {
    margin: 0;
    padding: 0;
    border: 0;
    list-style-type: none;
}

#menu li {
    margin: 0;
    padding-right: 30px;
    border: 0;
    display: block;
    float: left;
    position: relative;
}

#menu a {
    display: block;
}

#menu li li {
    padding: 0;
    width: 100%;
}

/* fix the position for 2nd level submenus. first make sure no horizontal scrollbars are visible on initial page load... */
#menu li li ul {
    top: 0;
    left: 0;
}

/* ...and then place it where it should be when shown */
#menu li li:hover ul {
    left: 100%;
    z-index: 100;
}

/* initialy hide all sub menus */
#menu li ul {
    display: none;
    position: absolute;
    z-index: 101;
}

/* display them on hover */
#menu li:hover>ul {
    display: block;
    z-index: 101;color: #FFFFFF;
}

/* this is needed if you want to style #menu div - force containment of floated LIs inside of main UL */
#menuList:after {
    content: ".";
    height: 0;
    display: block;
    visibility: hidden;
    overflow: hidden;
    clear: both;
}

/* Fix for IE5/Mac \*//*/
#menu a {
    float: left;
}

#menuList {
    display: inline-block;
}
/*  */

/*      ------   Make-up    --------            */

/* Main Menu Color */
#menu {
    border: none;
}



#menu li li a{
    color: <?php echo $dropdownLinkColor; ?>;
}

#menu li:hover {
    background-color: <?php echo($dropdownSelected); ?>;
    color: <?php echo($dropdownHoverFontColor); ?>;
}

#menu li li:hover {
    background-color:<?php echo $dropdownHoverBkg; ?>;
    color: <?php echo($dropdownHoverFontColor); ?>;
}



/* NormalText Color*/
#menu a {
    text-decoration: none;
    text-align: center;
    font: bold <?php echo $dropdownFontSize; ?> <?php echo $dropdownFontFamily; ?>;
    padding: 4px 5px 5px;
}

/* Text Color of Hover - IE Doesn't pickup this*/
#menu li:hover > a {
    color: <?php echo($dropdownHoverFontColor); ?>;
}

#menu li a:hover {
    background-color: transparent;
    color: <?php echo($dropdownHoverFontColor); ?>;
}


#menu li ul {
    color: <?php echo($dropdownHoverFontColor); ?>;
    background-color: <?php echo($dropdownBkg); ?>;
    border: 1px solid #A1BCD4;
    width: 130px;
}

#menu li ul a {
    text-align: left;
}