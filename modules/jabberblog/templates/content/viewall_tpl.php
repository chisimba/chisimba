<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 2 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$objImView = $this->getObject ( 'jbviewer' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );
//$this->objImOps = $this->getObject('imops', 'im');


$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading ( );
$header->str = $this->objLanguage->languageText ( 'mod_jabberblog_jabberblogof', 'jabberblog' ) . " " . $this->objUser->fullName ( $this->jposteruid );
$header->type = 1;

/* $script = '<script type="text/JavaScript" src="resources/rounded_corners.inc.js"></script>
    <script type="text/JavaScript">
      window.onload = function() {
          settings = {
              tl: { radius: 10 },
              tr: { radius: 10 },
              bl: { radius: 10 },
              br: { radius: 10 },
              antiAlias: true,
              autoPad: true
          }
          var myBoxObject = new curvyCorners(settings, "rounded");
          myBoxObject.applyCornersToAll();
      }
    </script>';
$this->appendArrayVar ( 'headerParams', $script );
*/
$objPagination = $this->newObject ( 'pagination', 'navigation' );
$objPagination->module = 'jabberblog';
$objPagination->action = 'viewallajax';
$objPagination->id = 'jabberblog';
$objPagination->numPageLinks = $pages;
$objPagination->currentPage = $pages - 1;

$middleColumn .= $header->show () . '<br/>' . $objPagination->show ();
//$middleColumn .= $objImView->renderOutputForBrowser($msgs);

if (! $this->objUser->isLoggedIn ()) {
    $leftColumn .= $objImView->showUserMenu ();
} else {
    $leftColumn .= $this->leftMenu->show ();
}

$leftColumn .= $objImView->renderBoxen();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
echo $cssLayout->show ();
