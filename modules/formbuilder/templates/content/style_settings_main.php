<style type="text/css">
    .homeButton  {
        WIDTH: 150px;
    }
    .listAllFormsButton{
        WIDTH: 150px;
    }
    .createNewFormButton{
        WIDTH: 150px;
    }
    .helpButton{
        WIDTH: 150px;
    }
        .styleSettingsButton{
        WIDTH: 150px;
    }
    .interactiveFormElementButtons{
        WIDTH: 255px;
    }

</style>
<?php
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());

$cssLayout = &$this->newObject('csslayout', 'htmlelements');

$userMenuBar = & $this->getObject('side_menu_handler', 'formbuilder');

$styleSettingsContent = & $this->getObject('style_settings_handler', 'formbuilder');

$content = $this->getObject('help_page_handler', 'formbuilder');
$pageContent = $content->showContent('intro', 0);

$leftContent = $userMenuBar->showSideMenu();

$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent("<div id='leftColumn' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:20px 20px 20px 20px;'> " . $leftContent . "</div>");
$cssLayout->setMiddleColumnContent("<div id='middleColumn' style='border:padding:10px 25px 15px 25px;'> " . $styleSettingsContent->buildThemeRoller() . "</div>");
echo $cssLayout->show();
?>
<div id="ajaxCallUrlsHiddenInputs">
<?php

    $this->loadClass('hiddeninput', 'htmlelements');
    $ajaxUrlToViewStyle = html_entity_decode($this->uri(array('action' => 'viewStyle'), 'formbuilder'));
    $ajaxUrlToSetStyle = html_entity_decode($this->uri(array('action' => 'setStyle'), 'formbuilder'));

    $hiddenInputToViewStyle = new hiddeninput("urlToViewStyle", $ajaxUrlToViewStyle);
    echo $hiddenInputToViewStyle->show();

    $hiddenInputToSetStyle = new hiddeninput("urlToSetStyle", $ajaxUrlToSetStyle);
    echo $hiddenInputToSetStyle->show();
?>
</div>
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
<script type="text/javascript" charset="utf-8">

          var selectedTheme = null;
                jQuery(document).ready(function(){
                    
        
        selectedTheme =  $("#input_stylelist_dropdown").val();
        jQuery("#themeLoaderContainer").html("The style <i> "+selectedTheme+" </i> is set.");
                
        jQuery("#input_stylelist_dropdown").change(function(){
            
            selectedTheme =  $("#input_stylelist_dropdown").val();
            
            //$("#themeLoaderContainer").html(selectedTheme);
            var styleDataToPost = {"selectedStyle": selectedTheme};
            var myurlToViewStyle = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToViewStyle]").val();
            jQuery('#themeLoaderContainer').load(myurlToViewStyle, styleDataToPost ,function postSuccessFunction(html) {
                jQuery('#themeLoaderContainer').html(html);
            });
        });
         
         

         $( "#selectTheme" ).button({
            icons: {
                primary: "ui-icon-gear",
                secondary:"ui-icon-gear"
            },
            text: true
        });


         $( "#selectTheme" ).click(function(){
             var styleDataToPost = {"selectedStyle": selectedTheme};
            var myurlToSetStyle = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToSetStyle]").val();
            jQuery('#themeLoaderContainer').load(myurlToSetStyle, styleDataToPost ,function postSuccessFunction(html) {
                jQuery('#themeLoaderContainer').html(html);
            });
         });
         
$( "#datepicker" ).datepicker();

$( ".buttonViewer button:first" ).button({
            icons: {
                primary: "ui-icon-locked"
            },
            text: false
        }).next().button({
            icons: {
                primary: "ui-icon-locked"
            }
        }).next().button({
            icons: {
                primary: "ui-icon-gear",
                secondary: "ui-icon-triangle-1-s"
            }
        }).next().button({
            icons: {
                primary: "ui-icon-gear",
                secondary: "ui-icon-triangle-1-s"
            },
            text: false
        });

//coordinateFinder = new findCoordinatesFromBrowser();
//coordinateFinder.determineGeoLocationCoordinates();
			});
		</script>