/*
 * FlowPlayer external configuration file.
 * Copyright 2005-2006 Anssi Piirainen
 *
 * All settings defined in this file can be alternatively defined in the 
 * embedding HTML object tag (as flashvars variables). Values defined in the
 * object tag override values defined in this file. You could use this 
 * config file to provide defaults for multiple player instances that 
 * are used in a Web site. Individual instances can be then customized 
 * with their embedding HTML.
 *
 * Note that you should probably remove all the comments from this file
 * before using it. That way the file will be smaller and will load faster.
 */
{
	/*
	 * Name of the video file. Used if only one video is shown.
	 *
	 * Note for testing locally: Specify an empty baseURL '', if you want to load
	 * the video from your local disk from the directory that contains
	 * FlowPlayer.swf. In this case the videoFile parameter value should start
	 * with a slash, for example '/video.flv'.
	 *
	 * See also: 'baseURL' that affects this variable
	 */
//	 videoFile: 'honda_accord.flv',

	/*
	 * Clip to be used if the file specified with 'videoFile' or any of the clips in the playlist
	 * was not found.  The missing video clips are replaced by this clip. This can be
	 * an image or a FLV clip. Typically this will contain an image/video saying
	 * "the video you requested cannot be found.....".
	 *
	 * The syntax for the value is the same is with the clips in a playlist
	 * including the possibility to have start/end and duration properties.
	 *
	 * See also: 'baseURL' that affects this variable
	 */
	 noVideoClip: { url: 'main_clickToPlay.jpg', duration: 10 },
	 //noVideoClip: { url: 'MiltonFriedmanonLimi.flv' },

	/*
	 * Playlist is used to publish several videos using one player instance.
	 * The clips in the playlist may have following properties:
	 *
	 * name: Name for the clip to be shown in the playlist view. If this is
	 *       not given, the clip will be hidden from the view.
	 *
	 * start: The start time (seconds) from where to start the playback. A nonzero
	 * value can only be used when using a streaming server!!
	 * end: The end time (seconds) where to stop the playback.
	 *
	 * You can also have images in the playlist. The playback pauses in the
	 * image unless a 'duration' property is given for the image:
	 *
	 * duration: The duration the image is to be shown. If not given the playback
	 *           pauses when the image is reached in the list.
	 *
	 * See also: 'baseURL' is prefixed with each URL
	 */
	playList: [
	{ url: 'main_clickToPlay.jpg' },
	{ name: 'Honda Accord', url: '!honda_accord.flv' },
	{ name: 'River', url: 'river.flv' },
	{ name: 'Ounasvaara', url: 'ounasvaara.flv' }
	],
	
	/*
	 * Specifies wether the playlist should be shown in the player SWF component or not.
	 * Optional, defaults to false. 
	 *
	 * I think it's better to have the visible part of the playlist in HTML 
	 * and use JavaScript to control the player (see FlowPlayerJs.html for an example).
	 */
	showPlayList: true,
	
	/*
	 * Specifies wether the playlist control buttons should be shown in the player SWF component or not.
	 * Optional, defaults to the value of showPlayList. 
	 */
	showPlayListButtons: true,

	/*
	 * Streaming server connection URL.
	 */
//	 streamingServerURL: 'rtmp://localahost:oflaDemo',
	
	/* 
	 * baseURL specifies the URL that is appended in front of different file names
	 * given in this file.
	 * 
	 * You don't need to specify this at all if you place the video next to
	 * the player SWF file on the Web server (to be available under the same URL path).
	 */
//	 baseURL: 'http://flowplayer.sourceforge.net/video',
	
	
	/*
	 * What kind of streaming server? Currently 'red5' and 'fms' provide different
	 * kind of timing information with the flv metadata. 
	 */
//	streamingServer: 'fms',
	
	/*
	 * Specifies whether thumbnail information is contained in the FLV's cue point 
	 * metadata. Cue points can be injected into the FLV file using 
	 * for example Flvtool2. See the FlowPlayer web site for more info.
	 * (optional, defaults to false)
	 * 
	 * See also: cuePoints below for an alternative way of specifying thumb metadata
	 */
//	thumbsOnFLV: true,
	
	/*
	 * Thumbnails specific to cue points. Use this if you don't want to
	 * embed thumbnail metadata into the FLV's cue points. 
	 * If you have thumbNails defined here you should have thumbsOnFLV: false !
	 * thumb times are given in seconds
	 */
// 	thumbs: [
// 	{ thumbNail:  'Thumb1.jpg', time: 10 },
// 	{ thumbNail:  'Thumb2.jpg', time: 24 },
// 	{ thumbNail:  'Thumb3.jpg', time: 54 },
// 	{ thumbNail:  'Thumb4.jpg', time: 74 },
// 	{ thumbNail:  'Thumb5.jpg', time: 94 },
// 	{ thumbNail:  'Thumb6.jpg', time: 110 }
// 	],
	// Location of the thumbnail files
// 	thumbLocation: 'http://www.kolumbus.fi/apiirain/video',
	
	/* 
	 * 'autoPlay' variable defines whether playback begins immediately or not.
	 * 
	 * Note that currently with red5 you should not have false in autoPlay 
	 * when you specify a nonzero starting position for the video clip. This is because red5
	 * does not send FLV metadata when the playback starts from a nonzero value.
	 * 
	 * (optional, defaults to true)
	 */
	autoPlay: true,

	/*
	 * 'autoBuffering' specifies wheter to start loading the video stream into
	 *  buffer memory  immediately. Only meaningful if 'autoPlay' is set to
	 * false. (optional, defaults to true)
	 */
	autoBuffering: true,

	/*
	 * 'startingBufferLength' specifies the video buffer length to be used to kick
	 * off the playback. This is used in the beginning of the playback and every time
	 * after the player has ran out of buffer memory. 
	 * More info at: http://www.progettosinergia.com/flashvideo/flashvideoblog.htm#031205
	 * (optional, defaults to the value of 'bufferLength' setting)
	 * 
	 * see also: bufferLength
	 */
//	startingBufferLength: 5,

	/*
	 * 'bufferLength' specifies the video buffer length in seconds. This is used
	 * after the playback has started with the initial buffer length. You should
	 * use an arbitrary large value here to ensure stable playback.
	 * (optional, defaults to 10 seconds)
	 * 
	 * see also: startingBufferLength
	 */
	bufferLength: 20,

	/*
	 * 'loop' defines whether the playback should loop to the first clip after
	 * all clips in the playlist have been shown. It is used as the
	 * default state of the toggle button that controls looping. (optional,
	 * defaults to true)
	 */
	loop: true,
	
	/*
	 * Specifies the height to be allocated for the video display. This is the
	 * maximum height available for the different resizing options.
	 *
	 * Note also that the height of the playlist widget is adjusted so that 
	 * it will show complete rows. The list widget will not have a weight that
	 * would make it to show only half of the height of a clip's name. This 
	 * adjustment may result in some empty space at the bottom of the coponent's
	 * allocated area. This empty space can be removed by adjusting the allocated
	 * size (changing the value of object tag's height attribute).
	 *
	 */
	videoHeight: 320,
	
	/*
	 * Specifies how the video is scaled initially. This can be then changed by
	 * the user through the menu. (optional, defaults to 'fit')
	 * Possible values:
	 * 'fit'   Fit to window by preserving the aspect ratios encoded in the FLV metadata.
	 *         This is the default behavior.
	 * 'half'  Half size (preserves aspect ratios)
	 * 'orig'  Use the dimensions encoded in FLV. If the video is too big for the 
	 *         available space the video is scaled as if using the 'fit' option.
	 * 'scale' Scale the video to fill all available space for the video. Ignores
	 *         the dimensions in metadata.
	 * 
	 */
	initialScale: 'fit',
	
	/*
	 * 'hideControls' if set to true, hides all buttons and the progress bar
	 * leaving only the video showing (optional, defaults to false)
	 */
	hideControls: false,

	/*
	 * URL that specifies a base URL that points to a folder containing
	 * images used to skin the player. You must specify this if you intend
	 * to load external button images (see 'loadButtonImages' below).
	 */
	skinImagesBaseURL: 'http://flowplayer.sourceforge.net/resources'

	/*
	 * Will button images be loaded from external files, or will images embedded
	 * in the player SWF component be used? Set this to false if you want to "skin"
	 * the buttons. Optional, defaults to true.
	 * 
	 * NOTE: If you set this to false, you need to have the skin images available
	 * on the server! Otherwise the player will not show up at all or will show
	 * up corrupted.
	 *
	 * See also: 'skinImagesBaseURL' that affects this variable
	 */
//	useEmbeddedButtonImages: false,
	
	/*
	 * Optional logo image file. Specify this variable if you want to include
	 * a logo image on the right side of the progress bar. 'skinImagesBaseURL'
	 * will be prefixed to the URL used in loading.
	 * 
	 * NOTE: If you set a value for this, you need to have the logo file available
	 * on the server! Otherwise the player will not show up at all or will show
	 * up corrupted.
	 *
	 * See also: 'skinImagesBaseURL' that affects this variable
	 */
//	logoFile: 'Logo.jpg',
	
	/*
	 * 'splashImageFile' specifies an image file to be used as a splash image.
	 * This is useful if 'autoPlay' is set to false and you want to show a
	 * welcome image before the video is played. Should be in JPG format. The
	 * value of 'baseURL' is used similarily as with the video file name and
	 * therefore the video and the image files should be placed in the Web
	 * server next to each other.
	 * 
	 * NOTE: If you set a value for this, you need to have the splash image available
	 * on the server! Otherwise the player will not show up at all or will show
	 * up corrupted.
	 *
	 * NOTE2: You can also specify the splash in a playlist. This is just
	 * an alternative way of doing it. It was preserved for backward compatibility.
	 *
	 * See also: 'skinImagesBaseURL' that affects this variable
	 */
//	splashImageFile: 'main_clickToPlay.jpg',
	
	/*
	 * Should the splash image be scaled to fit the entire video area? If false,
	 * the image will be centered. Optional, defaults to false.
	 */
//	scaleSplash: false,

	/*
	 * 'progressBarColor1' defines the color of the progress bar at the bottom
	 * and top edges. Specified in hexadecimal triplet form indicating the RGB
	 * color component values. (optional, defaults to light gray: 0xAAAAAA)
	 */
//	progressBarColor1: 0xFFFFFF,


	/*
	 * 'progressBarColor2' defines the color in the middle of the progress bar.
	 * The value of this and 'progressBarColor1' variables define the gradient
	 * color fill of the progress bar. (optional, defaults to dark gray: 0x555555)
	 */
//	progressBarColor2: 0xDDFFDD,

	/*
	 * 'bufferBarColor1' defines the color of the buffer size indicator bar at the bottom
	 * and top edges. (optional, defaults to 0xAAAAAA)
	 */
//	bufferBarColor1: 0xFFFFFF,


	/*
	 * 'bufferBarColor2' defines the color of the buffer size indicator bar in the middle
	 * of the bar. (optional, defaults to 0xDDDDDD)
	 */
//	bufferBarColor2: 0xDDFFDD,

	/*
	 * 'progressBarBorderColor1' defines the color of the progress bar's border at the bottom
	 * and top edges. (optional, defaults to 0xAAAAAA)
	 */
//	progressBarBorderColor1: 0xDDDDDD,


	/*
	 * 'progressBarBorderColor2' defines the color of the progress bar's border in the middle
	 * of the bar. (optional, defaults to 0xDDDDDD)
	 */
//	progressBarBorderColor2: 0xEEEEEE

}

