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

//var UpdateProgress_deleteprogressfile = false;
// Global variable to store UpdateProgress()'s response.
var UpdateProgress_response = '';
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
            var response = transport.responseText || "no response text";
            // Store response in global UpdateProgress_response variable.
            UpdateProgress_response = response;
            response = '<pre>' + response + '</pre>';
            // Assign response to innerHTML of the 'output' div element.
            $('output').innerHTML = response;
            // Force div element to scroll to the last line.
            $('output').scrollTop=$('output').scrollHeight; //-$('output').height
        },
        // onFailure callback.
        onFailure: function(){
            var response = 'Something went wrong...';
            // Store response in global UpdateProgress_response variable.
            UpdateProgress_response = response;
            response = '<pre>' + response + '</pre>';
            // Append response to 'output' div element.
            $('output').innerHTML += response;
            // Force div element to scroll to the last line.
            $('output').scrollTop=$('output').scrollHeight;
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
        onSuccess: function(transport){
            // If timerId is defined then stop the timer, and set timerId to null.
            if (timerId != null) {
                window.clearTimeout(timerId);
                timerId = null;
            }
            //UpdateProgress_deleteprogressfile = true;
            // Update the progress status one last time in case there have been changes to the progress status since UpdateProgress() was last triggered, and specify that the progress file should be deleted.
            UpdateProgress(true);
            //var response = transport.responseText || "no response text";
            // Set up message.
            var s = '<pre>' + UpdateProgress_response + "\nSuccess! " /* + response */ + ' Please wait while you are redirected...' + '</pre>';
            // Assign the message to the innerHTML of the 'output' div element.
            $('output').innerHTML = s;
            // Force div element to scroll to the last line.
            $('output').scrollTop=$('output').scrollHeight;
            ///ajax_progress/
            // If login_url is defined then redirect to the application and log in.
            if (login_url !== null) {
                window.location.replace(login_url); //?message='+encodeURI(response)
            }
        },
        onFailure: function(){
            // If timerId is defined then stop the timer, and set timerId to null.
            if (timerId != null) {
                window.clearTimeout(timerId);
                timerId = null;
            }
            var response = '\nSomething went wrong...';
            //response
            // Set up message.
            var s = '<pre>' + UpdateProgress_response + response + '</pre>';
            // Append the message to the innerHTML of the 'output' div element.
            $('output').innerHTML = s; //response
            // Force div element to scroll to the last line.
            $('output').scrollTop=$('output').scrollHeight;
        }
    });
    // Create a timer to refresh the progress status every 1 second.
    timerId = window.setTimeout('UpdateProgress_()', 1000);
    //return; //false;
}