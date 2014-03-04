<?php
/* ----------- data class extends dbTable for tbl_forum_temp_attachment------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Temporary Event Attachments Table
* This class stores records of attachments for events whilst the event is being added
* @author Tohir Solomons
* @copyright (c) 2005 University of the Western Cape
* @package calendarbase
* @version 1
*/
class dbtempattachments extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_calendar_temp_attachment');
    }

    /**
    * Method to get a list of attachment with all details
    *
    * @param string $id Temporary Post Id
    * @return array List of Attachments
    */
    public function getList($id)
    {
        $sql = 'SELECT tbl_calendar_temp_attachment.id AS attachment_id,
        tbl_filestore.* FROM tbl_calendar_temp_attachment
        INNER JOIN tbl_filestore ON (tbl_calendar_temp_attachment.attachment_id = tbl_filestore.fileId)
        WHERE tbl_calendar_temp_attachment.temp_id = "'.$id.'"';

        return $this->getArray($sql);
    }

	public function getTransferList($id)
	{
		return $this->getAll('WHERE tbl_calendar_temp_attachment.temp_id = "'.$id.'"');
	}

    /**
    * Method to get a list of attachment record by providing the temporary post id
    *
    * @param string $id Temporary Post Id
    * @return array List of Attachments
    */
    public function getQuickList($id)
    {
        $sql = ' WHERE temp_id="'.$id.'"';

        return $this->getAll($sql);
    }

    /**
    * Method to link an event to an attachment
    *
    * @param string $temp_id: Temporary Id of the post before it is being posted
    * @param string $attachment_id: File Id of attachment
    * @param string $userId: User Id of the person
    * @param string $dateLastUpdated: Date attachment is added
    */
    public function insertSingle($temp_id, $attachment_id, $userId)
    {
        $this->insert(array(
                'temp_id' => $temp_id,
                'attachment_id' => $attachment_id,
                'userId' => $userId,
                'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())));
    }

    /**
    * Method to delete the temporary records for attachments
    *
    * @param string $id Temporary Post Id
    */
    public function deleteTemps($id)
    {
        return $this->delete('temp_id', $id);
    }

	public function deleteAttachment($attachment_id, $event_id)
	{
		$files = $this->getAll(' WHERE id="'.$attachment_id.'" AND temp_id="'.$event_id.'" ');

		if (count($files) > 0) {
			return $this->delete('id', $attachment_id);
		} else {
			return FALSE;
		}
	}
} #end of class
?>