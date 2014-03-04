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
function insertHTMLHeadingParameters(formElementLabel)
{
    jQuery("#addParametersForTextElements").show();
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").show("slow");
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").children("#LayoutMenu").hide();
    jQuery("#addParametersForTextElements").children("#insertTextMenu").hide();
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").children("#SizeMenu").show();
    jQuery("#addParametersForTextElements").children("#selectLayoutMenu").children("#AlignMenu").show();

    jQuery("#submitTextLayout").click(function () {
        jQuery("#addParametersForTextElements").children("#selectLayoutMenu").hide("slow");
        jQuery("#addParametersForTextElements").children("#insertTextMenu").show("slow");
    });

    jQuery("#submitEndOfInsertionText").click(function () {
        jQuery("#addParametersForTextElements").children("span").html("<br>A HTML Heading object has been Inserted and Configured.<br>\n\
Please choose your next Form element.");
        jQuery("#addParametersForTextElements").children("#selectLayoutMenu").show().unbind();
        jQuery("#addParametersForTextElements").children("span").show("slow").unbind();
        //jQuery("#addParametersForTextElements span").fadeOut(2000);
        jQuery("#addParametersForTextElements").children("#selectLayoutMenu").hide().unbind();
        jQuery("#addParametersForTextElements").children("#insertTextMenu").hide().unbind();
            jQuery("#submitTextLayout").unbind();
            jQuery("#submitTextforFormElement").unbind();
            jQuery("#submitEndOfInsertionText").unbind();
        insertNewFormElement();
    });

    jQuery("#submitTextforFormElement").click(function () {
        var HTMLHeadingValue = jQuery('.formEntityText').val();
        var fontSize = jQuery('input:radio[name=formEntitySize]:checked').val();
        var textAlignment = jQuery('input:radio[name=formEntityAlign]:checked').val();
        var formElementLabel = jQuery('.formElementLabel').val();
        if (HTMLHeadingValue == "")
        {
            jQuery("#addParametersForTextElements").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addParametersForTextElements").children("span").fadeIn(1500);
            jQuery("#addParametersForTextElements").children("span").fadeOut(1500);
        }
        else
        {

            var dataToPost = {
                "formElementName": formElementLabel,
                "HTMLHeadingValue": HTMLHeadingValue,
                "fontSize": fontSize,
                "textAlignment": textAlignment
            };
            // var myurlToCreateHTMLHeading ="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditHTMLHeadingEntity";
            var myurlToCreateHTMLHeading = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceHTMLHeading]").val();
            jQuery("#submitTextforFormElement").unbind();
            jQuery('#tempdivcontainer').load(myurlToCreateHTMLHeading, dataToPost ,function postSuccessFunction(html) {

                jQuery('#tempdivcontainer').html(html);

                var HTMLheading = jQuery('#tempdivcontainer #WYSIWYGHTMLHeading').html();
                if (HTMLheading == 0)
                {
                    jQuery("#addParametersForTextElements").children("span").html(
                        "<br> ERROR!! A new HTML Heading \""+HTMLHeadingValue+"\" has NOT been made. <br> \n\
\""+HTMLHeadingValue+"\" already exists for the heading named \""+formElementLabel+"\". Please enter some unique HTML Heading or text.");
                    jQuery("#addParametersForTextElements").children("#insertTextMenu").hide("slow");
                    insertPropertiesForFormElement("HTML_heading", formElementLabel);
                }
                else
                {
                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementHTMLHeading"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(HTMLheading);
                        jQuery("#addParametersForTextElements").children("span").html("A HTML heading \""+formElementLabel+"\" has been configured and stored.");
                    }
                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(HTMLheading);
                        jQuery("#addParametersForTextElements").children("span").html("A HTML heading \""+formElementLabel+"\" has been configured and stored.");

                    }

                    insertPropertiesForFormElement("HTML_heading", formElementLabel);
                }
            });
        }
    });
}