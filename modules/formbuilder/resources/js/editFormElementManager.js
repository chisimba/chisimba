

function setUpEditFormElementButton(){
          jQuery("#editFormElementsButton").unbind();
           jQuery("#editFormElementsButton").button({icons: {
                primary: "ui-icon-pencil"
            }});
                jQuery( "#editFormElementsButton" ).button({ disabled: false });
        jQuery( "#editFormElementsButton" ).attr('checked',false);
        jQuery( "#editFormElementsButton").button( "refresh" );
        jQuery("#editFormElementsButton").change(function() {
            if (jQuery("#editFormElementsButton:checked").val() == "on" )
            {
                var formnumber = jQuery("#getFormNumber").html();
                var formNumber = jQuery.trim(formnumber);
                editFormElements(formNumber);
            }
            else
            {
                resetEditFormElements();
            }
        });
}

function editFormElements(formNumber){
        jQuery("#WYSIWYGForm").children().css("border", "1px dashed red");
        jQuery("#WYSIWYGForm").children().each(function (index, domEle)
        {
            var domFormElementIDs = jQuery(domEle).attr("id");
            jQuery(domEle).prepend('<span id="'+domFormElementIDs+'" class="editSpan"><span style="clear:none; float:right;" class="ui-icon ui-icon-pencil"></span><br></span>');
        });

        jQuery("#WYSIWYGForm").children().children('span').hover(
        function()
        {
            jQuery(this).children().css('background-color','yellow');
        },
        function()
        {
            jQuery("#WYSIWYGForm").children().children().children().css('background-color','white')
        }
    );

        jQuery("#WYSIWYGForm").children().children('.editSpan').unbind("click").bind('click',function () {
            var idOfElementToBeEdited= jQuery(this).parent().attr('id');
            var classOfElementToBeEdited= jQuery(this).parent().attr('class');
            var formnumber = jQuery("#getFormNumber").html();
            var formNumber = jQuery.trim(formnumber);
            editPertinentFormElement(formNumber,idOfElementToBeEdited,classOfElementToBeEdited);

        });
}

function resetEditFormElements(){
            if (jQuery("#sortFormElementsButton:checked").val() != "on" && jQuery("#deleteFormElementsButton:checked").val() != "on")
        {
            jQuery("#WYSIWYGForm").children().css("border", "0");
        }
        jQuery("#WYSIWYGForm").children().children().remove('.editSpan');
        jQuery("#WYSIWYGForm").children().children('.editSpan').unbind("click");
}

function editPertinentFormElement(formNumber,formElementName,classOfFormElement){
jQuery('.errorMessageDiv').empty();
    jQuery( "#dialog-box-editFormElements" ).bind( "dialogclose", function(event, ui) {
        jQuery( "#dialog-box-editFormElements" ).children("#content").empty();
        insertFormElement();
    });
                jQuery( "#dialog-box-editFormElements" ).dialog( "option", "buttons", {
                "Close": function() {

                    jQuery(this).dialog("close");
                }
            });
    jQuery( "#dialog-box-editFormElements").children("#content").html("");
    jQuery( "#dialog-box-editFormElements").children("#content").hide();
switch(classOfFormElement)
{
case "witsCCMSFormElementLabel":
    unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Label' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "center" });
    jQuery( "#dialog-box-editFormElements").dialog("open");
    LoadEditLabelForm(formNumber,formElementName,classOfFormElement);
  break;
  
case "witsCCMSFormElementTextInput":
    unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Text Input' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open");
    LoadEditTextInputForm(formNumber,formElementName,classOfFormElement);
  break;
  
  case "witsCCMSFormElementTextArea":
    unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Text Area' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open");  
    LoadEditTextAreaForm(formNumber,formElementName,classOfFormElement);
  break;
  
  case "witsCCMSFormElementHTMLHeading":
   unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit HTML Heading' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditHTMLHeadingForm(formNumber,formElementName,classOfFormElement);
  break;

  case "witsCCMSFormElementDatePicker":
   unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Date Picker' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "center" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditDatePickerForm(formNumber,formElementName,classOfFormElement);
  break;
  case "witsCCMSFormElementRadio":
     unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Radio Buttons' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditRadioForm(formNumber,formElementName,classOfFormElement);
  break;
  
    case "witsCCMSFormElementDropDown":
     unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Drop Down List' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditDropDownForm(formNumber,formElementName,classOfFormElement);
  break;
  
      case "witsCCMSFormElementCheckBox":
     unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Check Boxes' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditCheckBoxForm(formNumber,formElementName,classOfFormElement);
  break;
  
        case "witsCCMSFormElementMultiSelectDropDown":
     unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Multi Select Drop Down List' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditMSDropDownForm(formNumber,formElementName,classOfFormElement);
  break;
  
          case "witsCCMSFormElementButton":
     unsetRearrangeAndDeleteElementButtons(formNumber);
    jQuery( "#dialog-box-editFormElements").dialog({ title: 'Edit Button' });
    $( "#dialog-box-editFormElements" ).dialog({ position: "top" });
    jQuery( "#dialog-box-editFormElements").dialog("open"); 
    LoadEditButtonForm(formNumber,formElementName,classOfFormElement);
  break;
  
  
  
  
default:
 
}
}

