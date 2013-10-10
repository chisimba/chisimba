<?php

/**
 * This class is used to to display the top navigation of each document. It is used
 * to display consistent navigation across all forms of the document.
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
 * 59 Temple Place - Suite 3"50%", Boston, MA  02111-1"50%"7, USA.
 *
 * @category  Chisimba
 * @package   apo (document management system)
 * @author    Nguni Phakela
 * @copyright 2010
 */
if (!
        /**
         * Description for $GLOBALS
         * @global string $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class topnavigation extends object {
    /*
     * Constructor
     *
     */
    public function init() {}

    public function show($document) {
        $overviewlink = new link($this->uri(array("action" => "showoverview", "id" => $document['id'], 'formname' => 'editdocument')));
        $overviewlink->link = "Overview";

        $rulesandsyllabusonelink = new link($this->uri(array("action" => "showrulesandsyllabusone", "id" => $document['id'], 'formname' => 'editdocument')));
        $rulesandsyllabusonelink->link = "Rules and Syllabus (page one)";

        $rulesandsyllabustwolink = new link($this->uri(array("action" => "showrulesandsyllabustwo", "id" => $document['id'], 'formname' => 'editdocument')));
        $rulesandsyllabustwolink->link = "Rules and Syllabus (page two)";

        $subsidyrequirementslink = new link($this->uri(array("action" => "showsubsidyrequirements", "id" => $document['id'], 'formname' => 'editdocument')));
        $subsidyrequirementslink->link = "Subsidy Requirements";

        $outcomesandassessmentonelink = new link($this->uri(array("action" => "showoutcomesandassessmentone", "id" => $document['id'], 'formname' => 'editdocument')));
        $outcomesandassessmentonelink->link = "Outcomes and Assessment (page one)";

        $outcomesandassessmenttwolink = new link($this->uri(array("action" => "showoutcomesandassessmenttwo", "id" => $document['id'], 'formname' => 'editdocument')));
        $outcomesandassessmenttwolink->link = "Outcomes and Assessment (page two)";

        $outcomesandassessmentthreelink = new link($this->uri(array("action" => "showoutcomesandassessmentthree", "id" => $document['id'], 'formname' => 'editdocument')));
        $outcomesandassessmentthreelink->link = "Outcomes and Assessment (page three)";

        $resourceslink = new link($this->uri(array("action" => "showresources", "id" => $document['id'], 'formname' => 'editdocument')));
        $resourceslink->link = "Resources";

        $collaborationandcontractslink = new link($this->uri(array("action" => "showcollaborationandcontracts", "id" => $document['id'], 'formname' => 'editdocument')));
        $collaborationandcontractslink->link = "Collaboration and Contracts";

        $reviewlink = new link($this->uri(array("action" => "showreview", "id" => $document['id'], 'formname' => 'editdocument')));
        $reviewlink->link = "Review";

        $contactdetailslink = new link($this->uri(array("action" => "showcontactdetails", "id" => $document['id'], 'formname' => 'editdocument')));
        $contactdetailslink->link = "Contact Details";

        $commentslink = new link($this->uri(array("action" => "showcomments", "id" => $document['id'], 'formname' => 'editdocument')));
        $commentslink->link = "Comments";

        $feedbacklink = new link($this->uri(array("action" => "showfeedback", "id" => $document['id'], 'formname' => 'editdocument')));
        $feedbacklink->link = "Feedback";

        $links = "<b>Document</b>" . '&nbsp;|&nbsp;' . $overviewlink->show() . '&nbsp;|&nbsp;' .
                $rulesandsyllabusonelink->show() . '&nbsp;|&nbsp;' . $rulesandsyllabustwolink->show() . '&nbsp;|&nbsp;' .
                $subsidyrequirementslink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentonelink->show() . '&nbsp;|&nbsp;' .
                $outcomesandassessmenttwolink->show() . '&nbsp;|&nbsp;' . $outcomesandassessmentthreelink->show() . '&nbsp;|&nbsp;' .
                $resourceslink->show() . '&nbsp;|&nbsp;' . $collaborationandcontractslink->show() . '&nbsp;|&nbsp;' .
                $reviewlink->show() . '&nbsp;|&nbsp;' . $contactdetailslink->show() . '&nbsp;|&nbsp;' . $feedbacklink->show() . '&nbsp;|&nbsp;' .
                $commentslink->show() . '<br/>';

        $fs = new fieldset();
        $fs->setLegend('<b>Navigation</b>');
        $fs->addContent($links);

        return $fs->show() . '<br/>';
    }
}
?>
