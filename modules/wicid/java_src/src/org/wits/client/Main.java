/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.wits.client;

import com.extjs.gxt.ui.client.Style.LayoutRegion;
import com.extjs.gxt.ui.client.Style.Scroll;
import com.extjs.gxt.ui.client.Style.SortDir;
import com.extjs.gxt.ui.client.data.BaseListLoader;
import com.extjs.gxt.ui.client.data.BaseTreeLoader;
import com.extjs.gxt.ui.client.data.HttpProxy;
import com.extjs.gxt.ui.client.data.JsonLoadResultReader;
import com.extjs.gxt.ui.client.data.ListLoadResult;
import com.extjs.gxt.ui.client.data.ModelData;
import com.extjs.gxt.ui.client.data.ModelType;
import com.extjs.gxt.ui.client.data.TreeLoader;
import com.extjs.gxt.ui.client.data.XmlReader;
import com.extjs.gxt.ui.client.event.ButtonEvent;
import com.extjs.gxt.ui.client.event.Events;
import com.extjs.gxt.ui.client.event.Listener;
import com.extjs.gxt.ui.client.event.MenuEvent;
import com.extjs.gxt.ui.client.event.MessageBoxEvent;
import com.extjs.gxt.ui.client.event.SelectionChangedEvent;
import com.extjs.gxt.ui.client.event.SelectionListener;
import com.extjs.gxt.ui.client.store.ListStore;
import com.extjs.gxt.ui.client.store.TreeStore;
import com.extjs.gxt.ui.client.util.Format;
import com.extjs.gxt.ui.client.util.Margins;
import com.extjs.gxt.ui.client.widget.ContentPanel;
import com.extjs.gxt.ui.client.widget.Label;
import com.extjs.gxt.ui.client.widget.LayoutContainer;
import com.extjs.gxt.ui.client.widget.ListView;
import com.extjs.gxt.ui.client.widget.MessageBox;
import com.extjs.gxt.ui.client.widget.TabItem;
import com.extjs.gxt.ui.client.widget.TabPanel;
import com.extjs.gxt.ui.client.widget.button.Button;
import com.extjs.gxt.ui.client.widget.form.TextField;

import com.extjs.gxt.ui.client.widget.layout.BorderLayout;
import com.extjs.gxt.ui.client.widget.layout.BorderLayoutData;
import com.extjs.gxt.ui.client.widget.layout.FitLayout;
import com.extjs.gxt.ui.client.widget.menu.Menu;
import com.extjs.gxt.ui.client.widget.menu.MenuItem;
import com.extjs.gxt.ui.client.widget.menu.SeparatorMenuItem;
import com.extjs.gxt.ui.client.widget.toolbar.SeparatorToolItem;
import com.extjs.gxt.ui.client.widget.toolbar.ToolBar;
import com.extjs.gxt.ui.client.widget.treepanel.TreePanel;
import com.google.gwt.core.client.GWT;
import com.google.gwt.core.client.JavaScriptObject;
import com.google.gwt.http.client.Request;
import com.google.gwt.http.client.RequestBuilder;
import com.google.gwt.http.client.RequestCallback;
import com.google.gwt.http.client.RequestException;
import com.google.gwt.http.client.Response;
import com.google.gwt.user.client.Window;

import java.util.List;
import org.wits.client.ads.*;

/**
 * Main entry point.
 *
 * @author davidwaf
 */
public class Main {

    private ListView<ModelData> view;
    private ContentPanel center = new ContentPanel();
    private String currentPath = "";
    private ModelType treeFieldtype = new ModelType();
    private XmlReader<List<ModelData>> folderReader;
    private TreePanel<ModelData> tree;
    private ContentPanel west = new ContentPanel();
    private TreeStore<ModelData> folderStore;
    private TreeLoader<ModelData> folderLoader;
    private ModelData selectedFolder;
    private ModelData selectedFile;
    private Button newDocumentButton = new Button("Register a Document");
    private Button newCourseProposalButton = new Button("Course Proposal");
    private PermissionsFrame permissionsFrame;
    private ExtPanel extPanel;
    private Button fileExtButton = new Button("File Extensions");
    private Button folderUserButton = new Button("Topic permissions");
    private Button newFolderButton = new Button("New Topic");
    private Button unapprovedDocsButton = new Button("Unapproved docs");
    private MenuItem uploadMenuItem = new MenuItem();
    private MenuItem removeFolderMenuItem = new MenuItem();
    private MenuItem newFolderMenuItem = new MenuItem();
    private MenuItem deleteFileMenuItem = new MenuItem();
    private MenuItem renameFileMenuItem = new MenuItem();
    private MenuItem downloadFileMenuItem = new MenuItem();
    private MenuItem editMenuItem = new MenuItem();
    private NewDocumentDialog newDocumentDialog;
    private OverView overView;
    private RulesAndSyllabusOne rulesAndSyllabusOne;
    private RulesAndSyllabusTwo rulesAndSyllabusTwo;
    //private SavedFormData savedFormData;
    private Resources newResourcesForm;
    private CollaborationAndContracts newContractsForm;
    private Review newReviewForm;
    private ContactDetails newContactDetailsForm;
    private DocumentListPanel documentListPanel;
    private TabPanel tab = new TabPanel();
    private String getFoldersParams = Constants.MAIN_URL_PATTERN + "?module=wicid&action=getfolders";
    private TabItem docsTab = new TabItem("Documents");
    private NewCourseProposalDialog newCourseProposalDialog;
    private String mode = "default";
    private boolean admin = false;
    private TextField<String> searchField = new TextField<String>();
    private Button searchButton = new Button("Search");
    private Main thisInstance;

    /**
     * Creates a new instance of Main
     */
    public Main() {
    }

    public LayoutContainer createGUI() {
        documentListPanel = new DocumentListPanel(this);
        LayoutContainer container = new LayoutContainer();
        container.setWidth(Window.getClientWidth());
        container.setHeight(Window.getClientHeight());
        container.setBorders(false);
        container.setLayout(new BorderLayout());
        this.thisInstance = this;
        Constants.main = thisInstance;
        treeFieldtype.setRecordName("item");
        treeFieldtype.setRoot("items");
        treeFieldtype.addField("id", "@id");
        treeFieldtype.addField("name", "@name");
        treeFieldtype.addField("folder", "@folder");
        treeFieldtype.addField("viewfiles", "@viewfiles");
        treeFieldtype.addField("uploadfiles", "@uploadfiles");
        treeFieldtype.addField("createfolder", "@createfolder");



        ToolBar toolBar = new ToolBar();

        newFolderButton.setIconStyle("folderadd");
        //newFolderButton.setEnabled(false);
        newFolderButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                promptFolderName();
            }
        });

        Button refreshFolders = new Button("Refresh");
        refreshFolders.setIconStyle("refresh");
        refreshFolders.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                folderLoader.load();
                tree.setExpanded(selectedFolder, true);

            }
        });
        toolBar.add(newFolderButton);

        BorderLayoutData westData = new BorderLayoutData(LayoutRegion.WEST, Window.getClientWidth() / 4, 100, 600);
        westData.setMargins(new Margins(5, 0, 5, 5));
        westData.setSplit(true);
        container.add(west, westData);



        ModelType type2 = new ModelType();
        type2.setRoot("files");
        type2.addField("id", "id");
        type2.addField("text", "text");
        type2.addField("thumbnailpath", "thumbnailpath");

        RequestBuilder builder2 = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + "?module=wicid&action=getFiles");
        HttpProxy<String> proxy2 = new HttpProxy<String>(builder2);

        // need a loader, proxy, and reader
        JsonLoadResultReader<ListLoadResult<ModelData>> reader2 = new JsonLoadResultReader<ListLoadResult<ModelData>>(type2);

        final BaseListLoader<ListLoadResult<ModelData>> loader2 = new BaseListLoader<ListLoadResult<ModelData>>(proxy2,
                reader2);

        ListStore<ModelData> store2 = new ListStore<ModelData>(loader2);
        newFolderButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                loader2.load();
            }
        });


        center.setHeading("Files view (0 items selected)");
        center.setId("images-view");
        center.setScrollMode(Scroll.AUTO);
        // center.setBodyBorder(false);
        ToolBar toolBar2 = new ToolBar();


        newDocumentButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (newDocumentDialog == null) {
                    newDocumentDialog = new NewDocumentDialog();
                }
                newDocumentDialog.show();
            }
        });

        newCourseProposalButton.setIconStyle("addform");
        newCourseProposalButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (newCourseProposalDialog == null) {
                    newCourseProposalDialog = new NewCourseProposalDialog();
                }
                newCourseProposalDialog.show();
            }
        });
        newDocumentButton.setIconStyle("upload");

        folderUserButton.setEnabled(false);
        folderUserButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                if (permissionsFrame == null) {
                    permissionsFrame = new PermissionsFrame();

                }
                permissionsFrame.show();
            }
        });

        fileExtButton.setIconStyle("settings");
        fileExtButton.setEnabled(false);
        fileExtButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {

                if (extPanel == null) {
                    extPanel = new ExtPanel();
                }
                extPanel.show();
            }
        });



        folderUserButton.setIconStyle("folder_user");

        unapprovedDocsButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
            }
        });

        unapprovedDocsButton.setEnabled(false);
        unapprovedDocsButton.setIconStyle("docs");

        toolBar.add(refreshFolders);
        west.setTopComponent(toolBar);
        west.setHeading("Topic Index");


        searchButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                String filter = searchField.getValue();
                if (filter != null) {
                    if (filter.trim().length() > 2) {
                        searchFiles(filter);
                    } else {
                        MessageBox.info("3 Characters", "Type in atleast 3 characters to search for file", null);
                    }
                }
            }
        });

        searchButton.setIconStyle("search");
        toolBar2.add(newDocumentButton);
        toolBar2.add(newCourseProposalButton);
        toolBar2.add(new SeparatorToolItem());
        toolBar2.add(new Label("Search"));
        toolBar2.add(searchField);
        toolBar2.add(searchButton);
        center.setTopComponent(toolBar2);

        loader2.load();
        view = new ListView<ModelData>() {

            @Override
            protected ModelData prepareData(ModelData model) {
                model.set("shortName", Format.ellipse((String) model.get("text"), 15));
                return model;
            }
        };

        view.setTemplate(getTemplate());
        view.setBorders(false);
        view.setLoadingText("Loading ...");
        view.setStore(store2);
        view.setHeight("100%");
        view.setItemSelector("div.thumb-wrap");
        view.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedFile = md.getSelectedItem();
                        editMenuItem.setEnabled(true);

                    }
                });

        Menu viewContextMenu = new Menu();

        MenuItem viewMenuItem = new MenuItem();
        viewMenuItem.setText("Details");
        viewMenuItem.setIconStyle("details");
        viewMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                if (selectedFile != null) {

                    FileInfo fileInfo = new FileInfo(
                            (String) selectedFile.get("text"),
                            (String) selectedFile.get("refno"),
                            (String) selectedFile.get("lastmod"),
                            (String) selectedFile.get("owner"),
                            (String) selectedFile.get("filesize"),
                            (String) selectedFile.get("thumbnailpath"),
                            (String) selectedFile.get("group"));
                    fileInfo.show();
                }
            }
        });

        renameFileMenuItem.setText("Rename");
        renameFileMenuItem.setEnabled(false);

        downloadFileMenuItem.setIconStyle("download");
        downloadFileMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                if (selectedFile != null) {
                    downloadFile();

                }
            }
        });

        downloadFileMenuItem.setText("Download");

        deleteFileMenuItem.setText("Delete");
        deleteFileMenuItem.setEnabled(false);
        deleteFileMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                if (selectedFile != null) {
                }
            }
        });

        editMenuItem.setText("Edit");
        editMenuItem.setIconStyle("edit");
        editMenuItem.setEnabled(false);
        editMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                String docId = (String) selectedFile.get("docid");
                documentListPanel.showEditDialog(docId);
            }
        });
        viewContextMenu.add(downloadFileMenuItem);
        viewContextMenu.add(viewMenuItem);
        viewContextMenu.add(editMenuItem);

        viewContextMenu.add(new SeparatorMenuItem());
        viewContextMenu.add(renameFileMenuItem);
        viewContextMenu.add(deleteFileMenuItem);

        view.setContextMenu(viewContextMenu);

        ToolBar viewFilesToolbar = new ToolBar();
        Button refreshViewFilesButton = new Button("Refresh");
        refreshViewFilesButton.addSelectionListener(new SelectionListener<ButtonEvent>() {

            @Override
            public void componentSelected(ButtonEvent ce) {
                refreshFileList();
            }
        });
        refreshViewFilesButton.setIconStyle("refresh");
        viewFilesToolbar.add(refreshViewFilesButton);
        TabItem viewTab = new TabItem("File List");
        viewTab.setHeight("800");
        view.setHeight("800");
        ContentPanel viewFilesPanel = new ContentPanel();
        viewFilesPanel.setFrame(false);
        viewFilesPanel.setBodyBorder(false);
        viewFilesPanel.setLayout(new FitLayout());
        viewFilesPanel.setTopComponent(viewFilesToolbar);
        viewFilesPanel.add(view);
        viewTab.add(viewFilesPanel);

        tab.add(viewTab);


        documentListPanel.setHeight(500);
        docsTab.setIconStyle("docs");
        docsTab.setHeight(Window.getClientHeight());
        docsTab.add(documentListPanel);
        tab.add(docsTab);

        center.add(tab);



        BorderLayoutData centerData = new BorderLayoutData(LayoutRegion.CENTER, (Window.getClientWidth() / 4) * 3, 100, Window.getClientWidth());
        centerData.setMargins(new Margins(5));
        container.add(center, centerData);

        loadFolderList(getFoldersParams + "&mode=default");
        determinePermissions();
        return container;
    }

    private native String getTemplate() /*-{
    return ['<tpl for=".">',
    '<div class="thumb-wrap" id="{text}" style="border: 1px solid white">',
    '<div class="thumb"><img src="{thumbnailpath}" width="32" height= "32" title="{text}"></div>',
    '<span class="x-editable">{shortName}</span></div>',
    '</tpl>',
    '<div class="x-clear"></div>'].join("");

    }-*/;

    /*
     * Takes in a trusted JSON String and evals it.
     * @param JSON String that you trust
     * @return JavaScriptObject that you can cast to an Overlay Type
     */
    public static native JavaScriptObject parseJson(String jsonStr) /*-{
    return eval(jsonStr);
    }-*/;

    public void refreshFileList() {

        if (!selectedFolder.get("viewfiles").equals("true")) {
            view.getStore().removeAll();
            view.refresh();

            return;
        }
        ModelType type2 = new ModelType();
        type2.setRoot("files");
        type2.addField("id", "id");
        type2.addField("docid", "docid");
        type2.addField("text", "text");
        type2.addField("thumbnailpath", "thumbnailpath");
        type2.addField("lastmod", "lastmod");
        type2.addField("owner", "owner");
        type2.addField("filesize", "filesize");
        type2.addField("refno", "refno");
        type2.addField("group", "group");
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=getFiles&node=" + currentPath);
        HttpProxy<String> proxy = new HttpProxy<String>(builder);
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type2);
        final BaseListLoader<ListLoadResult<ModelData>> loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);
        ListStore<ModelData> store = new ListStore<ModelData>(loader);
        view.setStore(store);
        loader.setSortDir(SortDir.ASC);
        loader.setSortField("text");
        store.sort("text", SortDir.ASC);
        removeFolderMenuItem.setEnabled(selectedFolder == null ? true : false);
        loader.load();
        view.refresh();
    }

    private void searchFiles(String filter) {
        ModelType type2 = new ModelType();
        type2.setRoot("files");
        type2.addField("id", "id");
        type2.addField("docid", "docid");
        type2.addField("text", "text");
        type2.addField("thumbnailpath", "thumbnailpath");
        type2.addField("lastmod", "lastmod");
        type2.addField("owner", "owner");
        type2.addField("filesize", "filesize");
        type2.addField("refno", "refno");
        type2.addField("group", "group");
        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=searchfiles&filter=" + filter);
        HttpProxy<String> proxy = new HttpProxy<String>(builder);
        JsonLoadResultReader<ListLoadResult<ModelData>> reader = new JsonLoadResultReader<ListLoadResult<ModelData>>(type2);
        final BaseListLoader<ListLoadResult<ModelData>> loader = new BaseListLoader<ListLoadResult<ModelData>>(proxy,
                reader);
        ListStore<ModelData> store = new ListStore<ModelData>(loader);
        view.setStore(store);
        loader.setSortDir(SortDir.ASC);
        loader.setSortField("text");
        store.sort("text", SortDir.ASC);
        removeFolderMenuItem.setEnabled(selectedFolder == null ? true : false);
        loader.load();
        view.refresh();
    }

    private void promptFolderName() {

        final MessageBox box = MessageBox.prompt("Name", "Please enter topic name:");
        box.addCallback(new Listener<MessageBoxEvent>() {

            public void handleEvent(MessageBoxEvent be) {
                String val = be.getValue();
                if (val != null) {
                    createNewFolder(val);
                }
            }
        });

    }

    private void createNewFolder(String folderName) {
        String url =
                GWT.getHostPageBaseURL()
                + Constants.MAIN_URL_PATTERN + "?module=wicid&action=createfolder&foldername=" + folderName + "&folderpath=" + currentPath;
        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, url);

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new topic", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        // folderStore.removeAll();
                        folderLoader.load();
                        tree.setExpanded(selectedFolder, true);
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot create file", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot create file", null);
        }
    }

    private void doActualFolderDelete() {
        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL()
                + "?module=wicid&action=deletefolder&folderpath=" + currentPath);

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {
                    MessageBox.info("Error", "Error, cannot create new topic", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        // folderStore.removeAll();
                        loadFolderList(getFoldersParams);
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot download file", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot download file", null);
        }
    }

    private void deleteFolder() {
        if (selectedFolder != null) {
            final Listener<MessageBoxEvent> l = new Listener<MessageBoxEvent>() {

                public void handleEvent(MessageBoxEvent ce) {
                    Button btn = ce.getButtonClicked();
                    if (btn.getText().equalsIgnoreCase("Yes")) {
                        doActualFolderDelete();
                    }
                }
            };
            MessageBox.confirm("Confirm", "Are you sure you want to delete folder " + selectedFolder.get("text") + " ?", l);


        }
    }

    /**
     *
     */
    private void determinePermissions() {

        RequestBuilder builder =
                new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL()
                + Constants.MAIN_URL_PATTERN + "?module=wicid&action=determinepermissions");

        try {
            Request request = builder.sendRequest(null, new RequestCallback() {

                public void onError(Request request, Throwable exception) {

                    MessageBox.info("Error", "Error, cannot determine your permissions", null);
                }

                public void onResponseReceived(Request request, Response response) {
                    if (200 == response.getStatusCode()) {
                        String[] toks = response.getText().split(",");


                        for (String tok : toks) {
                            String[] props = tok.split("=");

                            if (props[0].equals("admin") && props[1].equals("true")) {
                                admin = true;
                            }
                            if (props[0].equals("mode")) {
                                mode = props[1];
                            }
                        }

                        if (admin) {
                            folderUserButton.setEnabled(true);
                            fileExtButton.setEnabled(true);
                            newFolderButton.setEnabled(true);
                            unapprovedDocsButton.setEnabled(true);
                        }
                        /*if (mode.equalsIgnoreCase("apo")) {
                        newDocumentButton.setEnabled(false);
                        docsTab.setEnabled(false);
                        } else {
                        newCourseProposalButton.setEnabled(false);
                        }*/
                    } else {
                        MessageBox.info("Error", "Error occured on the server. Cannot determine your permissions", null);
                    }
                }
            });
        } catch (RequestException e) {
            MessageBox.info("Fatal Error", "Fatal Error: cannot determine your permisions", null);
        }
    }

    private void downloadFile() {

        String url = GWT.getHostPageBaseURL()
                + "?module=wicid&action=downloadfile&filename=" + currentPath + "/" + selectedFile.get("text");

        Window.Location.assign(url);
    }

    private void loadFolderList(String params) {

        RequestBuilder builder = new RequestBuilder(RequestBuilder.GET, GWT.getHostPageBaseURL() + params);

        HttpProxy<ListLoadResult<ModelData>> proxy = new HttpProxy<ListLoadResult<ModelData>>(builder);

        folderReader = new XmlReader<List<ModelData>>(treeFieldtype);

        folderLoader = new BaseTreeLoader<ModelData>(proxy, folderReader) {

            @Override
            public boolean hasChildren(ModelData parent) {
                return "true".equals(parent.get("folder"));
            }

            @Override
            protected Object prepareLoadConfig(Object config) {
                // by default the load config will be the parent model
                // http proxy will set all properties of model into request
                // parameters, so the model name and id will be passed to server
                return super.prepareLoadConfig(config);
            }
        };

        folderStore = new TreeStore<ModelData>(folderLoader);
        tree = new TreePanel<ModelData>(folderStore);
        tree.setDisplayProperty("name");

        tree.setWidth(315);
        tree.setHeight("100%");
        folderLoader.load();


        tree.setContextMenu(initTreeContextTree());
        tree.getSelectionModel().addListener(Events.SelectionChange,
                new Listener<SelectionChangedEvent<ModelData>>() {

                    public void handleEvent(SelectionChangedEvent<ModelData> md) {
                        selectedFolder = md.getSelectedItem();
                        center.setHeading((String) md.getSelectedItem().get("id"));
                        String id = (String) md.getSelectedItem().get("id");
                        currentPath = id;
                        refreshFileList();

                    }
                });

        west.add(tree);

    }

    private Menu initTreeContextTree() {
        Menu contextMenu = new Menu();

        newFolderMenuItem.setText("New Topic");
        newFolderMenuItem.setIconStyle("folderadd");
        //newFolderButton.setEnabled(false);
        newFolderMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                promptFolderName();

            }
        });
        contextMenu.add(newFolderMenuItem);

        removeFolderMenuItem.setText("Remove Folder");
        removeFolderMenuItem.setIconStyle("delete");
        removeFolderMenuItem.setEnabled(false);
        removeFolderMenuItem.setIconStyle("folderadd");
        removeFolderMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                deleteFolder();
            }
        });
        contextMenu.add(removeFolderMenuItem);


        uploadMenuItem.setText("Register Document");
        uploadMenuItem.setIconStyle("upload");
        uploadMenuItem.setEnabled(false);
        uploadMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                if (newDocumentDialog == null) {
                    newDocumentDialog = new NewDocumentDialog();
                }
                newDocumentDialog.show();
            }
        });



        MenuItem selectNoneMenuItem = new MenuItem("Select None");
        selectNoneMenuItem.addSelectionListener(new SelectionListener<MenuEvent>() {

            public void componentSelected(MenuEvent ce) {
                tree.getSelectionModel().deselectAll();
                currentPath = "";
                center.setHeading(currentPath);
                selectedFolder = null;
            }
        });
        contextMenu.add(new SeparatorMenuItem());
        contextMenu.add(selectNoneMenuItem);
        contextMenu.add(new SeparatorMenuItem());
        contextMenu.add(uploadMenuItem);
        return contextMenu;
    }

    public void selectDocumentsTab() {
        tab.setSelection(docsTab);
    }

    public String getCurrentPath() {
        return currentPath;
    }

    public DocumentListPanel getDocumentListPanel() {
        return documentListPanel;
    }

    public TabPanel getTab() {
        return tab;
    }

    public String getMode() {
        return mode;
    }

    public boolean isAdmin() {
        return admin;
    }
}
