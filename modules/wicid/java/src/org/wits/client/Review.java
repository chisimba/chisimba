package org.wits.client;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.form.ComboBox;
import com.extjs.gxt.ui.client.widget.form.ComboBox.TriggerAction;
import com.extjs.gxt.ui.client.widget.form.DateField;

import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.form.Radio;
import com.extjs.gxt.ui.client.widget.form.RadioGroup;
import com.extjs.gxt.ui.client.widget.form.TextArea;
import com.extjs.gxt.ui.client.widget.form.TextField;
import com.extjs.gxt.ui.client.widget.layout.BorderLayout;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FormData;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.i18n.client.DateTimeFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 *
 * @author nguni
 */
public class Review {
    private Dialog newResourcesDialog = new Dialog();
    private FormPanel mainForm = new FormPanel();
    private FormData formData = new FormData("-20");
    private final TextField<String> G1a = new TextField<String>();
    private final TextField<String> G1b = new TextField<String>();
    private final TextField<String> G2a = new TextField<String>();
    private final TextField<String> G2b = new TextField<String>();
    private final TextField<String> G3a = new TextField<String>();
    private final TextField<String> G3b = new TextField<String>();
    private final TextField<String> G4a = new TextField<String>();
    private final TextField<String> G4b = new TextField<String>();
    private Button saveButton = new Button("Save");
    
    public Review() {
        createUI();
    }

    private void createUI() {

        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(700);
        mainForm.setLabelWidth(400);

        G1a.setFieldLabel("G.1.a How will the course/unit syllabus be reviewed?");
        G1a.setAllowBlank(false);
        G1a.setName("G1a");

        G1b.setFieldLabel("G.1.b  How often will the course/unit syllabus be reviewed?");
        G1b.setAllowBlank(false);
        G1b.setName("G1b");

        G2a.setFieldLabel("G.2.a How will the integration of course/unit outcomes, syllabus, teaching methods and assessment methods be evaluated? ");
        G2a.setAllowBlank(false);
        G2a.setName("G2a");

        G2b.setFieldLabel("G.2.b How often will the above integration be evaluated?");
        G2b.setAllowBlank(false);
        G2b.setName("G2b");

        G3a.setFieldLabel("G.3.a How will the course/unit through-put rate be evaluated?");
        G3a.setAllowBlank(false);
        G3a.setName("G3a");

        G3b.setFieldLabel("G.3.b How often will the course/unit through-put rate be evaluated?");
        G3b.setAllowBlank(false);
        G3b.setName("G3b");

        G4a.setFieldLabel("G.4.a How will the teaching on the course/unit be evaluated from a student perspective and from the lecturer’s perspective?");
        G4a.setAllowBlank(false);
        G4a.setName("G4a");

        G4b.setFieldLabel("G.4.b How often will the teaching on the course/unit be evaluated from these two perspectives?");
        G4b.setAllowBlank(false);
        G4b.setName("G4b");

        mainForm.add(G1a, formData);
        mainForm.add(G1b, formData);
        mainForm.add(G2a, formData);
        mainForm.add(G2b, formData);
        mainForm.add(G3a, formData);
        mainForm.add(G3b, formData);
        mainForm.add(G4a, formData);
        mainForm.add(G4b, formData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER);
        centerData.setMargins(new Margins(0));


        saveButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

            }
        });
        mainForm.addButton(saveButton);
        mainForm.setButtonAlign(HorizontalAlignment.LEFT);

        newResourcesDialog.setBodyBorder(false);
        newResourcesDialog.setHeading("Section E: Resources");
        newResourcesDialog.setWidth(800);
        //newResourcesDialog.setHeight(450);
        newResourcesDialog.setHideOnButtonClick(true);
        newResourcesDialog.setButtons(Dialog.CLOSE);
        newResourcesDialog.setButtonAlign(HorizontalAlignment.LEFT);

        newResourcesDialog.add(mainForm);
    }

    public void show() {
        newResourcesDialog.show();
    }
}
