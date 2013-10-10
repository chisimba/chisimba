/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.data.BaseTreeLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.data.TreeLoader;
import com.extjs.gxt.ui.client.data.XmlReader;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import com.extjs.gxt.ui.client.store.TreeStore;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.Dialog;
import com.extjs.gxt.ui.client.widget.LayoutContainer;

import com.extjs.gxt.ui.client.widget.layout.BorderLayout;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.treepanel.TreePanel;
import com.google.gwt.core.client.GWT;
import com.google.gwt.http.client.RequestBuilder;
import java.util.List;

/**
 *
 * @author davidwaf
 */
public class PermissionsFrame {

    private TreePanel<ModelData> tree;
    private LayoutContainer container = new LayoutContainer();
    private ContentPanel west = new ContentPanel();
    private ModelType treeFieldtype = new ModelType();
    private TreeStore<ModelData> folderStore;
    private TreeLoader<ModelData> folderLoader;
    private ModelData selectedFolder;
    private XmlReader<List<ModelData>> folderReader;
    private UserListPanel userListPanel;
    private Dialog permissionsDialog = new Dialog();
    
    public PermissionsFrame() {
        userListPanel = new UserListPanel(this);

        west.setHeading("Folders");
        container.setStyleAttribute("margin", "20px");
        container.setWidth(800);
        container.setHeight(450);
        container.setBorders(false);
        container.setLayout(new BorderLayout());

      
        BorderLayoutData westData = new BorderLayoutData(LayoutRegion.WEST, 200, 100, 600);
        westData.setMargins(new Margins(5, 0, 5, 5));
        westData.setSplit(true);


        treeFieldtype.setRecordName("item");
        treeFieldtype.setRoot("items");
        treeFieldtype.addField("id", "@id");
        treeFieldtype.addField("name", "@name");
        treeFieldtype.addField("folder", "@folder");
        loadFolderList();
        tree.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedFolder = md.getSelectedItem();
                        userListPanel.refreshUsers((String) selectedFolder.get("id"));
                    }
                });
        container.add(west, westData);

        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER, 600, 100, 800);
        centerData.setMargins(new Margins(5, 0, 5, 5));
        centerData.setSplit(true);

        container.add(userListPanel, centerData);

        permissionsDialog.setBodyBorder(false);
        permissionsDialog.setHeading("Folder permissions");
        permissionsDialog.setWidth(800);
        permissionsDialog.setHeight(500);
        permissionsDialog.setHideOnButtonClick(true);
        permissionsDialog.setButtons(Dialog.CLOSE);

        permissionsDialog.add(container);
    }

    public void show() {
        permissionsDialog.show();
    }

    private void loadFolderList() {
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() +Constants.MAIN_URL_PATTERN+ "?module=wicid&action=getFolders");
        HttpProxy<ListLoadResult<ModelData>> proxy = new HttpProxy<ListLoadResult<ModelData>>(builder);
        folderReader = new XmlReader<List<ModelData>>(treeFieldtype);
        folderLoader = new BaseTreeLoader<ModelData>(proxy, folderReader) {

            @Override
            public boolean hasChildren(ModelData parent) {
                return "true".equals(parent.get("folder"));
            }

            @Override
            protected Object prepareLoadConfig(Object config) {
                return super.prepareLoadConfig(config);
            }
        };

        folderStore = new TreeStore<ModelData>(folderLoader);
        tree = new TreePanel<ModelData>(folderStore);
        tree.setDisplayProperty("name");
        tree.setWidth(315);
        tree.setHeight("100%");
        folderLoader.load();
        west.add(tree);
    }

    public String getSelectedFolder() {
        if (selectedFolder != null) {
            return selectedFolder.get("id");
        }
        return null;
    }

    public void refreshUserList() {
        userListPanel.refreshUsers((String) selectedFolder.get("id"));
    }
}
