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
import com.extjs.gxt.ui.client.widget.form.CheckBox;
import com.extjs.gxt.ui.client.widget.form.CheckBoxGroup;
import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.LabelField;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.Response;
import org.wits.client.Constants;
import com.google.gwt.user.client.ui.Grid;
import com.google.gwt.user.client.ui.HasHorizontalAlignment;
import com.google.gwt.user.client.ui.HasVerticalAlignment;
import org.wits.client.util.Util;
import org.wits.client.util.WicidXML;

/**
 *
 * @author Jacqueline
 */
public class OutcomesAndAssessmentTwo {

    private Dialog outcomesAndAssessmentTwoDialog = new Dialog();
    private ModelData selectedFolder;
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private Button saveButton = new Button("Next");
    private Button backButton = new Button("Back");
    private Button forwardButton = new Button("Forward to...");
    private OutcomesAndAssessmentTwo outcomesAndAssessmentTwo;
    private CheckBoxGroup questionD4 = new CheckBoxGroup();
    private OutcomesAndAssessmentThree oldOutcomesAndAssessmentThree;
    private OutcomesAndAssessmentOne outcomesAndAssessmentOne;
    private OutcomesAndAssessmentOne oldOutcomesAndAssessmentOne;
    private String outcomesAndAssessmentTwoData;
    private String qD4a, qD4b, qD4c, qD4d, qD4e, qD4f, qD4g, qD4h;
    private CheckBox questionD4_1 = new CheckBox();
    private CheckBox questionD4_2 = new CheckBox();
    private CheckBox questionD4_3 = new CheckBox();
    private CheckBox questionD4_4 = new CheckBox();
    private CheckBox questionD4_5 = new CheckBox();
    private CheckBox questionD4_6 = new CheckBox();
    private CheckBox questionD4_7 = new CheckBox();
    private CheckBox questionD4_8 = new CheckBox();

    public OutcomesAndAssessmentTwo(OutcomesAndAssessmentOne outcomesAndAssessmentOne) {
        this.outcomesAndAssessmentOne = outcomesAndAssessmentOne;
        createUI();
        getFormData();
    }

    public OutcomesAndAssessmentTwo(OutcomesAndAssessmentThree oldOutcomesAndAssessmentThree) {
        this.oldOutcomesAndAssessmentThree = oldOutcomesAndAssessmentThree;
        createUI();
    }

    public OutcomesAndAssessmentTwo() {
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setHeight(530);
        mainForm.setWidth(800);
        mainForm.setLabelWidth(250);

        LabelField D41 = new LabelField();
        D41.setText("Identify and solve problems in which responses display "
                + "that responsible decisions using critical and creative thinking have been made.");
        D41.setWidth(500);

        LabelField D42 = new LabelField();
        D42.setText("Work effectively with others as a member of a team, "
                + "group, organisation, community.");
        D42.setWidth(500);

        LabelField D43 = new LabelField();
        D43.setText("Organise and manage oneself and one’s activities "
                + "responsibly and effectively.");
        D43.setWidth(500);

        LabelField D44 = new LabelField();
        D44.setText("Collect, analyse, organise and critically evaluate "
                + "information.");
        D44.setWidth(500);

        LabelField D45 = new LabelField();
        D45.setText("Communicate effectively using visual, mathematical and/or"
                + " language skills in the modes of oral and/ or written presentation.");
        D45.setWidth(500);

        LabelField D46 = new LabelField();
        D46.setText("Use science and technology effectively and critically, "
                + "showing responsibility towards the environment and health of others.");
        D46.setWidth(500);

        LabelField D47 = new LabelField();
        D47.setText("Demonstrate an understanding of the world as a set of "
                + "related systems by recognising that problem-solving contexts do not exist "
                + "in isolation.");
        D47.setWidth(500);

        LabelField D48 = new LabelField();
        D48.setText("In order to contribute to the full personal development "
                + "of each learner and the social economic development of the society at large, "
                + "it must be the intention underlying any programme of learning to make an "
                + "individual aware of the importance of:");
        LabelField D48_1 = new LabelField("-   Reflecting on and exploring a variety of strategies to learn more effectively;");
        LabelField D48_2 = new LabelField("-   Participating as responsible citizens in the life of local, national and global communities;");
        LabelField D48_3 = new LabelField("-   Being culturally and aesthetically sensitive across a range of social contexts;");
        LabelField D48_4 = new LabelField("-   Exploring education and career opportunities; and");
        LabelField D48_5 = new LabelField("-   Developing entrepreneurial opportunities.");
        D48.setWidth(500);



        questionD4.setFieldLabel("D.4. Specify the critical cross-field outcomes "
                + "(CCFOs) integrated into the course/unit using the list provided.");
        mainForm.add(questionD4, formData);


        Grid q4 = new Grid(13, 3);
        q4.getColumnFormatter().setWidth(0, "250px");
        q4.getColumnFormatter().setWidth(1, "20px");
        int r = 0;
        while (r < 13) {
            q4.getCellFormatter().setVerticalAlignment(r, 1, HasVerticalAlignment.ALIGN_TOP);
            r++;
        }

        q4.setWidget(0, 1, questionD4_1);
        q4.setWidget(1, 1, questionD4_2);
        q4.setWidget(2, 1, questionD4_3);
        q4.setWidget(3, 1, questionD4_4);
        q4.setWidget(4, 1, questionD4_5);
        q4.setWidget(5, 1, questionD4_6);
        q4.setWidget(6, 1, questionD4_7);
        q4.setWidget(7, 1, questionD4_8);
        q4.setWidget(0, 2, D41);
        q4.setWidget(1, 2, D42);
        q4.setWidget(2, 2, D43);
        q4.setWidget(3, 2, D44);
        q4.setWidget(4, 2, D45);
        q4.setWidget(5, 2, D46);
        q4.setWidget(6, 2, D47);
        q4.setWidget(7, 2, D48);
        q4.setWidget(8, 2, D48_1);
        q4.setWidget(9, 2, D48_2);
        q4.setWidget(10, 2, D48_3);
        q4.setWidget(11, 2, D48_4);
        q4.setWidget(12, 2, D48_5);


        mainForm.add(q4, formData);


        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        //function to ensure that all the fields are filled and the form is
        //completed before the user moves to the next form
        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

                if (questionD4_1.getValue() == false && questionD4_2.getValue() == false
                        && questionD4_3.getValue() == false && questionD4_4.getValue() == false
                        && questionD4_5.getValue() == false && questionD4_6.getValue() == false
                        && questionD4_7.getValue() == false && questionD4_8.getValue() == false) {
                    MessageBox.info("Missing answer", "Please check at "
                            + "least one Critical Cross-Field Outcome (CCFO)", null);
                    return;
                }
                //qD4a = questionD4.getValue().toString();

                storeDocumentInfo();

                String url =
                        GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN
                        + "?module=wicid&action=saveFormData&formname=" + "outcomesandassessmenttwo" + "&formdata=" + outcomesAndAssessmentTwoData + "&docid=" + Constants.docid;

                createDocument(url);

                if (oldOutcomesAndAssessmentThree == null) {

                    OutcomesAndAssessmentThree outcomesAndAssessment2 = new OutcomesAndAssessmentThree(OutcomesAndAssessmentTwo.this);
                    outcomesAndAssessment2.show();
                    outcomesAndAssessmentTwoDialog.hide();
                } else {
                    oldOutcomesAndAssessmentThree.show();
                    outcomesAndAssessmentTwoDialog.hide();
                }
            }
        });

        backButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                outcomesAndAssessmentOne.setOldOutComesAndAssessmentOne(OutcomesAndAssessmentTwo.this);
                outcomesAndAssessmentOne.show();
                outcomesAndAssessmentTwoDialog.hide();
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

        outcomesAndAssessmentTwoDialog.setBodyBorder(false);
        outcomesAndAssessmentTwoDialog.setHeading("Section D: Outcomes and Assessment - Page Two");
        outcomesAndAssessmentTwoDialog.setWidth(800);
        outcomesAndAssessmentTwoDialog.setHeight(600);
        outcomesAndAssessmentTwoDialog.setHideOnButtonClick(true);
        outcomesAndAssessmentTwoDialog.setButtons(Dialog.CLOSE);
        outcomesAndAssessmentTwoDialog.setButtonAlign(HorizontalAlignment.LEFT);
        outcomesAndAssessmentTwoDialog.setHideOnButtonClick(true);

        outcomesAndAssessmentTwoDialog.getButtonById(Dialog.CLOSE).addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                storeDocumentInfo();
            }
        });

        outcomesAndAssessmentTwoDialog.add(mainForm);

        //setDepartment();
    }

    public void storeDocumentInfo() {
        qD4a = questionD4_1.getValue().toString();
        qD4b = questionD4_2.getValue().toString();
        qD4c = questionD4_3.getValue().toString();
        qD4d = questionD4_4.getValue().toString();
        qD4e = questionD4_5.getValue().toString();
        qD4f = questionD4_6.getValue().toString();
        qD4g = questionD4_7.getValue().toString();
        qD4h = questionD4_8.getValue().toString();

        WicidXML wicidxml = new WicidXML("outcomesAndAssessmentTwo");

        wicidxml.addElement("qD4a", qD4a);
        wicidxml.addElement("qD4b", qD4b);
        wicidxml.addElement("qD4c", qD4c);
        wicidxml.addElement("qD4d", qD4d);
        wicidxml.addElement("qD4e", qD4e);
        wicidxml.addElement("qD4f", qD4f);
        wicidxml.addElement("qD4g", qD4g);
        wicidxml.addElement("qD4h", qD4h);

        outcomesAndAssessmentTwoData = wicidxml.getXml();
    }

    public void setDocumentInfo() {
    }

    public void show() {
        outcomesAndAssessmentTwoDialog.show();
    }

    public void setOldOutcomesAndAssessmentTwo(OutcomesAndAssessmentThree oldOutcomesAndAssessmentThree) {
        this.oldOutcomesAndAssessmentThree = oldOutcomesAndAssessmentThree;
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
                + "?module=wicid&action=getFormData&formname=outcomesAndAssessmentTwo&docid=" + Constants.docid;
        RequestBuilder builder = new RequestBuilder(RequestBuilder.POST, url);

        try {

            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot get outcomesAndAssessmentTwo data", null);
                }

                public void onResponseReceived(Request request, Response response) {

                    String data = response.getText();

                    String qD4a = Util.getTagText(data, "qD4a");
                    questionD4_1.setValue(Boolean.parseBoolean(qD4a));

                    String qD4b = Util.getTagText(data, "qD4b");
                    questionD4_2.setValue(Boolean.parseBoolean(qD4b));

                    String qD4c = Util.getTagText(data, "qD4c");
                    questionD4_3.setValue(Boolean.parseBoolean(qD4c));

                    String qD4d = Util.getTagText(data, "qD4d");
                    questionD4_4.setValue(Boolean.parseBoolean(qD4d));

                    String qD4e = Util.getTagText(data, "qD4e");
                    questionD4_5.setValue(Boolean.parseBoolean(qD4e));

                    String qD4f = Util.getTagText(data, "qD4f");
                    questionD4_6.setValue(Boolean.parseBoolean(qD4f));
                    
                    String qD4g = Util.getTagText(data, "qD4g");
                    questionD4_7.setValue(Boolean.parseBoolean(qD4g));

                    String qD4h = Util.getTagText(data, "qD4h");
                    questionD4_8.setValue(Boolean.parseBoolean(qD4h));


                    /*String resp[] = response.getText().split("|");

                    if (resp[0].equals("")) {

                    } else {
                    MessageBox.info("Error", "Error occured on the server. Cannot get overview data", null);
                    }*/

                }
            });
        } catch (Exception e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot get outcomesAndAssessmentTwo data", null);
        }
    }
}
