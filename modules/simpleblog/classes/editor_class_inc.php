<?php
/**
 *
 * Editor class for Simple Blog
 *
 * Editor class for Simple Blog which builds the edit interface for
 * blog posts
 *
 * PHP version 5
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
 * @category  Chisimba
 * @package   simpleblog
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Editor class for Simple Blog
 *
 * Editor class for Simple Blog which builds the edit interface for
 * blog posts
*
* @package   simpleblog
* @author    Derek Keats <derek@dkeats.com>
*
*/
class editor extends object
{

    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
    *
    * Intialiser for the simpleblog ops class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
        // Set the jQuery version to the latest functional.
        $this->setVar('JQUERY_VERSION', '1.4.2');
        // Load the functions specific to this module.
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('simpleblog.js', 'simpleblog'));
        // Get the blog posts db.
        $this->objDbPosts = $this->getObject('dbsimpleblog', 'simpleblog');

    }

    /**
     *
     * Load the form elements that needed to build the editor
     * @access private
     * @return VOID
     *
     */
    public function loadEditElements()
    {
        $this->loadClass('form','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('radio','htmlelements');
        $this->loadClass ('hiddeninput', 'htmlelements');
    }

    /**
     *
     * Get the edit form for adding and editing posts after first checking
     * that the user has rights
     * 
     * @param string $id The id of the post to edit
     * @return string The edit form
     * @access public
     *  
     */
    public function editForm()
    {
        $objSec = $this->getObject('simpleblogsecurity', 'simpleblog');
        $userId = $this->objUser->userId();
        $mode = $this->getParam('mode', 'add');
        if ($mode == 'edit') {
            $id = $this->getParam('postid', FALSE);
            if ($id) {
                $ar = $this->objDbPosts->getForEdit($id);
                $title = trim($ar['post_title']);
                $content = trim($ar['post_content']);
                $status = $ar['post_status'];
                $blogId = $ar['blogid'];
                $bloggerId = $ar['userid'];
                $blogType = $ar['post_type'];
                if ($objSec->checkRights($bloggerId, $userId, $blogType)) {
                    return $this->buildEditForm($id, $ar);
                } else {
                    return $this->noRights();
                }
            }
        } elseif ($mode == 'add') {
            if ($objSec->checkBloggingRights()) {
                return $this->buildEditForm(FALSE, FALSE);
            } else {
                return $this->noRights();
            }
        } else {
            return $this->noRights();
        }
    }
    
    private function noRights()
    {
        return '<div class="error">' 
            . $this->objLanguage->languageText("mod_simpleblog_norights",
            "simpleblog",
            "You do not have rights to edit or create posts in this blog.")
            . '</div>';
    } 
    
    /**
     *
     * Build the edit form for adding and editing posts  
     * 
     * @return string The edit form
     * @access private
     *  
     */
    private function buildEditForm($id, $ar)
    {
        // Set up empty values so we can use same form for add and edit
        $title = '';
        $content = '';
        $status = 'posted';
        $postTags ='';
        $postType = 'personal';
        $mode = $this->getParam('mode', 'add');
        if ($mode == 'edit') {
            if ($id) {
                $title = trim($ar['post_title']);
                $content = trim($ar['post_content']);
                $status = $ar['post_status'];
                $blogId = $ar['blogid'];
                $postType = $ar['post_type'];
                $postTags = $ar['post_tags'];
            }
        } else {
            $objGuesser = $this->getObject('guesser', 'simpleblog');
            $blogId =  $objGuesser->guessBlogId();
            if ($blogId == 'site') {
                $postType = 'site';
            }
        }
        
        // Load the form elements.
        $this->loadEditElements();

        //Set up the form action URL
        $paramArray=array(
            'action'=>'savepost',
            'mode'=>$mode);
        $formAction=$this->uri($paramArray, 'simpleblog');

        //Create the form class
        $objForm = new form('simpleblog_editor');
        $objForm->setAction($formAction);
        $objForm->displayType=3;

        // The blog title form element unlabled.
        $objBlogTitle = new textinput('post_title', $title);
        $objBlogTitle->id='post_title';
        $titleLabel = $this->objLanguage->languageText("mod_simpleblog_posttitle",
            "simpleblog", "The title of this post");
        $postTitleFormElement = $titleLabel . ":<br />" . $objBlogTitle->show();


        // Whether it is published or draft
        $objRadioElement = new radio('post_status');
        $objRadioElement->addOption('posted',
          $this->objLanguage->languageText("mod_simpleblog_posted",
          "simpleblog", "posted"));
        $objRadioElement->addOption('draft',
          $this->objLanguage->languageText("mod_simpleblog_draft",
          "simpleblog", "draft"));
        $objRadioElement->setSelected($status);
        $statusLabel =  $this->objLanguage->languageText("mod_simpleblog_statuslabel",
            "simpleblog", "Indicate the publication status of this post");
        $statusFormElement = $statusLabel . ": " . $objRadioElement->show();
        
        // Whether it is personal, site, or context.
        $typeLabel =  $this->objLanguage->languageText("mod_simpleblog_typelabel",
            "simpleblog", "Indicate the blog type for this post");
        $objRadioElement = new radio('post_type');
        $objRadioElement->addOption('personal',
          $this->objLanguage->languageText("mod_simpleblog_personal",
          "simpleblog", "My personal blog"));
        $objRadioElement->addOption('site',
          $this->objLanguage->languageText("mod_simpleblog_site",
          "simpleblog", "Site blog"));
        // If they are in a context, then display the context blog
        $objContext = $this->getObject('dbcontext', 'context');
        if($objContext->isInContext()){
            $objRadioElement->addOption('context',
            ucfirst($this->objLanguage->code2Txt("mod_simpleblog_context",
              "simpleblog", NULL, "[-CONTEXT-] blog")));
        }
        $objRadioElement->setSelected($postType);
        $typeFormElement = $typeLabel . ": " . $objRadioElement->show();
        
        
        // The blog content editor unlabled.
        $editor = $this->newObject('htmlarea', 'htmlelements');
        $editor->name = 'post_content';
        $editor->setContent($content);
        $contentFormElement = $editor->show();
        
        // Tags.
        $objTags = new textinput('post_tags', $postTags);
        $objTags->id='post_tags';
        $tagsLabel = $this->objLanguage->languageText("mod_simpleblog_tags",
            "simpleblog", "Tags for this post");
        $tagsFormElement = $tagsLabel . ":<br />" . $objTags->show();

        // Put the blogid in a hidden input
        $objHidden =  new hiddeninput ( 'blogid', $blogId );
        $objHidden->extra = ' id=\'blogid\'';
        $blogidFormElement = $objHidden->show();

        // Put the id in a hidden input
        if (isset($id)) {
            $objHidden =  new hiddeninput ( 'id', $id );
            $objHidden->extra = ' id=\'id\'';
            $postIdFormElement = $objHidden->show();
        } else {
            $postIdFormElement = NULL;
        }

        //Add a save button
        $objButton = $this->newObject('button', 'htmlelements');
        $objButton->setIconClass("save");
        $objButton->button('save',$this->objLanguage->languageText('word_save'));
        $objButton->setToSubmit();
        $saveFormElement = $objButton->show();

        // Build the form
        $objForm->addToForm( $blogidFormElement . $postIdFormElement
          . "<div class='title_form_element'>" . $postTitleFormElement . "</div>"
          . "<div class='status_form_element'>" . $statusFormElement . "</div>"
          . "<div class='type_form_element'>" . $typeFormElement . "</div>"
          . "<div class='content_form_element'>" . $contentFormElement . "</div>"
          . "<div class='tags_form_element'>" . $tagsFormElement . "</div>"
          . "<div class='savebutton_form_element'>" . $saveFormElement . "</div>");
        return $objForm->show();
    }

}
?>