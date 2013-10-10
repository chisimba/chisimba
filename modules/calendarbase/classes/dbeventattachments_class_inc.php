<?php
/* ----------- data class extends dbTable for tbl_forum_attachments------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Calendar Attachments Table
* This class controls all functionality relating to the tbl_calendar_event_attachment table
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package calendarbase
* @version 1
*/
class dbeventattachments extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_calendar_event_attachment');
    }


    /**
    * Insert an attachment into into the database
    *
    * @param string $fileId:                     File Id of the file in tbl_filestore
    * @param string $userId:                   User Id of the person uploading the file
    *
    * @return string Last Insert Id
    */
    public function insertSingle($fileId, $event, $userId)
    {
        $this->insert(array(
                'attachment_id' => $fileId,
				'event_id' => $event,
                'userId' => $userId,
                'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', mktime()))
		);

        return $this->getLastInsertId();
    }

    /**
    * Gets the list of attachments for a post
    *
    * @param string $forum: Forum Record ID
    * @param string $tempPost: Id of temporary post - post that is being written but has been sent yet
    *
    * @return array List of Attachments
    */
    public function getListAttachments($id)
    {
        $sql = 'SELECT tbl_calendar_event_attachment.id AS attachment_id,
        tbl_filestore.* FROM tbl_calendar_event_attachment
        INNER JOIN tbl_filestore ON (tbl_calendar_event_attachment.attachment_id = tbl_filestore.fileId)
        WHERE tbl_calendar_event_attachment.event_id = "'.$id.'"';

        return $this->getArray($sql);
    }

	public function getFile($attachment_id, $event_id)
	{
		$files = $this->getAll(' WHERE id="'.$attachment_id.'" AND event_id="'.$event_id.'" ');

		if (count($files) == 0) {
			return FALSE;
		} else {
			return $files[0];
		}
	}

	public function deleteAttachment($attachment_id, $event_id)
	{
		$files = $this->getAll(' WHERE id="'.$attachment_id.'" AND event_id="'.$event_id.'" ');

		if (count($files) > 0) {
			return $this->delete('id', $attachment_id);
		} else {
			return FALSE;
		}
	}

} #end of class
?>