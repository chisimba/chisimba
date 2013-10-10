<?php

/**
 * This class lists groups
 *
 * @author davidwaf
 */
class block_grouplisting extends object {

    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = "";
    }

    public function show() {
        $mode = $this->configData;
        $groupManager = $this->getObject("groupmanager", "oer");
        return $groupManager->getGroupListing();
    }

}

?>
