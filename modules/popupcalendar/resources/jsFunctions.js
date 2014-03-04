/**
* =============================================================
*  Javascript library to handle the popupcalendar js functions 
* =============================================================
*/
var URL = "index.php";

/**
* Function to build the calendar
* @param string xDay: The day that was selected
* @param string xMonth: The month that was selected
* @param string xYear: The year that was selected
*/
function jsBuildCal(xDay, xMonth, xYear)
{
    var target = "calDiv";
    var pars = "module=popupcalendar&action=buildcal&day="+xDay+"&month="+xMonth+"&year="+xYear;
    var myAjax = new Ajax.Updater(target, URL, {method: "post", parameters: pars});
}

/**
* Function to insert the date
* @param string xDay: The day that was selected
* @param string xMonth: The month that was selected
* @param string xYear: The year that was selected
*/
function jsInsertDate(xDay, xMonth, xYear)
{
    if(!isNaN(xDay)){
        if(xDay < 10){
            xDay.toString();
            xDay = "0"+xDay;            
        }else{
            xDay.toString();
        }
    }

    if(!isNaN(xMonth)){
        if(xMonth < 10){
            xMonth.toString();
            xMonth = "0"+xMonth;            
        }else{
            xMonth.toString();
        }
    }

    var el_Date = document.getElementById("input_date");
    el_Date.value = xYear+"-"+xMonth+"-"+xDay; /*xDay+"-"+xMonth+"-"+xYear; */
}

/**
* Function to insert the date
* @param string xHour: The hour that was selected
* @param string xMin: The minutes that was selected
*/
function jsInsertTime(xHour, xMin)
{
    var el_Time = document.getElementById("input_time");
    el_Time.value = xHour+":"+xMin; 
}
