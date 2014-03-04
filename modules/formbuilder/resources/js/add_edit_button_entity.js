//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  
 *  \brief This class models all the content and functionality for the
 * for button form element.
 * \note This js file is used inside the old WYSIWYG form eidtor and is not
 * being used any more. The code in this javascript file is dead.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
function insertButtonParameters(formElementLabel)
{
    jQuery("#addParametersForButton").show();
    jQuery("#addParametersForButton").children("#stylingForButton").show("slow");
    jQuery("#addParametersForButton").children("#endOfInsertionButton").show("slow");
    jQuery("#addParametersForButton").children("#insertButtonName").hide("slow");
    jQuery("input:radio[name=resetOrSubmitButtonRadio]:eq(0)").attr('checked', "checked");
    jQuery("#addParametersForButton").children("span").show();

    jQuery("#submitButtonParameters").unbind('click').bind('click',function () {

        var buttonLabel = jQuery('.buttonLabel').val();
        var submitOrResetButtonChoice =  jQuery("input:radio[name=resetOrSubmitButtonRadio]:checked").val();
        if (buttonLabel == "")
        {
            jQuery("#addParametersForButton").children("span").html("\
<br>ERROR. A NULL field is not allowed. Please enter a label for your button.");
            jQuery("#addParametersForButton").children("span").show("slow");
        }
        else if (submitOrResetButtonChoice== "submit")
        {
            jQuery("#addParametersForButton").children("span").html("\
<br>Parameters for a submit button labeled \""+buttonLabel+"\" has been configured.");
            jQuery("#addParametersForButton").children("#stylingForButton").hide("slow");
            jQuery("#addParametersForButton").children("#endOfInsertionButton").hide("slow");
            jQuery("#addParametersForButton").children("#insertButtonName").show("slow");
            jQuery("#addParametersForButton").children("span").show("slow");
        }
        else
        {
            jQuery("#addParametersForButton").children("span").html("\
<br>Parameters for a reset button labeled \""+buttonLabel+"\" has been configured.");
            jQuery("#addParametersForButton").children("#stylingForButton").hide("slow");
            jQuery("#addParametersForButton").children("#endOfInsertionButton").hide("slow");
            jQuery("#addParametersForButton").children("#insertButtonName").show("slow");
            jQuery("#addParametersForButton").children("span").show("slow");
        }

    });

    jQuery("#endOfInsertionButton").unbind("click").bind('click',function () {

        jQuery("#addParametersForButton").children("span").html("<br>A Button Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.");
        jQuery("#addParametersForButton").children("span").show("slow").unbind();
        jQuery("#addParametersForButton").children("#stylingForButton").hide("slow").unbind();
        jQuery("#addParametersForButton").children("#endOfInsertionButton").hide("slow").unbind();
        jQuery("#addParametersForButton").children("#insertButtonName").hide("slow").unbind();
        jQuery("#submitNameforButton").unbind()
        jQuery("#endOfInsertionButton").unbind();
        jQuery("#submitButtonParameters").unbind();
        //jQuery("*").unbind();
        insertNewFormElement();

    });

    jQuery("#submitNameforButton").click(function () {
        var formElementLabel = jQuery('.formElementLabel').val();
        var buttonLabel = jQuery('.buttonLabel').val();
        var submitOrResetButtonChoice =  jQuery("input:radio[name=resetOrSubmitButtonRadio]:checked").val();
        var buttonName = jQuery('.buttonName').val();
        if (buttonName == "")
        {
            jQuery("#addParametersForButton").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addParametersForButton").children("span").fadeIn(1500);
            jQuery("#addParametersForButton").children("span").fadeOut(1500);
        }
        else
        {
            var buttonDataToPost = {
                "formElementName": formElementLabel,
                "buttonName": buttonName,
                "buttonLabel" : buttonLabel,
                "submitOrResetButtonChoice": submitOrResetButtonChoice
            };
            var myurlToProduceButton = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceButton]").val();
            //  var myurlToProduceButton ="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditButton";
            //jQuery('#tempdivcontainer').show();
            // jQuery('#tempdivcontainer').append(defaultSelected);
            jQuery("#submitNameforButton").unbind();
            jQuery('#tempdivcontainer').load(myurlToProduceButton, buttonDataToPost ,function postSuccessFunction(html) {

                jQuery('#tempdivcontainer').html(html);

                var button = jQuery('#tempdivcontainer #WYSIWYGButton').html();
                if (button == 0)
                {
                    jQuery("#addParametersForButton").children("span").html(
                        "<br> Error. A new button object \""+buttonName+"\" has NOT been made. <br>\n\
                        It already exists in the database. Please choose a unique label");

                    insertPropertiesForFormElement("button", formElementLabel);

                }
                else
                {
                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementButton"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).html(button);
                        jQuery("#addParametersForButton").children("span").html(" A new button Object \""+buttonName+"\" has been configured and stored.");
                    }

                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(button);
                        jQuery("#addParametersForButton").children("span").html(" A new button Object \""+buttonName+"\" has been configured and stored.");

                    }
                    insertPropertiesForFormElement("button", formElementLabel);
                }

            });

        }
    });

}
