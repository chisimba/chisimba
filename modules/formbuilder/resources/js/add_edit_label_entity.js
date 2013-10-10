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
function insertLabelParameters(formElementLabel)
{

   // reset();
    jQuery("#addParametersForTextElements").show();
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").show("slow");
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").children("#LayoutMenu").show();
    jQuery("#addParametersForTextElements").children("#insertTextMenu").hide();
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").children("#SizeMenu").hide();
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").children("#AlignMenu").hide();
    jQuery("#addParametersForTextElements").children("span").show();
    jQuery("#submitTextLayout").unbind("click").bind('click',function () {
        jQuery("#addParametersForTextElements").children("#selectLayoutMenu").hide('slow');
        jQuery("#addParametersForTextElements").children("#insertTextMenu").show('slow');
    });

    jQuery("#submitEndOfInsertionText").unbind('click').bind('click',function () {
        jQuery("#addParametersForTextElements").children("span").html("<br>A Label has been Inserted and Configured.<br>\n\
Please choose your next Form element.").unbind();
        jQuery("#addParametersForTextElements").children("#selectLayoutMenu").show().unbind();
        jQuery("#addParametersForTextElements").children("span").show('slow').unbind();
        jQuery("#addParametersForTextElements").children("#selectLayoutMenu").hide("slow").unbind();
        jQuery("#addParametersForTextElements").children("#insertTextMenu").hide("slow").unbind();
        jQuery("#submitEndOfInsertionText").unbind();
        jQuery("#submitTextforFormElement").unbind();
        jQuery("#submitTextLayout").unbind();
        insertNewFormElement();


    });

    jQuery("#submitTextforFormElement").unbind('click').bind('click',function () {
        var labelValue = jQuery('.formEntityText').val();
        var layoutOption= jQuery('input:radio[name=textLayout]:checked').val();
        var formElementLabel = jQuery('.formElementLabel').val();
        if (labelValue == "")
        {
            jQuery("#addParametersForTextElements").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addParametersForTextElements").children("span").fadeIn(1500);
            jQuery("#addParametersForTextElements").children("span").fadeOut(1500);
        }
        else
        {
            var dataToPost = {
                "labelValue":labelValue,
                "formElementName":formElementLabel,
                "layoutOption":layoutOption
            };
            //var myurlToCreateLabel ="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditLabelEntity";
            var myurlToCreateLabel = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceLabel]").val();
            jQuery("#submitTextforFormElement").unbind();
            jQuery('#tempdivcontainer').load(myurlToCreateLabel, dataToPost ,function postSuccessFunction(html) {
                jQuery('#tempdivcontainer').html(html);

                var label = jQuery('#tempdivcontainer #WYSIWYGLabel').html();
                if (label == 0)
                {
                    jQuery("#addParametersForTextElements").children("span").html(
                        "<br> ERROR!! A new label \""+labelValue+"\" has NOT been made. <br> \n\
\""+labelValue+"\" already exists for \""+formElementLabel+"\" label. Please enter a unique label value.");

                    jQuery("#addParametersForTextElements").children("#insertTextMenu").hide("slow");
                    insertPropertiesForFormElement("label", formElementLabel);
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementLabel"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).html(label);
                        jQuery("#addParametersForTextElements").children("span").html("A label \""+formElementLabel+"\" has been configured and stored.");
                    }

                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(label);
                        jQuery("#addParametersForTextElements").children("span").html("A label \""+formElementLabel+"\" has been configured and stored.");
                    }

                    insertPropertiesForFormElement("label", formElementLabel);
                }
            });
        }
    });

}