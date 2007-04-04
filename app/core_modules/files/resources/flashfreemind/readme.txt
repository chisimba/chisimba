Just a personal freemind flash browser v.0.97.
It does only load jpg's images (limitation of flash), in this versión, if you have flash8
will load png and gif.

 it will improve with time.
 
 For the easy of development flashout (http://www.potapenko.com/flashout/) have
 been used with Eclipse.
 
The Sourcecode for this project can be found at: http://freemind.cvs.sourceforge.net/freemind/flash/

USE:
 - insert in any browser page like in the example.

 CONFIGURATION:
	All this variables can be added in the script. None of then if needed, they all
	have default values.

	//Where to open a link: 
	//default="_self"
		fo.addVariable("openUrl", "_self");

	// for changing the WordWrap size
		fo.addVariable("defaultWordWrap","300"); //default 600

	// for changing to old elipseNode edges
		fo.addVariable("noElipseMode","anyValue");

	// IF we want to initiate de freemind with al the nodes collapset from this level
	// =default "-1" that means, do nothing
		fo.addVariable("startCollapsedToLevel","1");

	// Initial mindmap to load
	// default="index.mm"
		fo.addVariable("initLoadFile", "index.mm");

	// To create de main node with a diferent shape.	// default="elipse "
		fo.addVariable("mainNodeShape", "rectangle");

	//set max width of a text node
	// default="600"
		fo.addVariable("defaultWordWrap", "600");

	//width of  snapshots
	// default="600"
		fo.addVariable("ShotsWidth", "600");

	//generate snapshots for all the mindmaps reachable from throught the main mindmap
	// default="false"
		fo.addVariable("genAllShots", "true");

	//for every mindmap loaded start the visualization with all the nodes unfolded
	// default="false"
		fo.addVariable("unfoldAll", "true");



 
CONFIGURATION OLD MODE:
	 		
	For iexplorer
	 <param name="FlashVars" value="initLoadFile=index.mm"/>
	For others
	 <embed FlashVars="initLoadFile=index.mm" 