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
function insertTextAreaParameters(formElementLabel)
{
   // reset();
    jQuery("#addParametersforTextArea").show();
    jQuery("#addParametersforTextArea").children("#stylingForTextArea").show("slow");
    jQuery("#addParametersforTextArea").children("#stylingForTextArea").children("#toolbarChoiceMenu").hide("slow");
    jQuery("input:radio[name=toolbarChoiceRadio]").attr("disabled","disabled");
    jQuery("#addParametersforTextArea").children("#endOfInsertionTextArea").show("slow");
    jQuery("#addParametersforTextArea").children("#insertTextAreaName").hide("slow");

    jQuery('.textAreaColumnSizeMenu').bind('keypress keydown keyup',function() {

        var test = jQuery('.textAreaColumnSizeMenu').val();
        var numeric1= "0123456789";
        var test1 = test.charAt(test.length-1);
        var bool = numeric1.search(test1);
        if (bool == -1)
        {
            test= test.substr(0,test.length-1);
        }
        jQuery('.textAreaColumnSizeMenu').val(test);
    });

    jQuery('.textAreaRowSizeMenu').bind('keypress keydown keyup',function() {
        var test = jQuery('.textAreaRowSizeMenu').val();
        var numeric1= "0123456789";
        var test1 = test.charAt(test.length-1);
        var bool = numeric1.search(test1);
        if (bool == -1)
        {
            test= test.substr(0,test.length-1);
        }
        jQuery('.textAreaRowSizeMenu').val(test);

    });

    jQuery("input:radio[name=simpleOrAdvancedTextAreaRadio]").change(function(){

        if ( jQuery('input:radio[name=simpleOrAdvancedTextAreaRadio]:checked').val() == "textarea")
        {

            jQuery("input:radio[name=toolbarChoiceRadio]").attr("disabled","disabled");
            jQuery("#addParametersforTextArea").children("#stylingForTextArea").children("#toolbarChoiceMenu").hide("slow");
            jQuery("input:radio[name=toolbarChoiceRadio]:eq(0)").attr('checked', "checked");
        }
        else
        {
            jQuery("input:radio[name=toolbarChoiceRadio]").removeAttr("disabled");
            jQuery("#addParametersforTextArea").children("#stylingForTextArea").children("#toolbarChoiceMenu").show("slow");
            jQuery("input:radio[name=toolbarChoiceRadio]:eq(0)").attr('checked', "checked");
        }

    });

    jQuery("#submitTextAreaParameters").unbind('click').bind('click',function () {
        jQuery('.textAreaRowSizeMenu').unbind('keypress keydown keyup');
        var textAreaColumnSize = jQuery('.textAreaColumnSizeMenu').val();
        var textAreaRowSize = jQuery('.textAreaRowSizeMenu').val();
        var defaultText = jQuery("textarea[name=setDefaulttextArea]").val();
        var toolbarChoice =  jQuery("input:radio[name=toolbarChoiceRadio]:checked").val();
        if (textAreaColumnSize <=1 || textAreaColumnSize >141 || textAreaRowSize <=1 ||textAreaRowSize >240)
        {
            jQuery("#addParametersforTextArea").children("span").html("\
<br>ERROR. The text area size has to between 1 and 140");
            jQuery("#addParametersforTextArea").children("span").show("slow");
        }
        else if ( jQuery('input:radio[name=simpleOrAdvancedTextAreaRadio]:checked').val() == "textarea")
        {
            jQuery("#addParametersforTextArea").children("span").html("\
<br>The text field input size is set to \""+textAreaRowSize+"\" by \""+textAreaColumnSize+"\" characters.<br> \n\
The default text is set to \""+defaultText+"\" to be displayed inside the text field.<br> \n\
No Tool bar is chosen for the text area.");

            jQuery("#addParametersforTextArea").children("#stylingForTextArea").hide("slow");
            jQuery("#addParametersforTextArea").children("#endOfInsertionTextArea").hide("slow");
            jQuery("#addParametersforTextArea").children("#insertTextAreaName").show("slow");
            jQuery("#addParametersforTextArea").children("span").show("slow");
        }
        else
        {

            jQuery("#addParametersforTextArea").children("span").html("\
<br>The text field input size is set to \""+textAreaRowSize+"\" by \""+textAreaColumnSize+"\" characters.<br> \n\
The default text is set to \""+defaultText+"\" to be displayed inside the text field.<br> \n\
The text area tool bar is set to \""+toolbarChoice+"\".");

            jQuery("#addParametersforTextArea").children("#stylingForTextArea").hide("slow");
            jQuery("#addParametersforTextArea").children("#endOfInsertionTextArea").hide("slow");
            jQuery("#addParametersforTextArea").children("#insertTextAreaName").show("slow");
            jQuery("#addParametersforTextArea").children("span").show("slow");
        }
    });

    jQuery("#endOfInsertionTextArea").unbind("click").bind('click',function () {
        jQuery("#addParametersforTextArea").children("span").html("<br>A Text Area Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.");
        jQuery("#addParametersforTextArea").children("span").show("slow").unbind();
        jQuery("#addParametersforTextArea").children("#stylingForTextArea").hide("slow").unbind();
        jQuery("#addParametersforTextArea").children("#endOfInsertionTextArea").hide("slow").unbind();
        jQuery("#addParametersforTextArea").children("#insertTextAreaName").hide("slow").unbind();
        //jQuery('.textAreaRowSizeMenu').unbind('keypress keydown keyup');
         jQuery("#submitTextforTextArea").unbind();
         jQuery("#submitTextAreaParameters").unbind();
    jQuery("#endOfInsertionTextArea").unbind();
     // jQuery('.textAreaColumnSizeMenu').unbind('keypress keydown keyup');
    jQuery('input:radio[name=simpleOrAdvancedTextAreaRadio]').unbind();
    //reset();
        insertNewFormElement();
    });

    jQuery("#submitTextforTextArea").click(function () {
      //  jQuery('.textAreaColumnSizeMenu').unbind('keypress keydown keyup');
        var formElementLabel = jQuery('.formElementLabel').val();
        var textAreaColumnSize = jQuery('.textAreaColumnSizeMenu').val();
        var textAreaRowSize = jQuery('.textAreaRowSizeMenu').val();
        var defaultText = jQuery("textarea[name=setDefaulttextArea]").val();
        var simpleOrAdvancedHAChoice = jQuery("input:radio[name=simpleOrAdvancedTextAreaRadio]:checked").val();
        var toolbarChoice =  jQuery("input:radio[name=toolbarChoiceRadio]:checked").val();
        var textAreaName = jQuery('.textAreaName').val();
        if (textAreaName == "")
        {
            jQuery("#addParametersforTextArea").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addParametersforTextArea").children("span").fadeIn(1500);
            jQuery("#addParametersforTextArea").children("span").fadeOut(1500);
        }
        else
        {
            var textAreaDataToPost = {
                "formElementName": formElementLabel,
                "textAreaName": textAreaName,
                "textAreaValue" : defaultText,
                "ColumnSize": textAreaColumnSize,
                "RowSize": textAreaRowSize,
                "simpleOrAdvancedHAChoice" : simpleOrAdvancedHAChoice,
                "toolbarChoice" : toolbarChoice
            };
            var myurlToProduceTextArea = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceTextArea]").val();
            // var myurlToProduceTextArea ="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditTextArea";
            jQuery("#submitTextforTextArea").unbind();
                            jQuery('#Sort').unbind();
       jQuery('#Stop').unbind();
            jQuery('#tempdivcontainer').load(myurlToProduceTextArea, textAreaDataToPost ,function postSuccessFunction(html) {

                jQuery('#tempdivcontainer').html(html);

                var textArea = jQuery('#tempdivcontainer').children('#WYSIWYGTextArea').html();

                if (textArea == 0)
                {
                    jQuery("#addParametersforTextArea").children("span").html(
                        "<br> ERROR!! A new text area Object \""+textAreaName+"\" has NOT been made. <br>\n\
It already exists in the database. Please choose a unique label");

                    jQuery("input:radio[name=simpleOrAdvancedTextAreaRadio]:eq(0)").attr('checked', "checked");
                    insertPropertiesForFormElement("text_area", formElementLabel);
                }
                else
                {

                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementTextArea"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(textArea);
                        jQuery("#addParametersforTextArea").children("span").html(" A new text area Object \""+textAreaName+"\" has been configured and stored.");
                    }
                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append(textArea);
                        jQuery("#addParametersforTextArea").children("span").html(" A new text area Object \""+textAreaName+"\" has been configured and stored.");
                    }
                    jQuery("input:radio[name=simpleOrAdvancedTextAreaRadio]:eq(0)").attr('checked', "checked").unbind();
                    insertPropertiesForFormElement("text_area", formElementLabel);
                }
            });
        }
    });
}
