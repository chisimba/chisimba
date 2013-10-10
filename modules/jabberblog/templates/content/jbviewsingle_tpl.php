<script type="text/javascript">
//<![CDATA[
function init () {
    $('input_redraw').onclick = function () {
        redraw();
    }
}
function redraw () {
    var url = 'index.php';
    var pars = 'module=security&action=generatenewcaptcha';
    var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
}
function showLoad () {
    $('load').style.display = 'block';
}
function showResponse (originalRequest) {
    var newData = originalRequest.responseText;
    $('captchaDiv').innerHTML = newData;
}
//]]>
</script>
<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 2 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );
$objImView = $this->getObject ( 'jbviewer' );


$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading ( );
$header->str = $this->objLanguage->languageText ( 'mod_jabberblog_jabberblogof', 'jabberblog' ) . " " . $this->objUser->fullName ( $this->jposteruid );
$header->type = 1;

$hlink = $this->getObject ( 'link', 'htmlelements' );
$hlink->href = $this->uri ( array ('action' => NULL ) );
$hlink->link = $header->show ();

$script = '<script type="text/JavaScript" src="resources/rounded_corners.inc.js"></script>
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

$middleColumn .= $hlink->show ();
$middleColumn .= $output;

if (! $this->objUser->isLoggedIn ()) {
    $leftColumn .= $objImView->showUserMenu ();
} else {
    $leftColumn .= $this->leftMenu->show ();
}

$leftColumn .= $objImView->renderBoxen();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
echo $cssLayout->show ();