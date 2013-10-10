var formName = null;

jQuery(document).ready(function() {
    
    tempformName = jQuery("#formName").val();
    formName = "form_"+tempformName;
    jQuery("#"+formName).prepend("<div class='errorMessageDiv'></div>");

    jQuery("#"+formName+" :button").click(function(event) {
        var formValidated = true;
        event.preventDefault();
        
        jQuery("#"+formName).each(function(index) {
            jQuery(this).find('*').each(function() {
                jQuery(this).removeClass('ui-state-error');
            });
        });
        
        //        $objElement->addOption('default', 'No Validation');
        //        $objElement->addOption('numeric', 'Numeric : Whole Numbers');
        //        $objElement->addOption('decimal', 'Decimal Numbers : 123.1234');
        //        $objElement->addOption('alphanumeric', 'Alphanumeric: Alphabets and numbers');
        //        $objElement->addOption('alpha', 'Alpha: Only alphabets');    
        //        $objElement->addOption('date_us', 'Date (US Format) : day/month/year');
        //        $objElement->addOption('date_iso', 'Date (ISO Format) : year-month-day');
        //        $objElement->addOption('time', 'Time : hour:minute');
        //        $objElement->addOption('phone', 'Phone Number : 000-000-0000');
        //        $objElement->addOption('document', 'Document Filenames : file.(pdf,doc,csv,txt)');
        //        $objElement->addOption('image', 'Image Filenames : image.(jpg,gif,png)');
        //        $objElement->addOption('ip', 'IP Address : 192.168.0.1');
        //        $objElement->addOption('html_hex', 'HTML Color Codes : #00ccff');
        //        $objElement->addOption('email', 'Email Address : salman.noor@wits.ac.za');
        //        $objElement->addOption('url', 'Internet Address : http://www.google.co.za');


        jQuery("#"+formName+" :input").each(function(domEle,index){
            var formElementType = jQuery(this).attr('type');
  
            if (formElementType != "hidden" && formElementType != "submit"){
               
                var nameOfFormElement = jQuery(this).attr('name');
                var valueOfFormElement = jQuery(this).val();  
                if (!formValidator.checkIfEmpty(jQuery(this),jQuery(this).attr('name')))
                {
                    formValidated = false;
                    return false;   
                }
            //                console.log(valueOfFormElement+ "-----"+formElementType+"-------"+nameOfFormElement);       
            }
            
            if (formElementType == 'text'){
                var validationType = jQuery(this).attr('class');
                var isValidated = true;
                switch(validationType)
                {
                    case "numeric":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^\s*\d+\s*$/,"The "+nameOfFormElement+" field only allows numeric characters (0-9).");
                        break;
                    case "alphanumeric":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789\s]*$/,"The "+nameOfFormElement+" field only allows alphanumeric characters (a-z 0-9). Additionally, this field will allow white space.");
                        break;
                    case "alpha":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ\s]*$/,"The "+nameOfFormElement+" field only allows alpha characters (a-z). Additionally, this field will allow white space.");
                        break;
                    case "date_us":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/,"The "+nameOfFormElement+" field only allows a date in US format to be inserted (dd/mm/yyyy). Please insert a date in the valid format.");

                        break;
                    case "date_iso":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^\d{4}-(0[0-9]|1[0,1,2])-([0,1,2][0-9]|3[0,1])$/,"The "+nameOfFormElement+" field only allows a date in ISO format to be inserted (yyyy-mm-dd). Please insert a date in the valid format.");

                        break;
                    case "time":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^([0-1][0-9]|[2][0-3])(:([0-5][0-9])){1,2}$/,"The "+nameOfFormElement+" field only allows a time to be inserted (hh:mm). Please insert a time in the valid format.");
                        break;
                    case "phone":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^[2-9]\d{2}-\d{3}-\d{4}$/,"The "+nameOfFormElement+" field only allows a phone number to be inserted (000-000-0000). Please insert a phone number in the valid format.");

                        break;
                    case "document":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^[a-zA-Z0-9-_\.]+\.(pdf|txt|doc|csv)$/,"The "+nameOfFormElement+" field only allows a document a specific extension to inserted (file_name.pdf). The specific file extensions are pdf,doc,txt and csv.");
                        break;
                    case "image":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^[a-zA-Z0-9-_\.]+\.(jpg|gif|png)$/,"The "+nameOfFormElement+" field only allows a image file a specific extension to inserted (file_name.jpg). The specific file extensions are jpg,png and gif.");
                        break;
                    case "ip":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/,"The "+nameOfFormElement+" field only allows a valid IP adress to be inserted eg. 192.168.0.1 .");
                        break;
                    case "html_hex":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$/,"The "+nameOfFormElement+" field only allows a valid hexidecimal HTML color code to be inserted eg. #00ccff .");
                        break;       
                    case "email":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,"The "+nameOfFormElement+" field only allows a valid email address to inserted eg. salman.noor@wits.ac.za . Please insert a valid email address.");
                        break;
                    case "url":
                        isValidated = formValidator.checkRegexp(jQuery(this),/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,"Enter a valid url, eg. http://www.witsccmsformbuilder.ac.za");

                        break;
                    default:

                }
                if (!isValidated){
                    formValidated = false;
                    return false;
                }
            }
            formValidated = true;
        });   
        
        if (formValidated){
          jQuery("#"+formName).submit();
        }
    });
    
//        $("#"+formName).submit(function() {
//
//    });

});
  
  
  
  
/**
* \class function formValidationHandler
* \brief This class is used to valide the form content on the client side.
* \author Salman Noor
*/
var formValidator = new formValidationHandler();
function formValidationHandler()
{
    this.checkLength = function(o,n,min,max) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass('ui-state-error');
            this.updateErrorMessage("Length of " + n + " must be between "+min+" and "+max+".");
            return false;
        } else {
            return true;
        }
    }
    this.checkIfEmpty = function (o,name)
    {
        if ( o.val().length <= 0 ) {
            o.addClass('ui-state-error');
            this.updateErrorMessage("The field "+name+" is empty.");
            o.parent().addClass('ui-state-error');
            return false;
        } else {
            return true;
        }
    }
    this.checkPasswordFields = function(fld1,fld2)
    {
        if ( fld1.val() == fld2.val())
        {
            return true;
        }
        else
        {
            fld1.addClass('ui-state-error');
            fld2.addClass('ui-state-error');
            this.updateErrorMessage("Password fields do not match.");
        }
    }
    this.updateErrorMessage = function(errorText) {
        jQuery('.errorMessageDiv').css("color","red");
        jQuery('.errorMessageDiv').html("Error. ");
        jQuery('.errorMessageDiv').append(errorText)
        .addClass('ui-state-highlight');
        jQuery('.errorMessageDiv').show('slow');
        setTimeout(function() {
            jQuery('.errorMessageDiv').removeClass('ui-state-highlight', 1500);
        }, 1500);
    }
    this.checkRegexp = function(o,regexp,n) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass('ui-state-error');
            o.parent().addClass('ui-state-error');
            this.updateErrorMessage(n);
            return false;
        } else {
            return true;
        }
    }
}