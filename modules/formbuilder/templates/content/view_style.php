<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
        $selectedStyle = $this->getParam('selectedStyle');
         $jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
         $viewSuccessful = $jqueryUILoader->viewTheme($selectedStyle);
         if ($viewSuccessful){
             echo "You are viewing the <b> ".$selectedStyle." </b> style. Press the 'Set Style' button set this style for the form builder module.";
         } else {
             echo "Error. This style cannot be view properly. No such style exists or the CSS files for this theme do not exist.";
         }

?>
