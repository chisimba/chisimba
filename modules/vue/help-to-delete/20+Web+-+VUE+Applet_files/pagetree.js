if (typeof(PageTreeManager)=="undefined") var PageTreeManager = (function () {
    var forest=[];

    function generateNodeID(treeId,nodeId) {
        return "pagetree"+treeId+"_"+nodeId;
    }

    var iconStyles ={
        computer:{dashboard:["workplace.png","workplace.png"],
            space:["folder_closed.png","folder.png"],
            page:["document_plain.png","document_plain.png"],
            recentpage:["document_plain_new.png","document_plain_new.png"],
            homepage:["home.png","home.png"],
            blogpost:["calendar.png","calendar.png"],
            recentblogpost:["calendar_preferences.png","calendar_preferences.png"],
            loading:["view.png","view.png"]},
        builder:{dashboard:["presentation.png","presentation.png"],
            space:["earth2.png","earth2.png"],
            page:["document.png","document.png"],
            recentpage:["document_new.png","document_new.png"],
            homepage:["home.png","home.png"],
            blogpost:["news.png","news.png"],
            recentblogpost:["news_new.png","news_new.png"],
            loading:["view.png","view.png"]},
        website:{dashboard:["home.png","home.png"],
            space:["earth2.png","earth2.png"],
            page:["document_plain.png","document_plain.png"],
            recentpage:["document_plain_new.png","document_plain_new.png"],
            homepage:["house.png","house.png"],
            blogpost:["date-time.png","date-time.png"],
            recentblogpost:["date-time_preferences.png","date-time_preferences.png"],
            loading:["view.png","view.png"]},
        bookshelf:{dashboard:["books.png","books.png"],
            space:["book_blue.png","book_open.png"],
            page:["document_text.png","document_text.png"],
            recentpage:["document_new.png","document_new.png"],
            homepage:["book_open2.png","book_open2.png"],
            blogpost:["document_time.png","document_time.png"],
            recentblogpost:["document_new.png","document_new.png"],
            loading:["view.png","view.png"]}
        };
    var iconPath="/download/resources/com.adaptavist.confluence.themes.sitebuilder:pagetree2/icons/";
    var contextPath="";
    function getIcons(style,type) {
        if (iconStyles[style]!=null && iconStyles[style][type]!=null) return contextPath+iconPath+iconStyles[style][type][0]+","+contextPath+iconPath+iconStyles[style][type][1];
        return null;
    }

    function compareNodeById (aN, bN) { return (aN.id >= bN.id); };
    function compareNode(aN, bN) { return (aN.capt >= bN.capt); };
    function compareSortedPage(aN, bN) { return (aN.xtra.sortid >= bN.xtra.sortid); };

    var sortFuncs={dashboard:compareNode,
            space:compareNode,
            page:null,
            recentpage:null,
            homepage:null,
            blogpost:compareNodeById,
            recentblogpost:compareNodeById};

    function onLoadData (treeId,parentNode) {
        return function (children) {
            var treeData = forest[treeId];
            if (treeData!=null) {
                treeData.children[parentNode]=children;
                var tree=treeData.tree;
                var parentNodeId=generateNodeID(treeId,parentNode);
                if (tree.getNodeById(parentNodeId)!=null) {
                    tree.remove(parentNodeId+"_loading");
                    if (children!=null) {
                        var i = children.length;
                        children=children.reverse();
                        var nodeId;
                        var clickaction;
                        var nodeData;
                        var node;
                        while (-1<--i) {
                            nodeData=children[i];
                            nodeId=generateNodeID(treeId,nodeData.id);
                            clickaction = (treeData.openpage) ? nodeData.url : "";
                            nodeData.treeId=treeId;
                            node=tree.add(nodeId, parentNodeId, nodeData.title, clickaction, getIcons(treeData.iconStyle, nodeData.type), false, false, nodeData, (treeData.titletip) ? nodeData.title : nodeData.tooltip);
                            node.sortFunc = sortFuncs[nodeData.type];
                            tree.setDrag(nodeId,treeData.allowdrag && (nodeData.type=="page") && nodeData.editPermission,false);
                            tree.setDrop(nodeId,treeData.allowdrag && (nodeData.type=="page" || nodeData.type=="homepage" || nodeData.type=="space") && nodeData.createPermission,false);
                            if (nodeData.hasChildren) tree.add(nodeId+"_loading", nodeId,"loading...","",getIcons(treeData.iconStyle, "loading"));
                        }
                    }
                    tree.reloadNode(parentNodeId);
                    tree.expandNode(parentNodeId);
                    if (treeData.selectNode!=null && treeData.selectNode==parentNode) {
                        tree.selectNodeById(parentNodeId);
                        tree.treeOnClick(null,parentNode);
                        treeData.selectNode=null;
                    }
                    if (treeData.preload.length) {
                        var preloadId = treeData.preload.shift();
                        PageTreeDWR.getChildren(preloadId,treeData.allowdrag,treeData.sort,treeData.reverse,onLoadData(treeId,preloadId));
                        treeData.selectNode=preloadId;
                    }
                }
            }
        }
    }

    function setTreeOpts(tree,treeData) {
        tree.opt.trg = treeData.target;
        tree.opt.icon = treeData.showIcons;
        tree.opt.editable = false;
        tree.opt.sort = "asc";
        tree.opt.selRow = (treeData.selectionMode=="row");
        tree.opt.oneExp = treeData.autoCollapse;
        tree.opt.indent = treeData.indent;
        tree.opt.hideRoot = !treeData.showRoot;
        switch (treeData.branchStyle) {
            default:
            case "plus-lines":
                // do nothing - this is the standard look
                break;
            case "plus-nolines":
                tree.ico.pnb=tree.defImgPath+"plusnl.gif";
                tree.ico.pb=tree.defImgPath+"plusnl.gif";
                tree.ico.mnb=tree.defImgPath+"minusnl.gif";
                tree.ico.mb=tree.defImgPath+"minusnl.gif";
                tree.ico.lnb=tree.defImgPath+"blank.gif";
                tree.ico.lb=tree.defImgPath+"blank.gif";
                tree.ico.lin=tree.defImgPath+"blank.gif";
                break;
            case "ball-lines":
                tree.ico.pnb=tree.defImgPath+"bulclpsnb.gif";
                tree.ico.pb=tree.defImgPath+"bulclpsb.gif";
                tree.ico.mnb=tree.defImgPath+"bulexpnb.gif";
                tree.ico.mb=tree.defImgPath+"bulexpb.gif";
                break;
            case "ball-nolines":
                tree.ico.pnb=tree.defImgPath+"bulclpsnl.gif";
                tree.ico.pb=tree.defImgPath+"bulclpsnl.gif";
                tree.ico.mnb=tree.defImgPath+"bulexpnl.gif";
                tree.ico.mb=tree.defImgPath+"bulexpnl.gif";
                tree.ico.lnb=tree.defImgPath+"blank.gif";
                tree.ico.lb=tree.defImgPath+"blank.gif";
                tree.ico.lin=tree.defImgPath+"blank.gif";
                break;
            case "arrow":
                tree.ico.pnb=tree.defImgPath+"arrowright.gif";
                tree.ico.pb=tree.defImgPath+"arrowright.gif";
                tree.ico.mnb=tree.defImgPath+"arrowdown.gif";
                tree.ico.mb=tree.defImgPath+"arrowdown.gif";
                tree.ico.lnb=tree.defImgPath+"blank.gif";
                tree.ico.lb=tree.defImgPath+"blank.gif";
                tree.ico.lin=tree.defImgPath+"blank.gif";
                break;
        }
    }

    function onExpandNode(treeId) {
        return function (nodeId) {
            var treeData = forest[treeId];
            if (treeData!=null) {
                var node = treeData.tree.getNodeById(nodeId);
                if (node!=null && node.xtra!=null && node.xtra.id!=null && treeData.children[node.xtra.id]==null) PageTreeDWR.getChildren(node.xtra.id,treeData.allowdrag,treeData.sort,treeData.reverse,onLoadData(treeId,node.xtra.id));
            }
        }
    }

    var treeListeners={};
    function onSelectNode(treeId) {
        return function (e,nodeId) {
            var treeData = forest[treeId];
            var listeners=treeListeners[treeData.name];
            if (treeData!=null && listeners!=null) {
                var node = treeData.tree.getNodeById(nodeId);
                if (node!=null && node.xtra!=null && node.xtra.id!=null) {
                    var i=listeners.length;
                    while (-1<--i) listeners[i](node.xtra);
                }
            }
        }
    }

    function addListener(treeName, fn) {
        if ((treeName==null && forest.length==0) || typeof(fn)!="function") return;
        if (treeName==null) treeName = forest[0].name;
        if (treeListeners[treeName]==null) treeListeners[treeName]=[];
        treeListeners[treeName].push(fn);
        var i = forest.length;
        while (-1<--i) {
            if (forest[i].name == treeName) {
                var current = forest[i].tree.getSelNode();
                if (current!=null && current.xtra!=null) {
                    fn(current.xtra);
                }
            }
        }
    }

    function onSetParent(state) {
        if (!state) alert("Failed to move page!");
    }

    function onNodeDrop(treeId,originalfunc) {
        return function(e) {
            var droptarget=nlsddSession.destData.xtra.id;
            if (droptarget!=null) {
                var dragged=nlsddSession.srcData;
                var i=dragged.length;
                while (-1<--i) {
                    if (dragged[i].xtra.id!=null) PageTreeDWR.setPageParent(dragged[i].xtra.id, droptarget, onSetParent);
                }
                originalfunc(e);
            }
        }
    }

    function generateTree(nodeData, treeData, cp) {
        contextPath = cp;
        if (typeof(treeData.iconpath)!="undefined") {
            iconPath=treeData.iconpath;
        }
        var treeId=forest.length;
        forest[treeId] = treeData;
        var tree=treeData.tree=new NlsTree("pagetree"+treeId);
        treeData.children={};
        treeData.selectNode=nodeData.id;
        treeData.treeDD=new NlsTreeDD("pagetree"+treeId);
        treeData.treeDD.onNodeDrop=onNodeDrop(treeId,treeData.treeDD.onNodeDrop);
        setTreeOpts(tree,treeData);
        tree.treeOnExpand=onExpandNode(treeId);
        tree.treeOnClick=onSelectNode(treeId);
        var nodeId=generateNodeID(treeId,nodeData.id);
        nodeData.treeId=treeId;
        var node = tree.add(nodeId, 0, nodeData.title, (treeData.openpage) ? nodeData.url : "", getIcons(treeData.iconStyle, nodeData.type), true, false, nodeData, (treeData.titletip) ? nodeData.title : nodeData.tooltip);
        node.sortFunc = sortFuncs[nodeData.type];
        tree.add(nodeId+"_loading", nodeId, "loading...", "", getIcons(treeData.iconStyle, "loading"));
        tree.setDrag(nodeId,false,false);
        tree.setDrop(nodeId,treeData.allowdrag && (nodeData.type=="page" || nodeData.type=="homepage" || nodeData.type=="space") && nodeData.createPermission,false);
        PageTreeDWR.getChildren(nodeData.id,treeData.allowdrag,treeData.sort,treeData.reverse,onLoadData(treeId,nodeData.id));
        tree.renderAttributes();
        return tree;
    }

    return {generateTree:generateTree, addListener:addListener};
})();

