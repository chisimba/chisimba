<?php


/**
* HTML_TreeNode class
*
* This class is supplementary to the above and provides a way to
* add nodes to the tree. A node can have other nodes added to it.
*
* @author  Richard Heyes <richard@php.net>
* @author  Harald Radi <harald.radi@nme.at>
* @access  public
* @package HTML_TreeMenu
*/
class treenode
{
    /**
    * The text for this node.
    * @var string
    */
    var $text;

    /**
    * The value for this node.
    * @var string
    */
    var $value;

    /**
    * The link for this node.
    * @var string
    */
    var $link;

    /**
    * The icon for this node.
    * @var string
    */
    var $icon;

    /**
    * The icon to show when expanded for this node.
    * @var string
    */
    var $expandedIcon;

    /**
    * The css class for this node
    * @var string
    */
    var $cssClass;

    /**
    * The link target for this node
    * @var string
    */
    var $linkTarget;

    /**
    * Indexed array of subnodes
    * @var array
    */
    var $items;

    /**
    * Whether this node is expanded or not
    * @var bool
    */
    var $expanded;

    /**
    * Whether this node is dynamic or not
    * @var bool
    */
    var $isDynamic;

    /**
    * Should this node be made visible?
    * @var bool
    */
    var $ensureVisible;

    /**
    * The parent node. Null if top level
    * @var object
    */
    var $parent;

    /**
    * Javascript event handlers;
    * @var array
    */
    var $events;

    /**
    * Constructor
    *
    * @access public
    * @param  array $options An array of options which you can pass to change
    *                        the way this node looks/acts. This can consist of:
    *                         o text          The title of the node, defaults to blank
    *                         o link          The link for the node, defaults to blank
    *                         o icon          The icon for the node, defaults to blank
    *                         o expandedIcon  The icon to show when the node is expanded
    *                         o cssClass      The CSS class for this node, defaults to blank
    *                         o expanded      The default expanded status of this node, defaults to false
    *                                         This doesn't affect non dynamic presentation types
    *                         o linkTarget    Target for the links. Defaults to linkTarget of the
    *                                         HTML_TreeMenu_Presentation.
    *                         o isDynamic     If this node is dynamic or not. Only affects
    *                                         certain presentation types.
    *                         o ensureVisible If true this node will be made visible despite the expanded
    *                                         settings, and client side persistence. Will not affect
    *                                         some presentation styles, such as Listbox. Default is false
    * @param  array $events An array of javascript events and the corresponding event handlers.
    *                       Additionally to the standard javascript events you can specify handlers
    *                       for the 'onexpand', 'oncollapse' and 'ontoggle' events which will be fired
    *                       whenever a node is collapsed and/or expanded.
    */
    function treenode($options = array(), $events = array())
    {
        $this->text          = '';
        $this->link          = '';
        $this->value         = '';
        $this->icon          = '';
        $this->expandedIcon  = '';
        $this->cssClass      = '';
        $this->expanded      = false;
        $this->isDynamic     = true;
        $this->ensureVisible = false;
        $this->linkTarget    = null;

        $this->parent        = null;
        $this->events        = $events;

        foreach ($options as $option => $value) {
            $this->$option = $value;
        }
    }

    /**
    * Allows setting of various parameters after the initial
    * constructor call. Possible options you can set are:
    *  o text
    *  o link
    *  o icon
    *  o cssClass
    *  o expanded
    *  o isDynamic
    *  o ensureVisible
    * ie The same options as in the constructor
    *
    * @access public
    * @param  string $option Option to set
    * @param  string $value  Value to set the option to
    */
    function setOption($option, $value)
    {
        $this->$option = $value;
    }

    /**
    * Adds a new subnode to this node.
    *
    * @access public
    * @param  object $node The new node
    */
    function &addItem(&$node)
    {
        $node->parent  = &$this;
        $this->items[] = &$node;

        /**
        * If the subnode has ensureVisible set it needs
        * to be handled, and all parents set accordingly.
        */
        if ($node->ensureVisible) {
            $this->_ensureVisible();
        }

        return $this->items[count($this->items) - 1];
    }

    /**
    * Private function to handle ensureVisible stuff
    *
    * @access private
    */
    function _ensureVisible()
    {
        $this->ensureVisible = true;
        $this->expanded      = true;

        if (!is_null($this->parent)) {
            $this->parent->_ensureVisible();
        }
    }
} // HTML_TreeNode

?>