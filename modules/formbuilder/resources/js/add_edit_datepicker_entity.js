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
function insertDatePickerParameters(formElementLabel)
{
    jQuery("#addParametersForDatePicker").show(1000);
    jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").show("slow");
    jQuery("#addParametersForDatePicker").children("#dateFormat").show("slow");
    jQuery("#addParametersForDatePicker").children("span").show("slow");
    jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").children("#selectDefaultDate").hide();
    jQuery("#addParametersForDatePicker").children("#insertDatePickerName").hide();

    jQuery("#endOfInsertionDatePicker").unbind("click").bind('click',function () {
        jQuery("#addParametersForDatePicker").children("span").html("<br>A Date Picker Enitity has been Inserted and Configured.<br>\n\
Please choose your next Form element.");
        jQuery("#addParametersForDatePicker").children("span").show("slow").unbind();
        jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").hide("slow").unbind();
        jQuery("#addParametersForDatePicker").children("#insertDatePickerName").hide("slow").unbind();
         jQuery("input:radio[name=Default Date Choice]").unbind();
             jQuery("#submitDatePickerParameters").unbind();
             jQuery("#submitTextforDatePicker").unbind();
              jQuery("#endOfInsertionDatePicker").unbind();
        insertNewFormElement();
    });

    jQuery("input:radio[name=Default Date Choice]").change(function(){
        if ( jQuery('input:radio[name=Default Date Choice]:checked').val() == "Real Date")
        {
            jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").children("#selectDefaultDate").hide('slow');
            jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").children("#selectDefaultDate :input").attr("disabled","disabled");
        }
        else
        {
            jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").children('#selectDefaultDate').show('slow');
            jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").children("#selectDefaultDate :input").removeAttr("disabled");

        }
    });

    jQuery("#submitDatePickerParameters").unbind('click').bind('click',function () {

        var dateFormat = jQuery('#input_Date_Format').val();

        if ( jQuery('input:radio[name=Default Date Choice]:checked').val() == "Custom Date")
        {
            var CustomDay = jQuery("#defaultDateSelection_Day_ID").val();
            var CustomMonth = jQuery("#defaultDateSelection_Month_ID option:selected").text();
            var CustomYear = jQuery("#defaultDateSelection_Year_ID").val();
            var defaultCustomDate = CustomMonth+"-"+CustomDay+"-"+CustomYear;
            jQuery("#addParametersForDatePicker").children("span").html("\
<br>Date Format is set to \""+dateFormat+"\".<br> \n\
The default selected customized date is set to \""+defaultCustomDate+"\" for Date Picker Object.\n\
<br>Please enter a name for your date picker \"without\" any spaces or numbers.");
            jQuery('.datePickerText').bind('keypress keydown keyup',function() {
                var test = jQuery('.datePickerText').val();
                var alphabetic= "qwertyuiopasdfghjklzxcvbnm";
                var symbols = ",./;'[]\\=-<>?:\"{}|+_)(*&$#@!";
                var test1 = test.charAt(test.length-1);

                var boolalphebetic = alphabetic.search(test1);
                if (boolalphebetic == -1)
                {
                    var test= test.substr(0,test.length-1);
                }
                var boolsymbols = symbols.search(test1);
                if (boolsymbols == 0)
                {
                    var test= test.substr(0,test.length-1);
                }
                jQuery('.datePickerText').val(test);
            });

        }
        else
        {
            defaultCustomDate = "Real Time Date";
            jQuery("#addParametersForDatePicker").children("span").html("\
<br>Date Format is set to \""+dateFormat+"\".<br> \n\
The default selected customized date is set to \""+defaultCustomDate+"\" for Date Picker Object.\n\
<br>Please enter a name for your date picker \"without\" any spaces or numbers.");
        }
        jQuery("#addParametersForDatePicker").children("span").show("slow");
        jQuery("#addParametersForDatePicker").children("#datePickerParametersContainer").hide('slow');
        jQuery("#addParametersForDatePicker").children("#insertDatePickerName").show("slow");

        jQuery('.datePickerText').bind('keypress keydown keyup',function() {
            var test = jQuery('.datePickerText').val();
            var alphabetic= "qwertyuiopasdfghjklzxcvbnm";
            var symbols = ",./;'[]\\=-<>?:\"{}|+_)(*&$#@!";
            var test1 = test.charAt(test.length-1);

            var boolalphebetic = alphabetic.search(test1);
            if (boolalphebetic == -1)
            {
                var test= test.substr(0,test.length-1);
            }
            var boolsymbols = symbols.search(test1);
            if (boolsymbols == 0)
            {
                var test= test.substr(0,test.length-1);
            }
            jQuery('.datePickerText').val(test);
        });
    });

    jQuery("#submitTextforDatePicker").click(function () {

        var formElementLabel = jQuery('.formElementLabel').val();
        var datePickerValue = jQuery('.datePickerText').val();


        var dateFormat = jQuery('#input_Date_Format').val();
        jQuery('#input_Date_Format').val("YYYY-MM-DD");

        if ( jQuery('input:radio[name=Default Date Choice]:checked').val() == "Custom Date")
        {
            var CustomDay = jQuery("#defaultDateSelection_Day_ID").val();
            var CustomMonth = jQuery("#defaultDateSelection_Month_ID option:selected").text();
            var CustomYear = jQuery("#defaultDateSelection_Year_ID").val();
            var defaultCustomDate = CustomMonth+"-"+CustomDay+"-"+CustomYear;
        }
        else
        {
            defaultCustomDate = "Real Time Date";
        }
        jQuery('input:radio[name=Default Date Choice]').filter('[value="Real Date"]').attr('checked', true);

        if (datePickerValue == "")
        {
            jQuery("#addParametersForDatePicker").children("span").html("<br>A NULL field is not allowed!<br>Please Complete Fields.");
            jQuery("#addParametersForDatePicker").children("span").fadeIn(1500);
            jQuery("#addParametersForDatePicker").children("span").fadeOut(1500);
        }
        else
        {
            var datePickerDataToPost = {
                "datePickerName": formElementLabel,
                "datePickerValue": datePickerValue,
                "dateFormat": dateFormat,
                "defaultCustomDate":defaultCustomDate
            };
            var myurlToProduceDatePicker = jQuery("#ajaxCallUrlsHiddenInputs").children(":input[name=urlToProduceDatePicker]").val();
            //  var myurlToProduceDatePicker="<?php echo $_SERVER[PHP_SELF];?>?module=formbuilder&action=addEditDatePickerEntity";
            jQuery("#submitTextforDatePicker").unbind();
            jQuery('#tempdivcontainer').load(myurlToProduceDatePicker, datePickerDataToPost ,function postSuccessFunction(html) {

                var datePicker = jQuery('#tempdivcontainer #WYSIWYGDatepicker').html();
                if (datePicker == 0)
                {
                    jQuery("#addParametersForDatePicker").children("span").html(
                        "<br> ERROR!! A new date Picker Object \""+datePickerValue+"\" has NOT been made. <br>\n\
It already exists in the database. Please choose a unique label.");
                    jQuery("*").unbind('keypress keydown keyup');
                    insertPropertiesForFormElement("datepicker", formElementLabel);
                }
                else
                {


                    if (jQuery("#WYSIWYGForm").children("#"+formElementLabel).length <= 0)
                    {
                        jQuery("#WYSIWYGForm").append('<div id ='+formElementLabel+' class="witsCCMSFormElementDatePicker"></div>');
                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append("<br>[JavaScript Conflict: Date Picker Object can not be displayed.\n\
It \"will\" be displayed in the built form.]<br>");
                        jQuery("#addParametersForDatePicker").children("span").html(" A new date Picker Object \""+datePickerValue+"\" has been configured and stored.");
                    }
                    else
                    {

                        jQuery("#WYSIWYGForm").children("#"+formElementLabel).append("<br>[JavaScript Conflict: Date Picker Object can not be displayed.\n\
It \"will\" be displayed in the built form.]<br>");
                        jQuery("#addOptionnValueForFormElements span").html("An option for the \""+formElementLabel+"\" checkbox has been configured and stored.");

                    }

                    jQuery('.datePickerText').unbind('keypress keydown keyup');
                    insertPropertiesForFormElement("datepicker", formElementLabel);
                }

            });

        }
    });
}