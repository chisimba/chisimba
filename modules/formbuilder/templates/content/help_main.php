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
    .interactiveFormElementButtons{
        WIDTH: 255px;
    }
    .styleSettingsButton{
      WIDTH: 150px;  
    }

</style>
<?php
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());

$cssLayout = &$this->newObject('csslayout', 'htmlelements');

$userMenuBar = & $this->getObject('side_menu_handler', 'formbuilder');
$welcomePage = & $this->getObject('home_page_handler', 'formbuilder');

$content = $this->getObject('help_page_handler', 'formbuilder');
$pageContent = $content->showContent('intro', 0);

$leftContent = $userMenuBar->showSideMenu();
$middleContent = "Help Section Under Construction. Comming Soon.";
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent("<div id='leftColumn' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:20px 20px 20px 20px;'> " . $leftContent . "</div>");
$cssLayout->setMiddleColumnContent("<div id='middleColumn' class='ui-accordion-content ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 25px 15px 25px;'> " . $pageContent . "</div>");
echo $cssLayout->show();
?>

<div id="dialog-box-formElements" title="Form Element Explaination">

    <div id="FormElementInserterTabs">
        <ul>
            <li><a href="#formElementExplaination">Form Element Explication</a></li>
            <li><a href="#formElementInserter">Form Element Inserter Modal Window</a></li>

        </ul>
        <div id="formElementExplaination">

        </div>
        <div id="formElementInserter">

        </div>
    </div>
</div>
<div id="tempAjaxContainer"></div>
<script type="text/javascript">

    function setUpHelpNavIcons()
    {
        jQuery(".interactiveFormElementButtons").button({

            icons: {
                //   primary: 'ui-icon-home'
            },
            text: true
        });
        jQuery(".downloadManualLink").button({
            icons: {
                primary: 'ui-icon-circle-arrow-s',
                secondary: 'ui-icon-circle-arrow-s'
            },
            text: true
        });

        jQuery(".introButton").button({

            icons: {
                primary: 'ui-icon-home'
            },
            text: true
        });
        jQuery(".formMetaDataButton").button({

            icons: {
                primary: 'ui-icon-comment'
            },
            text: true
        });
        jQuery(".formEditorButton").button({

            icons: {
                primary: 'ui-icon-gear'
            },
            text: true
        });
        jQuery(".formPublisherButton").button({

            icons: {
                primary: 'ui-icon-folder-collapsed'
            },
            text: true
        });
        jQuery(".formOptionsButton").button({

            icons: {
                primary: 'ui-icon-script'
            },
            text: true
        });

        jQuery(".htmlformelements").button({

            icons: {
                primary: 'ui-icon-comment'
            },
            text: true
        });



        setUpAjaxFunctions();
    }

    function setUpAjaxFunctions()
    {
        jQuery(".formMetaDataButton").unbind('click').bind('click',function () {
            var dataToPost = {"contentType":"metadata"};
            var myurlToGetMetaDataContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            // jQuery("#middleColumn").fadeOut('2500');
            jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#middleColumn").load(myurlToGetMetaDataContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#middleColumn").fadeIn('500');
                setUpHelpNavIcons();
            }
        );
        });


        jQuery(".introButton").unbind('click').bind('click',function () {
            var dataToPost = {"contentType":"intro"};
            var myurlToGetIntroContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#middleColumn").load(myurlToGetIntroContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#middleColumn").fadeIn('500');
                setUpHelpNavIcons();
            }
        );
        });

        jQuery(".formEditorButton").unbind('click').bind('click',function () {
            var dataToPost = {"contentType":"formeditor"};
            var myurlToGetIntroContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#middleColumn").load(myurlToGetIntroContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#middleColumn").fadeIn('500');
                setUpHelpNavIcons();
            }
        );
        });


        jQuery(".formPublisherButton").unbind('click').bind('click',function () {
            var dataToPost = {"contentType":"formpublisher"};
            var myurlToGetIntroContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#middleColumn").load(myurlToGetIntroContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#middleColumn").fadeIn('500');
                setUpHelpNavIcons();
            }
        );
        });

        jQuery(".formOptionsButton").unbind('click').bind('click',function () {
            var dataToPost = {"contentType":"formoptions"};
            var myurlToGetIntroContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#middleColumn").load(myurlToGetIntroContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#middleColumn").fadeIn('500');
                setUpHelpNavIcons();
            }
        );
        });

        jQuery(".htmlformelements").unbind('click').bind('click',function () {
            var dataToPost = {"contentType":"htmlformelements"};
            var myurlToGetIntroContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#middleColumn").load(myurlToGetIntroContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#middleColumn").fadeIn('500');
                setUpHelpNavIcons();
            }
        );
        });

        jQuery(".interactiveFormElementButtons").unbind('click').bind('click',function () {
            var pageContentType= jQuery(this).attr('name');
            var dataToPost = {"contentType":pageContentType};
            var myurlToGetFormElementContent ="<?php echo html_entity_decode($this->uri(array('action' => 'getHelpContent'), 'formbuilder')); ?>";
            //jQuery("#middleColumn").html("<img src='packages/formbuilder/resources/images/loading_gif.gif' alt='loading'>");
            jQuery("#tempAjaxContainer").empty();
            jQuery("#tempAjaxContainer").load(myurlToGetFormElementContent , dataToPost ,function postSuccessFunction(html) {
                jQuery("#tempAjaxContainer").hide();

                var firstTabContent = jQuery("#tempAjaxContainer").children('#firstTab').html();
                var secondTabContent = jQuery("#tempAjaxContainer").children('#secondTab').html();

                jQuery('#dialog-box-formElements').children('#FormElementInserterTabs').children('#formElementExplaination').html(firstTabContent);
                jQuery('#dialog-box-formElements').children('#FormElementInserterTabs').children('#formElementInserter').html(secondTabContent);

                setUpHelpNavIcons();
                jQuery('#dialog-box-formElements').children('#FormElementInserterTabs').tabs();
                jQuery('#dialog-box-formElements').children('#FormElementInserterTabs').children('#formElementExplaination').children('#dpContainer').children("#datepicker").datepicker({
                    showOn: 'button',
                    buttonImage: 'packages/formbuilder/resources/images/userManual/calendar.gif',
                    buttonImageOnly: true

                });

                jQuery('#dialog-box-formElements').dialog('open');

            });
        });

    }
    jQuery(document).ready(function() {

        jQuery("#dialog-box-formElements").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            width:1050,
            resizable: true,
            modal: true,
            closeOnEscape: true,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                }
            }
        });




        jQuery(".homeButton").button({

            icons: {
                primary: 'ui-icon-home'
            },
            text: true
        });
                
                jQuery(".styleSettingsButton").button({

            icons: {
                primary: 'ui-icon-gear'
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
        setUpHelpNavIcons();
        setUpAjaxFunctions();
    });
</script>
