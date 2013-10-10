/**********************************************************
Generic script for SCORM 2004 conformant SCOs.
by Claude Ostyn
This document is a companion sample script for
"In the Eye of the SCORM" version 0.8x

This script automates many of the common features of SCOs and
provides helper functions for the rest. For SCORM 2004 only.
See The Eye of the SCORM eBook at http://ostyn.com/resources.htm
for documentation.


*/
var gsOstynScriptVersion = "0.8.8 2007-03-21"
/*
Please send any bug reports to Tools at Ostyn dot com

== Copyright and License==
Copyright (c) 2004,2005,2006,2007 Ostyn Consulting. Some rights reserved.
Unless otherwise expressly stated, all original material of whatever nature
created by Claude Ostyn and included in this software sample is licensed
under the Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
To view a copy of this license, visit
http://creativecommons.org/licenses/by-nc-sa/2.5/ or send a letter to
Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.

Commercial licensing terms are also available. For additional information or
questions regarding copyright, commercial use, distribution and reproduction,
contact:
  Ostyn Consulting, PO Box 2362, Kirkland, WA 98083-2362, USA
or via email:
  Tools at Ostyn dot com

== Representations, Warranties and Disclaimer ==
OSTYN CONSULTING OFFERS THIS WORK AS-IS AND MAKES NO REPRESENTATIONS OR
WARRANTIES OF ANY KIND CONCERNING THE WORK, EXPRESS, IMPLIED, STATUTORY
OR OTHERWISE, INCLUDING, WITHOUT LIMITATION, WARRANTIES OF TITLE,
MERCHANTIBILITY, FITNESS FOR A PARTICULAR PURPOSE, NONINFRINGEMENT, OR THE
ABSENCE OF LATENT OR OTHER DEFECTS, ACCURACY, OR THE PRESENCE OF ABSENCE OF
ERRORS, WHETHER OR NOT DISCOVERABLE.

== Limitation on Liability ==
EXCEPT TO THE EXTENT REQUIRED BY APPLICABLE LAW, IN NO EVENT WILL
OSTYN CONSULTING OR CLAUDE OSTYN BE LIABLE TO YOU ON ANY LEGAL THEORY FOR
ANY SPECIAL, INCIDENTAL, CONSEQUENTIAL, PUNITIVE OR EXEMPLARY DAMAGES
ARISING OUT OF THE USE OF THIS WORK, EVEN IF OSTYN CONSULTING OR CLAUDE OSTYN
HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.

== Version info ==
0.8.5 - Removed a initial sanity test on API because SCORM RTE
        causes Java fatal error when testing for existence
        of the API.Initialize function.
        Changed default for gbTerminateOnBeforeUnload to false.
0.8.6 - Updated Creative Commons License from 2.0 to 2.5.
        Added utility functions ScormInteractionSetResponse
        and ScormInteractionMarkTime.
0.8.7 - Fixed bug in ScormObjectiveGetIndex function.
0.8.8 - Fixed bug in ScormSetCompletionStatusForMeasure.
        Special thanks to Massimiliano Di Luca


***************************************************************/

/***
 The following naming conventions apply to variable and function names
 * A leading underscore indicates a "private" object that should
   not be referenced from outside this script
 * The prefix "g" indicates a global variable.
 * The prefixes "b", "n", "wnd" etc. which may be preceded by "g" indicate
   an object type. b=boolean, n=numeric, s=string, wnd=window, etc.
 * Functions that are intended to be called by a SCO have the prefix "Scorm"

/*** Control variables and switches ***/
var gbAutoElapsedTime = true;
var gbAutoSuccessStatus = true;
var gbAutoCoarseCompletionStatus = true;
var gbAutoFineCompletionStatus = true;
var gbAutoTrackAllowedTime = true; // If true, keep track of allowed time
var gnAutoTrackAllowedTimePeriod = 300; // Centiseconds between time checks
var gbAutoPassingScoreIfCompleted = false;

/*** onbeforeunload can provide more reliable data saving when unloaded  ***/
var gbTerminateOnBeforeUnload = false;
  // gbTerminateOnBeforeUnload must be set to false if the SCO opens a popup window
  // or if the SCO uses Flash to load different movies.

/*** Window flag. If true, tries to close window automatically if allowed ***/
var gbEnableAutoCloseWindow = true;
  // If gbEnableAutoCloseWindow is true, try to close window automatically
  // but only if allowed - See SCORM RTE spec for allowable SCO behavior.

/*** Debug flag. If true, alerts are shown for some significant events ***/
var gbDebugSession = false; // default should be false

/*** Customizable messages ***/
// This needs to be customized only if gbAutoTrackAllowedTime is true,
// and the SCO does not provide a SCOTimeLimitDetected function
var gsTimeOutExitMessage = "The time allowed for this activity has expired. "
 + "No more data can be recorded."
var gsTimeOutContinueMessage = "The time allowed for this activity has expired. "
 + "You may continue but no more data will be recorded."

/*** Preset values that may be overridden by a query to the RTE ***/
var gnPassingScore = 1.0;
var gnCompletionThreshold = 1.0;

var gbTerminateOnBeforeUnload = false;
  // gbTerminateOnBeforeUnload must be set to false if the SCO opens a popup window
  // or if the SCO uses Flash to load different movies, or if the SCO contains
  // href="javascript:..." anchor elements, since those cause
  // spurious onbeforeunload events that would cause premature termination of
  // the communication session.

/*** Initializing before loading the page is more reliable with dynamic content  ***/
var gbInitializeBeforeLoad = true;
  // gbInitializeBeforeLoad must be set to true if the web page content will depend
  // on the SCORM API while it is loading, for example if there is an inline
  // script that writes different things to the page depending on whether the SCORM
  // environment is available.
  // If set to true, SCORMInitialize is called before the body or frameset
  // of the web page is loaded.
  // If set to false, SCORMInitialize is called only after the page has
  // been fully loaded, when the browser triggers the onload event.
  // In any case, if there is a custom handler in the SCO, it does not
  // get called until after the body or frameset has been loaded (onload event).

/*** End of control variables and switches ***/

// The functions commented out below can be defind in a SCO
// script. If they don't exist it is not a problem.
// If you need them, do not add them to this script, but
// add them to the script of your SCO. See documentation.
//function SCOSessionInitializedHandler(){;}
//function SCOSessionTerminatingHandler(){;}
//function SCOTimeLimitDetected(){;}

///////////////////////////////////////////////////////////
//////////// Do not modify anything below this ////////////
//////////// There are a lot of interdependencies /////////
///////////////////////////////////////////////////////////

//// "Private" section - the data and functions used by the script
//// and which should not be called or inspected directly by a SCO -
//// are identified by a leading underscore in the variable name
//// or function name

/*** Variables used in session management ***/
var _gAPI = null;
var _gsAPIVersion = null;
var _gnScormSessionState = 0;
  // 0=not initialized yet; 1=initializing; 2=initialized; 3=terminating;
  // 4=terminated; -1=Scorm session cannot be established.
var _gbProcessingUnload = false;

/*** Variables used in management of automated behaviors ***/
var _gbInitializeFollowUpDone = false; // Flag used by ScormInitialize
var _gbScoreHasBeenSet = false;
var _gbPassingScoreAlreadyQueriedFromRTE = false; // flag for next function
var _gbPassingScoreIsFromRTE = false; // flag for next function
var _gbCompletionThresholdAlreadyQueriedFromRTE = false; // flag for next function
var _gbCompletionThresholdIsFromRTE = false; // flag for next function
var _gnAllowedTime = NaN;
var _goAllowedTimeTimer = null;
var _gnPreviousTimeInAttempt = 0;
var _gnInitCentiseconds = NaN; // initial value must be NaN
var _gnTermCentiseconds = NaN; // initial value must be NaN
var _gnTotalCentiseconds = NaN; // initial value must be NaN


/***  API communication session management functions ***
 These functions and the associated global variables allow the
 generic script to locate the API implementation, and to
 initialize and terminate the communication session automatically.

 Statements in this section allow the generic script to be invoked
 to initialize and terminate the session without having to add
 onunload, onbeforeunload and onunload handlers to the body or
 frameset element of each SCO that uses this script.
 In other words, by just including this script you can turn just
 about any web page into a SCO.

 As soon as a session is successfully initialized, the generic
 script calls a SCO function named SCOSessionInitializedHandler,
 if such a function exists. Typically, this function could exist
 in the custom script for a particular SCO that also uses this
 generic script.

 Also, just before actually terminating the session, the generic
 script's ScormTerminate function calls a SCO function named
 SCOSessionTerminatingHandler, if such a function exists.
 Typically, this function could exist in the custom script for
 a particular SCO that also uses this generic script.
*/

function _GetAPI(win)
{
  var ScanForAPI = function(win)
  {
    var nFindAPITries = 500; // paranoid to prevent runaway
    var objAPI = null;
    var bOK = true;
    var wndParent = null;
    while ((!objAPI)&&(bOK)&&(nFindAPITries>0))
    {
      nFindAPITries--;
      try { objAPI = win.API_1484_11; } catch (e) { bOK = false; }
      if ((!objAPI)&&(bOK))
      {
        try { wndParent = win.parent; } catch (e) { bOK = false; }
        if ((!bOK)||(!wndParent)||(wndParent==win))
        {
          break;
        }
        win = wndParent;
      }
    }
    return objAPI;
  }

  var wndParent = null;
  var wndOpener = null;
  try { wndParent = win.parent; } catch(e) { }
  try { wndOpener = win.opener; } catch(e) { }
  if ((wndParent != null) && (wndParent != win))
  {
    _gAPI = ScanForAPI(wndParent);
  }
  if ((_gAPI == null) && (wndOpener != null))
  {
    _gAPI = ScanForAPI(wndOpener);
  }
}

/*** Generic session management functions ***/
function ScormVersion() // This script supports only SCORM 2004
{
  return ((_gnScormSessionState > 0)? "SCORM 2004": "unknown");
}

function ScormAPIVersion() // Available only if API has been found
{
  return ((_gsAPIVersion)? _gsAPIVersion: "unknown");
}

function ScormInitialize()
{
  // If already initialized, there may be some follow-up to do.
  if ((!_gbInitializeFollowUpDone ) && (_gnScormSessionState == 2))
  {
    _ScormInitializeFollowUp();
  }

  // If already tried to initialize, there is nothing left to do.
  if (_gnScormSessionState != 0) return "false";

  if (gbDebugSession) alert("Attempting to initialize SCORM communication session.");

  _GetAPI(window);
  if (_gAPI == null) // bug in SCORM RTE prevents ((_gAPI == null) || (_gAPI.Initialize == "undefined"))
  {
    _gAPI = null;
    if (gbDebugSession) alert("No valid API implementation found");
  }
  else
  {
    _gnScormSessionState = 1; // State is "initializing"
    if (_gAPI.Initialize("") == "true")
    {
      _gnScormSessionState = 2; // We are now "in session"

      // Cache version info so it will remain available even after session
      _gsAPIVersion = _gAPI.version;

      ScormMarkInitElapsedTime(); // Keep track of when we start
      if (true)
      {
        // If SCORMInitialize is called before loading the rest of
        // the page, we can't do the follow-up yet because
        // the target for the callback into the page may not
        // be initialized yet. When the onload event fires, it will
        // call SCORMInitialize again and that will in turn
        // call the follow up function.
        if (!gbInitializeBeforeLoad)_ScormInitializeFollowUp();
      }

      // Communication session is now initialized and ready to go
      return "true";
    }
  }
  if (gbDebugSession) alert("Initialize failed");
  _gnScormSessionState = -1; // State is "error". Give up.
  return "false";
}

function _ScormInitializeFollowUp()
{
  if (_gbInitializeFollowUpDone) return;

  _gbInitializeFollowUpDone = true;

  ScormMarkInitElapsedTime(); // Keep track of when we start; update if not already set.

  // Call SCO-specific initialization handler if one exists.
  // This allows the SCO to initialize some data; for example,
  // here the SCO might get the user's name or check on
  // entry status and possible suspend data.
  if (typeof(SCOSessionInitializedHandler)=="function") SCOSessionInitializedHandler();

  // Automatic behaviors; may have been turned off by SCOSessionInitializedHandler
  if (gbAutoCoarseCompletionStatus)
  {
    var strCS = ScormGetValue("cmi.completion_status");
    if ((strCS == "unknown") || (strCS == "not attempted"))
    {
      ScormSetValue("cmi.completion_status", "incomplete");
    }
  }
  if (gbAutoTrackAllowedTime)
  {
    // redundant if (isNaN(_gnInitCentiseconds)) ScormMarkInitElapsedTime();
    _gnAllowedTime = ISODurationToCentisec(ScormGetValue("cmi.max_time_allowed"));
    if ((!isNaN(_gnAllowedTime)) && (_gnAllowedTime > 0))
    {
      _gnPreviousTimeInAttempt = ISODurationToCentisec(ScormGetValue("cmi.total_time"));
      if (_CheckTimeAllowed()) // Check immediately, may already have run out of time.
      {
        _goAllowedTimeTimer = setInterval('_CheckTimeAllowed()', gnAutoTrackAllowedTimePeriod * 10);
      }
    }
  }
}


function ScormTerminate()
{
  // Do it only if in session, and prevent reentrance.
  if (_gnScormSessionState == 2)
  {
    // if (gbDebugSession) alert("Terminating");

    _gnScormSessionState = 3; // State is "terminating"
    if (isNaN(_gnTermCentiseconds))
    {
      // If not marked already, mark time of end of session
      ScormMarkTermElapsedTime();
    }
    if (gbAutoElapsedTime)
    {
      // Calculate and send session time to RTE.
      ScormSetSessionTime(CentisecsSinceSessionStart());
    }
    if (gbAutoCoarseCompletionStatus)
    {
      var strCS = ScormGetValue("cmi.completion_status");
      if (strCS == "incomplete")
      {
        ScormSetValue("cmi.completion_status", "completed");
      }
    }
    if ((gbAutoPassingScoreIfCompleted) && (ScormGetValue("cmi.completion_status") == "completed"))
    {
      if (!_gbScoreHasBeenSet)
      {
        ScormSetValue("cmi.score.scaled","1.0");
      }
    }
    // Call SCO-specific terminating handler if one exists.
    // This allows the SCO to set any unsaved data or to override
    // data values set by automatic behaviors.
    if (typeof(SCOSessionTerminatingHandler)=="function") SCOSessionTerminatingHandler();

    // If the SCO is running in a top level window, it may be allowed
    // to close if the corresponding flag is set. If allowed, this will
    // set a timer to close the window if Terminate succeeds.
    _PrepareCloseWindowIfAllowed();

    // Now call the API implementation to terminate the session
    if (_gAPI.Terminate("") == "true")
    {
      _gnScormSessionState = 4; // State is "terminated"
      return "true";
    }
    else
    {
      // Keep trying? -- TBD
    }
  }
  return "false";
}

/*** Timekeeping function if max time allowed is being tracked ***/
function _CheckTimeAllowed()
{
  if (gbDebugSession) window.status = Math.round(CentisecsSinceSessionStart() / 100);
  if (CentisecsSinceSessionStart() + _gnPreviousTimeInAttempt >= _gnAllowedTime)
  {
    if (_goAllowedTimeTimer) clearInterval(_goAllowedTimeTimer);
    if (gbDebugSession) alert("Time out detected")
    ScormSetValue("cmi.exit","time-out");
    if (typeof(SCOTimeLimitDetected)=="function")
    {
      SCOTimeLimitDetected()
    }
    else
    {
      var sTOAction = ScormGetValue("cmi.time_limit_action");
      var sThisLoc = window.location;
      switch (sTOAction)
      {
        case "exit,message":
          alert(gsTimeOutExitMessage);
          // Try navigation request to exit when RTE evaluates Terminate
          ScormSetValue("adl.nav.request","exit");
          ScormTerminate();
          // If navigation request did not work, use brute force.
          if (window.location == sThisLoc) window.location="about:blank";
          return false;
        case "continue,message": break;
          alert(gsTimeOutContinueMessage);
          break;
        case "exit,no message": break;
          // Try navigation request to exit when RTE evaluates Terminate
          ScormSetValue("adl.nav.request","exit");
          ScormTerminate();
          // If navigation request did not work, use brute force.
          if (window.location == sThisLoc) window.location="about:blank";
          return false;
        default: break;
      }
    }
  }
  return true;
}

/***  Load and unload event management functions ***
 These functions allow the generic script to be invoked
 to initialize and terminate the session without having to add
 onunload and onunload handlers to the body or frameset element
 of the actual SCO. In other words, by just including this
 script these functions can turn just about any web page into a SCO.
 The existing onload, onbeforeunload and onunload handlers
 that may be specified in the body or frameset tag for the web
 page are preserved. See the note about precedence below.
*/


// Relay function for onload event
function _Scorm_InitSession()
{
  ScormInitialize();
}

// Relay function for onunload event
function _Scorm_TerminateSession()
{
  //alert("unload detected");
  _gbProcessingUnload = true;
  ScormTerminate();
  return;
}

// Relay function for onbeforeunload event
function _Scorm_TerminateSessionBeforeUnload()
{
  //alert("onbeforeunload detected");
  if (gbTerminateOnBeforeUnload)
  {
    _gbProcessingUnload = true;
    ScormTerminate();
  }
  // One cannot use this event to prevent unloading without
  // causing serious problems, therefore this function
  // must specify explicitly that it has no return value.
  return;
}

// Important difference in behavior between IE and Firefox
// In IE, the event handler added by this script will execute
// after the event handler defined in the body tag, if any.
// In FF, the event handler added by this script will execute
// before the event handler defined in the body tag, if any.

// Inspired by http://www.tek-tips.com/faqs.cfm?fid=4862
function AddLoadAndUnloadEvents()
{
  var sfL = "_Scorm_InitSession";
  var sfU = "_Scorm_TerminateSession";
  var sfB = "_Scorm_TerminateSessionBeforeUnload";
  var fL = window._Scorm_InitSession;
  var fU = window._Scorm_TerminateSession;
  var fB = window._Scorm_TerminateSessionBeforeUnload;
  if (typeof(window.addEventListener) != "undefined")
  {
    // alert("addEventListener") // this fires off in FireFox
    window.addEventListener("load", fL, false );
    window.addEventListener("unload", fU, false );
    if (gbTerminateOnBeforeUnload) window.addEventListener("beforeunload", fB, false);
  }
  else if (typeof(window.attachEvent) != "undefined" )
  {
    // alert("attachEvent") // this fires off in IE 6
    window.attachEvent("onload", fL);
    window.attachEvent("onunload", fU);
    if (gbTerminateOnBeforeUnload) window.attachEvent("onbeforeunload", fB, false);
  }
  {
    var oldFunc;
    if (window.onload != null)
    {
      oldFunc = window.onload;
      window.onload = function ( e ) {
        oldFunc( e );
        fL();
      };
    }
    else
    {
      window.onload = fL;
    }
    if (window.onunload != null)
    {
      oldFunc = window.onunload;
      window.onunload = function ( e ) {
        oldFunc( e );
        fU();
      };
    }
    else
    {
      window.onunload = fU;
    }
    if (window.onbeforeunload != null)
    {
      oldFunc = window.onbeforeunload;
      window.onbeforeunload = function ( e ) {
        oldFunc( e );
        fB();
      };
    }
    else
    {
      window.onbeforeunload = fB;
    }
  }
}

AddLoadAndUnloadEvents();

/*** End load and unload event management functions ***/

/*** General session info helper functions ***/

function ScormIsInSession()
{
  // Returns true is SetValue and GetValue are allowed
  return ((_gnScormSessionState == 2) || (_gnScormSessionState == 3));
}

function ScormGetSessionState()
{
  return _gnScormSessionState;
}

function ScormGetLastError()
{
  var nErr = -1;
  if ((_gAPI) && (typeof(_gAPI.GetLastError) != undefined)) nErr = _gAPI.GetLastError();
  return nErr;
}
function ScormGetErrorString(nErr)
{
  var strErr = "SCORM API not available";
  if (_gAPI)
  {
    // Note: Get Error functions may work even if the session is not open
    // (to help diagnose session management errors), but we're still careful,
    // and so we check whether each function is available before calling it.
    if ((isNaN(nErr)) && (typeof(_gAPI.GetLastError) != undefined)) nErr = _gAPI.GetLastError();
    if (typeof(_gAPI.GetErrorString) != undefined) strErr = _gAPI.GetErrorString(nErr.toString());
  }
  return strErr;
}

function ScormGetDiagnostic(str)
{
  var strR = "";
  if (_gAPI)
  {
    strR = _gAPI.GetDiagnostic(str.toString());
  }
  return strR;
}

/*** General data helper functions ***/
function ScormGetValue(what, bIgnoreError)
{
  // bIgnoreError flag is set to true only when this function is used
  // for testing, for example to query a value that does not exist yet.
  var strR = "";
  if (ScormIsInSession())
  {
    strR = _gAPI.GetValue(what);
    if ((!bIgnoreError) && (gbDebugSession) && (strR=="") && (ScormGetLastError()!=0))
    {
      alert("GetValue Error:\nParam='" + what +
        "'\n\nError=" + ScormGetLastError() + "\n" + ScormGetErrorString());
    }
  }
  return strR;
}

function ScormSetValue(what, value)
{
  var err = "false"
  if (ScormIsInSession())
  {
    err = _gAPI.SetValue(what, value.toString());
    if ((gbDebugSession) && (err == "false"))
    {
      alert("SetValue Error:\nParam1='" + what + "'\n\nParam2='" + value +
        "'\n\nError=" + ScormGetLastError() + "\n" + ScormGetErrorString());
    }
    if (err == "true")
    {
      // Additional auto behaviors for certain data elements
       if ((what=="cmi.score.scaled")&&(err=="true"))
      {
        _gbScoreHasBeenSet = true; // set flag in case auto score is enabled
        if (gbAutoSuccessStatus==true)
        {
           ScormSetSuccessStatusForScore(parseFloat(value.toString()));
        }
      }
      else if ((what=="cmi.progress_measure")&&(err=="true")&&(gbAutoFineCompletionStatus==true))
      {
        ScormSetCompletionStatusForMeasure(parseFloat(value.toString()));
      }
    }
  }
  return err;
}

function ScormCommit()
{
  if (ScormIsInSession())
  {
    return _gAPI.Commit("");
  }
  return "false";
}

/*** Interaction helper functions ***/
var _gaInteractionIndexCache = new Array();

function ScormInteractionAddRecord (strID, strType)
{
  if (!ScormIsInSession()) return -1;
  var n = ScormInteractionGetIndex(strID);
  if (n > -1) // An interaction record exists with this identifier
  {
    if (ScormGetValue("cmi.interactions." + n + ".type") != strType) return -1;
    return n;
  }
  n = ScormInteractionGetCount();
  var strPrefix = "cmi.interactions." + n + ".";
  if (ScormSetValue(strPrefix + "id", strID) != "true") return -1;
  if (ScormSetValue(strPrefix + "type", strType) != "true") return -1;
  _IndexCacheAdd (_gaInteractionIndexCache,n,strID);
  return n
}

function ScormInteractionGetCount()
{
  var r = parseInt(ScormGetValue("cmi.interactions._count"));
  if (isNaN(r)) r = 0;
  return r;
}

function ScormInteractionGetData(strID, strElem)
{
  var n = ScormInteractionGetIndex(strID);
  if (n < 0)
  {
    return ""; // No interaction record exists with this identifier
  }
  return ScormGetValue("cmi.interactions." + n + "." + strElem);
}

function ScormInteractionGetIndex(strID)
{
  var i = _IndexCacheGet (_gaInteractionIndexCache,strID);
  if (i >= 0) return i;
  var n = ScormInteractionGetCount();
  for (i = 0; i < n; i++)
  {
    if (ScormGetValue("cmi.interactions." + i + ".id") == strID)
    {
      _IndexCacheAdd (_gaInteractionIndexCache,i,strID);
      return i;
    }
  }
  return -1;
}

function ScormInteractionSetData(strID, strElem, strVal)
{
  var n = ScormInteractionGetIndex(strID);
  var r = "true";
  if (n < 0)
  {
    return "false"; // No interaction record exists with this identifier
  }
  // Possible optimization -- don't set value if that is already the value
  //if (ScormGetValue("cmi.interactions." + n + "." + strElem) != strVal)
  //{
    r = ScormSetValue("cmi.interactions." + n + "." + strElem, strVal);
  //}
  return r
}

function ScormInteractionMarkTime(strID)
{
  return ScormInteractionSetData(strID, "timestamp", MakeISOtimeStamp());
}

function ScormInteractionMarkLatency(strID,val)
{
  // Will work only if a time stamp is already available,
  // e.g. if ScormInteractionMarkTime was previously called,
  // and only if latency has not been set previously.
  var n = ScormInteractionGetIndex(strID);
  var r = "false";
  if (n >= 0) // If an interaction record exists with this identifier
  {
    var t = ScormInteractionGetData(strID,"timestamp");
    if (t != "")
    {
      t = DateFromISOString(t);
      if (t)
      {
        var n = ((new Date()).getTime() - t.getTime()) /10 ;
        r = ScormInteractionSetData(strID, "latency", centisecsToISODuration(n,true));
      }
    }
  }
  return r
}

function ScormInteractionSetResponse(strID,val)
{
  // The type of val depends on the interaction type. In some
  // cases this means an array. In all cases, however, val
  // may be a string that is already structured according to the
  // syntax defined in SCORM2004. No syntax checking is done
  // before calling the API with the massaged value.
  var n = ScormInteractionGetIndex(strID);
  var r = "false";
  if (n >= 0) // If an interaction record exists with this identifier
  {
    var typ = ScormGetValue("cmi.interactions." + n + ".type");
    var s = "";
    var bOK = true;
    switch(typ)
    {
      case "fill_in":
      case "multiple_choice":
      case "sequencing":
        // Accept either an array or a single string
        if (typeof(val) == "Array")
        {
          s = val.join("[,]");
        }
        else s = val + "";
        break;
      case "likert":
      case "long_fill_in":
      case "other":
         s = val + "";
        break;
      case "matching":
        // Accept either array of pair arrays, or a single string
        if (typeof(val) == "Array")
        {
          try
          {
            for (var i=0;i<val.length;i++)
            {
              if (i>0) s += "[,]";
              s += val[i].join("[.]");
            }
          }
          catch(e)
          {
            bOK = false;
          }
        }
        else
        {
          s = val+"";
        }
        break;
      case "numeric":
        try
        {
          s = val.toString();
        }
        catch(e)
        {
          bOK = false; // unspecified error
        }
        break;
      case "performance":
        // Accept either array of pair arrays, or a single string
        if (typeof(val) == "Array")
        {
          try
          {
            for (var i=0;i<val.length;i++)
            {
              if (i>0) s += "[,]";
              if (typeof(val[i]) == "Array")
              {
                if (val[i].length == 2) // step, answer
                {
                  s += val[i][0] + "[.]" + val[i][1].toString();
                }
                else // assume step only
                {
                  s += val[i][0].toString() + "[.]";
                }
              }
            }
          }
          catch(e)
          {
            bOK = false; // unspecified error
          }
        }
        else
        {
          s = val+"";
        }
        break;
      case "true_false":
        s = ((val==true)||(val=="true")||(val==1))?"true":"false";
        break;
      default:
        bOK = false; // Not a recognized interaction type;

    }
    if (bOK)
    {
      r = ScormInteractionSetData(strID, "learner_response", s);
    }
  }
  return r
}


//// Objective helper functions ////

function _IndexCacheGet (aCache,strID)
{
  for (i=0;i<aCache.length;i++)
  {
    if (aCache[i][1] == strID) return aCache[i][0];
  }
  return -1;
}
function _IndexCacheAdd (aCache,n,strID)
{
  for (i=0;i<aCache.length;i++)
  {
    if (aCache[i][1] ==strID) return;
  }
  aCache[aCache.length]=new Array(n,strID);
}

var _gaObjectiveIndexCache = new Array();

function ScormObjectiveAddRecord (strID)
{
  var n = ScormObjectiveGetIndex(strID);
  if (n > -1) // An objective record exists with this identifier
  {
    return n;
  }
  n = ScormObjectiveGetCount();
  var strPrefix = "cmi.objectives." + n + ".";
  if (ScormSetValue(strPrefix + "id", strID) != "true") return -1;
  _IndexCacheAdd (_gaObjectiveIndexCache,n,strID);
  return n
}

function ScormObjectiveGetCount()
{
  var r = parseInt(ScormGetValue("cmi.objectives._count"));
  if (isNaN(r)) r = 0;
  return r;
}

function ScormObjectiveGetData(strID, strElem)
{
  var n = ScormObjectiveGetIndex(strID);
  if (n < 0)
  {
    return ""; // No objectiverecord exists with this identifier
  }
  return ScormGetValue("cmi.objectives." + n + "." + strElem);
}

function ScormObjectiveGetIndex(strID)
{
  var i = _IndexCacheGet (_gaObjectiveIndexCache,strID);
  if (i >= 0) return i;
  var n = ScormObjectiveGetCount();
  for (i = 0; i < n; i++)
  {
    if (ScormGetValue("cmi.objectives." + i + ".id") == strID)
    {
      _IndexCacheAdd (_gaObjectiveIndexCache,i,strID);
      return i;
    }
  }
  return -1;
}

function ScormObjectiveSetData(strID, strElem, strVal)
{
  var n = ScormObjectiveGetIndex(strID);
  if (n < 0) // If no objective record with this ID
  {
    n = ScormObjectiveAddRecord(strID);
    if (n < 0) return "false"; // No objective record and failed to create one
  }
  return ScormSetValue("cmi.objectives." + n + "." + strElem, strVal);
}

//// comments_from_learner helper function ////

function ScormCommentFromLearnerAddRecord (comment,location)
{
  // Location is optional. Timestamp is added automatically.
  var n = ScormCommentFromLearnerGetCount();
  var r = "";
  var strPrefix = "cmi.comments_from_learner." + n + ".";
  if ((comment) && (comment.length > 0))
  {
    r = ScormSetValue(strPrefix + "comment", comment+"");
  }
  if ((r != "false") && (location) && (location.length > 0))
  {
    r = ScormSetValue(strPrefix + "location", location+"");
  }
  if ((r != "false") && (location) && (location.length > 0))
  {
    r = ScormSetValue(strPrefix + "timestamp", MakeISOtimeStamp());
  }
  if (r == "true") return r;
  return "false";
}

function ScormCommentFromLearnerFindRecordIndex(location)
{
  // Finds a comment from learner index based on location
  if ((!location) || (location.length == 0)) return -1;
  var nCnt = ScormCommentFromLearnerGetCount();
  for (var i=0;i<nCnt;i++)
  {
    if (ScormGetValue("cmi.comments_from_learner." + n + ".location") == location)
    {
      return n;
    }
  }
  return -1;
}

function ScormCommentFromLearnerGetComment(location)
{
  // Finds a comment from learner based on location
  var n = ScormCommentFromLearnerFindRecordIndex(location);
  if (n > -1)
  {
    return (ScormGetValue("cmi.comments_from_learner." + n + ".comment"));
  }
  return "";
}

function ScormCommentFromLearnerReplaceRecord (comment,location)
{
  // Location is required, because it is used to identify the
  // record to replace. Timestamp is added automatically.
  var n = ScormCommentFromLearnerFindRecordIndex(location)
  var r;
  if (n > -1)
  {
    var strPrefix = "cmi.comments_from_learner." + n + ".";
    r = ScormSetValue(strPrefix + "comment", comment+"");
    ScormSetValue(strPrefix + "timestamp", MakeISOtimeStamp());
  }
  else
  {
    r = ScormCommentFromLearnerAddRecord (comment,location);
  }
  return r;
}

function ScormCommentFromLearnerGetCount()
{
  var r = parseInt(ScormGetValue("cmi.comments_from_learner._count"));
  if (isNaN(r)) r = 0;
  return r;
}


function ScormCommentFromLMSGetCount()
{
  var r = parseInt(ScormGetValue("cmi.comments_from_lms._count"));
  if (isNaN(r)) r = 0;
  return r;
}

// Data shaping functions for various CMI values

function _IsValidScaledScore(n)
{
  return ((!isNaN(n)) && (n >= -1.0) && (n <= 1.0))
}

function _IsValidProgressMeasure(n)
{
  return ((!isNaN(n)) && (n >= -1.0) && (n <= 1.0))
}

function ScormSetSuccessStatusForScore(nScore)
{
  if (!_gbPassingScoreAlreadyQueriedFromRTE)
  {
    var n = parseFloat(ScormGetValue("cmi.scaled_passing_score"));
    _gbPassingScoreAlreadyQueriedFromRTE = true;
    if (_IsValidScaledScore(n))
    {
      gnPassingScore = n;
      _gbPassingScoreIsFromRTE = true;
    }
  }
  if ((_IsValidScaledScore(nScore)) && (_IsValidScaledScore(gnPassingScore)))
  {
    ScormSetValue("cmi.success_status",(nScore >= gnPassingScore)?"passed":"failed");
  }
}

function ScormSetCompletionStatusForMeasure(nMeas)
{
  if (!_gbCompletionThresholdAlreadyQueriedFromRTE)
  {
    var nThreshold = parseFloat(ScormGetValue("cmi.completion_threshold"));
    _gbCompletionThresholdAlreadyQueriedFromRTE = true;
    if (_IsValidProgressMeasure(nThreshold))
    {
      gnCompletionThreshold = nThreshold;
      _gbCompletionThresholdIsFromRTE = true;
    }
  }
  if (_IsValidProgressMeasure(nMeas))
  {
    gbAutoCoarseCompletionStatus = false;
    if (_IsValidProgressMeasure(gnCompletionThreshold))
    {
      if (nMeas >= gnCompletionThreshold)
      {
        ScormSetValue("cmi.completion_status","completed");
      }
      else if (nMeas == 0)
      {
         ScormSetValue("cmi.success_status","not attempted");
      }
      else
      {
         ScormSetValue("cmi.success_status","incomplete");
      }
    }
  }
}

/*** End data shaping and validation functions ***/

/*** TimeStamp helper function. Returns a timestamp in ISO format ***/

function MakeISOTimeStamp(objDate, bRelative, nResolution)
{
  // Make an ISO 8601 timestamp string as specified for SCORM 2004
  // * objDate is an optional ECMAScript Date object;
  //   if objDate is null, "this instant" is assumed.
  // * bRelative is optional; if bRelative is true,
  //   the timestamp will show local time with a time offset from UTC;
  //   otherwise the timestamp will show UTC (a.k.a. Zulu) time.
  // * nResolution is optional; it specifies max decimal digits
  //   for fractions of second; it can be null, 0 or 2. If null, 2 is assumed.
  var nMs, nCs = 0;
  var s = "";
  if (objDate)
  {
    if ((typeof(objDate)).indexOf("date") < 0) objDate = null;
  }
  if (!objDate) objDate = new Date();
  if (bRelative) nMs = objDate.getMilliseconds();
  else nMs = objDate.getUTCMilliseconds();
  if (nResolution == 0)
  {
    // Precision is whole seconds; round up if necessary
    if (nMs > 500)
    {
      if (bRelative) objDate.setMilliseconds(1000);
      else objDate.setUTCMilliseconds(1000);
    }
  }
  else
  {
    // Default precision is centisecond. Let us see whether we need to add
    // a rounding up adjustment
    if (nMs > 994)
    {
      if (bRelative) objDate.setMilliseconds(1000);
      else objDate.setUTCMilliseconds(1000)
    }
    else
    {
      nCs = Math.floor(nMs / 10);
    }
  }
  if (bRelative)
  {
    s = objDate.getFullYear() + "-" +
    ZeroPad(objDate.getMonth(), 2) + "-" +
    ZeroPad(objDate.getDate(), 2) + "T" +
    ZeroPad(objDate.getHours(), 2) + ":" +
    ZeroPad(objDate.getMinutes(), 2) + ":" +
    ZeroPad(objDate.getSeconds(),2);
  }
  else
  {
    s = objDate.getUTCFullYear() + "-" +
    ZeroPad(objDate.getUTCMonth(), 2) + "-" +
    ZeroPad(objDate.getUTCDate(), 2) + "T" +
    ZeroPad(objDate.getUTCHours(), 2) + ":" +
    ZeroPad(objDate.getUTCMinutes(), 2) + ":" +
    ZeroPad(objDate.getUTCSeconds(),2);
  }
  if (nCs > 0)
  {
    s += "." + ZeroPad(nCs,2);
  }
  if (bRelative)
  {
    // Need to flip the sign of the time zone offset
    var nTZOff = -objDate.getTimezoneOffset();
    if (nTZOff >= 0) s += "+";
    s += ZeroPad(Math.round(nTZOff / 60), 2);
    nTZOff = nTZOff % 60;
    if (nTZOff > 0) s += ":" +  ZeroPad(nTZOff, 2);
  }
  else
  {
    s += "Z";
  }
  return s;
}

function ZeroPad(n, nLength)
{
  // Takes a number and pads it with leading 0 to the length specified.
  // The padded length does not include negative sign if present.
  var bNeg = (n < 0);
  var s = n.toString();
  if (bNeg) s = s.substr(1,s.length);
  while (s.length < nLength) s = "0" + s;
  if (bNeg) s = "-" + s;
  return s
}

function DateFromISOString(strDate)
{
  // Convert an ISO 8601 formatted string to a local date
  // Returns an ECMAScript Date object or null if an error was detected
  // Assumes that the string is well formed and SCORM conformant
  // otherwise a runtime error may occur in this function.
  var objDate = new Date();
  var sDate = strDate; // The date part of the input, after a little massaging
  var sTime = null; // The time part of the input, if it is included
  var sTimeOffset = null; // UTC offset, if specified in the input string
  var sTimeOffsetSign = "";
  var a = null; // Will be reused for all kinds of string splits
  var n, nY, nM, nD, nH, nMin, nS, nCs = 0;

  // If this is "Zulu" time, it will make things a little easier
  var bZulu = (strDate.indexOf("Z") > -1);
  if (bZulu) strDate = strDate.substr(0, strDate.length - 1);

  // Parse the ISO string into date and time
  if (strDate.indexOf("T") > -1)
  {
    var a = strDate.split("T");
    sDate = a[0];
    var sTime = a[1];
  }
  // Parse the date part
  a = sDate.split("-");
  nY = parseInt(a[0]);
  if (a.length > 1) nM = parseInt(a[1]);
  if (a.length > 2) sD = a[2];
  // If this was only a date but with no time, there might still be an offset
  if ((sTime == null) && (a.length == 4))
  {
    // There is a negative time offset but no time (assume midnight)
    sTimeOffset = a[3];
    sTimeOffsetSign = "-";
  }
  else if ((a.length == 3) && (sD.indexOf("+")> -1))
  {
    a = sD.split("+");
    sD = a[0];
    sTimeOffset = a[1];
    sTimeOffsetSign = "+";
  }
  var nD = parseInt(sD);
  // Done with the date. If there is a time part, parse it out.
  if (sTime)
  {
    if (sTime.indexOf("-")) sTimeOffsetSign = "-";
    if (sTime.indexOf("+")) sTimeOffsetSign = "+";
    if (sTimeOffsetSign != "")
    {
      a = sTime.split(sTimeOffsetSign);
      sTime = a[0];
      sTimeOffset = a[1];
    }
    a = sTime.split(":");
    nH = parseInt(a[0]);
    if (a.length > 1) nMin = parseInt(a[1]);
    if (a.length > 2)
    {
      nSec = parseFloat(a[2]);
      if (isNaN(nSec)) return null;
      nCs = Math.round(nSec / 100);
      nSec = Math.round(nSec - (nCs * 100));
    }
  }
  if (bZulu)
  {
    objDate.setUTCFullYear(nY,nM,nD);
    objDate.setUTCHours(nH,nMin,nSec,nCs * 10);
  }
  else
  {
    objDate.setFullYear(nY,nM,nD);
    objDate.setHours(nH,nMin,nSec,nCs * 10);

    // Calculate and set the time offset for local time
    if (sTimeOffset)
    {
      var nOffset = 0;
      a = sTimeOffset.split(":");
      nOffset = parseInt(a[0]);
      if (isNaN(nOffset)) return null;
      nOffset = nOffset * 60
      if (a.length > 1)
      {
        n = parseInt(a[1]);
        if (isNaN(n)) return null;
        nOffset += n;
      }
      nOffset = nOffset * 60; // minutes to milliseconds
      if (sTimeOffsetSign == "-") nOffset = -nOffset;
      objDate.setTime(objDate.getTime() + nOffset);
    }
  }
  return objDate //.toString();
}


/***  Timekeeping management functions ***
 These functions allow the generic script to keep track
 of elapsed time automatically. Some of these functions
 can also be used as helper functions to
*/

function ScormMarkInitElapsedTime()
{
  // Called by ScormInitialize when successful;
  var d = new Date();
  _gnInitCentiseconds  = Math.round((new Date()).getTime() / 10);
  return _gnInitCentiseconds;
}

function ScormMarkTermElapsedTime()
{
  // Called by ScormTerminate
  _gnTermCentiseconds  = Math.round((new Date()).getTime() / 10);
  return _gnTermCentiseconds;
}

function CentisecsSinceSessionStart()
{
  if (isNaN(_gnInitCentiseconds)) return 0;
  if ((isNaN(_gnTermCentiseconds)) || (_gnTermCentiseconds == 0))
  {
    return Math.round((new Date()).getTime() / 10) - _gnInitCentiseconds;
  }
  return _gnTermCentiseconds - _gnInitCentiseconds;
}

function CentisecsSinceAttemptStart()
{
  var n = 0;
  if (isNaN(_gnTotalCentiseconds))
  {
    n = ISODurationToCentisec(ScormGetValue("cmi.total_time"))
    if (!isNaN(n)) _gnTotalCentiseconds = n;
  }
  n = CentisecsSinceSessionStart();
  if (!isNaN(_gnTotalCentiseconds))
  {
    return _gnTotalCentiseconds + n;
  }
  return n;
}

function ScormSetSessionTime(nCentisec)
{
  if (isNaN(nCentisec)) nCentisec = CentisecsSinceSessionStart();
  //if (gbDebugSession) alert("Centisecs since session start: " + nCentisec);
  return ScormSetValue("cmi.session_time",centisecsToISODuration(nCentisec));
}

// Helper functions for duration
function centisecsToISODuration(n) {
    // Note: SCORM and IEEE 1484.11.1 require centisec precision
    // Months calculated by approximation based on average number
  // of days over 4 years (365*4+1), not counting the extra day
  // every 1000 years. If a reference date was available,
  // the calculation could be more precise, but becomes complex,
    // since the exact result depends on where the reference date
    // falls within the period (e.g. beginning, end or ???)
  // 1 year ~ (365*4+1)/4*60*60*24*100 = 3155760000 centiseconds
  // 1 month ~ (365*4+1)/48*60*60*24*100 = 262980000 centiseconds
  // 1 day = 8640000 centiseconds
  // 1 hour = 360000 centiseconds
  // 1 minute = 6000 centiseconds
  n = Math.max(n,0); // there is no such thing as a negative duration
  var str = "P";
  var nCs = n;
  // Next set of operations uses whole seconds
  var nY = Math.floor(nCs / 3155760000);
  nCs -= nY * 3155760000;
  var nM = Math.floor(nCs / 262980000);
  nCs -= nM * 262980000;
  var nD = Math.floor(nCs / 8640000);
  nCs -= nD * 8640000;
  var nH = Math.floor(nCs / 360000);
  nCs -= nH * 360000;
  var nMin = Math.floor(nCs /6000);
  nCs -= nMin * 6000
  // Now we can construct string
  if (nY > 0) str += nY + "Y";
  if (nM > 0) str += nM + "M";
  if (nD > 0) str += nD + "D";
  if ((nH > 0) || (nMin > 0) || (nCs > 0))
  {
    str += "T";
    if (nH > 0) str += nH + "H";
    if (nMin > 0) str += nMin + "M";
    if (nCs > 0) str += (nCs / 100) + "S";
  }
  if (str == "P") str = "PT0H0M0S";
    // technically PT0S should do as well.
  return str;
}

function ISODurationToCentisec(str)
{
  // Only gross syntax check is performed here
  // Months calculated by approximation based on average number
  // of days over 4 years (365*4+1), not counting the extra day
  // every 1000 years. If a reference date was available,
  // the calculation could be more precise, but becomes complex,
  // since the exact result depends on where the reference date
  // falls within the period (e.g. beginning, end or ???)
  // 1 year ~ (365*4+1)/4*60*60*24*100 = 3155760000 centiseconds
  // 1 month ~ (365*4+1)/48*60*60*24*100 = 262980000 centiseconds
  // 1 day = 8640000 centiseconds
  // 1 hour = 360000 centiseconds
  // 1 minute = 6000 centiseconds
  var aV = new Array(0,0,0,0,0,0);
  var bErr = false;
  var bTFound = false;
  if (str.indexOf("P") != 0) bErr = true;
  if (!bErr)
  {
    var aT = new Array("Y","M","D","H","M","S")
    var p=0;
    var i = 0;
    str = str.substr(1); //get past the P
    for (i = 0 ; i < aT.length; i++)
    {
      if (str.indexOf("T") == 0)
      {
        str = str.substr(1);
        i = Math.max(i,3);
        bTFound = true;
      }
      p = str.indexOf(aT[i]);
      //alert("Checking for " + aT[i] + "\nstr = " + str);
      if (p > -1)
      {
        // Is this a M before or after T? Month or Minute?
        if ((i == 1) && (str.indexOf("T") > -1) && (str.indexOf("T") < p)) continue;
        if (aT[i] == "S")
        {
          aV[i] = parseFloat(str.substr(0,p))
        }
        else
        {
          aV[i] = parseInt(str.substr(0,p))
        }
        if (isNaN(aV[i]))
        {
          bErr = true;
          break;
        }
        else if ((i > 2) && (!bTFound))
        {
          bErr = true;
          break;
        }
        str = str.substr(p+1);
      }
    }
    if ((!bErr) && (str.length != 0)) bErr = true;
    //alert(aV.toString())
  }
  if (bErr)
  {
     //alert("Bad format: " + str)
    return 0
  }
  return aV[0]*3155760000 + aV[1]*262980000
      + aV[2]*8640000 + aV[3]*360000 + aV[4]*6000
      + Math.round(aV[5]*100)
}

/*** End timekeeping management functions ***/

/*** Window auto close management functions ***/

var _gTimerOwnWindowClose = null;
var _gbAlreadyTriedToCloseOwnWindow = false;

function _IsClosingWindowOK()
{
  if (!gbEnableAutoCloseWindow) return false;
  // Tweaking of the rule may be required for some LMS
  // that use what looks like a popup window but is actually a frameset.
  // A function to try to detect such a situation might be inserted here.
  return (!((window.parent) && (window.parent || window)));
}

function _PrepareCloseWindowIfAllowed()
{
  if ((!_gbAlreadyTriedToCloseOwnWindow)
      && (!_gbProcessingUnload)
      && (_IsClosingWindowOK()))
  {
    gTimerWindowClose = setInterval("_CloseTheSCOWindow()", 1500);
  }
}

function _CloseTheSCOWindow()
{
  if (_gTimerOwnWindowClose)
  {
    clearInterval(_gTimerOwnWindowClose);
    _gTimerOwnWindowClose = null;
  }
  if (_gbAlreadyTriedToCloseOwnWindow) return;
  _gbAlreadyTriedToCloseOwnWindow = true;
  if (!window.closed) window.close();
}
/*** End Window Management functions ***/


/*** Dynamic initialization before onload event ***/

if (gbInitializeBeforeLoad)
{
  ScormInitialize();
}

/*** Used for debugging ***/

function ostyn2004scoScriptOK()
{
  alert ("ostyn2004sco.js OK\nVersion: " + gsOstynScriptVersion);
}

