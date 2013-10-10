/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;
import java.util.Arrays;

import com.extjs.gxt.ui.client.Style.HorizontalAlignment;
import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.MenuEvent;
import com.extjs.gxt.ui.client.event.MessageBoxEvent;
import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.LayoutContainer;
import com.extjs.gxt.ui.client.widget.MessageBox;

import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.button.ButtonBar;
import com.extjs.gxt.ui.client.widget.form.CheckBox;
import com.extjs.gxt.ui.client.widget.grid.CellEditor;
import com.extjs.gxt.ui.client.widget.grid.CheckBoxSelectionModel;
import com.extjs.gxt.ui.client.widget.grid.ColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnModel;
import com.extjs.gxt.ui.client.widget.grid.EditorGrid;
import com.extjs.gxt.ui.client.widget.layout.FitLayout;
import com.extjs.gxt.ui.client.widget.menu.Menu;
import com.extjs.gxt.ui.client.widget.menu.MenuItem;
import com.extjs.gxt.ui.client.widget.menu.SeparatorMenuItem;
import com.extjs.gxt.ui.client.widget.toolbar.ToolBar;
import com.google.gwt.core.client.GWT;
import com.google.gwt.core.client.JsArray;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.user.client.Element;
import com.google.gwt.user.client.Window;


import java.util.ArrayList;
import java.util.List;

/**
 *
 * @author davidwaf
 */
public class RejectedDocsPanel extends LayoutContainer {

    private BaseListLoader<ListLoadResult<ModelData>> loader;
    private EditorGrid<ModelData> grid;
    private ColumnModel cm;
    private Button editButton = new Button("Edit");
    private Button approveButton = new Button("Approve");
    private Button refreshButton = new Button("Refresh");
    private List<ModelData> selectedRows;
    private CheckBoxSelectionModel<ModelData> sm;
    private boolean removeUsersDone = false;
    private ListStore<ModelData> store;
    private Menu contextMenu = new Menu();
    private Main main;
    private String defaultParams = "";

    public RejectedDocsPanel(Main main) {
        super();
        this.main = main;
        editButton.setEnabled(false);
        approveButton.setEnabled(false);
    }

    @Override
    protected void onRender(Element parent, int index) {
        super.onRender(parent, index);
        defaultParams = "?module=wicid&action=getrejecteddocuments&mode=" + main.getMode();

        List<ColumnConfig> columns = new ArrayList<ColumnConfig>();
        sm = new CheckBoxSelectionModel<ModelData>();
        columns.add(new ColumnConfig("Owner", "Owner", 170));
        columns.add(new ColumnConfig("RefNo", "RefNo", 100));
        columns.add(new ColumnConfig("Title", "Title", 150));
        columns.add(new ColumnConfig("Topic", "Topic", 100));
        columns.add(new ColumnConfig("Date", "Date", 100));
        columns.add(new ColumnConfig("Attachment", "Attachment", 100));
        

        // create the column model
        cm = new ColumnModel(columns);
        editButton.setEnabled(false);
        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("documents");
        type.addField("userid", "userid");
        type.addField("group", "group");
        type.addField("Owner", "owner");
        type.addField("RefNo", "refno");
        type.addField("Title", "title");
        type.addField("Topic", "topic");
        type.addField("Date", "date");
        type.addField("Attachment", "attachmentstatus");


        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + "?module=wicid&action=getrejecteddocuments");
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);
        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        store = new ListStore<ModelData>(loader);
        grid = new EditorGrid<ModelData>(store, cm);
        grid.setBorders(true);
        grid.setLoadMask(true);

        grid.getView().setEmptyText("No documents found.");
        grid.setAutoExpandColumn("Title");
        grid.addPlugin(sm);
        grid.setSelectionModel(sm);
        grid.setContextMenu(contextMenu);
        grid.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedRows = md.getSelection();
                        approveButton.setEnabled(selectedRows.size() > 0);
                        editButton.setEnabled(selectedRows.size() > 0);
                    }
                });


        ContentPanel panel = new ContentPanel();
        panel.setFrame(true);
        panel.setButtonAlign(HorizontalAlignment.CENTER);
        ToolBar toolbar = new ButtonBar();

        refreshButton.setIconStyle("refresh");
        refreshButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                refreshDocumentList(defaultParams);
            }
        });
        toolbar.add(refreshButton);
        panel.setTopComponent(toolbar);

        panel.setFrame(false);
        panel.setBodyBorder(false);
        panel.setLayout(new FitLayout());
        panel.add(grid);

        panel.setWidth("100%");
        panel.setHeight(Window.getClientHeight());

        add(panel);
        refreshDocumentList(defaultParams);
    }

    public void refreshDocumentList(String params) {
        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("documents");
        type.addField("userid", "userid");
        type.addField("group", "group");
        type.addField("docid", "docid");
        type.addField("Owner", "owner");
        type.addField("RefNo", "refno");
        type.addField("Title", "title");
        type.addField("Topic", "topic");
        type.addField("Department", "department");
        type.addField("Telephone", "telephone");
        type.addField("Date", "date");
        type.addField("Attachment", "attachmentstatus");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + params);
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        store = new ListStore<ModelData>(loader);

        grid.reconfigure(store, cm);
        loader.load();

    }
}
