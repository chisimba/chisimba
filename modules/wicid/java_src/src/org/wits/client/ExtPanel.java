/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.MessageBoxEvent;
import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.button.ButtonBar;
import com.extjs.gxt.ui.client.widget.form.TextField;
import com.extjs.gxt.ui.client.widget.grid.CheckBoxSelectionModel;
import com.extjs.gxt.ui.client.widget.grid.ColumnConfig;
import com.extjs.gxt.ui.client.widget.grid.ColumnModel;
import com.extjs.gxt.ui.client.widget.grid.EditorGrid;

import com.extjs.gxt.ui.client.widget.toolbar.ToolBar;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import java.util.ArrayList;
import java.util.List;

/**
 *
 * @author davidwaf
 */
public class ExtPanel {

    private BaseListLoader<ListLoadResult<ModelData>> loader;
    private EditorGrid<ModelData> grid;
    private ColumnModel cm;
    private TextField<String> nameField = new TextField<String>();
    private Button deleteButton = new Button("Delete");
    private Button addButton = new Button("Add");
    private Button cancelButton = new Button("Cancel");
    private List<ModelData> selectedRows;
    private CheckBoxSelectionModel<ModelData> sm;
    private Dialog extDialog = new Dialog();

    public ExtPanel() {

        extDialog.setBodyBorder(false);
        extDialog.setHeading("Allowed Extensions");
        extDialog.setWidth(800);
        extDialog.setHeight(420);
        extDialog.setHideOnButtonClick(true);
        extDialog.setButtons(Dialog.CLOSE);

        ToolBar toolbar = new ButtonBar();
        toolbar.add(addButton);
        toolbar.add(deleteButton);
        deleteButton.setEnabled(false);
        extDialog.setTopComponent(toolbar);


        List<ColumnConfig> columns = new ArrayList<ColumnConfig>();

        sm = new CheckBoxSelectionModel<ModelData>();
        columns.add(sm.getColumn());
        columns.add(new ColumnConfig("Ext", "Ext", 200));
        columns.add(new ColumnConfig("Description", "Description", 345));

        // create the column model
        cm = new ColumnModel(columns);

        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("exts");
        type.addField("id", "id");
        type.addField("Ext", "ext");
        type.addField("Description", "name");
        type.addField("delete", "delete");


        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() +Constants.MAIN_URL_PATTERN+ "?module=wicid&action=getfileextensions");
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        ListStore<ModelData> store = new ListStore<ModelData>(loader);
        grid = new EditorGrid<ModelData>(store, cm);
        grid.setBorders(true);
        grid.setLoadMask(true);
        grid.getView().setEmptyText("No  extensions found.");
        grid.setAutoExpandColumn("Description");
        grid.setAutoHeight(true);
        grid.addPlugin(sm);
        grid.setSelectionModel(sm);
        grid.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedRows = md.getSelection();
                        deleteButton.setEnabled(selectedRows.size() > 0);
                    }
                });


        cancelButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                extDialog.setVisible(false);
            }
        });

        addButton.setIconStyle("add");
        addButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                addExt();
            }
        });
        deleteButton.setIconStyle("delete");
        deleteButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                deleteExt();
            }
        });
        extDialog.add(grid);
        loader.load();
    }

    public void show() {
        extDialog.show();
    }

    private void deleteExt() {
        for (ModelData row : selectedRows) {
            RequestBuilder builder =
                    new RequestBuilder(RequestBuilder.POST, GWT.getHostPageBaseURL()
                    + "?module=wicid&action=deleteext&id="
                    + row.get("id"));

            try {
                Request request = builder.sendRequest(null, new RequestCallback() {

                    public void onError(Request request, Throwable exception) {
                        MessageBox.info("Error", "Error, cannot delete ext", null);
                    }

                    public void onResponseReceived(Request request, Response response) {
                        if (200 == response.getStatusCode()) {
                            sm.deselectAll();
                            refreshExts();

                        } else {
                            MessageBox.info("Error", "Error occured on the server. Cannot delete ext", null);
                        }
                    }
                });
            } catch (RequestException e) {
                MessageBox.info("Fatal Error", "Fatal Error: cannot add user", null);
            }
        }

    }

    private void addExt() {
        final MessageBox box = MessageBox.prompt("Extension", "Please enter ext, followed by description,"
                + "separated by comma e.g txt,Text documents:");
        box.addCallback(new Listener<MessageBoxEvent>() {

            public void handleEvent(MessageBoxEvent be) {
                String val = be.getValue();
                if (val != null) {
                    createNewExt(val);
                }
            }
        });

    }

    private void createNewExt(String extStr) {
        String exts[] = extStr.split(",");
        String ext = exts[0];
        String desc = exts[1];

        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.POST, GWT.getHostPageBaseURL()
               +Constants.MAIN_URL_PATTERN+ "?module=wicid&action=addfileextension&ext="
                + ext + "&desc=" + desc);

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot add extension", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        sm.deselectAll();
                        refreshExts();
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot add user", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot add user", null);
        }
    }

    private void refreshExts() {
        // defines the xml structure
        ModelType type = new ModelType();
        type.setRoot("exts");
        type.addField("id", "id");
        type.addField("Ext", "ext");
        type.addField("Description", "name");
        type.addField("delete", "delete");

        // use a http proxy to get the data
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() +Constants.MAIN_URL_PATTERN+ "?module=wicid&action=getfileextensions");
        HttpProxy<String> proxy = new HttpProxy<String>(builder);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type);

        loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);

        ListStore<ModelData> store = new ListStore<ModelData>(loader);

        grid.reconfigure(store, cm);
        loader.load();

    }
}
