<?php
/* ----------- data class extends dbTable for tbl_discussion_post_attachment------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Post Attachments Table
* This class controls all functionality relating to the tbl_discussion_post_attachment table and gets attachments for posts
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class dbpostattachments extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_discussion_post_attachment');
        $this->objFileRegister =& $this->getObject('registerfileusage', 'filemanager');
    }

    /**
    * Link attachment to a post
    *
    * @param string $post_id: Record ID of the Post
    * @param string $attachment_id: Attachment Id for the file
    * @param string $userId: User Id of the person 
    * @param string $dateLastUpdated: Date attachment is added
    */
    function insertSingle($post_id, $attachment_id, $userId, $dateLastUpdated)
    {
        $recordId = $this->insert(array(
                'post_id' => $post_id,
                'attachment_id' => $attachment_id,
                'userid' => $userId,
                'datecreated' => strftime('%Y-%m-%d %H:%M:%S', $dateLastUpdated)));
        $this->objFileRegister->registerUse($attachment_id, 'discussion', 'tbl_discussion_post_attachment', $recordId, 'attachment_id');
    }
    
    /**
    *  Method to get the list of attachments for a post
    *
    * @param string $post_id: the id for the post
    * @return array List of attachments
    */
    function getAttachments($post_id)
    {
        $sql = 'SELECT tbl_discussion_post_attachment.id AS id, filename, attachment_id FROM tbl_discussion_post_attachment 
        INNER JOIN tbl_files ON (attachment_id = tbl_files.id)
        WHERE tbl_discussion_post_attachment.post_id="'.$post_id.'"';
        
        return $this->getArray($sql);
    }
    
    /**
    *  Method to download an attachment
    * @param string $attachment_id: the id for the attachment
    * @return array attachment details
    */
    function downloadAttachment($attachment_id)
    {
        $sql = 'SELECT tbl_files.id, filename FROM tbl_discussion_post_attachment 
        INNER JOIN tbl_files ON (attachment_id = tbl_files.id)
        WHERE tbl_discussion_post_attachment.id="'.$attachment_id.'"';
        
        return $this->getArray($sql); 
    }
    
    /**
    *  Method to delete all attachments to a post
    * @param string $postId Record Id of the Post
    */
    function deleteAttachments($postId)
    {
        return $this->delete('post_id', $postId);
    }


} #end of class
?>
