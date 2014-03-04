/*
 * class to create an instance of the overview section of the main document. It will
 * initially serve as a test to ensure that the implemented stuff works
 */
package org.wits.client.ads;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
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
import com.google.gwt.http.client.Response;
import com.google.gwt.i18n.client.DateTimeFormat;

import com.extjs.gxt.ui.client.data.*;
import com.google.gwt.core.client.GWT;
import org.wits.client.Constants;
import org.wits.client.EditDocumentDialog;
import org.wits.client.util.Util;
import org.wits.client.util.WicidXML;

//import com.extjs.gxt.ui.client.data.DataReader;
/**
 *
 * @author luigi
 */
public class OverView {

    private Dialog overViewDialog = new Dialog();
    // private Dialog topicListingDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormPanel q5Panel = new FormPanel();
    private FormData formData = new FormData("-20");
    private DateTimeFormat fmt = DateTimeFormat.getFormat("y/M/d");
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private Button commentButton = new Button("Comments");
    private TextArea topicField = new TextArea();
    private String qA1 = "", qA3 = "", qA2 = "", qA4 = "", qA5 = "";
    private TextField<String> questionA1 = new TextField<String>();
    private NewCourseProposalDialog newCourseProposalDialog;
    private RulesAndSyllabusOne oldRulesAndSyllabusOne;
    public String overViewData;
    private ForwardTo forwardTo;
    private String data;
    private final RadioGroup questionA2 = new RadioGroup();
    private final TextArea questionA3 = new TextArea();
    private final TextArea questionA4 = new TextArea();
    private final RadioGroup questionA5 = new RadioGroup();
    private Radio radio = new Radio();
    private int radioA2, radioA5;
    private Radio radio2 = new Radio();
    private Radio radio3 = new Radio();
    private Radio radio4 = new Radio();
    private Radio radio5 = new Radio();
    private Radio radio6 = new Radio();
    private Radio radio7 = new Radio();
    private EditDocumentDialog editDocumentDialog;
    private boolean[] quesA2 = new boolean[2];//{radio.getValue(), radio2.getValue()};
    private boolean[] quesA5 = new boolean[5];//{radio3.getValue(), radio4.getValue(), radio5.getValue(), radio6.getValue(), radio7.getValue()};

    public OverView(EditDocumentDialog editDocumentDialog) {
        this.editDocumentDialog = editDocumentDialog;
        // get the data from database

        createUI();
        getFormData();
    }

    public OverView(EditDocumentDialog editDocumentDialog, String data) {
        this.editDocumentDialog = editDocumentDialog;
        this.data = data;
        createUI();
    }

    public OverView(NewCourseProposalDialog newCourseProposalDialog) {
        this.newCourseProposalDialog = newCourseProposalDialog;
        createUI();
    }

    public OverView(RulesAndSyllabusOne oldRulesAndSyllabusOne) {
        this.oldRulesAndSyllabusOne = oldRulesAndSyllabusOne;
        createUI();
    }

    //creates the GUI in which the selected text will be displayed. sets only one
    //layer for the interface.NewCourseProposalDialog newCourseProposalDialog
    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setHeight(500);
        mainForm.setWidth(780);
        mainForm.setLabelWidth(200);

        //test database entry using the first input field on overview...


        overViewDialog.setButtonAlign(HorizontalAlignment.LEFT);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        // set radio buttons, their labels and thrir positon relative
        // to the mainForm...

        radio.setBoxLabel("proposal for a new course/unit ");
        radio.getValueAttribute();

        radio2.setBoxLabel("change to the outcomes or credit value of a course/unit");
        radio2.setPagePosition(221, 123);

        //radio3.setPosition(5, 10);
        radio3.setBoxLabel("linked to other recent course/unit proposal/s, or proposal/s currently in development ");

        radio4.setPagePosition(221, 415);
        radio4.setBoxLabel("linked to other recent course/unit amendment/s, or amendment/s currently in development");

        radio5.setPagePosition(221, 435);
        radio5.setBoxLabel("linked to a new qualification/ programme proposal, or one currently in development");

        radio6.setPagePosition(221, 455);
        radio6.setBoxLabel("linked to a recent qualification/ programme amendment, or one currently in development");

        radio7.setPagePosition(221, 475);
        radio7.setBoxLabel("not linked to any other recent academic developments, nor those currently in development ");

        if ((quesA2 == null) || (quesA5 == null)) {
            radio.setValue(true);
            radio2.setValue(false);
            radio3.setValue(true);
            radio4.setValue(false);
            radio5.setValue(false);
            radio6.setValue(false);
            radio7.setValue(false);
        }

        questionA1.setFieldLabel("A.1. Name of course/ unit.");
        questionA1.setEmptyText("Enter the course/unit name");
        if (editDocumentDialog != null) {
            questionA1.setValue(editDocumentDialog.getTitleField().getValue());
        }
        if (newCourseProposalDialog != null) {
            questionA1.setValue(newCourseProposalDialog.getTitleField().getValue());
        }
        if (this.data != null) {
            questionA1.setValue(Util.getTagText(data, "qA1"));

        }
        questionA1.setAllowBlank(false);
        questionA1.setMinLength(100);
        questionA1.getValue();

        mainForm.add(questionA1, formData);

        questionA2.setFieldLabel("A.2. This is a");
        questionA2.add(radio);
        questionA2.add(radio2);
        questionA2.setHeight(40);
        mainForm.add(questionA2, formData);

        questionA3.setPreventScrollbars(false);
        questionA3.setHeight(120);
        questionA3.setFieldLabel("A.3. Provide a brief motivation for the introduction/ amendment of the course/unit ");
        mainForm.add(questionA3, formData);

        questionA4.setPreventScrollbars(false);
        questionA4.setHeight(120);
        questionA4.setFieldLabel("A.4. Towards which qualification(s) can the course/unit be taken? ");
        mainForm.add(questionA4, formData);

        questionA5.setSelectionRequired(true);
        questionA5.setFieldLabel("A.5. This new or amended course proposal is");
        questionA5.add(radio3);
        questionA5.add(radio4);
        questionA5.add(radio5);
        questionA5.add(radio6);
        questionA5.add(radio7);
        mainForm.add(questionA5, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));

        //dont forget to add constraints for radioGroups. need to find out how.
        //used to ensure that all the data is added into the required fields
        //before saving the content. Will not proceed unless all fields are entered
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

                qA1 = questionA1.getValue();
                if (qA1 == null) {
                    MessageBox.info("Missing answer", "Provide an answer to question A.1.", null);
                    return;
                } else {
                    qA1.replaceAll(" ", "--");
                }

                quesA2[0] = radio.getValue();
                quesA2[1] = radio2.getValue();
                qA2 = "";
                for (int i = 0; i < 2; i++) {
                    switch (new Boolean(quesA2[i]).toString().charAt(0)) {
                        case 't':
                            qA2 = qA2 + "1";
                            break;
                        case 'f':
                            qA2 = qA2 + "0";
                            break;
                    }
                }

                if (qA2 == null) {
                    MessageBox.info("Missing selection", "Please make a selection for question A.2.", null);
                    return;
                } else {
                    qA2.replaceAll(" ", "--");

                }

                qA3 = questionA3.getValue();//.toString().replaceAll(" ", "--");// deptField.getValue().getId();
                if (qA3 == null) {
                    MessageBox.info("Missing answer", "Provide an answer to question A.3.", null);
                    return;
                } else {
                    qA3.toString().replaceAll(" ", "--");
                }
                //MessageBox.info("test", "missing", null);

                qA4 = questionA4.getValue();//.toString();//.replaceAll(" ", "");// deptField.getValue().getId();
                if (qA4 == null) {
                    MessageBox.info("Missing department", "Provide your answer to question A.4.", null);
                    return;
                } else {
                    qA4.toString().replaceAll(" ", "--");
                }

                quesA5[0] = radio3.getValue();
                quesA5[1] = radio4.getValue();
                quesA5[2] = radio5.getValue();
                quesA5[3] = radio6.getValue();
                quesA5[4] = radio7.getValue();
                qA5 = "";
                for (int i = 0; i < 5; i++) {
                    switch (new Boolean(quesA5[i]).toString().charAt(0)) {
                        case 't':
                            qA5 = qA5 + "1";
                            break;
                        case 'f':
                            qA5 = qA5 + "0";
                            break;
                    }
                }

                if (qA5 == null) {
                    MessageBox.info("Missing department", "Please make a selection for question A.5.", null);
                    return;
                } else {
                    qA5.replaceAll(" ", "--");
                }

                storeDocumentInfo();

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=overview" + "&docid=" + Constants.docid + "&formdata=" + overViewData;

                createDocument(url);

                if (oldRulesAndSyllabusOne == null) {
                    RulesAndSyllabusOne rulesAndSyllabusOne = new RulesAndSyllabusOne(OverView.this);
                    rulesAndSyllabusOne.show();
                    overViewDialog.hide();
                } else {

                    oldRulesAndSyllabusOne.show();
                    overViewDialog.hide();

                }

            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
//                editDocumentDialog.setOldOverView(OverView.this);
                editDocumentDialog.show();
                overViewDialog.hide();
                storeDocumentInfo();
            }
        });

        forwardButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                ForwardTo forwardToDialog = new ForwardTo();
                forwardToDialog.show();

            }
        });

        commentButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                CommentDialog commentDialog = new CommentDialog("overview");
                commentDialog.show();
            }
        });

        mainForm.addButton(backButton);
        mainForm.addButton(saveButton);
        mainForm.addButton(forwardButton);
        mainForm.addButton(commentButton);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);

        overViewDialog.setBodyBorder(false);
        overViewDialog.setHeading("Section A: Overview");
        overViewDialog.setWidth(790);
        overViewDialog.setHeight(580);
        overViewDialog.setHideOnButtonClick(true);
        overViewDialog.setButtons(Dialog.CLOSE);
        overViewDialog.setButtonAlign(HorizontalAlignment.LEFT);
        overViewDialog.setHideOnButtonClick(true);

        overViewDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        overViewDialog.add(mainForm);

        //setDepartment();
    }

    public void storeDocumentInfo() {

        WicidXML wicidxml = new WicidXML("overview");
        wicidxml.addElement("qA1", qA1);
        wicidxml.addElement("qA2", qA2);
        wicidxml.addElement("qA3", qA3);
        wicidxml.addElement("qA4", qA4);
        wicidxml.addElement("qA5", qA5);
        overViewData = wicidxml.getXml();

    }

    public void setOldRulesAndSyllabusOne(RulesAndSyllabusOne oldRulesAndSyllabusOne) {
        this.oldRulesAndSyllabusOne = oldRulesAndSyllabusOne;
    }

    public void show() {
        overViewDialog.show();
    }

    public void setSelectedFolder(ModelData selectedFolder) {
        this.selectedFolder = selectedFolder;
        topicField.setValue((String) this.selectedFolder.get("id"));
        topicField.setToolTip((String) this.selectedFolder.get("id"));
    }

    private void createDocument(String url) {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot save overview data", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create document", null);
                    }
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create new document", null);
        }

    }

    private void getFormData() {
        
        String url = GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                + "?module=wicid&action=getFormData&formname=overview&docid=" + Constants.docid;
        
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get overview data", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    String data = response.getText();
                   

                    String qA1 = Util.getTagText(data, "qA1");
                    
                    if (qA1 == null) {
                        questionA1.setValue(editDocumentDialog.getTitleField().getValue());
                    } else {
                        questionA1.setValue(qA1);
                    }


                    String qA2 = Util.getTagText(data, "qA2");
                    
                    if (qA2 != null) {
                        for (int i = 0; i < 2; i++) {
                            if (qA2.charAt(i) == '0') {
                                quesA2[i] = false;
                            }
                            if (qA2.charAt(i) == '1') {
                                quesA2[i] = true;
                            }
                        }
                        radio.setValue(quesA2[0]);
                        radio2.setValue(quesA2[1]);
                    } else {
                        radio.setValue(true);
                        radio2.setValue(false);
                    }

                    String qA3 = Util.getTagText(data, "qA3");
                    questionA3.setValue(qA3);

                    String qA4 = Util.getTagText(data, "qA4");
                    questionA4.setValue(qA4);

                    String qA5 = Util.getTagText(data, "qA5");
                    if (qA5 != null) {
                        for (int i = 0; i < 5; i++) {
                            if (qA5.charAt(i) == '0') {
                                quesA5[i] = false;
                            }
                            if (qA5.charAt(i) == '1') {
                                quesA5[i] = true;
                            }
                        }
                        radio3.setValue(quesA5[0]);
                        radio4.setValue(quesA5[1]);
                        radio5.setValue(quesA5[2]);
                        radio6.setValue(quesA5[3]);
                        radio7.setValue(quesA5[4]);
                    } else {
                        radio3.setValue(true);
                        radio4.setValue(false);
                        radio5.setValue(false);
                        radio6.setValue(false);
                        radio7.setValue(false);
                    }
                    /*String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {

                    } else {
                    MessageBox.info("Error", "Error occured on the server. Cannot get overview data", null);
                    }*/
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get overview data", null);
        }
    }
}
