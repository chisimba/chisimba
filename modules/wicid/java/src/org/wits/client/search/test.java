package org.wits.client.search;

import com.extjs.gxt.ui.client.Style.Orientation;
import com.extjs.gxt.ui.client.Style.Scroll;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.LayoutContainer;
import com.extjs.gxt.ui.client.widget.Text;
import com.extjs.gxt.ui.client.widget.layout.FlowData;
import com.extjs.gxt.ui.client.widget.layout.RowData;
import com.extjs.gxt.ui.client.widget.layout.RowLayout;
import com.extjs.gxt.ui.client.widget.form.FormPanel;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.Style.HorizontalAlignment;

public class test extends LayoutContainer {
    private FormPanel mainForm = new FormPanel();
    private Dialog newDocumentDialog = new Dialog();

    public test() {
        createGUI();
    }

    public void createGUI() {
        
        mainForm.setFrame(false);
        mainForm.setBodyBorder(false);
        mainForm.setWidth(480);

        setScrollMode(Scroll.AUTOY);
        ContentPanel panel = new ContentPanel();
        Text label1 = new Text("Test Label 1");
        Text label2 = new Text("Test Label 2");
        
        panel.setHeading("RowLayout: Orientation set to horizontal");
        panel.setLayout(new RowLayout(Orientation.HORIZONTAL));
        panel.setSize(400, 300);
        panel.setFrame(true);
        panel.setCollapsible(true);

        label1 = new Text("Test Label 1");
        label1.addStyleName("pad-text");
        label1.setStyleAttribute("backgroundColor", "white");
        label1.setBorders(true);

        label2 = new Text("Test Label 2");
        label2.addStyleName("pad-text");
        label2.setStyleAttribute("backgroundColor", "white");
        label2.setBorders(true);

        panel.add(label1, new RowData(-1, 1, new Margins(4)));
        panel.add(label2, new RowData(1, 1, new Margins(4, 0, 4, 0)));
            
        mainForm.add(panel, new FlowData(10));


        newDocumentDialog.setBodyBorder(false);
        newDocumentDialog.setHeading("Test");
        newDocumentDialog.setWidth(500);
        newDocumentDialog.setHeight(500);
        newDocumentDialog.setHideOnButtonClick(true);
        newDocumentDialog.setButtons(Dialog.CLOSE);
        newDocumentDialog.setButtonAlign(HorizontalAlignment.LEFT);
        newDocumentDialog.add(mainForm);
    }

    public void show() {
        newDocumentDialog.show();
    }
}
