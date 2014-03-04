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
import com.extjs.gxt.ui.client.event.BaseEvent;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
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
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import com.google.gwt.i18n.client.DateTimeFormat;
import org.wits.client.Constants;
import org.wits.client.util.Util;
import org.wits.client.util.WicidXML;

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
    private Button forwardButton = new Button("Forward to...");
    private TextArea topicField = new TextArea();
    private Radio radio4 = new Radio();
    private Radio radio5 = new Radio();
    private Radio radio6 = new Radio();
    private Radio radio7 = new Radio();
    private Radio radio8 = new Radio();
    private Radio radio9 = new Radio();
    private Radio radio10 = new Radio();
    private Radio radio11 = new Radio();
    private Radio radio12 = new Radio();
    private Radio radio13 = new Radio();
    private Radio radio14 = new Radio();
    private Radio radio15 = new Radio();
    private Radio radio16 = new Radio();
    private Radio radio17 = new Radio();
    private Radio radio18 = new Radio();
    private Radio radio19 = new Radio();
    private Radio radio20 = new Radio();
    private Radio radio21 = new Radio();
    private Radio radio22 = new Radio();
    private Radio radio23 = new Radio();
    private Radio radio24 = new Radio();
    private Radio radio25 = new Radio();
    private Radio radio26 = new Radio();
    private final TextArea questionB6b = new TextArea();
    private boolean[] quesB5a = new boolean[9];
    private final TextArea questionB5b = new TextArea();
    private boolean[] quesB6a = new boolean[12];
    private boolean[] quesB6c = new boolean[2];
    private String courseTitle;
    private OverView overView;
    private RulesAndSyllabusOne rulesAndSyllabusOne;
    private SubsidyRequirements oldSubsidyRequirements;
    private String rulesAndSyllabusTwoData, qB5a, qB5b, qB6a, qB6b, qB6c;
    //private SavedFormData savedFormData;
    //private String rulesAndSyllabusOneData = rulesAndSyllabusOne.getRulesAndSyllabusDataOne().toString();

    public RulesAndSyllabusTwo(RulesAndSyllabusOne rulesAndSyllabusOne) {
        this.rulesAndSyllabusOne = rulesAndSyllabusOne;
        createUI();
        getFormData();
    }

    public RulesAndSyllabusTwo(SubsidyRequirements oldSubsidyRequirements) {
        this.oldSubsidyRequirements = oldSubsidyRequirements;
        createUI();
    }

    //creates the GUI in which the selected text will be displayed. sets only one
    //layer for the interface.
    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setHeight(610);
        mainForm.setWidth(810);
        mainForm.setLabelWidth(300);


        radio4.setBoxLabel("a 1st year unit");
        radio4.setValue(true);
        radio4.getBoxLabel();

        radio5.setPagePosition(321, 97);
        radio5.setBoxLabel("a 2nd year unit");

        radio6.setPagePosition(321, 117);
        radio6.setBoxLabel("a 3rd year unit");

        radio7.setPagePosition(321, 137);
        radio7.setBoxLabel("a 4th year unit ");

        radio8.setPagePosition(321, 157);
        radio8.setBoxLabel("a 5th year unit ");

        radio9.setPagePosition(321, 177);
        radio9.setBoxLabel("a 6th year unit ");

        radio10.setPagePosition(321, 197);
        radio10.setBoxLabel("an honours unit ");

        radio11.setPagePosition(321, 217);
        radio11.setBoxLabel("a postgraduate diploma unit ");

        radio12.setPagePosition(321, 237);
        radio12.setBoxLabel("a masters unit ");

        //radio12.setPagePosition(96,403);
        radio13.setBoxLabel("full year unit offered in semester 1 and 2 ");
        radio13.setValue(true);

        radio14.setPagePosition(321, 347);
        radio14.setBoxLabel("half year unit offered in  ");

        radio15.setPagePosition(477, 347);
        radio15.setBoxLabel("semester1 ");

        radio16.setPagePosition(477, 367);
        radio16.setBoxLabel("semester 2 ");

        radio17.setPagePosition(477, 387);
        radio17.setBoxLabel("or semester 1 and 2  ");

        radio18.setPagePosition(321, 407);
        radio18.setBoxLabel("block unit offered in ");

        radio19.setPagePosition(460, 407);
        radio19.setBoxLabel("block 1 ");

        radio20.setPagePosition(460, 427);
        radio20.setBoxLabel("block 2 ");

        radio21.setPagePosition(460, 447);
        radio21.setBoxLabel("block 3 ");

        radio22.setPagePosition(460, 467);
        radio22.setBoxLabel("block 4");

        radio23.setPagePosition(321, 487);
        radio23.setBoxLabel("attendance course/unit");

        radio24.setPagePosition(321, 507);
        radio24.setBoxLabel("other ");

        //radio12.setPagePosition(96,403);
        radio25.setBoxLabel("yes ");
        radio25.setValue(true);

        //radio12.setPagePosition(96,403);
        radio26.setBoxLabel("no ");

        final RadioGroup questionB5a = new RadioGroup();
        questionB5a.setFieldLabel("B.5.a. At what level is the course/unit taught?");
        questionB5a.setHeight(190);
        questionB5a.add(radio4);
        questionB5a.add(radio5);
        questionB5a.add(radio6);
        questionB5a.add(radio7);
        questionB5a.add(radio8);
        questionB5a.add(radio9);
        questionB5a.add(radio10);
        questionB5a.add(radio11);
        questionB5a.add(radio12);
        mainForm.add(questionB5a, formData);

        questionB5b.setPreventScrollbars(false);
        questionB5b.setHeight(50);
        questionB5b.setFieldLabel("B.5.b. In which year/s of study is the course/unit to be taught? ");
        //questionB5b.getValue();

        mainForm.add(questionB5b, formData);

        // for B6a use checkboxes instead of radio buttons
        final RadioGroup questionB6a1 = new RadioGroup();
        questionB6a1.setFieldLabel("B.6.a. This is a ");
        questionB6a1.setSelectionRequired(true);
        questionB6a1.setHeight(150);
        questionB6a1.add(radio13);
        questionB6a1.add(radio14);
        questionB6a1.add(radio18);
        questionB6a1.add(radio23);
        questionB6a1.add(radio24);
        mainForm.add(questionB6a1, formData);

        final RadioGroup questionB6a2 = new RadioGroup();
        questionB6a2.setSelectionRequired(true);
        questionB6a2.setLabelSeparator(" ");
        questionB6a2.add(radio15);
        questionB6a2.add(radio16);
        questionB6a2.add(radio17);
        mainForm.add(questionB6a2, formData);

        final RadioGroup questionB6a3 = new RadioGroup();
        questionB6a3.setSelectionRequired(true);
        questionB6a3.setLabelSeparator(" ");
        questionB6a3.add(radio19);
        questionB6a3.add(radio20);
        questionB6a3.add(radio21);
        questionB6a3.add(radio22);
        mainForm.add(questionB6a3, formData);

        if (radio14.getValue() == false) {
            radio15.disable();
            radio16.disable();
            radio17.disable();
        }
        if (radio18.getValue() == false) {
            radio19.disable();
            radio20.disable();
            radio21.disable();
            radio22.disable();
        }


        questionB6b.setPreventScrollbars(false);
        questionB6b.setHeight(50);
        questionB6b.setWidth(50);
        questionB6b.setFieldLabel("B.6.b. If ‘other’, provide details of the course/unit duration and/or the number of lectures which comprise the course/unit ");
        mainForm.add(questionB6b, formData);

        if (radio24.getValue() == false) {
            questionB6b.disable();
        }

        questionB6a1.addListener(Events.Change, new Listener<BaseEvent>() {

            public void handleEvent(BaseEvent be) {
                if (radio24.getValue() == false) {
                    questionB6b.disable();
                }
                if (radio24.getValue() == true) {
                    questionB6b.enable();
                }
                if (radio14.getValue() == false) {
                    radio15.setValue(false);
                    radio16.setValue(false);
                    radio17.setValue(false);
                    radio15.disable();
                    radio16.disable();
                    radio17.disable();

                }
                if (radio14.getValue() == true) {
                    radio15.enable();
                    radio16.enable();
                    radio17.enable();
                    radio15.setValue(true);
                }
                if (radio18.getValue() == false) {
                    radio19.setValue(false);
                    radio20.setValue(false);
                    radio21.setValue(false);
                    radio22.setValue(false);
                    radio19.disable();
                    radio20.disable();
                    radio21.disable();
                    radio22.disable();
                }
                if (radio18.getValue() == true) {
                    radio19.enable();
                    radio20.enable();
                    radio21.enable();
                    radio22.enable();
                    radio19.setValue(true);
                }
            }
        });

        final RadioGroup questionB6c = new RadioGroup();
        questionB6c.setFieldLabel("B.6.c.Is the unit assessed ");
        questionB6c.add(radio25);
        questionB6c.add(radio26);

        mainForm.add(questionB6c, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        //dont forget to add constraints for radioGroups. need to find out how.
        //used to ensure that all the data is added into the required fields
        //before saving the content. Will not proceed unless all fields are entered
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                //replaceAll is used to replace spaces which give problems when trying to save to the database. spaces(" ")
                //are replaced by ("--")

                quesB5a[0] = radio4.getValue();
                quesB5a[1] = radio5.getValue();
                quesB5a[2] = radio6.getValue();
                quesB5a[3] = radio7.getValue();
                quesB5a[4] = radio8.getValue();
                quesB5a[5] = radio9.getValue();
                quesB5a[6] = radio10.getValue();
                quesB5a[7] = radio11.getValue();
                quesB5a[8] = radio12.getValue();
                qB5a = "";
                for (int i = 0; i < 9; i++) {
                    switch (new Boolean(quesB5a[i]).toString().charAt(0)) {
                        case 't':
                            qB5a = qB5a + "1";
                            break;
                        case 'f':
                            qB5a = qB5a + "0";
                            break;
                    }
                }
                if (qB5a.equals("000000000")) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.5.a", null);
                    return;
                }


                qB5b = questionB5b.getValue();//.toString().replaceAll(" ", "--");
                if (qB5b == null) {
                    MessageBox.info("Missing answer", "Please make a selection for question B.5.b", null);
                    return;
                } else {
                    qB5b.toString().replaceAll(" ", "--");

                }

                if ((radio14.getValue() == true) && (questionB6a2.getValue() == null)) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.a " +
                            "(select radio button 'semester 1', 'semester 2' or 'or semester 1 and 2')", null);
                    return;
                }

                if ((radio18.getValue() == true) && (questionB6a3.getValue() == null)) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.a " +
                            "(select radio button 'block 1', 'block 2', 'block 3' or 'block 4')", null);
                    return;
                }

                quesB6a[0] = radio13.getValue();
                quesB6a[1] = radio14.getValue();
                quesB6a[2] = radio15.getValue();
                quesB6a[3] = radio16.getValue();
                quesB6a[4] = radio17.getValue();
                quesB6a[5] = radio18.getValue();
                quesB6a[6] = radio19.getValue();
                quesB6a[7] = radio20.getValue();
                quesB6a[8] = radio21.getValue();
                quesB6a[9] = radio22.getValue();
                quesB6a[10] = radio23.getValue();
                quesB6a[11] = radio24.getValue();
                qB6a = "";
                for (int i = 0; i < 12; i++) {
                    switch (new Boolean(quesB6a[i]).toString().charAt(0)) {
                        case 't':
                            qB6a = qB6a + "1";
                            break;
                        case 'f':
                            qB6a = qB6a + "0";
                            break;
                    }
                }

                if ((radio14.getValue() == true) && (questionB6a2.getValue() == null)) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.a (select radio button 'semester 1', 'semester 2' or 'or semester 1 and 2')", null);
                    return;
                }

                if ((radio14.getValue() == true) && (questionB6a2.getValue() == null)) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.a (select radio button 'semester 1', 'semester 2' or 'or semester 1 and 2')", null);
                    return;
                }

                if (qB6a.equals("000000000000")) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.a", null);
                    return;
                }

                if ((radio24.getValue() == true) && (qB6b == null)) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.b", null);
                    return;
                }
                if ((radio24.getValue() == true) && (qB6b != null)) {
                    qB6b.toString().replaceAll(" ", "--");
                }

                quesB6c[0] = radio25.getValue();
                quesB6c[1] = radio26.getValue();
                qB6c = "";
                for (int i = 0; i < 2; i++) {
                    switch (new Boolean(quesB6c[i]).toString().charAt(0)) {
                        case 't':
                            qB6c = qB6c + "1";
                            break;
                        case 'f':
                            qB6c = qB6c + "0";
                            break;
                    }
                }
                if (qB6c.equals("00")) {
                    MessageBox.info("Missing answer", "Provide your answer to question B.6.c", null);
                    return;
                }

                //WicidXml util function might be used at a later stage...

                storeDocumentInfo();
                //data saved into a single string with each data varieable seperated by ("_")

                //rulesAndSyllabusTwoData = qB5a+"_"+qB5b+"_"+qB6a+"_"+qB6b+"_"+qB6c;

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=" + "rulesandsyllabustwo" + "&formdata=" + rulesAndSyllabusTwoData + "&docid=" + Constants.docid;

                createDocument(url);

                if (oldSubsidyRequirements == null) {
                    SubsidyRequirements subsidyRequirements = new SubsidyRequirements(RulesAndSyllabusTwo.this);
                    subsidyRequirements.show();
                    rulesAndSyllabusTwoDialog.hide();
                } else {
                    oldSubsidyRequirements.show();
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
                storeDocumentInfo();
            }
        });

        forwardButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                ForwardTo forwardToDialog = new ForwardTo();
                forwardToDialog.show();
                storeDocumentInfo();
            }
        });

        mainForm.addButton(backButton);
        mainForm.addButton(saveButton);
        mainForm.addButton(forwardButton);
        mainForm.setButtonAlign(HorizontalAlignment.RIGHT);

        rulesAndSyllabusTwoDialog.setBodyBorder(false);
        rulesAndSyllabusTwoDialog.setHeading("Section B: Rules and Syllabus Book- Page Two");
        rulesAndSyllabusTwoDialog.setWidth(825);
        rulesAndSyllabusTwoDialog.setHeight(680);
        rulesAndSyllabusTwoDialog.setHideOnButtonClick(true);
        rulesAndSyllabusTwoDialog.setButtons(Dialog.CLOSE);
        rulesAndSyllabusTwoDialog.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusTwoDialog.setHideOnButtonClick(true);
        rulesAndSyllabusTwoDialog.setButtons(Dialog.CLOSE);
        rulesAndSyllabusTwoDialog.setButtonAlign(HorizontalAlignment.RIGHT);
        rulesAndSyllabusTwoDialog.setHideOnButtonClick(true);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);
        rulesAndSyllabusTwoDialog.setButtonAlign(HorizontalAlignment.LEFT);

        rulesAndSyllabusTwoDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        rulesAndSyllabusTwoDialog.add(mainForm);

        //setDepartment();
    }

    public void storeDocumentInfo() {
        WicidXML wicidxml = new WicidXML("rulesAndSyllabusTwo");
        wicidxml.addElement("qB5a", qB5a);
        wicidxml.addElement("qB5b", qB5b);
        wicidxml.addElement("qB6a", qB6a);
        try {
            wicidxml.addElement("qB6b", qB6b);
        } catch (NullPointerException npe) {
            wicidxml.addElement("qB6b", "");
        }
        wicidxml.addElement("qB6c", qB6c);
        rulesAndSyllabusTwoData = wicidxml.getXml();
    }

    public void show() {
        rulesAndSyllabusTwoDialog.show();
    }

    public void setOldRulesAndSyllabusTwo(SubsidyRequirements oldSubsidyRequirements) {
        this.oldSubsidyRequirements = oldSubsidyRequirements;
    }

    public void setSelectedFolder(ModelData selectedFolder) {
        this.selectedFolder = selectedFolder;
        topicField.setValue((String) this.selectedFolder.get("id"));
        topicField.setToolTip((String) this.selectedFolder.get("id"));
    }

    private void createDocument(String url) {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new document", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {
                        /*if (oldOverView == null) {

                        Constants.docid = resp[1];
                        OverView overView = new OverView(NewCourseProposalDialog.this);
                        overView.show();
                        newDocumentDialog.hide();
                        } else {
                        oldOverView.show();
                        newDocumentDialog.hide();

                        }*/
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
                + "?module=wicid&action=getFormData&formname=rulesAndSyllabusTwo&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get rulesAndSyllabusTwo data", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    String data = response.getText();

                    String qB5a = Util.getTagText(data, "qB5a");
                    if (qB5a != null) {
                        for (int i = 0; i < 9; i++) {
                            if (qB5a.charAt(i) == '0') {
                                quesB5a[i] = false;
                            }
                            if (qB5a.charAt(i) == '1') {
                                quesB5a[i] = true;
                            }
                        }
                        radio4.setValue(quesB5a[0]);
                        radio5.setValue(quesB5a[1]);
                        radio6.setValue(quesB5a[2]);
                        radio7.setValue(quesB5a[3]);
                        radio8.setValue(quesB5a[4]);
                        radio9.setValue(quesB5a[5]);
                        radio10.setValue(quesB5a[6]);
                        radio11.setValue(quesB5a[7]);
                        radio12.setValue(quesB5a[8]);
                    } else {
                        radio4.setValue(true);
                        radio5.setValue(false);
                        radio6.setValue(false);
                        radio7.setValue(false);
                        radio8.setValue(false);
                        radio9.setValue(false);
                        radio10.setValue(false);
                        radio11.setValue(false);
                        radio12.setValue(false);
                    }
                    String qB5b = Util.getTagText(data, "qB5b");
                    questionB5b.setValue(qB5b);
                    /*<rulesAndSyllabusTwo><qB5a>010000000</qB5a><qB5b>1.1</qB5b><qB6a></qB6a><qB6b>null000</qB6b><qB6c>null0000</qB6c></rulesAndSyllabusTwo>*/
                    String qB6a = Util.getTagText(data, "qB6a");
                    if (qB6a != null) {
                        for (int i = 0; i < 12; i++) {
                            if (qB6a.charAt(i) == '0') {
                                quesB6a[i] = false;
                            }
                            if (qB6a.charAt(i) == '1') {
                                quesB6a[i] = true;
                            }
                        }
                        radio13.setValue(quesB6a[0]);
                        radio14.setValue(quesB6a[1]);
                        radio15.setValue(quesB6a[2]);
                        radio16.setValue(quesB6a[3]);
                        radio17.setValue(quesB6a[4]);
                        radio18.setValue(quesB6a[5]);
                        radio19.setValue(quesB6a[6]);
                        radio20.setValue(quesB6a[7]);
                        radio21.setValue(quesB6a[8]);
                        radio22.setValue(quesB6a[9]);
                        radio23.setValue(quesB6a[10]);
                        radio24.setValue(quesB6a[11]);
                    } else {
                        radio13.setValue(true);
                        radio14.setValue(false);
                        radio15.setValue(false);
                        radio16.setValue(false);
                        radio17.setValue(false);
                        radio18.setValue(false);
                        radio19.setValue(false);
                        radio20.setValue(false);
                        radio21.setValue(false);
                        radio22.setValue(false);
                        radio23.setValue(false);
                        radio24.setValue(false);
                    }

                    String qB6b = Util.getTagText(data, "qB6b");
                    questionB6b.setValue(qB6b);

                    String qB6c = Util.getTagText(data, "qB6c");
                    if (qB6c != null) {
                        for (int i = 0; i < 2; i++) {
                            if (qB6c.charAt(i) == '0') {
                                quesB6c[i] = false;
                            }
                            if (qB6c.charAt(i) == '1') {
                                quesB6c[i] = true;
                            }
                        }
                        radio25.setValue(quesB6c[0]);
                        radio26.setValue(quesB6c[1]);
                    } else {
                        radio25.setValue(true);
                        radio26.setValue(false);
                    }
                    /*String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {

                    } else {
                    MessageBox.info("Error", "Error occured on the server. Cannot get overview data", null);
                    }*/

                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get rulesAndSyllabusTwo data", null);
        }
    }
}
