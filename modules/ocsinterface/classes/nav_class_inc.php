<?php

class nav extends object {

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->loadclass('link','htmlelements');
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->storyparser=$this->getObject('ocsstoryparser');
    }

    public function getLeftContent() {

        $sidecatid=$this->objDbSysconfig->getValue('SIDE_NAV_CATEGORY','ocsinterface');
        $sidenavs=$this->storyparser->getStoryByCategory($sidecatid);
        $objWashout = $this->getObject('washout', 'utilities');
        $list=array();

        foreach($sidenavs as $nav) {
            $sidelink = new link ($this->uri(array('action'=>'viewstory', 'storyid'=>$nav['id']),"ocsinterface"));
            $sidelink->link=   $objWashout->parseText($nav['title']);
            $list[] = $sidelink->show();
        }

        $desc=
            '<ul id="nav-secondary">';
        $cssClass = '';
        foreach($list as $element) {
            if(strtolower($element) == strtolower($toSelect)) {
                $cssClass = ' class="active" ';
            }
            $desc.='<li $cssClass>'.$element.'</li>';
        }
        $desc.='</ul><br/>';


        $sidecatid2=$this->objDbSysconfig->getValue('SIDE_NAV2_CATEGORY','ocsinterface');
        $sidenavs2=$this->storyparser->getStoryByCategory($sidecatid2);

        foreach($sidenavs2 as $nav) {
            $desc.=$objWashout->parseText($nav['maintext']);
        }


        return $desc;
    }



    public function getRightContent() {
        $sidecatid=$this->objDbSysconfig->getValue('RIGHT_SIDE_NAV_CATEGORY','ocsinterface');
        $sidenavs=$this->storyparser->getStoryByCategory($sidecatid);
        $objWashout = $this->getObject('washout', 'utilities');
        $list=array();

        foreach($sidenavs as $nav) {
            $sidelink = new link ($this->uri(array('action'=>'viewstory', 'storyid'=>$nav['id']),"ocsinterface"));
            $sidelink->link=   $objWashout->parseText($nav['title']);
            $list[] = $sidelink->show();
        }

        $desc=
            '<ul id="nav-secondary">';
        $cssClass = '';
        foreach($list as $element) {
            if(strtolower($element) == strtolower($toSelect)) {
                $cssClass = ' class="active" ';
            }
            $desc.='<li $cssClass>'.$element.'</li>';
        }
        $desc.='</ul><br/>';


        $sidecatid2=$this->objDbSysconfig->getValue('RIGHT_SIDE_NAV2_CATEGORY','ocsinterface');
        $sidenavs2=$this->storyparser->getStoryByCategory($sidecatid2);

        foreach($sidenavs2 as $nav) {
            $desc.=$objWashout->parseText($nav['maintext']);
        }


        return $desc;
    }
}

?>
