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
import com.extjs.gxt.ui.client.widget.form.TextField;


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

/**
 *
 * @author davidwaf
 */
public class RulesAndSyllabusTwo {

    private Dialog rulesAndSyllabusTwoDialog = new Dialog();
    // private Dialog topicListingDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormPanel qA2Panel = new FormPanel();
    private FormPanel qB5aPanel = new FormPanel();
    private FormPanel qB6aPanel = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private TextArea topicField = new TextArea();
    private String courseTitle;
    private RulesAndSyllabusOne rulesAndSyllabusOne;
    private RulesAndSyllabusTwo oldRulesAndSyllabusTwo;
    //private NewCourseProposalDialog newCourseProposalDialog;

    public RulesAndSyllabusTwo(RulesAndSyllabusOne rulesAndSyllabusOne) {
        this.rulesAndSyllabusOne = rulesAndSyllabusOne;
        createUI();
    }

    public RulesAndSyllabusTwo(RulesAndSyllabusTwo oldRulesAndSyllabusTwo) {
        this.oldRulesAndSyllabusTwo = oldRulesAndSyllabusTwo;
        createUI();
    }
    //creates the GUI in which the selected text will be displayed. sets only one
    //layer for the interface.
    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setHeight(600);
        mainForm.setWidth(810);
        mainForm.setLabelWidth(300);

        Radio radio4 = new Radio();
        //radio4.setPagePosition(231, 400);
        radio4.setBoxLabel("a 1st year unit");
        radio4.setValue(true);

        Radio radio5 = new Radio();
        radio5.setPagePosition(331,126);
        radio5.setBoxLabel("a 2nd year unit");

        Radio radio6 = new Radio();
        radio6.setPagePosition(331,142);
        radio6.setBoxLabel("a 3rd year unit");

        Radio radio7 = new Radio();
        radio7.setPagePosition(331,162);
        radio7.setBoxLabel("a 4th year unit ");

        Radio radio8 = new Radio();
        radio8.setPagePosition(331,182);
        radio8.setBoxLabel("a 5th year unit ");

        Radio radio9 = new Radio();
        radio9.setPagePosition(331,202);
        radio9.setBoxLabel("a 6th year unit ");

        Radio radio10 = new Radio();
        radio10.setPagePosition(331,222);
        radio10.setBoxLabel("an honours unit ");

        Radio radio11 = new Radio();
        radio11.setPagePosition(331,242);
        radio11.setBoxLabel("a postgraduate diploma unit ");

        Radio radio12 = new Radio();
        radio12.setPagePosition(331,262);
        radio12.setBoxLabel("a masters unit ");

        Radio radio13 = new Radio();
        //radio12.setPagePosition(96,403);
        radio13.setBoxLabel("full year unit offered in semester 1 and 2 ");
        radio13.setValue(true);

        Radio radio14 = new Radio();
        radio14.setPagePosition(331,410);
        radio14.setBoxLabel("half year unit offered in  ");

        Radio radio15 = new Radio();
        radio15.setPagePosition(477,410);
        radio15.setBoxLabel("semester1 ");

        Radio radio16 = new Radio();
        radio16.setPagePosition(560,410);
        radio16.setBoxLabel("semester 2 ");

        Radio radio17 = new Radio();
        radio17.setPagePosition(641,410);
        radio17.setBoxLabel("or semester 1 and 2  ");

        Radio radio18 = new Radio();
        radio18.setPagePosition(331,426);
        radio18.setBoxLabel("block unit offered in ");

        Radio radio19 = new Radio();
        radio19.setPagePosition(459,426);
        radio19.setBoxLabel("block 1 ");

        Radio radio20 = new Radio();
        radio20.setPagePosition(526,426);
        radio20.setBoxLabel("block 2 ");

        Radio radio21 = new Radio();
        radio21.setPagePosition(584,426);
        radio21.setBoxLabel("block 3 ");

        Radio radio22 = new Radio();
        radio22.setPagePosition(644,426);
        radio22.setBoxLabel("block 4");

        Radio radio23 = new Radio();
        radio23.setPagePosition(331,442);
        radio23.setBoxLabel("attendance course/unit");

        Radio radio24 = new Radio();
        radio24.setPagePosition(331, 458);
        radio24.setBoxLabel("other ");

        Radio radio25 = new Radio();
        //radio12.setPagePosition(96,403);
        radio25.setBoxLabel("yes ");
        radio25.setValue(true);

        Radio radio26 = new Radio();
        //radio12.setPagePosition(96,403);
        radio26.setBoxLabel("no ");

        RadioGroup questionB5a = new RadioGroup();
        questionB5a.setFieldLabel("B.5.a. At what level is the course/unit taught?");
        questionB5a.add(radio4);
        questionB5a.add(radio5);
        questionB5a.add(radio6);
        questionB5a.add(radio7);
        questionB5a.add(radio8);
        questionB5a.add(radio9);
        questionB5a.add(radio10);
        questionB5a.add(radio11);
        questionB5a.add(radio12);

        qB5aPanel.setFrame(false);
        qB5aPanel.setBodyBorder(false);
        //q5Panel.setPosition(200, 600);
        qB5aPanel.isHeaderVisible();
        qB5aPanel.setHeight(230);
        qB5aPanel.setWidth(700);
        qB5aPanel.setLabelWidth(300);
        mainForm.add(qB5aPanel, formData);
        qB5aPanel.add(questionB5a, formData);

        TextArea questionB5b = new TextArea();
        questionB5b.setPreventScrollbars(false);
        questionB5b.setHeight(50);
        questionB5b.setFieldLabel("B.5.b. In which year/s of study is the course/unit to be taught? ");

        mainForm.add(questionB5b, formData);

        // for B6a use checkboxes instead of radio buttons
        RadioGroup questionB6a = new RadioGroup();
        questionB6a.setFieldLabel("B.6.a. This is a ");
        questionB6a.add(radio13);
        questionB6a.add(radio14);
        questionB6a.add(radio15);
        questionB6a.add(radio16);
        questionB6a.add(radio17);
        questionB6a.add(radio18);
        questionB6a.add(radio19);
        questionB6a.add(radio20);
        questionB6a.add(radio21);
        questionB6a.add(radio22);
        questionB6a.add(radio23);
        questionB6a.add(radio24);

        qB6aPanel.setFrame(false);
        qB6aPanel.setBodyBorder(false);
        //q5Panel.setPosition(200, 600);
        qB6aPanel.isHeaderVisible();
        qB6aPanel.setHeight(150);
        qB6aPanel.setWidth(830);
        qB6aPanel.setLabelWidth(300);
        mainForm.add(qB6aPanel, formData);
        
        qB6aPanel.add(questionB6a, formData);

        TextArea questionB6b = new TextArea();
        questionB6b.setPreventScrollbars(false);
        questionB6b.setHeight(50);
        questionB6b.setWidth(50);
        questionB6b.setFieldLabel("B.6.b. If ‘other’, provide details of the course/unit duration and/or the number of lectures which comprise the course/unit ");
        mainForm.add(questionB6b, formData);

        RadioGroup questionB6c = new RadioGroup();
        questionB6c.setFieldLabel("B.6.c.Is the unit assessed ");
        questionB6c.add(radio25);
        questionB6c.add(radio26);

        mainForm.add(questionB6c, formData);

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
                if(oldRulesAndSyllabusTwo == null){
                    Resources resources = new Resources();
                    resources.show();
                    rulesAndSyllabusTwoDialog.hide();
                }
                else{
                    oldRulesAndSyllabusTwo.show();
                    rulesAndSyllabusTwoDialog.hide();
                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

                @Override
                public void componentSelected(ButtonEvent ce) {
                    rulesAndSyllabusOne.setOldRulesAndSyllabusOne(RulesAndSyllabusTwo.this);
                    rulesAndSyllabusOne.show();
                    rulesAndSyllabusTwoDialog.hide();
                }
            });

        mainForm.addButton(backButton);
        mainForm.addButton(saveButton);
        mainForm.setButtonAlign(HorizontalAlignment.RIGHT);

        rulesAndSyllabusTwoDialog.setBodyBorder(false);
        rulesAndSyllabusTwoDialog.setHeading("Section B: Rules and Syllabus Book- Page Two");
        rulesAndSyllabusTwoDialog.setWidth(830);
        rulesAndSyllabusTwoDialog.setHeight(670);
        rulesAndSyllabusTwoDialog.setHideOnButtonClick(true);
        rulesAndSyllabusTwoDialog.setButtons(Dialog.CLOSE);
        rulesAndSyllabusTwoDialog.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusTwoDialog.setHideOnButtonClick(true);
        rulesAndSyllabusTwoDialog.setButtons(Dialog.CLOSE);
        rulesAndSyllabusTwoDialog.setButtonAlign(HorizontalAlignment.RIGHT);
        rulesAndSyllabusTwoDialog.setHideOnButtonClick(true);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusTwoDialog.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusTwoDialog.add(mainForm);

        //setDepartment();
    }

    public void show() {
        rulesAndSyllabusTwoDialog.show();
    }

    /*public void setOldRulesAndSyllabusTwo(RulesAndSyllabusTwo oldRulesAndSyllabusTwo) {
        this.oldRulesAndSyllabusTwo = oldRulesAndSyllabusTwo;
    }*/

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
                        rulesAndSyllabusTwoDialog.setVisible(false);
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
