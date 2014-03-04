This module provides a basic wall functionality for Chisimba. It enables
users, the site, and contexts (courses) to maintain a wall similar to the old
Facebook wall. It allows users to post to site, personal, and context walls,
and integrates with OEMBED so that links to OEMBED compatible sites are
recognised and parsed.

To use in code:

        $objWallOps = $this->getObject('wallops', 'wall');
        echo $objWallOps->showWall($wallType, $numOfPostsToDisplay);

To use in JSON templates

        {
            "display" : "block",
            "module" : "wall",
            "block" : "userwall"
        }

or

        {
            "display" : "block",
            "module" : "wall",
            "block" : "sitewall"
        }

or

        {
            "display" : "block",
            "module" : "wall",
            "block" : "contextwall"
        }

To use as a normal wideblock:
Blocks are available for modules that can display dynamic blocks, so - for
example - to add a wall to a course, simply add the contextwall block to the
course to have a course wall.

To try it out, install the wall module (it comes with some test data at this
stage, this will be removed when it is more tested), and browse to
http://localhost/ch/index.php?module=wall (or select it from the Developer
dropdown menu). This will give you the site wall. To test out your personal
wall, open
http://localhost/ch/index.php?module=wall&walltype=personal
Also use it as a wideblock.

You can also now do
http://localhost/ch/index.php?module=wall&walltype=personal&username=username
or
http://localhost/ch/index.php?module=wall&walltype=personal&userid=userid

REQUIREMENTS:
- You must update the canvas skin in the core skins
- You must update the security module for the user images to work correctly
- Only has correct layout in version 3 skins (canvases) derived from the canvas
  skin. If you want to use it with older skin versions, you must copy the CSS
  styles from the canvasbase.css in the canvas skin (they are all at the end)

KNOWN ISSUES:

- It is not yet tested very much in context (though it seems to work). You can
  use it as a wide context block (with 'Turn editing on').
- There is no security, I want to add something to prevent spam. Should your wall
  only be visible to buddies / classmates? If so, is anyone working on making
  buddies a proper social module?

