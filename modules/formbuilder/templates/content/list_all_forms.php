<?php
/*! \file list_all_forms.php
 * \brief The template file is called by the action listAllForms in the controller.php.
 * \todo Comment this file for doxygen. This is a very big file to comment with a lot
 * of intricate logic. The developer Salman Noor will comment it soon.
 * \todo Add the functionality to restrict access to forms. Right now every user
 * can add, edit or delete any form on the form list. A little functionality is included but it is
 * not complete and not being used. The reason behind this, is that there might be
 * many access protocols when accessing data. WITS University encompasses the
 * HAL system to query whether a user can have access to certain kinds of data. So if you want to include
 * your own user access restriction, you can easily add this functionality at your
 * own discretion.
 */
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());

//$jQueryUILibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqueryUI/jquery.ui.core.min.js', 'formbuilder').'" type="text/javascript"></script>';
//$jQueryUICSS = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('js/jqueryUI/jquery.ui.all.css', 'formbuilder').'"';
//$jqplotBarGraphLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.barRenderer.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotAxisLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.categoryAxisRenderer.min.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotPieGraphLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.pieRenderer.js', 'formbuilder').'" type="text/javascript"></script>';
//$jqplotPntLabelsLibrary = '<script language="JavaScript" src="'.$this->getResourceUri('js/jqplot/jqplot.pointLabels.js', 'formbuilder').'" type="text/javascript"></script>';
//
////[If browser is IE]; this library needs to be included to jqplot to work
//$this->appendArrayVar('headerParams', $jQueryUILibrary);
/////[End IF]
//$this->appendArrayVar('headerParams', $jQueryUICSS);
////$this->appendArrayVar('headerParams', $jqplotCSS);
////$this->appendArrayVar('headerParams', $jqplotBarGraphLibrary);
////$this->appendArrayVar('headerParams', $jqplotAxisLibrary);
////$this->appendArrayVar('headerParams', $jqplotPieGraphLibrary);
////$this->appendArrayVar('headerParams', $jqplotPntLabelsLibrary);
?>
<style type="text/css">
    span#toolbar {
        padding: 10px 4px;
    }
</style>



<div id="dialog-paginationIndicator" title="Pagination Menu">
    <p><span class="ui-icon ui-icon-transferthick-e-w" style="float:left; margin:0 7px 20px 0;"></span>
        Select how many forms to be viewed in one page.</p>
    <?php
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('radio', 'htmlelements');
    $objForm = new form('newPaginationBatchForm', $this->uri(array("action" => "listAllForms"), "formbuilder"));
    $paginationBatchRadio = new radio('paginationBatchRadio');
    $paginationBatchRadio->addOption('5', '5');
    $paginationBatchRadio->addOption('10', '10');
    $paginationBatchRadio->addOption('15', '15');
    $paginationBatchRadio->addOption('20', '20');
    $paginationBatchRadio->addOption('25', '25');
    $paginationBatchRadio->setSelected('5');
    $paginationBatchRadio->setBreakSpace("<br>");
    $objForm->addToForm($paginationBatchRadio->show() . '<br>');
    echo $objForm->show();
    ?>
</div>
<div id="getPaginationBatchSize">
<?php
    echo $paginationBatchSize = $this->getParam('paginationBatchRadio', 10);
?>
</div>
<div id="test">
<?php
    $objFormList = $this->getObject('view_form_list', 'formbuilder');

    echo $objFormList->showSearchMenu();
    $objFormList->setNumberOfEntriesInPaginationBatch($paginationBatchSize);

    echo $objFormList->showPaginationMenu(0);
    $objSideMenu = $this->getObject('side_menu_handler', 'formbuilder');
    echo $objSideMenu->showSlideMenu();
?>
</div>
<!--  <div id="mainMenu" style="float:left;">
<?php
?>
</div>-->
<div id="paginationPageFormer">
<?php
    $objFormList = $this->getObject('view_form_list', 'formbuilder');
?>
    </div>
    <div id="paginationPageLatter">

    <?php
    $action = $this->getParam("action", "listAllForms");
    if ($action == 'searchAllForms') {
        $searchValue = $this->getParam("searchFormList", NULL);
        echo $objFormList->showPaginationIndicator(0, $searchValue);
        echo $objFormList->show(0, $searchValue);
    } else {
        echo $objFormList->showPaginationIndicator(0, NULL);
        echo $objFormList->show(0, NULL);
    }
    ?>
</div>
<div id="dialog-viewPublishingParameters" title="View Form Publishing and General Details">

    <div id="publishingFormIndicator">
    </div>
    <div id="viewPublishingFormTabs">
        <ul>
            <li><a href="#generalPublishingFormDetails">General Information</a></li>
            <li><a href="#simpleViewPublishingFormDetails">Simple Publishing Details</a></li>
            <li><a href="#advancedViewPublishingFormDetails">Advanced Publishing Details</a></li>

        </ul>
        <div id="generalPublishingFormDetails">
        </div>
        <div id="simpleViewPublishingFormDetails">

        </div>
        <div id="advancedViewPublishingFormDetails">
        </div>
    </div>
</div>

<div id="dialog-editPublishingParameters" title="Form Publishing Parameters">

    <div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>


    <div id="publishingFormOption">
<?php
    echo $objFormList->showFormPublishingIndicator();
?>
    </div>
    <div id="editPublishingFormTabs">
        <ul>
            <li><a href="#simpleEditPublishingForm">Simple</a></li>
            <li><a href="#advancedEditPublishingForm">Advanced</a></li>

        </ul>
        <div id="simpleEditPublishingForm">
        <?php
        echo $objFormList->showSimplePublishingForm();
        ?>
        </div>
        <div id="advancedEditPublishingForm">
<?php
        echo $objFormList->showAdvancedPublishingForm();
?>
        </div>

    </div>
</div>
<div id="getNumberOfPaginationRequests"style="float:right;clear:none;">
<?php
        if ($action == 'searchAllForms') {
            echo $objFormList->getNumberofPaginationRequests($searchValue);
        } else {
            echo $objFormList->getNumberofPaginationRequests(NULL);
        }
?>

</div>
<div id="getFormSearchValue"style="float:right;clear:none;">
    <?php
        if ($action == 'searchAllForms') {
            echo $searchValue;
        } else {
            echo NULL;
        }
    ?>

    </div>

    <div id="dialog-formDisplayer" title="Preview Form">
        <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>
            This form is read-only and cannot be submitted, augmented or altered in anyway.</p>
        <div id="formPreviewDiv" class="ui-widget ui-corner-all" style="border:1px solid #CCCCCC;padding:10px 25px 15px 25px;">
        </div>
    </div>
    <div id="dialog-formOptionsDisplayer" title="Form Options">
        <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>
            This menu has all possible form commands whether general or specifically for your form.</p>
        <div id="formOptionsMenu" class="ui-widget ui-corner-all" style="border:1px solid #CCCCCC;padding:10px 25px 15px 25px;">
        </div>
    </div>

    <div id="dialog-deleteForm" title="Delete Form">
        <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>
            Are you sure you want to delete this form?</p>
        <div id="deleteMessage" class="ui-widget ui-corner-all" style="border:1px solid #CCCCCC;padding:10px 25px 15px 25px;">
        </div>
    </div>


    <div id="dialog-box-generalHelp" title="Form Options inside Form Listings Help">
        <div id="helpContent">

            <ul>
                <li><a href="#formOptions">Form Options in the Form Listings</a></li>
                <li><a href="#formPublisher">Form Publisher</a></li>

            </ul>
            <div id="formOptions">
<?php
        $content = $this->getObject('help_page_handler', 'formbuilder');
        echo $pageContent = $content->showContent('formoptions', 1);
?>
            </div>
            <div id="formPublisher">
<?php
        echo $pageContent = $content->showContent('formpublisher', 1);
?>
            </div>

        </div>
    </div>

    <div id="tempdivcontainer"style="float:right;clear:none;"></div>
    <script type="text/javascript">

        function intObject() {
            this.i;
            return this;
        }

        function setUpAccordion()
        {

            if (jQuery("#paginationPageFormer").children("#accordion").length > 0)
            {
                jQuery("#paginationPageFormer").children("#accordion").accordion({
                    autoHeight: true,
                    navigation: true
                });
            }
            else if (jQuery("#paginationPageLatter").children("#accordion").length > 0)
            {
                jQuery("#paginationPageLatter").children("#accordion").accordion({
                    autoHeight: true,
                    navigation: true
                });
            }
        }

        function setUpAccordionButtons(restrictiveUserButtons)
        {

            var formOptionsButton = jQuery('.formOptionsButton');
            var editPublishingDataButton = jQuery('.editPublishingDataButton');
            var viewSubmitResultsButton = jQuery('.viewSubmitResultsButton');


            var restrictiveUserButtonsa = jQuery([]).add(formOptionsButton).add(editPublishingDataButton).add(viewSubmitResultsButton);

            jQuery(".formOptionsButton, .constructFormButton, .previewFormButton, .viewPublishingDataButton, .editPublishingDataButton , .viewSubmitResultsButton, .searchFormListButton").button();

            var accessRestrictionArray= new Array();
            jQuery(".userAccessRestriction").each(function (index, domEle) {

                var a = jQuery(domEle).val();
                //     jQuery('#tempdivcontainer').append(a+"<br>");
                var i=0;
                for (i=0;i<3;i++)
                {
                    accessRestrictionArray[(index*3)+i]= a;
                }

                if (a ==3)
                {
                    //   jQuery('input[name=viewSubmitResultsButton]').button( "disable" );
                    //   jQuery(':input[value=Edit Publishing Parameters]').button( "disable" );
                }
                {
                }

            });
            jQuery(restrictiveUserButtonsa).each(function (index, domEle) {
                var a= accessRestrictionArray[index];

                if (a==3)
                {
                    var q= jQuery(domEle).attr('name');
                    //  jQuery('#tempdivcontainer').append('<br>'+q);
                    //     jQuery(domEle).button().button('disable');
                    //    jQuery(domEle).button( "disable" );
                }
            });
        }

        function setUpPaginationButtons(paginationNumber, numberOfPaginationRequests)
        {
            var numberOfPaginationRequests=	jQuery('#getNumberOfPaginationRequests').html()
            numberOfPaginationRequests = jQuery.trim(numberOfPaginationRequests);
            //        if (numberOfPaginationRequests ==null)
            //        {
            //            numberOfPaginationRequests=0;
            //        }


            if (jQuery("#paginationMenu").children('#paginationIndicator').length > 0)
            {
                jQuery("#paginationMenu").children('#paginationIndicator').remove();
            }
            if (jQuery('#paginationPageFormer').children().length <= 0)
            {
                jQuery(".previousButton").after( jQuery('#paginationPageLatter>button') );
            }
            else
            {
                jQuery(".previousButton").after( jQuery('#paginationPageFormer>button') );
            }


            jQuery(".previousButton").button({

                icons: {
                    primary: 'ui-icon-seek-prev'
                },
                text: true
            }).next().button({
                icons: {
                    // primary: 'ui-icon-seek-next',
                    //secondary: 'ui-icon-seek-next'
                },
                text: true
            }).next().button({
                icons: {
                    // primary: 'ui-icon-seek-next',
                    secondary: 'ui-icon-seek-next'
                },
                text: true
            });
            if (paginationNumber.i <= 0)
            {
                jQuery(".previousButton").button('disable');
            }
            else
            {
                jQuery(".previousButton").button('enable');
            }

            if (numberOfPaginationRequests == '1')
            {
                jQuery(".nextButton").button('disable');
            }
            else if (paginationNumber.i >= numberOfPaginationRequests-1)
            {
                jQuery(".nextButton").button('disable');
                // jQuery("#tempdivcontainer").html(paginationNumber.i+"test"+numberOfPaginationRequests);
            }
            else
            {
                jQuery(".nextButton").button('enable');
            }
            jQuery("#tempdivcontainer").html(paginationNumber.i+"test"+numberOfPaginationRequests);
            setUpPaginationIndicator();
        }


        function callAjaxQueryForPaginatedResults()
        {

        }



        function setUpPaginationIndicator()
        {
            jQuery("#paginationIndicator").unbind('click').bind('click',function () {

                jQuery("#dialog-paginationIndicator").dialog('open');

            });

        }

        function setUpPreviewFormIcons()
        {
            jQuery(".previewFormButton").unbind('click').bind('click',function () {

                var formNumber= jQuery(this).attr('name');
                //jQuery(".previewFormButton").unbind();
                var dataToPost = {"formNumber":formNumber};
                var myurlToProduceAreadOnlyForm ="<?php echo html_entity_decode($this->uri(array('action' => 'buildAReadOnlyForm'), 'formbuilder')); ?>";
            jQuery("#dialog-formDisplayer").children("#formPreviewDiv").load(myurlToProduceAreadOnlyForm , dataToPost ,function postSuccessFunction(html) {
                if (jQuery("#dialog-formDisplayer").children("#formPreviewDiv").children().length <= 0)
                {
                    jQuery("#dialog-formDisplayer").children("#formPreviewDiv").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>This form is empty and \n\
has no form elements. The form designer should complete building this form or delete it. </p>"  );
                }
            });
            jQuery("#dialog-formDisplayer").dialog('open');
            //setUpFormOptionsIcons();
        });

    }

    function setUpFormOptionsIcons()
    {
        jQuery(".formOptionsButton").unbind('click').bind('click',function () {
            var formNumber= jQuery(this).attr('name');
            var dataToPost = {"formNumber":formNumber};
            var myurlToProduceFormOptionsMenu ="<?php echo html_entity_decode($this->uri(array('action' => 'listCurrentFormOptions'), 'formbuilder')); ?>";
            jQuery("#dialog-formOptionsDisplayer").children("#formOptionsMenu").load(myurlToProduceFormOptionsMenu , dataToPost ,function postSuccessFunction(html) {
                if (jQuery("#dialog-formOptionsDisplayer").children("#formOptionsMenu").children().length <= 0)
                {
                    jQuery("#dialog-formOptionsDisplayer").children("#formOptionsMenu").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Internal Error.\n\
Form Options for this form cannot be shown. </p>"  );
                }
                else
                {
                    jQuery(".createNewFormButton").button({

                        icons: {
                            primary: 'ui-icon-document'
                        },
                        text: true
                    });
                    jQuery(".editFormMetaData").button({
                        icons: {
                            primary: 'ui-icon-pencil'
                        },
                        text: true
                    }).next().button({
                        icons: {
                            primary: 'ui-icon-gear'
                            //secondary: 'ui-icon-seek-next'
                        },
                        text: true
                    });

                    jQuery(".previewFormButton").unbind('click').bind('click',function () {
                        jQuery("#dialog-formOptionsDisplayer").dialog('close');
                        var formNumber= jQuery(this).attr('name');
                        jQuery(".previewFormButton").unbind();
                        var dataToPost = {"formNumber":formNumber};
                        var myurlToProduceAreadOnlyForm ="<?php echo html_entity_decode($this->uri(array('action' => 'buildAReadOnlyForm'), 'formbuilder')); ?>";
                        jQuery("#dialog-formDisplayer").children("#formPreviewDiv").load(myurlToProduceAreadOnlyForm , dataToPost ,function postSuccessFunction(html) {
                            if (jQuery("#dialog-formDisplayer").children("#formPreviewDiv").children().length <= 0)
                            {
                                jQuery("#dialog-formDisplayer").children("#formPreviewDiv").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>This form is empty and \n\
has no form elements. The form designer should complete building this form or delete it. </p>"  );

                            }

                        });
                        setUpPreviewFormIcons();
                        jQuery("#dialog-formDisplayer").dialog('open');
                    });


                    jQuery(".previewFormButton").button({

                        icons: {
                            primary: 'ui-icon-folder-open'
                        },
                        text: true
                    }).next().button({
                        icons: {
                            primary: 'ui-icon-wrench'
                            //secondary: 'ui-icon-seek-next'
                        },
                        text: true
                    });

                    jQuery(".deleteFormSubmissions").unbind('click').bind('click',function () {
                        jQuery("#dialog-formOptionsDisplayer").dialog('close');
                        var formNumber= jQuery(this).attr('name');
                        jQuery( "#dialog-deleteForm").dialog({ title: 'Confirm Form Submissions Delete' });
                        jQuery("#dialog-deleteForm").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>The following items will be\n\
permanently deleted and cannot be recovered. Are you sure?</p><p><span class="ui-icon ui-icon-bullet" style="float:left; margin:10 4px 0px 0;"></span> All Submissions for this form.');
                        jQuery("#dialog-deleteForm").dialog("open");
                        jQuery( "#dialog-deleteForm" ).dialog( "option", "buttons", {
                            "Delete All Submissions": function() {
                                // jQuery(this).dialog("close");
                                deleteAllSubmissions(formNumber);
                            },  "Cancel": function() {
                                jQuery(this).dialog("close");
                            }
                            //jQuery(this).dialog("close");
                        });

                    });

                    jQuery(".deleteForm").unbind('click').bind('click',function () {
                        jQuery("#dialog-formOptionsDisplayer").dialog('close');
                        var formNumber= jQuery(this).attr('name');
                        jQuery( "#dialog-deleteForm").dialog({ title: 'Confirm Form Delete' });
                        jQuery("#dialog-deleteForm").html('<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 25px 0;"></span>The following items will be\n\
permanently deleted and cannot be recovered. Are you sure?</p><p><span class="ui-icon ui-icon-bullet" style="float:left; margin:0 4px 0px 0;"></span> Form \n\
Metadata and Contents.</p><p><span class="ui-icon ui-icon-bullet" style="float:left; margin:0 4px 0px 0;"></span> All \n\
Submissions for this form.</p><p><span class="ui-icon ui-icon-bullet" style="float:left; margin:0 4px 0px 0;"></span> All \n\
publishing parameters for this form.</p>');
                        jQuery("#dialog-deleteForm").dialog("open");
                        jQuery( "#dialog-deleteForm" ).dialog( "option", "buttons", {
                            "Delete Form": function() {

                                deleteForm(formNumber);
                            },  "Cancel": function() {
                                jQuery(this).dialog("close");
                            }
                        });

                    });

                    jQuery(".deleteFormSubmissions").button({

                        icons: {
                            primary: 'ui-icon-trash'
                        },
                        text: true
                    }).next().button({
                        icons: {
                            primary: 'ui-icon-trash'
                        },
                        text: true
                    });

                    jQuery(".constructFormButtonForSubmission").button({

                        icons: {
                            primary: 'ui-icon-wrench'
                        },
                        text: true
                    }).next().button({
                        icons: {
                            primary: 'ui-icon-folder-open'
                        },
                        text: true
                    });
                }
            });



            jQuery("#dialog-formOptionsDisplayer").dialog('open');

        });
    }

    function deleteAllSubmissions(formNumber)
    {

        FormNumberToPost = {"formNumber":formNumber};
        var myurlToDeleteFormSubmisions = "<?php echo html_entity_decode($this->uri(array('action' => 'deleteAllFormSubmissions'), 'formbuilder')); ?>";

        jQuery('#tempdivcontainer').load(myurlToDeleteFormSubmisions, FormNumberToPost ,function postSuccessFunction(html) {
            jQuery('#tempdivcontainer').show();

            var deleteSuccess = jQuery('#tempdivcontainer').html();
            deleteSuccess = jQuery.trim(deleteSuccess);
            if (deleteSuccess == true)
            {
                jQuery("#dialog-deleteForm").dialog("close");
                jQuery( "#dialog-deleteForm").dialog({ title: 'Delete Successful' });
                jQuery("#dialog-deleteForm").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>All Submissions for this form are deleted succesfully.</p>');
                jQuery("#dialog-deleteForm").dialog("open");
                jQuery( "#dialog-deleteForm" ).dialog( "option", "buttons", { "Ok": function() {

                        jQuery("#dialog-deleteForm").dialog("close"); } } );
            }
            if (deleteSuccess == 2)
            {
                jQuery("#dialog-deleteForm").dialog("close");
                jQuery( "#dialog-deleteForm").dialog({ title: 'Delete Unsuccessful' });
                jQuery("#dialog-deleteForm").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Submissions for this form could not \n\
be deleted from the database. Possible reasons:</p><p><span class='ui-icon ui-icon-bullet' style='float:left; margin:0 4px 20px 0;'></span>This form is currently not designed correctly or not published.\n\
</p><p><span class='ui-icon ui-icon-bullet' style='float:left; margin:0 7px 20px 0;'></span>There are currently no submissions for this form.\n\
</p><p><span class='ui-icon ui-icon-bullet' style='float:left; margin:0 7px 28px 0;'></span>Form submissions are set to be sent by email only and are not saved in the database.</p>");
                jQuery("#dialog-deleteForm").dialog("open");
                jQuery( "#dialog-deleteForm" ).dialog( "option", "buttons", { "Ok": function() {
                        //  jQuery("#"+idOfElementToBeDeleted).remove();
                        jQuery("#dialog-deleteForm").dialog("close"); } } );
            }
            else
            {

            }
        });
    }
    function deleteForm(formNumber)
    {
        FormNumberToPost = {"formNumber":formNumber};
        var myurlToDeleteForm = "<?php echo html_entity_decode($this->uri(array('action' => 'deleteForm'), 'formbuilder')); ?>";

        jQuery('#tempdivcontainer').load(myurlToDeleteForm, FormNumberToPost ,function postSuccessFunction(html) {
            //jQuery('#tempdivcontainer').html(html);
            jQuery('#tempdivcontainer').show();

            var deleteSuccess = jQuery('#tempdivcontainer').html();
            deleteSuccess = jQuery.trim(deleteSuccess);
            if (deleteSuccess == true)
            {
                jQuery("#dialog-deleteForm").dialog("close");
                jQuery( "#dialog-deleteForm").dialog({ title: 'Delete Successful' });
                jQuery("#dialog-deleteForm").html('<p><span class="ui-icon ui-icon-check" style="float:left; margin:0 7px 20px 0;"></span>Form has been deleted succesfully.</p>');
                jQuery("#dialog-deleteForm").dialog("open");
                jQuery( "#dialog-deleteForm" ).dialog( "option", "buttons", { "Ok": function() {
                        // jQuery("#"+idOfElementToBeDeleted).remove();
                        jQuery("#dialog-deleteForm").dialog("close");
                        window.location.replace("<?php echo html_entity_decode($this->uri(array('action' => 'listAllForms'), 'formbuilder')); ?>");
                    } } );
            }
            if (deleteSuccess == 2)
            {
                jQuery("#dialog-deleteForm").dialog("close");
                jQuery( "#dialog-deleteForm").dialog({ title: 'Delete Unsuccessful' });
                jQuery("#dialog-deleteForm").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>This form could not \n\
be deleted from the database. Possible reasons:</p><p><span class='ui-icon ui-icon-bullet' style='float:left; margin:0 4px 20px 0;'></span>This form metadata and form element data does not match in the database.\n\
</p><p><span class='ui-icon ui-icon-bullet' style='float:left; margin:0 7px 20px 0;'></span>A form element type in your form does not exist.\n\
</p><p><span class='ui-icon ui-icon-notice' style='float:left; margin:0 7px 28px 0;'></span>Form data saved for this form has possibly been corrupted. Please contact your software administrator to solve this problem.</p>");
                jQuery("#dialog-deleteForm").dialog("open");
                jQuery( "#dialog-deleteForm" ).dialog( "option", "buttons", { "Ok": function() {
                        //  jQuery("#"+idOfElementToBeDeleted).remove();
                        jQuery("#dialog-deleteForm").dialog("close"); } } );
            }
            else
            {

            }
        });
    }

    function createTooltip(event){

        jQuery('<div class="tooltip">test<div>').appendTo('body');
        positionTooltip(event);

    }

    function positionTooltip(event){
        var tPosX = event.pageX  -10;
        var tPosY = event.pageY -100;
        jQuery('.tooltip').css({top: tPosY, left: tPosX});
    }


    function checkLength(o,n,min,max) {

        if ( o.val().length > max || o.val().length < min ) {
            o.addClass('ui-state-error');
            updateErrorMessage("Length of " + n + " must be between "+min+" and "+max+".");
            return false;
        } else {
            return true;
        }

    }
    function updateErrorMessage(errorText) {

        jQuery('.errorMessageDiv').css("color","red");
        jQuery('.errorMessageDiv').html("Error. ");

        jQuery('.errorMessageDiv').append(errorText)
        .addClass('ui-state-highlight');
        jQuery('.errorMessageDiv').show('slow');
        setTimeout(function() {
            jQuery('.errorMessageDiv').removeClass('ui-state-highlight', 1500);
        }, 1500);
    }
    function checkRegexp(o,regexp,n) {

        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass('ui-state-error');
            updateErrorMessage(n);
            return false;
        } else {
            return true;
        }

    }

    function setUpViewPublishingParametersMenu()
    {
        jQuery(".viewPublishingDataButton").unbind('click').bind('click',function () {
            var formNumber= jQuery(this).attr('name');
            //jQuery(this).unbind();
            var dataToPost = {"formNumber":formNumber};
            var myurlToProduceEditPublishingDataMenu ="<?php echo html_entity_decode($this->uri(array('action' => 'listCurrentFormGeneralandPublishingDetails'), 'formbuilder')); ?>";
            jQuery("#tempdivcontainer").load(myurlToProduceEditPublishingDataMenu , dataToPost ,function postSuccessFunction(html) {
                jQuery("#tempdivcontainer").hide();
                var publishingIndictor = jQuery("#tempdivcontainer").children("#publishingFormIndicator").html();
                publishingIndictor = jQuery.trim(publishingIndictor);


                //if (a)
                //  {
                var generalInfo = jQuery("#tempdivcontainer").children("#general").html();

                var simpleInfo = jQuery("#tempdivcontainer").children("#simple").html();
                var advancedInfo = jQuery("#tempdivcontainer").children("#advanced").html();
                jQuery("#tempdivcontainer").empty();
                jQuery("#dialog-viewPublishingParameters").children("#viewPublishingFormTabs").children("#generalPublishingFormDetails").html(generalInfo);
                jQuery("#dialog-viewPublishingParameters").children("#viewPublishingFormTabs").children("#simpleViewPublishingFormDetails").html(simpleInfo);
                jQuery("#dialog-viewPublishingParameters").children("#viewPublishingFormTabs").children("#advancedViewPublishingFormDetails").html(advancedInfo);
                jQuery("#dialog-viewPublishingParameters").dialog("open");
                jQuery("#dialog-viewPublishingParameters").children("#viewPublishingFormTabs").tabs();
                //}
                //else
                //        {
                //           jQuery("#dialog-viewPublishingParameters").children("#publishingFormIndicator").html(a+publishingIndictor+"Not published");
                //            jQuery("#dialog-viewPublishingParameters").children("#viewPublishingFormTabs").hide();
                //         jQuery("#dialog-viewPublishingParameters").dialog("open");
                //    }





            });
        });
    }
    function setUpEditPublishingParametersMenu()
    {

        jQuery(".editPublishingDataButton").unbind('click').bind('click',function () {
            var formNumber= jQuery(this).attr('name');
            var dataToPost = {"formNumber":formNumber};
            var myurlToProduceEditPublishingDataMenu ="<?php echo html_entity_decode($this->uri(array('action' => 'listCurrentFormPublishingData'), 'formbuilder')); ?>";
            jQuery("#tempdivcontainer").load(myurlToProduceEditPublishingDataMenu , dataToPost ,function postSuccessFunction(html) {
                jQuery("#tempdivcontainer").hide();
                var publishingIndictor = jQuery("#tempdivcontainer").children("#publishingFormOption").html();
                var simplePublishingForm = jQuery("#tempdivcontainer").children("#simple").html();
                var advancedPublishingForm = jQuery("#tempdivcontainer").children("#advanced").html();
                jQuery("#dialog-editPublishingParameters").children("#publishingFormOption").html(publishingIndictor);
                jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").children("#simpleEditPublishingForm").html(simplePublishingForm);
                jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").children("#advancedEditPublishingForm").html(advancedPublishingForm);
                jQuery("#dialog-editPublishingParameters").children(".errorMessageDiv").html("");
                jQuery("#tempdivcontainer").empty();
                jQuery("#dialog-editPublishingParameters").dialog("open");
                jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs();
                var simpleOrAdvancedTab =jQuery("#dialog-editPublishingParameters").children("#publishingFormOption").children('input:hidden[name=simpleOrAdvancedHiddenInput]').val();
                if (simpleOrAdvancedTab == 'advanced')
                {
                    jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs('select', 1);
                }else
                {
                    jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs('select', 0);
                }
                var simpleFormUrlChoice =  jQuery('input[name=urlChoice]');
                var advancedNextActionModule = jQuery(':input[name=nextActionModule]');
                var advancedNextAction = jQuery(':input[name=nextAction]');

                var allFields = jQuery([]).add(simpleFormUrlChoice).add(advancedNextActionModule).add(advancedNextAction);

                jQuery(":input:radio[name=publishingRadio]").button();
                jQuery(":input:radio[name=simplePostActionRadio]").button();
                jQuery("input:radio[name=simpleDivertDelayRadio]").button();
                jQuery("input:radio[name=advancedDivertDelayRadio]").button();
                if ( jQuery('input:radio[name=publishingRadio]:checked').val() == "unpublish")
                {
                    jQuery("#editPublishingFormTabs").hide("slow");
                }
                else
                {
                    jQuery("#editPublishingFormTabs").show("slow");
                }

                jQuery("input:radio[name=publishingRadio]").change(function(){

                    if ( jQuery('input:radio[name=publishingRadio]:checked').val() == "unpublish")
                    {
                        jQuery("#editPublishingFormTabs").hide("slow");
                    }
                    else
                    {
                        jQuery("#editPublishingFormTabs").show("slow");
                    }

                });
                if ( jQuery('input:radio[name=simplePostActionRadio]:checked').val() == "internal")
                {
                    jQuery("#urlInserter").hide("slow");
                    simpleFormUrlChoice.val("")
                }
                else
                {
                    jQuery("#urlInserter").show("slow");
                }
                jQuery("input:radio[name=simplePostActionRadio]").change(function(){

                    if ( jQuery('input:radio[name=simplePostActionRadio]:checked').val() == "internal")
                    {
                        jQuery("#urlInserter").hide("slow");
                        simpleFormUrlChoice.val("")
                    }
                    else
                    {
                        jQuery("#urlInserter").show("slow");
                    }

                });

                jQuery( "#dialog-editPublishingParameters" ).dialog( "option", "buttons", {
                    "Set Parameters": function() {
                        var selected =   jQuery("#dialog-editPublishingParameters").children("#editPublishingFormTabs").tabs( "option", "selected" );
                        if ( jQuery('input:radio[name=publishingRadio]:checked').val() == "unpublish")
                        {
                            addeditPublishingParameters(formNumber, '','',"","","");
                            jQuery("#dialog-editPublishingParameters" ).dialog('close');
                        }
                        else
                        {
                            if (selected == 0)
                            {
                                var bValid = true;
                                if (jQuery('input:radio[name=simplePostActionRadio]:checked').val() == "internal")
                                {
                                    var publishingOption = "simple";
                                    var urlChoice = "";
                                    jQuery("#dialog-editPublishingParameters" ).dialog('close');
                                    addeditPublishingParameters(formNumber, publishingOption, urlChoice,"","","",'');
                                }
                                else
                                {
                                    allFields.removeClass('ui-state-error');
                                    bValid = bValid && checkLength(simpleFormUrlChoice,"the dirverting site url",4,550);
                                    bValid = bValid && checkRegexp(simpleFormUrlChoice,/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,"Enter a valid url, eg. http://www.witsccmsformbuilder.ac.za");
                                    if (bValid) {
                                        jQuery("#dialog-editPublishingParameters" ).dialog('close');
                                        var publishingOption = "simple";
                                        var urlChoice = simpleFormUrlChoice.val();
                                        var delay =jQuery(':input:radio[name=simpleDivertDelayRadio]:checked').val();
                                        addeditPublishingParameters(formNumber, publishingOption, urlChoice,"","","",delay);

                                    }
                                }
                            }
                            else
                            {
                                var bValid = true;
                                allFields.removeClass('ui-state-error');
                                bValid = bValid && checkLength(advancedNextActionModule,"chisimba module",1,550);
                                bValid = bValid && checkLength(advancedNextAction,"next chisimba action",1,550);
                                if (bValid) {
                                    jQuery("#dialog-editPublishingParameters" ).dialog('close');
                                    var publishingOption = "advanced";
                                    if (jQuery('input[name=chisimbaParameters]:checked').val() ==  'on')
                                    {
                                        var  chisimbaParameters = "yes";
                                    }
                                    else
                                    {
                                        chisimbaParameters = "no";
                                    }

                                    var chisimbaModule = advancedNextActionModule.val();
                                    var chisimbaNextAction = advancedNextAction.val();
                                    var delay =jQuery('input:radio[name=advancedDivertDelayRadio]:checked').val();
                                    addeditPublishingParameters(formNumber, publishingOption, '',chisimbaNextAction,chisimbaModule,chisimbaParameters,delay);

                                }
                            }
                        }

                    },  "Cancel": function() {
                        jQuery(this).dialog("close");
                    }
                });


            });
        });



    }

    function addeditPublishingParameters(formNumber, publishingOption, urlChoice,chisimbaAction,chisimbaModule,formParameters,divertDelay)
    {

        var dataToPost = {"formNumber":formNumber, "publishingOption":publishingOption, "urlChoice":urlChoice,"chisimbaAction": chisimbaAction,"chisimbaModule":chisimbaModule,"formParameters":formParameters, "divertDelay":divertDelay };
        var myurlToAddEditPublishingData ="<?php echo html_entity_decode($this->uri(array('action' => 'addEditFormPublishingData'), 'formbuilder')); ?>";
        jQuery("#tempdivcontainer").load(myurlToAddEditPublishingData , dataToPost ,function postSuccessFunction(html) {
            jQuery("#tempdivcontainer").hide();
            jQuery("#tempdivcontainer").html(html);


        });

    }

    function setUpMainMenuIcons()
    {
        jQuery('.homeButton').button({
            text: true,
            icons: {
                primary: 'ui-icon-home'
            }
        }).next().button({
            icons: {
                primary: 'ui-icon-script'

            },
            text: true
        }).next().button({
            icons: {
                primary: 'ui-icon-document'

            },
            text: true
        }).next().button({
            icons: {
                // primary: 'ui-icon-seek-next',
                primary: 'ui-icon-help'
            },
            text: true
        });

        jQuery(".helpButton").removeAttr('onclick');
        jQuery("#dialog-box-generalHelp").children('#helpContent').tabs();
        jQuery(".helpButton").unbind("click").bind('click',function () {

            jQuery("#dialog-box-generalHelp").dialog('open');
        });

    }
    jQuery(document).ready(function() {


        jQuery("#dialog-box-generalHelp").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            zIndex: 3900,
            width:1050,
            resizable: true,
            modal: true,
            closeOnEscape: true,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                    jQuery('html, body').animate({scrollTop:0}, 'slow');
                }
            }
        });


        jQuery("#dialog-viewPublishingParameters").dialog({
            resizable: true,
            width:640,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'Close': function() {
                    jQuery(this).dialog('close');
                }
            }
        });

        jQuery("#dialog-editPublishingParameters").dialog({
            resizable: true,
            width:740,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'Close': function() {
                    jQuery(this).dialog('close');
                }
            }
        });

        jQuery("#dialog-deleteForm").dialog({
            resizable: true,
            width:440,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'Close': function() {
                    jQuery(this).dialog('close');
                }
            }
        });

        jQuery("#dialog-formOptionsDisplayer").dialog({
            resizable: true,
            width:540,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'Close': function() {
                    jQuery(this).dialog('close');
                }
            }
        });
        jQuery("#dialog-paginationIndicator").dialog({
            resizable: false,
            width:340,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'Set': function() {
                    jQuery(this).dialog('close');
                    jQuery("#dialog-paginationIndicator").children("#form_newPaginationBatchForm").submit();
                },
                Cancel: function() {

                    jQuery("input:radio[name=paginationBatchRadio]:eq(0)").attr('checked', "checked");
                    jQuery(this).dialog('close');
                }
            }
        });
        jQuery("#dialog-formDisplayer").dialog({
            resizable: true,
            width:840,
            autoOpen: false,
            modal: true,
            hide: 'clip',
            show: 'clip',
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                }
            }
        });

        var myIntObject = new intObject();

        myIntObject.i = 0;
        var formOptionsButton = jQuery('.formOptionsButton');
        var editPublishingDataButton = jQuery('.editPublishingDataButton');
        var viewSubmitResultsButton = jQuery('.viewSubmitResultsButton');


        var restrictiveUserButtons = jQuery([]).add(formOptionsButton).add(editPublishingDataButton).add(viewSubmitResultsButton);
        var numberOfPaginationRequests = jQuery("#getNumberOfPaginationRequests").html();
        var paginationbatchSize = jQuery("#getPaginationBatchSize").html();

        var searchValue = jQuery("#getFormSearchValue").html();

        jQuery(".nextButton").unbind('click').bind('click',function () {
            myIntObject.i++;
            if (jQuery('#paginationPageFormer').children().length <= 0)
            {
                var dataToPost = {"paginationRequestNumber":myIntObject.i, "paginationbatchSize":paginationbatchSize, "searchValue":searchValue};
                        var myurlToProduceMSDropdown ="<?php echo html_entity_decode($this->uri(array('action' => 'listAllFormsPaginated'), 'formbuilder')); ?>";
//                var myurlToProduceMSDropdown="?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageFormer').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
                    jQuery('#paginationPageFormer').hide();
                    jQuery('#paginationPageFormer').html(html);
                    jQuery('#paginationPageLatter').hide("slide", { direction: "left" }, 1000);
                    jQuery('#paginationPageLatter').children().remove();
                    jQuery('#paginationPageFormer').show("slide", { direction: "right" }, 1000);


                    setUpPaginationButtons(myIntObject);
                    setUpEditPublishingParametersMenu();
                    setUpViewPublishingParametersMenu();
                    setUpAccordionButtons(restrictiveUserButtons);
                    setUpPreviewFormIcons();
                    setUpFormOptionsIcons();
                    setUpAccordion();
                    setUpPaginationIndicator();

                });
            }
            else
            {
                var dataToPost = {"paginationRequestNumber":myIntObject.i,"paginationbatchSize":paginationbatchSize, "searchValue":searchValue};
                  var myurlToProduceMSDropdown ="<?php echo html_entity_decode($this->uri(array('action' => 'listAllFormsPaginated'), 'formbuilder')); ?>";
//                var myurlToProduceMSDropdown="?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageLatter').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
                    jQuery('#paginationPageLatter').hide();
                    jQuery('#paginationPageLatter').html(html);
                    jQuery('#paginationPageFormer').hide("slide", { direction: "left" }, 1000);
                    jQuery('#paginationPageFormer').children().remove();
                    jQuery('#paginationPageLatter').show("slide", { direction: "right" }, 1000);

                    setUpPaginationButtons(myIntObject);
                    setUpEditPublishingParametersMenu();
                    setUpViewPublishingParametersMenu();
                    setUpAccordionButtons(restrictiveUserButtons);
                    setUpPreviewFormIcons();
                    setUpFormOptionsIcons();
                    setUpAccordion();
                    setUpPaginationIndicator();
                });
            }
        });

        jQuery(".previousButton").unbind('click').bind('click',function () {
            myIntObject.i--;
            if (jQuery('#paginationPageFormer').children().length <= 0)
            {
                var dataToPost = {"paginationRequestNumber":myIntObject.i, "paginationbatchSize":paginationbatchSize, "searchValue":searchValue};
                  var myurlToProduceMSDropdown ="<?php echo html_entity_decode($this->uri(array('action' => 'listAllFormsPaginated'), 'formbuilder')); ?>";
//                var myurlToProduceMSDropdown="?module=formbuilder&action=listAllFormsPaginated";
                jQuery('#paginationPageFormer').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
                    jQuery('#paginationPageFormer').hide();
                    jQuery('#paginationPageFormer').html(html);
                    jQuery('#paginationPageLatter').hide("slide", { direction: "left" }, 1000);
                    jQuery('#paginationPageLatter').children().remove();
                    jQuery('#paginationPageFormer').show("slide", { direction: "right" }, 1000);

                    setUpPaginationButtons(myIntObject);
                    setUpEditPublishingParametersMenu();
                    setUpViewPublishingParametersMenu();
                    setUpAccordionButtons(restrictiveUserButtons);
                    setUpPreviewFormIcons();
                    setUpFormOptionsIcons();
                    setUpAccordion();
                    setUpPaginationIndicator();
                });
            }
            else
            {
                var dataToPost = {"paginationRequestNumber":myIntObject.i, "paginationbatchSize":paginationbatchSize, "searchValue":searchValue};
//                var myurlToProduceMSDropdown="?module=formbuilder&action=listAllFormsPaginated";
                  var myurlToProduceMSDropdown ="<?php echo html_entity_decode($this->uri(array('action' => 'listAllFormsPaginated'), 'formbuilder')); ?>";
                jQuery('#paginationPageLatter').load(myurlToProduceMSDropdown , dataToPost ,function postSuccessFunction(html) {
                    jQuery('#paginationPageLatter').hide();
                    jQuery('#paginationPageLatter').html(html);
                    jQuery('#paginationPageFormer').hide("slide", { direction: "left" }, 1000);
                    jQuery('#paginationPageFormer').children().remove();
                    jQuery('#paginationPageLatter').show("slide", { direction: "right" }, 1000);

                    setUpPaginationButtons(myIntObject);
                    setUpEditPublishingParametersMenu();
                    setUpViewPublishingParametersMenu();
                    setUpAccordionButtons(restrictiveUserButtons);
                    setUpPreviewFormIcons();
                    setUpFormOptionsIcons();
                    setUpAccordion();
                    setUpPaginationIndicator();
                });
            }
        });
        jQuery('#getPaginationBatchSize').hide();
        jQuery('#tempdivcontainer').hide();
        jQuery('#getNumberOfPaginationRequests').hide();
        setUpMainMenuIcons();
        setUpPaginationButtons(myIntObject);
        setUpEditPublishingParametersMenu();
        setUpViewPublishingParametersMenu();
        setUpAccordionButtons(restrictiveUserButtons);
        setUpPreviewFormIcons();
        setUpFormOptionsIcons();
        setUpAccordion();
        setUpPaginationIndicator();

    });



</script>
