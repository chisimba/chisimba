<?php

/* ----------- data class extends dbTable------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/*!  \class dbhosportal_messages
*
*  \brief Class that models the hosportal message database.
*  \brief It basically is an interface class between the hosportal module and the hosportal message database.
*  \brief This class provides functions to insert, sort, edit and delete entries in the database.
*  \author Salman Noor
*  \author MIU Intern
*  \author School of Electrical Engineering, WITS Unversity
*  \version 0.68
*  \date    May 3, 2010
* \warning This class's parent is dbTable which is a chisimba core class.
* \warning If the dbTable class is altered. This class may not work.
* \warning Apart from normal PHP. This class uses the mysql language to provide
* actual functionality. This language is encapsulated with in "double quotes" in a string format.
*/

class dbhosportal_messages extends dbTable {

    /*!
* \brief Private data member of class dbhosportal_messages that stores an object of another class.
* \brief This class is composed of one object from the user_module class in the utilities module of chisimba.
* \brief This object provides options to manipulate and utilize user data.
    */
    private $objUser;

    /*!
* \brief Private data member of class dbhosportal_messages that stores an object of another class.
* \brief This class is composed of one object from the dbhosportal_original_messages class.
* \brief Similiar to this class, this object provides the same functionality but for a different database
* called tbl_hosportal_original_messages.
    */
    private $objDBOriginalComments;

    /*!
* \brief Private data member of class dbhosportal_messages that stores an object of another class.
* \brief This class is composed of one object from the dbhosportal_replies class.
* \brief Similiar to this class, this object provides the similiar functionality but for a different database
* called tbl_hosportal_replies.
    */
    private $objDBReplies;

    /**
     *\brief Constructor that set up this class.
     * \brief It defines the table tbl_hosportal_messages and creates private
     * data members from other classes for this class to use.
     *
     */
    function init() {
///Define the table tbl_hosportal_messages. This can only be done if this class
///is inherited by dbTable.
        parent::init('tbl_hosportal_messages');

///Instatiate an object user from the class user_module.
        $this->objUser = $this->getObject('user_module','hosportal');
///Instatiate an object DBOriginalComments from the class dbhosportal_original_messages.
        $this->objDBOriginalComments = $this->getObject('dbhosportal_original_messages','hosportal');
///Instatiate an object DBReplies from the class dbhosportal_replies.
        $this->objDBReplies = $this->getObject('dbhosportal_replies','hosportal');
    }

    /**
     *\brief Memeber function that returns all entries in the tbl_hosportal_messages database.
     * \return A array with all the entries stored.
     */
    function listAll() {
///Return an array.
        return $this->getAll();
///Notice that this funcion is part of the dbTable parent class.

    }

    /**
     *\brief Member function to search the entire database for a certain comment and return it.
     * \param comments A string. The comment could be any small part of the entire comment.
     * Examples choosing the word "can" in a comment that contains  the word "canister".
     * The modulus signs % allows you to do this.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return A string that contains the entire comment.
     */
    function listComment($comments) {
        return $this->getAll("WHERE commenttxt LIKE '%" . $comments . "%'");
    }

    /**
     *\brief Member function to sort all entries by latest modified and return that result as an array.
     * \return An array with the all messages sorted by latest modified.
     */
    function sortByLatestModified() {
///Select all or * entries from the table hosportal messages and order them by the field modified
///and store the result in a temporary variable. The language MYSQL is used to do this. It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by modified desc";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);

    }

    /**
     *\brief Member function to sort all entries by oldest modified and return that result as an array.
     * \return An array with the all messages sorted by oldest modified.
     */
    function sortByOldestModified() {
///Select all or * entries from the table hosportal messages and order them by the field modified
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by modified asc";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);

    }

    /**
     *\brief Member function to sort all entries by author A to Z and return that result as an array.
     * \return An array with the all messages sorted by by author A to Z
     */
    function sortByAuthorAtoZ() {
///Select all or * entries from the table hosportal messages and order them by the field author asc
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by userid asc";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);
    }

    /**
     *\brief Member function to sort all entries by author Z to A and return that result as an array.
     * \return An array with the all messages sorted by by author Z to A.
     */
    function sortByAuthorZtoA() {
///Select all or * entries from the table hosportal messages and order them by the field author in desc order
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by userid desc";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);
    }
    /**
     *\brief Member function to sort all entries by subject matter A to Z and return that result as an array.
     * \return An array with the all messages sorted by by subject matter A to Z.
     */
    function sortBySubjectMatterAtoZ() {
///Select all or * entries from the table hosportal messages and order them by the field title in asc order
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by title asc";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);
    }

    /**
     *\brief Memeber function to sort all entries by subject matter Z to A and return that result as an array.
     * \return An array with the all messages sorted by by subject matter Z to A.
     */
    function sortBySubjectMatterZtoA() {
///Select all or * entries from the table hosportal messages and order them by the field title in dessc order
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by title desc";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);
    }

    /**
     *\brief Member function to sort all entries by most replies and return that result as an array.
     * \return An array with the all messages sorted by by most replies.
     */
    function sortByMostReplies() {
///Select all or * entries from the table hosportal messages and order them by the field replies in desc order
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by replies desc";
        ///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);
    }
    /**
     *\brief Member function to sort all entries by least replies and return that result as an array.
     * \return An array with the all messages sorted by by least replies.
     */
    function sortByLeastReplies() {
///Select all or * entries from the table hosportal messages and order them by the field replies in asc order
///and store the result in a temporary variable. The language MYSQL is used to do this.It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_hosportal_messages order by replies asc";
        ///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);

    }


    /**
     *\brief Member function to return a single entry with a specific id.
     * \param id A string. This Id is generated by mysql and in unique for all entries.
     * the Id is assigned when you create an entry in the database.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \warning Make sure you pass the correct id for the entry you want.
     * \return A single entry with all its fields from the database.
     */
    function listSingle($id) {
        return $this->getAll("WHERE id='" . $id . "'");
    }

    /**
     *\brief Member function to insert a new entry into the messages database.
     * \param title A string. It is the subject matter of the entry.
     * \param comments A string. It is the comment of the entry.
     * \param unreplied A Boolean Value. It is to determine whether the entry has
     * been replied to or not.
     * \param noofreplies A Integer. how many replies this entry has.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return An automatically generated unique id for that new entry.
     */
    function insertSingle($title,$comments,$unreplied,$noofreplies) {
///Get the user's name that is inserting the entry.
        $userid = $this->objUser->getUserFullName();
///Insert the entry into the database with its relevant fields. If succesful,
///store the entries id into a temporary variable. The insert function is
///from the parent class dbTable.
        $id = $this->insert(array(
                'userid' => $userid,
                'title' => $title,
                'commenttxt' => $comments,
                'modified'=> $this->now(),
                'commenttxtshort'=> $comments,
                'unreplied'=> $unreplied,
                'replies' => $noofreplies
        ));

///return an automatically generated unique id for that new entry.
        return $id;
    }

    /**
     *\brief Member function to insert a new entry into the messages AND original messages database.
     * \param title A string. It is the subject matter of the entry.
     * \param comments A string. It is the comment of the entry.
     * \param unreplied A Boolean Value. It is to determine whether the entry has
     * been replied to or not.
     * \param noofreplies A Integer. how many replies this entry has.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return An automatically generated unique id for that new entry.
     */
    function insertSingleOriginalMessage($id,$title,$comments,$unreplied,$noofreplies) {

///Get the user's name that is inserting the entry.
        $userid = $this->objUser->getUserFullName();
///Insert the entry into the messages database with its relevant fields. If succesful,
///store the entries id into a temporary variable. The insert function is
///from the parent class dbTable.
        $id = $this->insert(array(
                'userid' => $userid,
                'title' => $title,
                'commenttxt' => $comments,
                'modified'=> $this->now(),
                'commenttxtshort'=> $comments,
                'unreplied'=> $unreplied,
                'replies' => $noofreplies
        ));
///Insert the entry into the original messages database with its relevant fields. If succesful,
///store the entries id into a temporary variable. The insert function is
///from the class dbhosportal_original_messages.
        $this->objDBOriginalComments->insertSingle($id,$title,$comments,$unreplied,$noofreplies);
///Notice the id values for the same entry that is stored in two different databases are the SAME.
        return $id;
    }
    /**
     *\brief Member function to insert a new reply entry into the messages AND replies database.
     * param id A string. This id is from the message the reply belongs to.
     * \param title A string. It is the subject matter of the entry. It will be the same
     * \param comments A string. It is the comment of the entry.
     * \param unreplied A Boolean Value. It is to determine whether the entry has
     * been replied to or not.
     * \param noofreplies A Integer. how many replies this entry has.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \return An automatically generated unique id for that new entry.
     */
    function insertSingleReply($id,$title,$comments,$unreplied,$noofreplies) {

///Get the user's name that is inserting the reply entry.
        $userid = $this->objUser->getUserFullName();
///Insert the entry into the messages database with its relevant fields. If succesful,
///store the entries id into a temporary variable. The insert function is
///from the parent class dbTable.
        $id = $this->insert(array(
                'userid' => $userid,
                'title' => $title,
                'commenttxt' => $comments,
                'modified'=> $this->now(),
                'commenttxtshort'=> $comments,
                'unreplied'=> $unreplied,
                'replies' => $noofreplies
        ));
///Insert the entry into the replies database with its relevant fields. If succesful,
///store the entries id into a temporary variable. The insert function is
///from the class dbhosportal_replies.
        $this->objDBReplies->insertSingle($id,$title,$comments,$unreplied,$noofreplies);
///Notice the id values for the same entry that is stored in two different databases are the SAME.
        return $id;
    }
    /**
     *\brief Member function to edit an existing entry in the messages database.
     * \param id A string. Unique caller id for the exsiting entry.
     * \param title A string. It is the edited or the same subject matter for the exisitng entry.
     * \param comments A string. It is the edited or the same comment for the exisitng entry.
     * \param unreplied A Boolean Value. It is to determine whether the entry has
     * been replied to or not.
     * \param noofreplies A Integer. How many replies this entry has.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \warning MAKE SURE you have the correct id as the wrong id will edit the wrong entry
     * \return The same id for that exisiting entry.
     */
    function updateSingle($id, $title, $comments,$unreplied,$noofreplies) {
///Get the user's name that is editing the exisitng entry.
        $userid = $this->objUser->getUserFullName();
///Update the entry into the database with its relevant fields. If succesful,
///store the entries id into a temporary variable. The update function is
///from the parent class dbTable.
        $data=array("userid"=>$userid,"title"=>$title,"commenttxt"=>$comments,"modified"=>$this->now(),"commenttxtshort"=>$comments,"unreplied"=>$unreplied,"replies" => $noofreplies );

        $result = $this->update('id',$id,$data);
///return the same id for that exisitng entry. The id does not change.
        return $result;
    }

    /**
     *\brief Member function to edit an existing entry in the messages AND original messages database.
     * \param id A string. Unique caller id for the exsiting entry.
     * \param title A string. It is the edited or the same subject matter for the exisitng entry.
     * \param comments A string. It is the edited or the same comment for the exisitng entry.
     * \param unreplied A Boolean Value. It is to determine whether the entry has
     * been replied to or not.
     * \param noofreplies A Integer. The update or same value of how many replies this entry has.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \warning MAKE SURE you have the correct id as the wrong id will edit the wrong entry
     * \return The same id for that exisiting entry.
     */
    function updateSingleOriginalMessage($id, $title, $comments,$unreplied,$noofreplies) {
///Get the user's name that is editing the exisitng entry.
        $userid = $this->objUser->getUserFullName();
///Update the entry into the database with its relevant fields. If successful,
///store the entries id into a temporary variable. The update function is
///from the parent class dbTable.
        $data=array("userid"=>$userid,"title"=>$title,"commenttxt"=>$comments,"modified"=>$this->now(),"commenttxtshort"=>$comments,"unreplied"=>$unreplied,"replies" => $noofreplies );
///Update the entry into the original messages database with its relevant fields. If succesful,
///store the entry's id into a temporary variable. The update function is
///from the class dbhosportal_original_messages.
        $this->objDBOriginalComments->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
        $result = $this->update('id',$id,$data);
///Notice the id values for the same entry that is update in two different databases are the SAME.
        return $result;
    }
    /**
     *\brief Member function to edit an existing entry in the messages AND replies database.
     * \param id A string. Unique caller id for the exsiting entry.
     * \param title A string. It is the edited or the same subject matter for the exisitng entry.
     * \param comments A string. It is the edited or the same comment for the exisitng entry.
     * \param unreplied A Boolean Value. It is to determine whether the entry has
     * been replied to or not.
     * \param noofreplies A Integer. The update or same value of how many replies this entry has.
     * \warning Do NOT pass variable types as parameters that are not specified in this member function.
     * \warning MAKE SURE you have the correct id as the wrong id will edit the wrong entry
     * \return The same id for that exisiting entry.
     */
    function updateSingleReply($id, $title, $comments,$unreplied,$noofreplies) {
///Get the user's name that is editing the exisitng entry.
        $userid = $this->objUser->getUserFullName();
///Update the entry into the database with its relevant fields. If successful,
///store the entries id into a temporary variable. The update function is
///from the parent class dbTable.
        $data=array("userid"=>$userid,"title"=>$title,"commenttxt"=>$comments,"modified"=>$this->now(),"commenttxtshort"=>$comments,"unreplied"=>$unreplied,"replies" => $noofreplies );
///Update the entry into the replies database with its relevant fields. If succesful,
///store the entry's id into a temporary variable. The update function is
///from the class dbhosportal_relies.
        $this->objDBReplies->updateSingle($id, $title, $comments,$unreplied,$noofreplies);

        $result = $this->update('id',$id,$data);
///Notice the id values for the same entry that is update in two different databases are the SAME.
        return $result;
    }
    /**
     *\brief Member function to delete an existing entry in the messages database.
     * \param id A string. Unique caller id for the exsiting entry.
     * \warning MAKE SURE you are passing the right id as the wrong id will
     * delete the wrong entry.
     */
    function deleteSingle($id) {
///Delete the entry which that specific id.
        $this->delete("id", $id);
///The delete function is from the parent class dbTable.
    }
    /**
     *\brief Member function to delete the same existing entry in the messages AND original messages database.
     * \param id A string. Unique caller id for the exsiting entry.
     * \warning MAKE SURE you are passing the right id as the wrong id will
     * delete the wrong entry.
     */
    function deleteSingleOriginalMessage($id) {
//Delete the entry into the original messages database. The delete function is
///from the class dbhosportal_original_messages.
        $this->objDBOriginalComments->deleteSingle($id);
///Delete the entry which that specific id in the messages database.
///The delete function is from the parent class dbTable.
        $this->delete("id", $id);
    }

    /**
     *\brief Member function to delete the same existing entry in the messages AND replies database.
     * \param id A string. Unique caller id for the exsiting entry.
     * \warning MAKE SURE you are passing the right id as the wrong id will
     * delete the wrong entry.
     */
    function deleteSingleReply($id) {

//Delete the entry into the replies database. The delete function is
///from the class dbhosportal_replies.
        $this->objDBReplies->deleteSingle($id);
///Delete the entry which that specific id in the messages database.
///The delete function is from the parent class dbTable.
        $this->delete("id", $id);
    }
}
?>