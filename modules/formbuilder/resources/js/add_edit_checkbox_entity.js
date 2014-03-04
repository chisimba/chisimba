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
function insertCheckBoxEntityParameters(formElementLabel)
{
    jQuery('#Sort').unbind();
       jQuery('#Stop').unbind();

jQuery("#addOptionnValueForFormElements").show();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setMenuSize").hide();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").children("#setCustomMenuSize").hide();
    jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").show("slow");

    jQuery("label[for='submitFormElementLayout']").html("Select Layout for Checkbox Entity:");
    jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide();
    jQuery("#addOptionnValueForFormElements #endOfInsertion").show();
    jQuery("#addOptionnValueForFormElements .formEntityDefaultOption").show();
    jQuery("#addOptionnValueForFormElements").children("span").show("slow");
    jQuery("#addOptionnValueForFormElements #submitFormElementLayout").attr("value","Apply Layout and Insert Option for Checkbox Item");

    jQuery("#submitFormElementLayout").unbind('click').bind('click',function () {
        jQuery("#addOptionnValueForFormElements").children("span").html("<br>Layout for Checkbox Option Has Been Stored.");
        jQuery("#addOptionnValueForFormElements").children("span").show("slow");
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow");
        jQuery("#addOptionnValueForFormElements #insertOptionMenu").fadeIn(500);
    });


    jQuery("#submitEndOfInsetion").unbind('click').bind('click',function () {

        jQuery("#addOptionnValueForFormElements").children("span").html("<br>A Checkbox Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.").unbind();
        jQuery("#addOptionnValueForFormElements").children("span").show("slow").unbind();
        //jQuery("#addOptionnValueForFormElements").children("span").fadeOut(1000);
        jQuery("#addOptionnValueForFormElements").children("#selectLayoutMenu").hide("slow").unbind();
        jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide().unbind();
        jQuery("#addOptionnValueForFormElements #endOfInsertion").hide().unbind();
        jQuery('input[name=Default Value]').attr('checked', false).unbind();
            jQuery("#submitFormElementLayout").unbind();
            jQuery("#submitEndOfInsetion").unbind();
              jQuery("#submitOptionforFormElement").unbind();
        insertNewFormElement();
    });

    jQuery("#addOptionnValueForFormElements .formEntityDefaultOptionLabel").html("\"Check\" this this option selected as default.");
    jQuery(".formEntityValueOption").html("Enter a value for your checkbox option:");
    jQuery(".formEntityLabelOption").html("Enter a label for your checkbox option:");
    jQuery("#submitOptionforFormElement").attr("value","Insert Checkbox Option");

    jQuery("#submitOptionforFormElement").click(function () {
        var optionValue = jQuery('.formEntityValue').val();
        var optionLabel = jQuery('.formEntityLabel').val();
        var layoutOption= jQuery('input:radio[name=formEntityLayout]:checked').val();
        var defaultSelected = jQuery(".formEntityDefaultOption:checked").val();
        var formElementLabel = jQuery('.formElementLabel').val();

        if (optionValue == "" ||optionLabel == "")
        {
            jQuery("#addOptionnValueForFormElements span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addOptionnValueForFormElements span").fadeIn(1500);
            jQuery("#addOptionnValueForFormElements span").fadeOut(1500);
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
            // var myurl="http://146.141.77.59/chisimbasalman/index.php?module=formbuilder&action=addEditCheckboxEntity";
            var myurlToProduceCheckbox = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceCheckbox]").val();
           // jQuery('#tempdivcontainer').show();
            jQuery("#submitOptionforFormElement").unbind();
            jQuery('#tempdivcontainer').load(myurlToProduceCheckbox, dataToPost ,function postSuccessFunction(html) {
                jQuery('#tempdivcontainer').html(html);
                var checkbox = jQuery('#tempdivcontainer #WYSIWYGCheckbox').html();
                if (checkbox == 0)
                {
                    jQuery("#addOptionnValueForFormElements span").html(
                        "<br> ERROR!! A new checkbox option \""+optionValue+"\" has NOT been made. <br> \n\
\""+optionValue+"\" option already exists for \""+formElementLabel+"\" checkbox. Please enter a unique option value.");
                    //jQuery("#addOptionnValueForFormElements span").fadeIn(2000);
                    // jQuery("#addOptionnValueForFormElements span").fadeOut(2500);
                    jQuery("#addOptionnValueForFormElements #insertOptionMenu").hide(100);
                    insertPropertiesForFormElement("checkbox", formElementLabel);
                    jQuery('input[name=Default Value]').attr('checked', false);
                }
                else
                {
                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementCheckBox"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).html(checkbox);
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" dropdown menu has been configured and stored.<br>\n\
<br> The option with value \""+optionValue+"\" has been set as default");
                    }
                    else
                    {
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(checkbox);
                        jQuery("#addOptionnValueForFormElements span").html("An option for the \""+formElementLabel+"\" checkbox has been configured and stored.");

                    }
                    if (jQuery(".formEntityDefaultOption:checked").val() =="on")
                    {
                        jQuery('input[name=Default Value]').attr('checked', false);
                        jQuery("#addOptionnValueForFormElements").children("span").html(
                            "An option for the \""+formElementLabel+"\" checkbox element has been configured and stored.<br>\n\
<br> The option with value \""+optionValue+"\" has been set as default");
                    }

                    insertPropertiesForFormElement("checkbox", formElementLabel);

                }
            }
            );
        }
    });
}