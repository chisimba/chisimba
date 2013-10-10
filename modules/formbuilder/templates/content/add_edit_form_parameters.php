<style type="" >

    input.text {  padding: .4em; margin-bottom: 8px; }
    div#formSubmissionRadio span.ui-button-text{width:420px}

</style>

<div style ="font-size: 62.5%;" id="dialog-newFormParametersForm" title="New Form Parameters Form">
<div style =" border: 1px solid transparent; padding: 7px" class="errorMessageDiv"> </div>
    <?php
    /*! \file add_edit_form_parameters.php
 * \brief The template file is called by the actions addFormParameters
     * editFormParameters to insert the metadata new forms and update them
     * for existing forms.
 * \section sec Explanation
 * - Request whether the action is for editing and or creating metadata for
     * a new form.
     * - Get the metadata form and display it into dialog box.
     * - Include the javascript member functions for validation.
     * - Depending on the action, once the user hits submit, the form will
     * POST the relevant action for inserting or updating metadata.
     * \note The javascript member functions insertFields and testSubmitButton
     * are not used and there are dead code.
*/
    $jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
    echo ($jqueryUILoader->includeJqueyUI());

    $objEditForm = $this->getObject('add_form_parameters_form', 'formbuilder');
    echo $objEditForm->show();
    ?>
</div>

<div id="getEditOrAddAction">
    <?php
 $action = $this->getParam("action", "addFormParameters");
if ($action == "editFormParameters") {
   $action = "edit";
}
else
{
$action = "add";
}
    echo trim("$action");
    ?>

</div>

   <div id="dialog-box-generalHelp" title="Form Metadata Help">
<div id="helpContent">
    <?php
                $content = $this->getObject('help_page_handler', 'formbuilder');
echo $pageContent=$content->showContent('metadata',1);
    ?>
      </div>
</div>

<div id="tempdivcontainer"></div>

<script type="text/javascript">
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
    function submitFormParameters()
    {
       // var formTitle = jQuery(':input[name=formTitle]').val();
       // formTitle = jQuery.trim(formTitle);

        var formLabel = jQuery(':input[name=formLabel]').val();
        formLabel = jQuery.trim(formLabel);

                var formEmail = jQuery(':input[name=formEmail]').val();
        formEmail = jQuery.trim(formEmail);

        var submissionOption = jQuery('#input_formSubmissionRadio').val();

        var formDescription = jQuery('textarea[name=formCaption]').val();
        formDescription = jQuery.trim(formDescription);



        var formParametersToPost = {
            "formLabel": formLabel,
            "formEmail" : formEmail,
            "submissionOption" : submissionOption,
            "formDescription" : formDescription};
        jQuery('#tempdivcontainer').hide();
         var myurlToStoreFormParameters = "<?php echo html_entity_decode($this->uri(array('action'=>'addNewFormParameters'),'formbuilder')); ?>";
        
        jQuery('#tempdivcontainer').load(myurlToStoreFormParameters, formParametersToPost ,function postSuccessFunction(html) {
            jQuery('#tempdivcontainer').html(html);
           //             jQuery('#tempdivcontainer').show();
            var postSuccess = jQuery('#tempdivcontainer #insertFormDetailsSuccessParameter').html();
            var formNumber = jQuery('#tempdivcontainer #insertFormNumber').html();
            if (postSuccess == 0)
            {
                updateErrorMessage("The Form Database Name \""+formLabel+"\" has NOT been made. <br>\n\
It already exists in the database. Please choose a unique form name");
                jQuery(':input[name=formLabel]').addClass('ui-state-error');
            }
            else
            {
                jQuery("#dialog-newFormParametersForm").children("#form_formDetails").children(":input[type='hidden']").attr("value",formNumber);
                jQuery("#tempdivcontainer").children('#insertFormNumber').children("#form_formDetails").submit();
            }
        });
    }
    jQuery(document).ready(function() {

        //jQuery("#formSubmissionRadio").buttonset();
        jQuery('.errorMessageDiv').hide();
          jQuery('#getEditOrAddAction').hide();

       // var formTitle = jQuery(':input[name=formTitle]');
        var formLabel = jQuery(':input[name=formLabel]');
        var formEmail = jQuery(':input[name=formEmail]');
        var formDescription = jQuery('textarea[name=formCaption]');
        var allFields = jQuery([]).add(formLabel).add(formDescription).add(formEmail);

            jQuery("#dialog-box-generalHelp").dialog({
            autoOpen: false,
            show: 'clip',
            hide: 'clip',
            zIndex: 3900,
            width:1050,
            resizable: true,
            modal: true,
                        closeOnEscape: false,
            buttons: {
                'OK': function() {
                    jQuery(this).dialog('close');
                     jQuery('html, body').animate({scrollTop:0}, 'slow');
                }
            }
        });

//jQuery('.ui-dialog-titlebar-close').parent().append('<div class="ui-dialog-titlebar-mini" id="'+id+'">minimize</div>');
//        jQuery(':input[name=formTitle]').bind('keypress keydown keyup',function() {
//            var test = jQuery(':input[name=formTitle]').val();
//            var alphabetic= "qwertyuiopasdfghjklzxcvbnm1234567890";
//            var symbols = ",./;'[]\\=-<>?:\"{}|+_)(*&$#@!";
//            var test1 = test.charAt(test.length-1);
//
//            var boolalphebetic = alphabetic.search(test1);
//            if (boolalphebetic == -1)
//            {
//                var test= test.substr(0,test.length-1);
//            }
//            var boolsymbols = symbols.search(test1);
//            if (boolsymbols == 0)
//            {
//                var test= test.substr(0,test.length-1);
//            }
//            jQuery(':input[name=formTitle]').val(test);
//        });
             var editOrAddAction = jQuery('#getEditOrAddAction').html();
             editOrAddAction = jQuery.trim(editOrAddAction);
               
             if (editOrAddAction == "edit")
                 {
                      jQuery("#dialog-newFormParametersForm").attr("title","Edit Form Metadata");
                  //      jQuery("#dialog-newFormParametersForm").children("#form_formDetails").children("#input_formTitle").attr("disabled", true);
//jQuery(':input[name=formTitle]').addClass('ui-state-disabled');

               jQuery("#dialog-newFormParametersForm").dialog({
            resizable: true,
            width: 540,
            modal: true,
            show: 'clip',
            hide: 'clip',
            closeOnEscape: false,
            buttons: {
                'Help': function() {
            jQuery("#dialog-box-generalHelp").dialog('open');

                },
              'Update Form Metadata': function() {
                    var bValid = true;
                    allFields.removeClass('ui-state-error');
                 //   bValid = bValid && checkRegexp(formTitle,/^([0-9a-zA-Z])+$/,"The database name field only allow alphanumeric characters (a-z 0-9).");
                  //  bValid = bValid && checkLength(formTitle,"database name",4,100);
                    bValid = bValid && checkLength(formLabel,"form title",3,100);
                    bValid = bValid && checkRegexp(formEmail,/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Enter a valid email address, eg. yourname@wits.ac.za.");
		//bValid = bValid && checkRegexp(password,/^([0-9a-zA-Z])+$/,"Password field only allow : a-z 0-9");
                    bValid = bValid && checkLength(formDescription,"form description",7,1000);
                    if (bValid) {
                    jQuery(this).dialog('close');
                     jQuery("#dialog-newFormParametersForm").children("#form_formDetails").children("#input_formTitle").removeAttr("disabled"); 
               // jQuery("#dialog-newFormParametersForm").children("#form_formDetails").submit();
                jQuery("form").submit();
                    }
                },
                Cancel: function() {
                  var myurlToLoadAllForms ="<?php echo html_entity_decode($this->uri(array('action'=>'listAllForms'),'formbuilder')); ?>";
                             jQuery(this).dialog('close');
                  window.location.replace(myurlToLoadAllForms);
       
                }
            }
        });

           jQuery(".ui-dialog-buttonset").css('width','510px');
            jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
          var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          btnHelp.css('float', 'left');
              jQuery( "#dialog-newFormParametersForm" ).bind( "dialogclose", function(event, ui) {
                  var myurlToLoadAllForms ="<?php echo html_entity_decode($this->uri(array('action'=>'listAllForms'),'formbuilder')); ?>";
                       //      jQuery(this).dialog('close');
                  window.location.replace(myurlToLoadAllForms);
    });
             }
                 else
                     {

                                   
                                    jQuery("#dialog-newFormParametersForm").attr("title","Create New Form");
                                  jQuery("#dialog-newFormParametersForm").dialog({
            resizable: true,
            width: 540,
            modal: true,
            show: 'clip',
            hide: 'clip',
            buttons: {
                'Help': function() {
            jQuery("#dialog-box-generalHelp").dialog('open');

                },
              'Create Form': function() {
                    var bValid = true;
                    allFields.removeClass('ui-state-error');
                   // bValid = bValid && checkRegexp(formTitle,/^([0-9a-zA-Z])+$/,"The database name field only allow alphanumeric characters (a-z 0-9).");
                   // bValid = bValid && checkLength(formTitle,"database name",4,100);
                    bValid = bValid && checkLength(formLabel,"form title",3,100);
                    bValid = bValid && checkRegexp(formEmail,/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"Enter a valid email address, eg. yourname@wits.ac.za.");
		//bValid = bValid && checkRegexp(password,/^([0-9a-zA-Z])+$/,"Password field only allow : a-z 0-9");
                    bValid = bValid && checkLength(formDescription,"form description",7,1000);
                    if (bValid) {
                        submitFormParameters();
                    }
                },
                Cancel: function() {
                  var myurlToLoadAllForms ="<?php echo html_entity_decode($this->uri(array('action'=>'home'),'formbuilder')); ?>";
                             jQuery(this).dialog('close');
                  window.location.replace(myurlToLoadAllForms);
       
                }
            }
        });
                   jQuery(".ui-dialog-buttonset").css('width','510px');
            jQuery(".ui-dialog-buttonpane").find("button").css('float', 'right');
          var  btnHelp = jQuery('.ui-dialog-buttonpane').find('button:contains("Help")');
          btnHelp.css('float', 'left');

                        jQuery( "#dialog-newFormParametersForm" ).bind( "dialogclose", function(event, ui) {
                  var myurlToLoadAllForms ="<?php echo html_entity_decode($this->uri(array('action'=>'home'),'formbuilder')); ?>";
                            // jQuery(this).dialog('close');
                  window.location.replace(myurlToLoadAllForms);
    });
                   }
       
      
    });


    function insertFields()
    {
        jQuery(':input[name=formTitle]').live('keypress keydown keyup',function() {

            var test = jQuery(':input[name=formTitle]').val();
            if (test.length <= 3)
            {
                jQuery('#formNameIcon').attr("src","skins/_common/icons/failed.gif");
                jQuery('#formNameIcon').attr("title","Database Name of less than 3 characters is not allowed");
                testSubmitButton();
            }
            if (test.length >= 5)
            {
                jQuery('#formNameIcon').attr("src","skins/_common/icons/warning.gif");
                jQuery('#formNameIcon').attr("title","Database Name of less than 15 characters is weak");
                testSubmitButton();
            }
            if (test.length > 15)
            {
                jQuery('#formNameIcon').attr("src","skins/_common/icons/ok.gif");
                jQuery('#formNameIcon').attr("title","Database Name is strong");
                testSubmitButton();
            }

        });

        jQuery(':input[name=formLabel]').live('keypress keydown keyup',function() {
            var test = jQuery(':input[name=formLabel]').val();
            if (test.length <= 3)
            {
                jQuery('#formLabelIcon').attr("src","skins/_common/icons/failed.gif");
                jQuery('#formLabelIcon').attr("title","Title of less than 3 characters is not allowed");
                testSubmitButton();
            }
            if (test.length >= 5)
            {
                jQuery('#formLabelIcon').attr("src","skins/_common/icons/warning.gif");
                jQuery('#formLabelIcon').attr("title","Form title of less than 10 characters is weak");
                testSubmitButton();
            }
            if (test.length > 10)
            {
                jQuery('#formLabelIcon').attr("src","skins/_common/icons/ok.gif");
                jQuery('#formLabelIcon').attr("title","form title is strong");
                testSubmitButton();
            }

        });

        jQuery('textarea[name=formCaption]').live('keypress keydown keyup',function() {
            var test = jQuery('textarea[name=formCaption]').val();
            if (test.length <= 5)
            {
                jQuery('#formDescriptionIcon').attr("src","skins/_common/icons/failed.gif");
                jQuery('#formDescriptionIcon').attr("title","Form Description of less than 5 characters is not allowed");
                testSubmitButton();
            }
            if ( test.length >= 5)
            {
                jQuery('#formDescriptionIcon').attr("src","skins/_common/icons/warning.gif");
                jQuery('#formDescriptionIcon').attr("title","Form Description of less than 25 characters is weak");
                testSubmitButton();
            }
            if (test.length > 25)
            {
                jQuery('#formDescriptionIcon').attr("src","skins/_common/icons/ok.gif");
                jQuery('#formDescriptionIcon').attr("title","Form Description is strong");
                testSubmitButton();
            }

        });
    }
    function testSubmitButton()
    {
        var formDesciptionOK = jQuery('#formDescriptionIcon').attr("src");
        var formLabelOK = jQuery('#formLabelIcon').attr("src");
        var formTitleOK = jQuery('#formNameIcon').attr("src");

        if (formTitleOK == "skins/_common/icons/ok.gif" && formLabelOK == "skins/_common/icons/ok.gif" && formDesciptionOK == "skins/_common/icons/ok.gif")
        {
            jQuery(':input[name=submitNewFormDetails]').removeAttr("disabled");
            //jQuery(':input[name=submitNewFormDetails]').("type");
            jQuery(':input[name=submitNewFormDetails]').children('span').children('span').children('span').html("Submit General Form Details");
            jQuery(':input[name=submitNewFormDetails]').children('span').children('span').children('span').removeClass();
            jQuery(':input[name=submitNewFormDetails]').children('span').children('span').children('span').addClass('ok');
        }
        else
        {
            jQuery(':input[name=submitNewFormDetails]').attr("disabled","false");
            jQuery(':input[name=submitNewFormDetails]').children('span').children('span').children('span').html("Complete All Fields");
            jQuery(':input[name=submitNewFormDetails]').children('span').children('span').children('span').removeClass();
           // jQuery(':input[name=submitNewFormDetails]').children('span').children('span').children('span').addClass('decline');
        }


    }
</script>