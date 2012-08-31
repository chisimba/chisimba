<?php


/**
* DHTML class
*
* This class is a presentation class for the tree structure
* created using the TreeMenu/TreeNode. It presents the
* traditional tree, static for browsers that can't handle
* the DHTML.
*/
require_once('presentation_class_inc.php');
class dhtml extends presentation
{
    /**
    * Dynamic status of the treemenu. If true (default) this has no effect. If
    * false it will override all dynamic status vars and set the menu to be
    * fully expanded an non-dynamic.
    */
    var $isDynamic;

    /**
    * Path to the images
    * @var string
    */
    var $images;

    /**
    * Target for the links generated
    * @var string
    */
    var $linkTarget;

    /**
    * Whether to use clientside persistence or not
    * @var bool
    */
    var $userPersistence;

    /**
    * The default CSS class for the nodes
    */
    var $defaultClass;

    /**
    * Whether to skip first level branch images
    * @var bool
    */
    var $noTopLevelImages;

    /**
    * Constructor, takes the tree structure as
    * an argument and an array of options which
    * can consist of:
    *  o images            -  The path to the images folder. Defaults to "images"
    *  o linkTarget        -  The target for the link. Defaults to "_self"
    *  o defaultClass      -  The default CSS class to apply to a node. Default is none.
    *  o usePersistence    -  Whether to use clientside persistence. This persistence
    *                         is achieved using cookies. Default is true.
    *  o noTopLevelImages  -  Whether to skip displaying the first level of images if
    *                         there is multiple top level branches.
    *  o maxDepth          -  The maximum depth of indentation. Useful for ensuring
    *                         deeply nested trees don't go way off to the right of your
    *                         page etc. Defaults to no limit.
    *
    * And also a boolean for whether the entire tree is dynamic or not.
    * This overrides any perNode dynamic settings.
    *
    * @param object $structure The menu structure
    * @param array  $options   Array of options
    * @param bool   $isDynamic Whether the tree is dynamic or not
    */
    function dhtml(&$structure, $options = array(), $isDynamic = true)
    {
        $this->presentation($structure);
        $this->isDynamic = $isDynamic;

        // Defaults
        $this->images           = 'images';
        $this->maxDepth         = 0;        // No limit
        $this->linkTarget       = '_self';
        $this->defaultClass     = '';
        $this->usePersistence   = true;
        $this->noTopLevelImages = false;

        foreach ($options as $option => $value) {
            $this->$option = $value;
        }
    }

    /**
    * Returns the HTML for the menu. This method can be
    * used instead of printMenu() to use the menu system
    * with a template system.
    *
    * @access public
    * @return string The HTML for the menu
    */
    function toHTML()
    {
        static $count = 0;
        $menuObj     = 'objTreeMenu_' . ++$count;

        $html  = "\n";
        $html .= '<script language="javascript" type="text/javascript">' . "\n\t";
        $html .= sprintf('%s = new TreeMenu("%s", "%s", "%s", "%s", %s, %s);',
                         $menuObj,
                         $this->images,
                         $menuObj,
                         $this->linkTarget,
                         $this->defaultClass,
                         $this->usePersistence ? 'true' : 'false',
                         $this->noTopLevelImages ? 'true' : 'false');

        $html .= "\n";

        /**
        * Loop through subnodes
        */
        if (isset($this->menu->items)) {
            for ($i=0; $i<count($this->menu->items); $i++) {
                $html .= $this->_nodeToHTML($this->menu->items[$i], $menuObj);
            }
        }

        $html .= sprintf("\n\t%s.drawMenu();", $menuObj);
        $html .= sprintf("\n\t%s.writeOutput();", $menuObj);

        if ($this->usePersistence && $this->isDynamic) {
            $html .= sprintf("\n\t%s.resetBranches();", $menuObj);
        }
        $html .= "\n</script>";

        return $html;
    }

    /**
    * Prints a node of the menu
    *
    * @access private
    */
    function _nodeToHTML($nodeObj, $prefix, $return = 'newNode', $currentDepth = 0, $maxDepthPrefix = null)
    {
        $prefix = empty($maxDepthPrefix) ? $prefix : $maxDepthPrefix;
        
        $expanded  = $this->isDynamic ? ($nodeObj->expanded  ? 'true' : 'false') : 'true';
        $isDynamic = $this->isDynamic ? ($nodeObj->isDynamic ? 'true' : 'false') : 'false';
        $html = sprintf("\t %s = %s.addItem(new TreeNode('%s', %s, %s, %s, %s, '%s', '%s', %s));\n",
                        $return,
                        $prefix,
                        str_replace("'", "\\'", $nodeObj->text),
                        !empty($nodeObj->icon) ? "'" . $nodeObj->icon . "'" : 'null',
                        !empty($nodeObj->link) ? "'" . $nodeObj->link . "'" : 'null',
                        $expanded,
                        $isDynamic,
                        $nodeObj->cssClass,
                        $nodeObj->linkTarget,
                        !empty($nodeObj->expandedIcon) ? "'" . $nodeObj->expandedIcon . "'" : 'null');

        foreach ($nodeObj->events as $event => $handler) {
            $html .= sprintf("\t %s.setEvent('%s', '%s');\n",
                             $return,
                             $event,
                             str_replace(array("\r", "\n", "'"), array('\r', '\n', "\'"), $handler));
        }

        if ($this->maxDepth > 0 AND $currentDepth == $this->maxDepth) {
            $maxDepthPrefix = $prefix;
        }

        /**
        * Loop through subnodes
        */
        if (!empty($nodeObj->items)) {
            for ($i=0; $i<count($nodeObj->items); $i++) {
                $html .= $this->_nodeToHTML($nodeObj->items[$i], $return, $return . '_' . ($i + 1), $currentDepth + 1, $maxDepthPrefix);
            }
        }

        return $html;
    }
} // End class HTML_TreeMenu_DHTML





?>