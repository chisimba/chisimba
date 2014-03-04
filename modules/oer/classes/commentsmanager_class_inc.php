<?php

/**
 * contains util methods for managing comments
 *
 * @author pwando
 */
class commentsmanager extends object {

    public $objDbProductComments;
    private $objLanguage;

    function init() {
        $this->objDbProductComments = $this->getObject('dbproductcomments', 'oer');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject("user", "security");
        $this->objAdaptationManager = $this->getObject("adaptationmanager", "oer");
    }

    function addNewComment() {
        //Flag to check if user has perms to manage adaptations
        $hasPerms = $this->objAdaptationManager->userHasPermissions();
        $comment = $this->getParam('comment');
        $product_id = $this->getParam('product_id');
        if ($hasPerms && !empty($comment)) {
            $errors = array();
            $comment = $this->getParam('usercomment');
            $product_id = $this->getParam('product_id');
            $userId = $this->objUser->userId();

            $umbrellaTheme = $this->getParam("umbrellatheme");
            if (count($errors) > 0) {
                $this->setVar('title', $title);
                $this->setVar('mode', "fixup");

                return array('id' => $product_id, 'status'=>'notok', 'errors'=>$errors);
            } else {
                $data = array(
                    'comment' => $comment,
                    'product_id' => $product_id,
                    'userid' => $userId
                );
                $this->objDbProductComments->addComment($data);

                return array('id' => $product_id, 'status'=>'ok');
            }
        } else {
            return array('id' => $product_id, 'status'=>'notok');
        }
    }

}

?>
