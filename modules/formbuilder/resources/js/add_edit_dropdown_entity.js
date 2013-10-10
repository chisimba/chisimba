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
function insertDropdownEntityParameters(formElementLabel)
{
    jQuery('#Sort').unbind();
       jQuery('#Stop').unbind();
    jQuery("#addOptionnValueForFormElements").show();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").show("slow");
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setMenuSize").hide();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setCustomMenuSize").hide();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#LayoutMenu").hide();
    jQuery("#addOptionnValueForFormElements #submitFormElementLayout").show();
    jQuery("#addOptionnValueForFormElements #submitFormElementLayout").attr("value","Insert Option for Dropdown Menu");
    jQuery("label[for='submitFormElementLayout']").html("Select Layout for Dropdown Entity:");
    jQuery("#addOptionnValueForFormElements").children("span").show("slow");
    jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide();
    jQuery("#addOptionnValueForFormElements #endOfInsertion").show();

    jQuery("#submitFormElementLayout").unbind('click').bind('click',function () {
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow");
        jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").show("slow");
    });

    jQuery("#submitEndOfInsetion").unbind('click').bind('click',function () {
        jQuery("#addOptionnValueForFormElements").children("span").html("<br>A Dropdown Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.").unbind();
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#LayoutMenu").show();
        jQuery("#addOptionnValueForFormElements").children("span").show("slow").unbind();
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow").unbind();
        jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide(100).unbind();
        jQuery("#addOptionnValueForFormElements #endOfInsertion").hide().unbind();
        jQuery('input[name=Default Value]').attr('checked', false).unbind();
        jQuery("#submitEndOfInsetion").unbind();
        jQuery("#submitOptionforFormElement").unbind();
            jQuery("#submitFormElementLayout").unbind();
           // jQuery("#WYSIWYGForm").unbind();
        insertNewFormElement();
    });


    if (jQuery(".formEntityDefaultOption:checked").val() == "on" )
    {
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").html("Selected Default Option has been chosen.");
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").hide();
        jQuery('input[name=Default Value]').attr('checked', false);
    }
    else
    {
        jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").html("Set this option selected as default.");
    }

    jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").html("Set this this option selected as default.");
    jQuery(".formEntityValueOption").html("Enter a value for your dropdown option:");
    jQuery(".formEntityLabelOption").html("Enter a label for your dropdown option:");
    jQuery("#submitOptionforFormElement").attr("value","Insert Dropdown Option");

    jQuery("#submitOptionforFormElement").unbind('click').bind('click',function () {
        var optionValue = jQuery('.formEntityValue').val();
        var optionLabel = jQuery('.formEntityLabel').val();
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
                "defaultSelected":defaultSelected
            };
            //  var myurl="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditDropdownEntity";
            var myurlToProduceDropdown = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceDropdown]").val();
            jQuery("#submitOptionforFormElement").unbind();
                jQuery('#Sort').unbind();
       jQuery('#Stop').unbind();
            jQuery('#tempdivcontainer').load(myurlToProduceDropdown , dataToPost ,function postSuccessFunction(html) {
                jQuery('#tempdivcontainer').html(html);
                var dropdown = jQuery('#tempdivcontainer #WYSIWYGDropdown').html();
                if (dropdown == 0)
                {
                    jQuery("#addOptionnValueForFormElements").children("span").html(
                        "<br> ERROR!! A new dropdown option \""+optionValue+"\" has NOT been made. <br> \n\
\""+optionValue+"\" option already exists for \""+formElementLabel+"\" dropdown. Please enter a unique option value.");
                    jQuery("#addOptionnValueForFormElements").children("#insertOptionMenu").hide("slow");
                    if(  jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").is(":visible") == true )
                    {
                        jQuery('input[name=Default Value]').attr('checked', false);
                    }
                    insertPropertiesForFormElement("dropdown", formElementLabel);
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementDropDown"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append('<div id =input_'+formElementLabel+'></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).children("#input_"+formElementLabel).replaceWith(dropdown);
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.");
                    }
                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).children("#input_"+formElementLabel).replaceWith(dropdown);
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.");
                    }

                    if (jQuery(".formEntityDefaultOption:checked").val() =="on")
                    {
                        jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").hide();
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.<br>\n\
<br> The option with value \""+optionValue+"\" has been set as default");

                    }
                    insertPropertiesForFormElement("dropdown", formElementLabel);
                }
            }
            );

        }
    });
}