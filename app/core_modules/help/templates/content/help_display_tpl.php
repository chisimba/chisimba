<div style="padding-bottom: 60px;">
<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = new htmlheading();
$heading->type = 1;
$heading->str = $helptitle.' '.$viewletHelp;


echo $heading->show();

echo $helptext;


if (count($moduleHelp) > 0) {
    echo '<h5>Related Help for this Module</h5>';

    echo ('<ul>');

    $link = new link();

    foreach ($moduleHelp as $text)
    {

        if ($text['code'] == 'help_'.$module.'_about_title') {
            $helpItem = 'about';
        } else {
            $helpItem = str_replace('help_'.$module.'_title_', '', $text['code']);
        }

        $link->href = $this->uri(array('action'=>'view', 'rootModule'=>$module, 'helpid'=>$helpItem));

        $helpTitle = $objLanguage->code2Txt($text['code']);

        if (strtoupper(substr($helpTitle, 0, 12)) == '[*HELPLINK*]') {
            $array = explode('/', $helpTitle);

            $helpTitle = $objLanguage->code2Txt('help_'.$array[1].'_title_'.$array[2]);
        }

        $link->link = $helpTitle;

        echo ('<li>'.$link->show());
    }

    echo ('</ul>');
}


?>
</div>

<div style="position: fixed; height: 40px; bottom: 0; left: 0; width:100%; right: 0; padding: 5px;" id="footer"><?php echo $richHelp; ?></div>