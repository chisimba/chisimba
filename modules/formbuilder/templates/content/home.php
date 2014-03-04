<style type="text/css">
    button.ui-button {
        WIDTH: 150px;
    }
</style>
<?php

/*! \file home.php
 * \brief The template file is called by default action home in the controller.php
 * file. This template file sets up all the contents of the home page.
 * \section sec Template Code Explanation
 * - Set the css layout to two columns
 * - Insert the side menu on the left column.
 * - Insert the home page text content in the middle column.
 * - Set up the javascript jQuery UI functions for the side menu.
 */
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());

$cssLayout = &$this->newObject('csslayout', 'htmlelements');

$userMenuBar = & $this->getObject('side_menu_handler', 'formbuilder');
$welcomePage = & $this->getObject('home_page_handler', 'formbuilder');

$leftContent = $userMenuBar->showSideMenu();
$middleContent = $welcomePage->showHomePage();
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent("<div id='formPreviewDiv' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:20px 20px 20px 20px;'> " . $leftContent . "</div>");
$cssLayout->setMiddleColumnContent("<div id='formPreviewDiv' class='ui-accordion-content ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 25px 15px 25px;'> " . $middleContent . "</div>");
echo $cssLayout->show();
?>
<script type="text/javascript">


    jQuery(document).ready(function() {
        jQuery(".homeButton").button({

            icons: {
                primary: 'ui-icon-home'
            },
            text: true
        });
        jQuery(".listAllFormsButton").button({

            icons: {
                primary: 'ui-icon-script'
            },
            text: true
        });
        jQuery(".createNewFormButton").button({

            icons: {
                primary: 'ui-icon-document'
            },
            text: true
        });

        jQuery(".helpButton").button({

            icons: {
                primary: 'ui-icon-help'
            },
            text: true
        });
        
                jQuery(".styleSettingsButton").button({

            icons: {
                primary: 'ui-icon-gear'
            },
            text: true
        });
        
        
    });
</script>