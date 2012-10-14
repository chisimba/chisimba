WHAT IT IS
----------

This is Cortado, a multimedia framework for Java written by Fluendo.

It contains:
- JST, a port of the GStreamer 0.10 design to Java
- jcraft, a copy of the JCraft JOgg/Jorbis code
- jheora, an implementation of Theora in Java
- codecs (currently only containing the Smoke codec, a variant on Jpeg)
- JST plugins for:
  - HTTP source element
  - Ogg and Multipart demuxers
  - Theora, JPEG and Smoke video decoders
  - Vorbis and MuLaw audio decoders
   - Java 1.1 sun.audio API audio sink
   - Java 1.4 javax.sound.sampled API audio sink
- examples
- applets

This release has support for
- seeking in on-demand files
- the above-mentioned plugins
- basic HTTP authentication
- buffering

FAQ
---

We maintain a FAQ in our trac installation:
https://core.fluendo.com/flumotion/trac/wiki/FAQ

If any questions should be added, let us know.

BUGS
----

You can file bugs at Fluendo's issue tracker:
https://core.fluendo.com/tracker

Make sure to choose the "cortado" component.

BUILDING
--------

The build uses ant exclusively now.

Normally, running

  ant

Should build everything, if your system is setup correctly.

You can copy build.config.sample to build.config and tweak it to choose
certain settings.

Run

  ant -p

to see all the possible targets.

FAQ
---
You can find our FAQ on-line at
https://core.fluendo.com/flumotion/trac/wiki/FAQ#about-cortado

EXAMPLES
--------

You need a Java Virtual Machine to run the example code.
You also need to set the classpath to your build tree when running
(output/build/debug or output/build/stripped)

- Jikes does not have a VM
- gij (the GNU Java VM) does not have a javax.sound.sampled implementation, so
  fails when playing audio, but can still play video-only files
- Sun Java VM works for me:

  /usr/java/jre1.5.0_04/bin/java -cp output/build/debug com.fluendo.examples.Player http://stream.fluendo.com:8850

  (with a JPackage java-1.5.0-sun package)
  /usr/lib/jvm/java-1.5.0-sun-1.5.0.05/jre/bin/java -cp output/build/debug com.fluendo.examples.Player http://stream.fluendo.com:8850


USAGE
-----

Embed the applet in a web page with code like this:

<html>
 <head>
 </head>
 <body>
   <applet code="com.fluendo.player.Cortado.class" 
           archive="cortado.jar" 
	   width="352" height="288">
     <param name="url" value="http://localhost/test6.ogg"/>
     <param name="local" value="false"/>
     <param name="duration" value="232"/>
     <param name="keepAspect" value="true"/>
     <param name="video" value="true"/>
     <param name="audio" value="true"/>
     <param name="bufferSize" value="200"/>
     <param name="userId" value="user"/>
     <param name="password" value="test"/>
   </applet>
 </body>
</html>


parameters:
-----------

  url:        string
              the URL to load, must be a fully qualified URL.
              IMPORTANT: if the applet is not signed, the hostname of the
              url *is required* to be the same as the hostname of the link
              to the page with the applet tag.  This is a Java security limitation.

  seekable:   boolean
              Whether or not you can seek in the file.  For live streams,
              this should be false; for on-demand files, this can be true.
	      Defaults to false

  duration:   int
              Length of clip in seconds.  Needed when seekable is true,
              to allow the seek bar to work.

  keepAspect: boolean
              Try to keep the natural aspect of the video when resizing the
              applet window. true or false.
	      Defaults to true

  video:      boolean
              Use video. When not using video, this property will not create
              resources to play a video stream. true or false.
	      Defaults to true
	      
  audio:      boolean
              Use audio. When not using audio, this property will not create
              resources to play an audio stream. true or false.
	      Defaults to true

  statusHeight: int
              The height of the status area (default 12)

  autoPlay:   boolean
              Automatically start playback (default true)

  showStatus: enum (auto|show|hide)
              Controls how to make the status area visible. 
	      auto will show the status area when hovered over with the mouse.
	      hide will only show the status area on error.
	      show will always show the status area.
	      (default auto)

  hideTimeout: int 
              Timeout in seconds to hide the status area when showStatus is
	      auto. This timeout is to make sure that the status area is visible
	      for the first timeout seconds of playback so that the user can see
	      that there is a clickable status area too.
	      (default 0)

  bufferSize: int
              The size of the network buffer, in KB.
              A good value is max Kbps of the stream * 33
              Defaults to 200

  bufferLow:  int
              Percentage of low watermark for buffer.  Below this, the applet
              will stop playing and rebuffer until the high watermark is
              reached.
              Defaults to 10

  bufferHigh: int
              Percentage of high watermark for buffer.  At startup or when
              rebuffering, the applet will not play until this percentage of
              buffer fill status is reached.
              Defaults to 70

  userId:     string
              user id for basic authentication.

  password:   string
              password for basic authentication.

  debug:      int
              debug level, 0 - 4.  Defaults to 3.  Output goes to the Java
              console.

Using javascript
----------------

The applet parameters can be changed from javascript by calling the
setParam(key, value) on the applet. After setting the new parameters in the
applet it needs to be restarted with the restart() method for the changes to
take effect.

The following piece of HTML demonstrates switching URLs with an without sound
using javascript:

<html>
 <head>
 </head>
 <body>
   <script language="javascript">
     function restart() {
       document.applets[0].restart(); 
     }
     function loadUrl(uri, audio) {
       document.applets[0].setParam("audio", audio); 
       document.applets[0].setParam("url", uri); 
       restart();
     }
   </script>
   <applet archive="cortado.jar" code="com.fluendo.player.Cortado.class" width="320" height="240"> 
     <param name="url" value="http://localhost:8800"/>
     <param name="local" value="false"/>
     <param name="framerate" value="5.0"/>
     <param name="keepaspect" value="true"/>
     <param name="video" value="true"/>
     <param name="audio" value="true"/>
   </applet>

   <br/>
   <br/>

   <button onClick="restart()">
    Restart
   </button>
   <button onClick="loadUrl('http://localhost:8800', 'true')">
    With Audio
   </button>
   <button onClick="loadUrl('http://localhost:8802', 'false')">
    Without Audio
   </button>
   
 </body>
</html>

The applet can be controlled with the following javascript methods:

  doPlay(): Start playback
  doPause(): Pause playback
  doStop(): Stop playback
  doSeek(double pos); seek to a new position, must be between 0.0 and 1.0.


