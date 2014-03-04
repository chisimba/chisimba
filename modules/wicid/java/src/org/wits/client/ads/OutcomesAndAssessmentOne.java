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
import com.extjs.gxt.ui.client.widget.form.SimpleComboBox;
import com.extjs.gxt.ui.client.widget.Label;
import com.google.gwt.user.client.ui.Grid;
import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import com.extjs.gxt.ui.client.widget.form.ComboBox;
import com.extjs.gxt.ui.client.widget.form.LabelField;
import com.google.gwt.core.client.GWT;
import com.google.gwt.user.client.ui.HasHorizontalAlignment;
import com.google.gwt.user.client.ui.Widget;
import org.wits.client.Constants;
import org.wits.client.util.Util;
import org.wits.client.util.WicidXML;

/**
 *
 * @author Jacqueline
 */
public class OutcomesAndAssessmentOne {

    private Dialog outcomesAndAssessmentDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private SimpleComboBox<String> questionD1a = new SimpleComboBox<String>();
    private SimpleComboBox<String> questionD1b = new SimpleComboBox<String>();
    private SubsidyRequirements subsidyRequirements;
    private OutcomesAndAssessmentOne OutcomesAndAssessmentOne;
    private Grid questionD2 = new Grid(2, 3);
    private TextArea questionD3 = new TextArea();
    //private OutcomesAndAssessmentTwo outcomesAndAssessmentTwo;
    private OutcomesAndAssessmentTwo oldOutcomesAndAssessmentTwo;
    private String outcomesAndAssessmentOneData, qD1a, qD1b, qD2a, qD2b, qD2c, qD3;
    private TextArea D2a = new TextArea();
    private TextArea D2b = new TextArea();
    private TextArea D2c = new TextArea();

    public OutcomesAndAssessmentOne(SubsidyRequirements subsidyRequirements) {
        this.subsidyRequirements = subsidyRequirements;
        createUI();
        getFormData();
    }

    public OutcomesAndAssessmentOne(OutcomesAndAssessmentTwo oldOutcomesAndAssessmentTwo) {
        this.oldOutcomesAndAssessmentTwo = oldOutcomesAndAssessmentTwo;
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setHeight(400);
        mainForm.setWidth(700);
        mainForm.setLabelWidth(250);

        questionD1a.setFieldLabel("D.1.a. On which OLD NQF (National Qualifications "
                + "Framework) level (e.g. NQF 5, 6, 7 & 8) is the course/unit positioned?");
        questionD1a.add("NQF 5");
        questionD1a.add("NQF 6");
        questionD1a.add("NQF 7");
        questionD1a.add("NQF 8");
        questionD1a.setTriggerAction(ComboBox.TriggerAction.ALL);
        questionD1a.setEditable(false);
        questionD1a.setAllowBlank(false);
        questionD1a.setForceSelection(true);

        mainForm.add(questionD1a, formData);

        questionD1b.setFieldLabel("D.1.b. On which NEW NQF (National Qualifications "
                + "Framework) level (e.g. NQF 5, 6, 7, 8, 9 & 10) is the course/unit positioned?");
        questionD1b.add("NQF 5");
        questionD1b.add("NQF 6");
        questionD1b.add("NQF 7");
        questionD1b.add("NQF 8");
        questionD1b.add("NQF 9");
        questionD1b.add("NQF 10");
        questionD1b.setTriggerAction(ComboBox.TriggerAction.ALL);
        questionD1b.setEditable(false);
        questionD1b.setAllowBlank(false);
        questionD1b.setForceSelection(true);
        mainForm.add(questionD1b, formData);

        Label questionD2Label = new Label("D.2. Specify the course/unit oucomes, assessment "
                + "criteria and methods of assessment using the table provided.");
        mainForm.add(questionD2Label, formData);

        questionD2.setPixelSize(500, 80);
        questionD2.setBorderWidth(1);
        questionD2.getCellFormatter().setHorizontalAlignment(0, 0, HasHorizontalAlignment.ALIGN_CENTER);
        questionD2.getCellFormatter().setHorizontalAlignment(0, 1, HasHorizontalAlignment.ALIGN_CENTER);
        questionD2.getCellFormatter().setHorizontalAlignment(0, 2, HasHorizontalAlignment.ALIGN_CENTER);
        questionD2.setWidget(0, 0, new LabelField("Learning Outcomes of the Course/Unit"));
        questionD2.setWidget(0, 1, new LabelField("Assessment Criteria for the Learning Outcomes"));
        questionD2.setWidget(0, 2, new LabelField("Assessment Methods to be Used"));

        D2a.setWidth(215);
        D2b.setWidth(215);
        D2c.setWidth(215);

        questionD2.setWidget(1, 0, D2a);
        questionD2.setWidget(1, 1, D2b);
        questionD2.setWidget(1, 2, D2c);
        mainForm.add(questionD2, formData);
        mainForm.add(new Label(), formData);


        questionD3.setFieldLabel("D.3. How do the course/unit outcomes contribute "
                + "to the acheivement of the overall qualification/programme outcomes?");
        mainForm.add(questionD3, formData);




        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        //function to ensure that all the fields are filled and the form is
        //completed before the user moves to the next form
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if ((questionD1a.getValue() == null) && (questionD1b.getValue() == null)) {
                    MessageBox.info("Missing answer", "Provide an answer to question D.1.a and question D.1.b", null);
                    return;
                }

                if (questionD1a.getValue() == null) {
                    MessageBox.info("Missing answer", "Provide an answer to question D.1.a", null);
                    return;
                }

                if (questionD1b.getValue() == null) {
                    MessageBox.info("Missing answer", "Provide an answer to question D.1.b", null);
                    return;
                }

                if ((D2a.getValue() == null) && (D2b.getValue() == null) && (D2c.getValue() == null)) {
                    MessageBox.info("Missing answer", "Fill in the table for question D.2", null);
                    return;
                }

                if ((D2a.getValue() == null) || (D2b.getValue() == null) || (D2c.getValue() == null)) {
                    MessageBox.info("Missing answer", "Missing an answer for question D.2", null);
                    return;
                }

                if (questionD3.getValue() == null) {
                    MessageBox.info("Missing answer", "Provide an answer to question D.3", null);
                    return;
                }


                storeDocumentInfo();

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=" + "outcomesandassessmentone" + "&formdata=" + outcomesAndAssessmentOneData + "&docid=" + Constants.docid;

                createDocument(url);

                if (oldOutcomesAndAssessmentTwo == null) {

                    OutcomesAndAssessmentTwo outcomesAndAssessmentTwo = new OutcomesAndAssessmentTwo(OutcomesAndAssessmentOne.this);
                    outcomesAndAssessmentTwo.show();
                    outcomesAndAssessmentDialog.hide();
                } else {

                    oldOutcomesAndAssessmentTwo.show();
                    outcomesAndAssessmentDialog.hide();

                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                subsidyRequirements.setOldSubsdyRequirements(OutcomesAndAssessmentOne.this);
                subsidyRequirements.show();
                outcomesAndAssessmentDialog.hide();
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
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);

        outcomesAndAssessmentDialog.setBodyBorder(false);
        outcomesAndAssessmentDialog.setHeading("Section D: Outcomes and Assessment-Page one");
        outcomesAndAssessmentDialog.setWidth(700);
        outcomesAndAssessmentDialog.setHeight(470);
        outcomesAndAssessmentDialog.setHideOnButtonClick(true);
        outcomesAndAssessmentDialog.setButtons(Dialog.CLOSE);
        outcomesAndAssessmentDialog.setButtonAlign(HorizontalAlignment.LEFT);
        outcomesAndAssessmentDialog.setHideOnButtonClick(true);

        outcomesAndAssessmentDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        outcomesAndAssessmentDialog.add(mainForm);

        //setDepartment();
    }

    public void storeDocumentInfo() {
        //outcomesAndAssessmentOneData, qD1a, qD1b, q2, qD3
        qD1a = questionD1a.getValue().getValue();
        qD1b = questionD1b.getValue().getValue();
        qD2a = ((TextArea) questionD2.getWidget(1, 0)).getValue();
        qD2b = ((TextArea) questionD2.getWidget(1, 0)).getValue();
        qD2c = ((TextArea) questionD2.getWidget(1, 0)).getValue();
        qD3 = questionD3.getValue().toString();

        WicidXML wicidxml = new WicidXML("outcomesandassessmentone");
        wicidxml.addElement("qD1a", qD1a);
        wicidxml.addElement("qD1b", qD1b);
        try {
            wicidxml.addElement("qD2a", qD2a);
            wicidxml.addElement("qD2b", qD2b);
            wicidxml.addElement("qD2c", qD2c);
        } catch (NullPointerException npe) {
            wicidxml.addElement("qD2a", "qD2a");
            wicidxml.addElement("qD2b", "qD2b");
            wicidxml.addElement("qD2c", "qD2c");
        }

        wicidxml.addElement("qD3", qD3);
        outcomesAndAssessmentOneData = wicidxml.getXml();
    }

    public void show() {
        outcomesAndAssessmentDialog.show();
    }

    public void setOldOutComesAndAssessmentOne(OutcomesAndAssessmentTwo oldOutcomesAndAssessmentTwo) {
        this.oldOutcomesAndAssessmentTwo = oldOutcomesAndAssessmentTwo;

    }


    /*  public void setSelectedFolder(ModelData selectedFolder) {
    this.selectedFolder = selectedFolder;
    topicField.setValue((String) this.selectedFolder.get("id"));
    topicField.setToolTip((String) this.selectedFolder.get("id"));
    }
     */
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
                + "?module=wicid&action=getFormData&formname=outcomesandassessmentone&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get outcomesAndAssessmentOne data", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    /*<outcomesandassessmentone><qD1a>NQF 6</qD1a><aD1b>NQF 6</aD1b><q2a>2</q2a><q2b>2</q2b>
                    <q2c>2</q2c><qD3>1</qD3></outcomesandassessmentone>*/

                    String data = response.getText();

                    String qD1a = Util.getTagText(data, "qD1a");
                    questionD1a.setSimpleValue(qD1a);

                    String qD1b = Util.getTagText(data, "qD1b");
                    questionD1b.setSimpleValue(qD1b);

                    String qD2a = Util.getTagText(data, "qD2a");
                    D2a.setValue(qD2a);

                    String qD2b = Util.getTagText(data, "qD2b");
                    D2b.setValue(qD2b);

                    String qD2c = Util.getTagText(data, "qD2c");
                    D2c.setValue(qD2c);

                    questionD2.setWidget(1, 0, D2a);
                    questionD2.setWidget(1, 1, D2b);
                    questionD2.setWidget(1, 2, D2c);

                    String qD3 = Util.getTagText(data, "qD3");
                    questionD3.setValue(qD3);
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get outcomesAndAssessmentOne data", null);
        }
    }
}
