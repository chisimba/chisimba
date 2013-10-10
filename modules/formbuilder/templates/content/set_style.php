<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

     $selectedStyle = $this->getParam('selectedStyle');
       $styleSettingsHandlerObj = $this->getObject('style_settings_handler','formbuilder');
             $jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
         $setSyleSuccessdul = $styleSettingsHandlerObj->setNewTheme($selectedStyle);
     
         if ($setSyleSuccessdul){
           $jqueryUILoader->viewTheme($selectedStyle);  
           echo "The style <b> ".$selectedStyle." </b> has been set. Enjoy the new look.";
         } else {
            echo "Error. This style cannot be set. No such style exists or the CSS files for this theme do not exist."; 
         }

?>
