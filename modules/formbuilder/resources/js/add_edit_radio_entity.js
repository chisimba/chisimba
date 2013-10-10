//function insertRadioEntityParameters(formElementLabel)
//{
//     jQuery("label[for='submitFormElementLayout']").html("Select Layout for Radio Entity:");
//         var data = jQuery("label[for='submitFormElementLayout']").html();
//     data = data +"<br>"+ jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#LayoutMenu").html();
//    //var data =
//    //jQuery("*").unbind();
//
//   //var test = jQuery("#setCustomMenuSize").html();
//    jQuery("#dialog-confirm").html(data);
//       jQuery("#dialog-confirm").attr("title","Radio Option Layout Menu");
//    jQuery("#dialog-confirm").dialog('open');
//    		jQuery("#dialog-confirm").dialog({
//    			show: 'clip',
//    			hide: 'clip',
//                          resizable: true,
//                         autoOpen : true,
//                         bgiframe: true,
//    		width:700,
//
//                               autoSize: true,
//    		modal: true,
//    		buttons: {
//    			'Set': function() {
//    				jQuery(this).dialog('close');
//                                        var myurlToProduceButton = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceButton]").val();
//      var buttonDataToPost = {"formElementName": "formElementLabel1",
//                      "buttonName": "buttonName1", "buttonLabel" : "buttonLabel1",
//                      "submitOrResetButtonChoice": "submit"};
//
//     jQuery('#tempdivcontainer').load(myurlToProduceButton, buttonDataToPost ,function postSuccessFunction(html) {
//
//         jQuery('#tempdivcontainer').html(html);
//                   jQuery('#tempdivcontainer').show();
//     });
//
//    			},
//    			Cancel: function() {
//    				jQuery(this).dialog('close');
//    			}
//    		}
//    	});
//    jQuery('#Sort').unbind();
//       jQuery('#Stop').unbind();
//
//          jQuery("label[for='submitFormElementLayout']").html("Select Layout for Radio Entity:");
//
//}
//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!
 *  \brief This class models all the content and functionality for the
 * for WYSIWYG form element.
 * \note This js file is used inside the old WYSIWYG form eidtor and is not
 * being used any more. The code in this javascript file is dead.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
function insertRadioEntityParameters(formElementLabel)
{

          


    jQuery("#addOptionnValueForFormElements").show();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setMenuSize").hide();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setCustomMenuSize").hide();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").show("slow");
    jQuery("label[for='submitFormElementLayout']").html("Select Layout for Radio Entity:");
    jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide();
    jQuery("#addOptionnValueForFormElements #endOfInsertion").show();
    jQuery("#addOptionnValueForFormElements").children("span").show("slow");
    jQuery("#addOptionnValueForFormElements #submitFormElementLayout").attr("value","Apply Layout and Insert Option for Radio Entity");

    jQuery("#submitFormElementLayout").unbind('click').bind('click',function () {
        jQuery("#addOptionnValueForFormElements").children("span").html("<br>Layout for Radio Option Has Been Stored.");
        jQuery("#addOptionnValueForFormElements").children("span").show("slow");
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow");
        jQuery("#addOptionnValueForFormElements #insertOptionMenu").fadeIn(1000);
    });


    jQuery("#submitEndOfInsetion").unbind("click").bind('click',function () {
        jQuery("#addOptionnValueForFormElements").children("span").html("<br>A Radio Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.");
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").show().unbind();
        jQuery('input[name=Default Value]').attr('checked', false).unbind();
        jQuery("#addOptionnValueForFormElements").children("span").show("slow").unbind();
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow").unbind();
        jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide().unbind();
        jQuery("#addOptionnValueForFormElements #endOfInsertion").hide().unbind();
        jQuery("#submitEndOfInsetion").unbind();
jQuery("#submitFormElementLayout").unbind();
 jQuery("#submitOptionforFormElement").unbind();
        insertNewFormElement();
    });


    if (jQuery(".formEntityDefaultOption:checked").val() == "on" )
    {
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").html("Selected Default Option has been chosen.");
        jQuery('input[name=Default Value]').attr('checked', false);
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").hide(5500);

    }
    else
    {
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").html("Set this option selected as default.");
    }
    jQuery(".formEntityValueOption").html("Enter a value for your radio option:");
    jQuery(".formEntityLabelOption").html("Enter a label for your radio option:");
    jQuery("#submitOptionforFormElement").attr("value","Insert Radio Option");

    jQuery("#submitOptionforFormElement").click(function () {
        var optionValue = jQuery('.formEntityValue').val();
        var optionLabel = jQuery('.formEntityLabel').val();
        var layoutOption= jQuery('input:radio[name=formEntityLayout]:checked').val();
        var defaultSelected = jQuery(".formEntityDefaultOption:checked").val();
        var formElementLabel = jQuery('.formElementLabel').val();

        if (optionValue == "" ||optionLabel == "")
        {
            jQuery("#addOptionnValueForFormElements").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addOptionnValueForFormElements").children("span").fadeIn(1500);
            jQuery("#addOptionnValueForFormElements").children("span").fadeOut(1500);
        }
        else
        {

            var dataToPost = {
                "optionValue":optionValue,
                "optionLabel":optionLabel,
                "formElementName":formElementLabel,
                "defaultSelected":defaultSelected,
                "layoutOption":layoutOption
            };
            // var myurl="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditRadioEntity";
            var myurlToCreateRadio = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceRadio]").val();
            jQuery("#submitOptionforFormElement").unbind();
            jQuery('#tempdivcontainer').load(myurlToCreateRadio, dataToPost ,function postSuccessFunction(html) {
                jQuery('#tempdivcontainer').html(html);
                var radio = jQuery('#tempdivcontainer #WYSIWYGRadio').html();
                if (radio == 0)
                {
                    jQuery("#addOptionnValueForFormElements").children("span").html(
                        "<br> ERROR!! A new radio option \""+optionValue+"\" has NOT been made. <br> \n\
\""+optionValue+"\" option already exists for \""+formElementLabel+"\" radio. Please enter a unique option value.");
                    jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide();
                    insertPropertiesForFormElement("radio", formElementLabel);
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementRadio"></div>');
                        // jQuery("#WYSIWYGForm").children("#"+formElementLabel).append('<div id =input_'+formElementLabel+'></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).html(radio);
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" radio element has been configured and stored.");
                    }

                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(radio);
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" radio menu has been configured and stored.");

                    }

                    if (jQuery(".formEntityDefaultOption:checked").val() =="on")
                    {
                        jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").hide();
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" radio element has been configured and stored.<br>\n\
<br> The option with value \""+optionValue+"\" has been set as default");

                    }

                    insertPropertiesForFormElement("radio", formElementLabel);
                }

            }
            );
        }
    });
}
