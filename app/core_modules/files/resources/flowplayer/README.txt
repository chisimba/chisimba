FlowPlayer Version history 
==========================

0.9     Initial public release. All basic features are in place.

0.9.1   Added 'autoPlay' variable that can be used to specify whether the
        playback should begin immediately when the player has been loaded into
        the Web browser.

0.9.2   Bug fixes.

0.9.3   Added new 'bufferLength' variable that can be used in the HTML page to
        specify the length of the buffer in seconds when streaming FLV. Fixed a
        bug that prevented playback after an FLV was completely played.</param>

0.9.4   Added a 'baseURL' variable that can be used to specify the location of
        the video file to be loaded by Player.swf. See Player.html for an
        example.

        If the 'videoFile' variable's value contains a '.flv' or '.swf'
        extension, the standalone player (Player.swf) will NOT append this based
        on the detected flash version. If a prefix is not present, the player
        always appends either one of these prefixes.

1.0
	- Displays a "BUFFERING..." text when filling the video buffer.
	- Fixed playback of the start and the end of the video where the player
	  was errorneously cutting off some of the video.
	- Added a new start() function to the FlowPlayer class.
	- Fixed Sample.fla

1.1
    - Added ability to loop; Contains a new toggle button to control looping.
      Default looping state is defined in 'loop' parameter. Thanks Jeff Wagner
      for contributing the initial looping support.
    - Now resizes according to the size defined in the HTML page
    - Fixed some flaws in the graphics
    - The color of the progress bar is now gray by default (more neutral). The
      color can be customized by parameters.
    - Removed support to play videos in SWF format.

1.2
    - Added a 'autoBuffering' option and welcome image support.
    - Added a 'hideContols' option to hide all buttons and other widgets and
      leaving only the video display showing.
    - Added support for welcome images
    - Most of the UI is now built dynamically with ActionScript instead of using
      pre-drawn images. This results in 50% smaller download size.

1.2.b2
	- Fixed binary build that contained an old buggy FlowPlayer.swf

1.3
    - Fixed resizing problem that occurred with Internet Explorer: The video was
      not resized when the page was refreshed.

1.4
	- Removed the blue the background color of the player. The light blue color
	  became visible when using only the obect-tag to embed the player into a page.
	  By using only the object tag it's possible to author valid XHTML. The sample
	  FlowPlayer.html now shows this kind of markup.

1.5
	- Support for playlists
	- Extenal configuration file that enables configuring all the existing
	  settings. All settings defined in this configuration file can be
	  overridden using flashvars in the HTML object tag.
	- Basic skinning support: Images for all buttons (play, pause, looping
	  toggle, and dragger) can be loaded from external JPG files. Smaller
	  versions of the default buttons are provided as an example.
	  FlowPlayerLiht.swf is meant to be used with skinning: it does not contain
	  any button images in itself and therefore is slightly smaller in download
	  size.
	- 'hideBorder' option
	- visual improvement of control buttons
	- dragging can be now done by clicking anywhere in the progress bar area
	- clicking on the video area pauses and resumes playback
	- scaling the splash image is now optional. Alternatively it can be centered
	  into the video area.
	- removed the border surrounding the video area
	- plus some more minor changes
	Bug fixes:
	- Seeking using the dragger button is more accurate. Now it is possible to
	  seek to the very beginning of a clip.

1.6
	Bug fixes:
	- Does not buffer unnessessarily if looping through one clip again and again
	- Playback can be started normally again when the clip is done and looping
	is off. There was a bug that prevented this.
	- Clicking on the video area did not start playing if splash not used and
	when autoPlay=false
	- The seeker button aligned to the right from the mouse position when it was
	grabbed using the mouse button. Now it stays on the same position it was in
	when the mouse button was pressed down.
	- It was not possible to use characters 'y' and 'Y' in the names inside 
	the playList. Now following characters are available: 
	"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz?!/-:,. 1234567890"

1.7
	Fixes:
	- baseURLs are not appended to file name values that are complete URLs
	- minor visual enhancements
	New features:
	- Support for long videos: Initial support for streaming servers (tested with red5) +
	thumbnails corresponding to cue points.
	- Video duration and time indicator
	- Resizing of the video area through a menu

1.7.1
	- Now the original FLV video dimensions are detected from the FLV metadata. The different resizing options are based on those. Initially the size is maximized according to the 'videoHeight' setting and preserving the aspect ratio.
	Fixes:
	- progress bar now goes to the start position (left) even when autoPlay=false and autoBuffering=false [1574640]
	- resizing menu does not toggle pause/resume anymore [1581085]
	- fixed missing audio on some FLV files [1568612]
	- Flash Media Servers seems to send the FLV metadata again and again when seeking. This caused unnessessary rearranging of the thumbnails.
	- Thumnail list's scrollbar is now hidden if all thumbnails fit the available visible space.

1.8
	- Initial JavaScript API (Requires FlashPlayer 8 or above):
		* Possibility to configure the player using JavaScript and a configuration object
		similar to the external config file (using JavaScript Object Notation, JSON)
		* Possibility to move the playback to different clips in the playlist using JavaScript
	- Changed the format how thumbnails are specified in the config object
	- The numbers from the playlist's clips were removed
	- Unnamed clips are hidden from the playlist
	- Possibility to have images in the playlist. Now the splash image is internally handled
	as being the first clip in the playlist.
	- Adjacent clips are played as one stream when using a streaming server

1.9	- More complete JavaScript API
	- Hierarchical configuration
	- New config variable "initialScale" to control the initial scaling of the video
	- New resizing option "fill window" that will fill all available space (does not care about preserving the aspect ratios)
	- Changed the default buffer to 10 seconds
	- noVideoClip config setting that can be used to specify a video clip or an image to be played when
	  the primary clip is not found
	Fixes:
	- Fixed buffering indicator, did not show the buffer lenght correctly when not using a streaming server
	- It was not possible to pass an empty string in baseURL when using setConfig() in the JavaScript API
	- loop config setting was broken
	- Clip is now better recognized as completely played

1.10
	Changed to use the same configuration syntax with flashVars as with external configuration files. Now
	the same configuration style is used consistently everywhere.
	Fixes:
	- It was impossible to disable autoPlay and autoBuffering

1.10.1
	- Fix for the message on IE: "Object doesn't support this property or method' Line 48, Char 3". This
	  was caused by two method names in the new JavaScript API. As a result, these methods are now renamed
	  in the JavaScript API.
	- Inlcuded javascript.txt (documentation for the JavaScript API) in the distribution packages.

1.11
	- Finally added a volume control slider
	- Made all progress bar and volume slider colors customizable
	- Added a possibility to hide the playlist control buttons (next clip and previous clip buttons)
	- Fixed the sample html files to work in Internet Explorer. The pages now use SWFObject
	  (http://blog.deconcept.com/swfobject/) to embed the player.

1.11.1
	- Changed volume slider to change the volume while the slider is being moved. The previous version changed
	  it only after the mouse button was released.
	Fixes:
	- Now resets the play/pause button correctly when the clip ends and looping is not used
	- Looping does not go to the splash after the last clip in the playlist is finished. Instead the playback loops
	  to the first clip after the splash. This is valid also when only one video is configured to be played using
	  the 'videoFile' config option.
	
How to use it
=============

Please see http://flowplayer.sourceforge.net/howto.html

Hacking
-------

FlowPlayer has following dependencies:
* Luminic Box logger (http://luminicbox.com)
* as2lib (http://www.as2lib.org)

You need to specify locations for these in the build.xml. Modify the file accordingly.

To compile FlowPlayer you need following tools:
* mtasc compiler (http://www.mtasc.org)
* swfmill (http://iterative.org/swfmill)
* Ant (http://ant.apache.org)
* as2ant (http://www.as2lib.org)
* as2lib (http://sourceforge.net/projects/as2lib)

You need to add a method signature for onCuePoint method into MTASC/std/NetStream.as:
	function onCuePoint(info:Object):Void;
For some weird reason that is missing.

You need to set up as2ant targets for Ant. I have configured as2ant targets using 
Eclipse's integrated Ant configuration (Eclipse/Preferences/Ant/Runtime/Tasks). 
After that you should be able to buld FlowPlayer using Ant. There is no need to
use the Adobe/Macromedia Flash tool at all, everything can be done using these tools.

To build run: ant build

To test cd to 'build' directory and open FlowPlayer.html using a browser. 
Change the parameters in FlowPlayer.html to try with your own videos.

Bug reports, questions, contributing 
==================================== 
If you have bug reports, questions or would like to contribute, please send a
message to: api@iki.fi
