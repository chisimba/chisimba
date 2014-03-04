/*
 * class to create an instance of the overview section of the main document. It will
 * initially serve as a test to ensure that the implemented stuff works
 */
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;

import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.Radio;
import com.extjs.gxt.ui.client.widget.form.RadioGroup;
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.i18n.client.DateTimeFormat;
import org.wits.client.Document;
import org.wits.client.EditDocumentDialog;
//import org.wits.client.ads.OverView;
import org.wits.client.NewCourseProposalDialog;

/**
 *
 * @author davidwaf
 */
public class RulesAndSyllabusOne {

    private Dialog rulesAndSyllabusOneDialog = new Dialog();
    // private Dialog topicListingDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormPanel qA2Panel = new FormPanel();
    private FormPanel qB5aPanel = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private TextArea topicField = new TextArea();
    private String courseTitle;
    private OverView overView;
    private RulesAndSyllabusOne oldRulesAndSyllabusOne;
    //private NewCourseProposalDialog newCourseProposalDialog;

    public RulesAndSyllabusOne(OverView overViewDialog) {
        this.overView = overViewDialog;
        createUI();
    }

    public RulesAndSyllabusOne(RulesAndSyllabusOne oldRulesAndSyllabusOne) {
        this.oldRulesAndSyllabusOne = oldRulesAndSyllabusOne;
        createUI();
    }

    //creates the GUI in which the selected text will be displayed. sets only one
    //layer for the interface.
    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setHeight(530);
        mainForm.setWidth(650);
        mainForm.setLabelWidth(300);

        TextArea questionB1 = new TextArea();
        questionB1.setPreventScrollbars(false);
        questionB1.setHeight(50);
        questionB1.setFieldLabel("B.1. How does this course/unit change the rules for the curriculum? ");

        mainForm.add(questionB1, formData);

        TextArea questionB2 = new TextArea();
        questionB2.setPreventScrollbars(false);
        questionB2.setHeight(50);
        questionB2.setFieldLabel("B.2. Describe the course/unit syllabus. ");

        mainForm.add(questionB2, formData);

        TextArea questionB3a = new TextArea();
        questionB3a.setPreventScrollbars(false);
        questionB3a.setHeight(50);
        questionB3a.setFieldLabel("B.3. a. What are the pre-requisites for the course/unit if any? ");

        mainForm.add(questionB3a, formData);

        TextArea questionB3b = new TextArea();
        questionB3b.setPreventScrollbars(false);
        questionB3b.setHeight(50);
        questionB3b.setFieldLabel("B.3.b. What are the co-requisites for the course/unit if any? ");

        mainForm.add(questionB3b, formData);

        Radio radio = new Radio();
        radio.setBoxLabel("a compulsory course/unit ");
        radio.setValue(true);

        Radio radio2 = new Radio();
        radio2.setPagePosition(331, 345);
        radio2.setBoxLabel("an optional course/unit");

        Radio radio3 = new Radio();
        radio3.setPagePosition(331, 361);
        radio3.setBoxLabel("both compulsory and optional as the course/unit is offered toward qualifications/programmes with differing curriculum structures ");

        RadioGroup questionA2 = new RadioGroup();
        questionA2.setFieldLabel("B.4.a This is a");
        questionA2.add(radio);
        questionA2.add(radio2);
        questionA2.add(radio3);
        //mainForm.add(questionA2, formData);

        qA2Panel.setFrame(false);
        qA2Panel.setBodyBorder(false);
        //q5Panel.setPosition(200, 600);
        qA2Panel.setHeight(110);
        qA2Panel.setWidth(700);
        qA2Panel.setLabelWidth(300);
        mainForm.add(qA2Panel, formData);
        qA2Panel.add(questionA2, formData);

        TextArea questionB4b = new TextArea();
        questionB4b.setPreventScrollbars(false);
        questionB4b.setHeight(50);
        questionB4b.setFieldLabel("B.4.b. If it is a compulsory course/unit, which course/unit is it replacing, or is the course/unit to be taken by students in addition to the current workload of courses/unit? ");

        mainForm.add(questionB4b, formData);

        TextArea questionB4c = new TextArea();
        questionB4c.setPreventScrollbars(false);
        questionB4c.setHeight(50);
        questionB4c.setFieldLabel("B.4.c. If it is both a compulsory and optional course/unit, provide details explaining for which qualifications/ programmes " +
                                   "the course/unit would be optional and for which it would be compulsory. ");

        mainForm.add(questionB4c, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        //dont forget to add constraints for radioGroups. need to find out how.
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                /*qB1 = questionB1.getValue();// deptField.getValue().getId();
                if (qB1 == null) {
                    MessageBox.info("Missing answer", "Provide your answer to question A.1.", null);
                    return;
                }


                qB2 = questionB2.getValue();
                if (qB2 == null) {
                    MessageBox.info("Missing selection", "Please make a selection for question A.2.", null);
                    return;
                }

                qB3a = questionB3a.getValue();// deptField.getValue().getId();
                if (qB3a == null) {
                    MessageBox.info("Missing answer", "Provide your answer to question A.1.", null);
                    return;
                }

                qB3b = questionB3b.getValue();// deptField.getValue().getId();
                if (qB3b == null) {
                    MessageBox.info("Missing department", "Provide your answer to question 1", null);
                    return;
                }

                qB4b = questionB4b.getValue();// deptField.getValue().getId();
                if (qB4b == null) {
                    MessageBox.info("Missing department", "Provide your answer to question 1", null);
                    return;
                }

                qB4c = questionB4c.getValue();// deptField.getValue().getId();
                if (qB4c == null) {
                    MessageBox.info("Missing department", "Provide your answer to question 1", null);
                    return;
                }

                qB5b = questionB5b.getValue();// deptField.getValue().getId();
                if (qB5b == null) {
                    MessageBox.info("Missing department", "Provide your answer to question 1", null);
                    return;
                }*/

               

            }
        });

        
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if(oldRulesAndSyllabusOne == null){
                    RulesAndSyllabusTwo rulesAndSyllabusTwo = new RulesAndSyllabusTwo(RulesAndSyllabusOne.this);
                    rulesAndSyllabusTwo.show();
                    rulesAndSyllabusOneDialog.hide();
                }
                else{
                    oldRulesAndSyllabusOne.show();
                    rulesAndSyllabusOneDialog.hide();
                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

                @Override
                public void componentSelected(ButtonEvent ce) {
                    overView.setOldRulesAndSyllabusOne(RulesAndSyllabusOne.this);
                    overView.show();
                    rulesAndSyllabusOneDialog.hide();
                }
            });

        mainForm.addButton(backButton);
        mainForm.addButton(saveButton);
        mainForm.setButtonAlign(HorizontalAlignment.RIGHT);

        //mainForm.setButtonAlign(HorizontalAlignment.RIGHT);

        //FormButtonBinding binding = new FormButtonBinding(mainForm);
        //binding.addButton(saveButton);

        rulesAndSyllabusOneDialog.setBodyBorder(false);
        rulesAndSyllabusOneDialog.setHeading("Section B: Rules and Syllabus Book- Page One");
        rulesAndSyllabusOneDialog.setWidth(700);
        rulesAndSyllabusOneDialog.setHeight(600);
        rulesAndSyllabusOneDialog.setHideOnButtonClick(true);
        rulesAndSyllabusOneDialog.setButtons(Dialog.CLOSE);
        rulesAndSyllabusOneDialog.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusOneDialog.setHideOnButtonClick(true);
        rulesAndSyllabusOneDialog.setButtons(Dialog.CLOSE);

        rulesAndSyllabusOneDialog.setButtonAlign(HorizontalAlignment.RIGHT);
        rulesAndSyllabusOneDialog.setHideOnButtonClick(true);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        //newDocumentDialog.setButtons(Dialog.);
        //newDocumentDialog.setButtonAlign(HorizontalAlignment.RIGHT);
        rulesAndSyllabusOneDialog.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusOneDialog.add(mainForm);

        //setDepartment();
    }

    public void show() {
        rulesAndSyllabusOneDialog.show();
    }

    public void setOldRulesAndSyllabusOne(RulesAndSyllabusOne oldRulesAndSyllabusOne) {
        this.oldRulesAndSyllabusOne = oldRulesAndSyllabusOne;
    }

    public void setSelectedFolder(ModelData selectedFolder) {
        this.selectedFolder = selectedFolder;
        topicField.setValue((String) this.selectedFolder.get("id"));
        topicField.setToolTip((String) this.selectedFolder.get("id"));
    }

    private void createNewDocument(String url) {
        final MessageBox wait = MessageBox.wait("Wait",
                "Saving your data, please wait...", "Saving...");
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        String responseTxt[] = response.getText().split(",");

                        Document doc = new Document();
                        //doc.setDate(fmt.format(date));
                        doc.setRefNo(responseTxt[0]);
                        doc.setId(responseTxt[1]);
                        //doc.setQuestion(qB1);
                        //doc.setQuestion(qB2);
                        //doc.setQuestion(qB5b);
                        /*doc.setQuestion(qA4);
                        doc.setQuestion(qA5);*/
                        EditDocumentDialog editDocumentDialog = new EditDocumentDialog(doc, "all", null);
                        editDocumentDialog.show();
                        rulesAndSyllabusOneDialog.setVisible(false);
                        wait.close();
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create document", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }
    }
}
