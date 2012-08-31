/**
 * Ajax Install
 *
 * Implements AJAX driven final setup and
 * log in functionality for the Chisimba
 * installer.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @copyright (C) 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @author Jeremy O'Connor
 * @version   $Id$
 */

// Update the status div element.
// @param string message The message
function UpdateStatus(message)
{
    // Assign the message to the innerHTML of the 'status' div element.
    $('status').innerHTML = '<pre>' + message + '</pre>';
    // Force div element to scroll to the last line.
    $('status').scrollTop=$('status').scrollHeight-$('status').clientHeight;
}

// Update the progress bar.
// @param integer percentage The percentage
function UpdateProgressBar(percentage)
{
    $('progress_bar').style.width = percentage + '%';
}

//var UpdateProgress_deleteprogressfile = false;
// Global variable to store message.
var UpdateProgress_message = false;
// Global variable to store percentage.
var UpdateProgress_percentage = false;
// Execute an XML HTTP request to get the progress status.
// param: deleteprogressfile_ boolean Indicator to specify whether to delete the progress file.
function UpdateProgress(deleteprogressfile_)
{
    ///ajax_progress/
    //UpdateProgress_deleteprogress
    //var deleteprogressfile_ =UpdateProgress_deleteprogressfile;
    // Execute the XML HTTP request.
    new Ajax.Request('progress.php', {
        asynchronous: false,
        method:'get',
        parameters: {deleteprogressfile: deleteprogressfile_?'true':'false'},
        // onSuccess callback.
        onSuccess: function(transport){
            //var response = transport.responseText || "no response text";
            // Get response XML
            var response = transport.responseXML;
            // Check if empty, and if so exit the function
            if (!response) {
                //alert('!response');
                return;
            }
            // Get the 'message' tag
            var el_message = response.getElementsByTagName("message");
            //alert(el_status);
            //alert(el_status.length);
            // Check if zero length, and if so exit the function
            if (el_message.length == 0) {
                return;
            }
            else {
                //Extract the contents
                var message = el_message[0].textContent;
            }
            // Get the 'percentage' tag
            var el_percentage = response.getElementsByTagName("percentage");
            //alert(el_percentage);
            //alert(el_percentage.length);
            // Check if zero length, and if so exit the function
            if (el_percentage.length == 0) {
                return;
            }
            else {
                //Extract the contents
                var percentage = el_percentage[0].textContent;
            }
            // Store response in global UpdateProgress_response variable.
            //UpdateProgress_response = response;
            // Store message in global UpdateProgress_message variable.
            UpdateProgress_message = message;
            // Store percentage in global UpdateProgress_percentage variable.
            UpdateProgress_percentage = percentage;
            // Update status
            UpdateStatus(message);
            // Update progress bar
            UpdateProgressBar(percentage);
        },
        // onFailure callback.
        onFailure: function(transport){
            var message = UpdateProgress_message + 'Something went wrong...'+'\nStatus:'+transport.status;
            // Store message in global UpdateProgress_message variable.
            UpdateProgress_message = message;
            //UpdateProgress_percentage = percentage;
            UpdateStatus(message);
            //UpdateProgressBar(percentage);
        }
    });
}
// Stub timer function for UpdateProgress().
function UpdateProgress_()
{
    UpdateProgress(false);
    timerId = window.setTimeout('UpdateProgress_()', 1000);
}

// Global variable for timer.
var timerId = null;
// Global variable that stores login url for logging in to the application.
var login_url = null;
// Execute an XML HTTP request that launches Chisimba (the application), performs firsttimeregistration and logs in to the application.
// param: register_url string The URL that is used to launch Chisimba.
// param: register_url_params_ string The object literal that contains the GET paramters used when launching Chisimba.
// param: login_url_ string The URL that is used to log in to the applicaion.
function ajax_install(register_url, register_url_params_, login_url_)
{
    // Store login url in global variable.
    login_url = login_url_;
    // Create a statement using the register_url_params_ object literal and incorporate a register_url_params variable into the current scope.
    register_url_params_ = 'var register_url_params = ' + register_url_params_;
    eval(register_url_params_);
    ///ajax_progress/
    // Execute the XML HTTP request.
    new Ajax.Request(register_url, {
        asynchronous: true,
        method:'get',
        //parameters: encodeURI(register_url_params),
        parameters: register_url_params,
        // onSuccess callback
        onSuccess: function(transport){
            //var response = transport.responseText || "no response text";
            // If timerId is defined then stop the timer, and set timerId to null.
            if (timerId != null) {
                window.clearTimeout(timerId);
                timerId = null;
            }
            //UpdateProgress_deleteprogressfile = true;
            // Update the progress status one last time in case there have been changes to the progress status since UpdateProgress() was last triggered, and specify that the progress file should be deleted.
            UpdateProgress(true);
            // Set up message.
            UpdateStatus(UpdateProgress_message + '\nSuccess!' /* + response */ + ' Please wait while you are redirected...');
            ///ajax_progress/
            // If login_url is defined then redirect to the application and log in.
            if (login_url !== null) {
                window.location.replace(login_url); //?message='+encodeURI(response)
            }
        },
        // onFailure callback
        onFailure: function(){
            //var response = '\nSomething went wrong...';
            // If timerId is defined then stop the timer, and set timerId to null.
            if (timerId != null) {
                window.clearTimeout(timerId);
                timerId = null;
            }
            // Set up message
            var message = UpdateProgress_message + 'Something went wrong...'+'\nStatus:'+transport.status;
            // Store message in global UpdateProgress_message variable.
            UpdateProgress_message = message;
            //UpdateProgress_percentage = percentage;
            // Update status
            UpdateStatus(message);
            //UpdateProgressBar(percentage);
        }
    });
    // Create a timer to refresh the progress status every 1 second.
    timerId = window.setTimeout('UpdateProgress_()', 1000);
    //return; //false;
}