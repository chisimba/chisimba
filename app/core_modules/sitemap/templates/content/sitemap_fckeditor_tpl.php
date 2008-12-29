<script type="text/javascript">
function previewWindow(sUrl)
{
    parent.frames['previewiframe'].location.replace(sUrl);
}

function insertWysiLink(link)
{
    if (window.opener) {
        
    window.top.opener.SetUrl(link) ;
    window.close() ;
    }
}
</script>
<?php
$this->loadClass('treemenu','tree');
$this->loadClass('modulelinkspresentation');
$this->loadClass('listbox','tree');
$this->loadClass('listbox','tree');
$this->loadClass('htmlheading','htmlelements');

$htmlHeading = new htmlheading();
$htmlHeading->type = 1;
$htmlHeading->str = $this->objLanguage->languageText('phrase_insertalink');
echo $htmlHeading->show();

echo '<p>'.$this->objLanguage->languageText('mod_sitemap_explaininsertlink', 'sitemap').'</p>';

$menu = new treemenu();


foreach ($modules as $module)
{
    $modObject = $this->getObject('modulelinks_'.$module, $module);
    $menu->addItem($modObject->show());
}


$htmllist  = &new modulelinkspresentation($menu, array('target'=>'iframe'));
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressXML', TRUE);
//$this->setPageTemplate(NULL);

echo '<div style="height: 150px; overflow-x: hidden; overflow-y:scroll;">';
echo $htmllist->getMenu();
echo '</div>';
echo '<br />';

$htmlHeading = new htmlheading();
$htmlHeading->type = 3;
$htmlHeading->str = $this->objLanguage->languageText('phrase_previewwindow');
echo $htmlHeading->show();

echo '<div style="height: 300px; padding: 0px; margin: 0px;">';//border: 1px solid yellow; 
echo '<div style="height: 300px; width: 98%;  position:absolute; z-index:20; ">&nbsp;</div>';//border: 1px solid green;
echo '<div style="height: 50%; width: 100%; position:absolute; z-index:10;">
<iframe name="previewiframe" src="about:blank" style="height:100%; width:100%;" frameborder="0"></iframe>
</div>';

echo '</div>';

$css = '
<style type="text/css">
body { overflow: hidden;}
</style>
';
$this->appendArrayVar('headerParams', $css);


?>