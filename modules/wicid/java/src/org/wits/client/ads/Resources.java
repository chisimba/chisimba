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
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import org.wits.client.Constants;
import org.wits.client.util.Util;
import org.wits.client.util.WicidXML;

/**
 *
 * @author nguni
 */
public class Resources {

    private Dialog newResourcesDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    /*private final TextField<String> E1a = new TextField<String>();
    private final TextField<String> E1b = new TextField<String>();
    private final TextField<String> E2a = new TextField<String>();
    private final TextField<String> E2b = new TextField<String>();
    private final TextField<String> E2c = new TextField<String>();
    private final TextField<String> E3a = new TextField<String>();
    private final TextField<String> E3b = new TextField<String>();
    private final TextField<String> E3c = new TextField<String>();
    private final TextField<String> E4 = new TextField<String>();
    private final TextField<String> E5a = new TextField<String>();
    private final TextField<String> E5b = new TextField<String>();*/
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private String title = "";
    private OutcomesAndAssessmentThree outcomesAndAssessmentThree;
    // private OutcomesAndAssessmentTwo outcomesAndAssessmentTwo;
    private CollaborationAndContracts collaborationAndContracts;
    private CollaborationAndContracts oldCollaborationAndContracts;
    private String resourcesData, qE1a, qE1b, qE2a, qE2b, qE2c, qE3a, qE3b, qE3c, qE4, qE5a, qE5b;
    private final TextArea E1a = new TextArea();
    private final TextArea E1b = new TextArea();
    private final TextArea E2a = new TextArea();
    private final TextArea E2b = new TextArea();
    private final TextArea E2c = new TextArea();
    private final TextArea E3a = new TextArea();
    private final TextArea E3b = new TextArea();
    private final TextArea E3c = new TextArea();
    private final TextArea E4 = new TextArea();
    private final TextArea E5a = new TextArea();
    private final TextArea E5b = new TextArea();

    public Resources(OutcomesAndAssessmentThree outcomesAndAssessmentThree) {
        this.outcomesAndAssessmentThree = outcomesAndAssessmentThree;
        createUI();
        getFormData();
    }

    public Resources(CollaborationAndContracts collaborationAndContracts) {
        this.collaborationAndContracts = collaborationAndContracts;
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(700);
        mainForm.setLabelWidth(300);

        E1a.setFieldLabel("E.1.a Is there currently adequate teaching capacity with regard to the introduction of the course/unit?");
        E1a.setAllowBlank(false);
        E1a.setName("E1a");
        E1a.setHeight(50);

        E1b.setFieldLabel("E.1.b Who will teach the course/unit?");
        E1b.setAllowBlank(false);
        E1b.setName("E1b");
        E1b.setHeight(50);

        E2a.setFieldLabel("E.2.a How many students will the course/unit attract?");
        E2a.setAllowBlank(false);
        E2a.setName("E2a");
        E2a.setHeight(50);

        E2b.setFieldLabel("E.2.a How has this been factored into the enrolment planning in your Faculty?");
        E2b.setAllowBlank(false);
        E2b.setName("E2b");
        E2b.setHeight(50);

        E2c.setFieldLabel("E.2.c How has it been determined if the course/unit is sustainable in the long term, or short term if of topical interest?");
        E2c.setAllowBlank(false);
        E2c.setName("E2c");
        E2c.setHeight(50);

        E3a.setFieldLabel("E.3.a Specify the space requirements for the course/unit.");
        E3a.setAllowBlank(false);
        E3a.setName("E3a");
        E3a.setHeight(50);

        E3b.setFieldLabel("E.3.b Specify the IT teaching resources required for the course/unit.");
        E3b.setAllowBlank(false);
        E3b.setName("E3b");
        E3b.setHeight(50);

        E3c.setFieldLabel("E.3.c Specify the library resources required to teach the course/unit.");
        E3c.setAllowBlank(false);
        E3c.setName("E3c");
        E3c.setHeight(50);

        E4.setFieldLabel("E.4 Does the School intend to offer the course/unit in addition to its current course/unit offering, or is the intention to eliminate an existing course/unit?");
        E4.setAllowBlank(false);
        E4.setName("E4");
        E4.setHeight(50);

        E5a.setFieldLabel("E.5.a Specify the name of the course/unit co-ordinator.");
        E5a.setAllowBlank(false);
        E5a.setName("E5a");
        E5a.setHeight(50);

        E5b.setFieldLabel("E.5.b State the Staff number of the course/unit coordinator (consult your Faculty Registrar)");
        E5b.setAllowBlank(false);
        E5b.setName("E5b");
        E5b.setHeight(50);
        
        mainForm.add(E1a, formData);
        mainForm.add(E1b, formData);
        mainForm.add(E2a, formData);
        mainForm.add(E2b, formData);
        mainForm.add(E2c, formData);
        mainForm.add(E3a, formData);
        mainForm.add(E3b, formData);
        mainForm.add(E3c, formData);
        mainForm.add(E4, formData);
        mainForm.add(E5a, formData);
        mainForm.add(E5b, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {


                if (E1a.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E1a", null);
                    return;
                }

                if (E1b.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E1b", null);
                    return;
                }

                if (E2a.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E2a", null);
                    return;
                }


                if (E2b.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E2b", null);
                    return;
                }


                if (E2c.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E2c", null);
                    return;
                }


                if (E3a.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E3a", null);
                    return;
                }


                if (E3b.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E3b", null);
                    return;
                }



                if (E3c.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E3c", null);
                    return;
                }




                if (E4.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E4", null);
                    return;
                }

                if (E5a.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E5a", null);
                    return;
                }


                if (E5b.getValue() == null) {
                    MessageBox.info("Missing answer", "Please provide an answer for E5b", null);
                    return;
                }

                title = E1a.getValue().toString();
                if (title.trim().equals("")) {
                    MessageBox.info("Missing answer", "Please provide an answer for E1a", null);
                    return;
                }
                storeDocumentInfo();

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=" + "resources" + "&formdata=" + resourcesData + "&docid=" + Constants.docid;

                createDocument(url);
                if (oldCollaborationAndContracts == null) {
                    CollaborationAndContracts collaborationAndContracts = new CollaborationAndContracts(Resources.this);
                    collaborationAndContracts.show();
                    newResourcesDialog.hide();
                } else {
                    oldCollaborationAndContracts.show();
                    newResourcesDialog.hide();
                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                outcomesAndAssessmentThree.setOldOutcomesAndAssessmentThree(Resources.this);
                outcomesAndAssessmentThree.show();
                newResourcesDialog.hide();
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

        newResourcesDialog.setBodyBorder(false);
        newResourcesDialog.setHeading("Section E: Resources");
        newResourcesDialog.setWidth(700);
        //newResourcesDialog.setHeight(450);
        newResourcesDialog.setHideOnButtonClick(true);
        newResourcesDialog.setButtons(Dialog.CLOSE);
        newResourcesDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newResourcesDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        setDocumentInfo();
        newResourcesDialog.add(mainForm);
    }

    public void storeDocumentInfo() {

        qE1b = E1b.getValue();
        qE2a = E2a.getValue();
        qE2b = E2b.getValue();
        qE2c = E2c.getValue();
        qE3a = E3a.getValue();
        qE3b = E3b.getValue();
        qE3c = E3c.getValue();
        qE4 = E4.getValue();
        qE5a = E5a.getValue();
        qE5b = E5b.getValue();

        WicidXML wicidxml = new WicidXML("resources");
        wicidxml.addElement("qE1a", title);
        wicidxml.addElement("qE1b", qE1b);
        wicidxml.addElement("qE2a", qE2a);
        wicidxml.addElement("qE2b", qE2b);
        wicidxml.addElement("qE2c", qE2c);
        wicidxml.addElement("qE3a", qE3a);
        wicidxml.addElement("qE3b", qE3b);
        wicidxml.addElement("qE3c", qE3c);
        wicidxml.addElement("qE4", qE4);
        wicidxml.addElement("qE5a", qE5a);
        wicidxml.addElement("qE5b", qE5b);
        resourcesData = wicidxml.getXml();

    }

    public void setDocumentInfo() {
    }

    public void show() {
        newResourcesDialog.show();
    }

    public void setOldResources(CollaborationAndContracts oldCollaborationAndContracts) {
        this.oldCollaborationAndContracts = oldCollaborationAndContracts;
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
                + "?module=wicid&action=getFormData&formname=resources&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get resources data", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    String data = response.getText();

                    String qE1a = Util.getTagText(data, "qE1a");
                    E1a.setValue(qE1a);

                    String qE1b = Util.getTagText(data, "qE1b");
                    E1b.setValue(qE1b);

                    String qE2a = Util.getTagText(data, "qE2a");
                    E2a.setValue(qE2a);

                    String qE2b = Util.getTagText(data, "qE2b");
                    E2b.setValue(qE2b);

                    String qE2c = Util.getTagText(data, "qE2c");
                    E2c.setValue(qE2c);

                    String qE3a = Util.getTagText(data, "qE3a");
                    E3a.setValue(qE3a);

                    String qE3b = Util.getTagText(data, "qE3b");
                    E3b.setValue(qE3b);

                    String qE3c = Util.getTagText(data, "qE3c");
                    E3c.setValue(qE3c);

                    String qE4 = Util.getTagText(data, "qE4");
                    E4.setValue(qE4);

                    String qE5a = Util.getTagText(data, "qE5a");
                    E5a.setValue(qE5a);

                    String qE5b = Util.getTagText(data, "qE5b");
                    E5b.setValue(qE5b);

                    /*String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {

                    } else {
                    MessageBox.info("Error", "Error occured on the server. Cannot get overview data", null);
                    }*/
                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get resources data", null);
        }
    }
}
