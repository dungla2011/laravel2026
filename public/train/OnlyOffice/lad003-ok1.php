
<!DOCTYPE html>
<html>
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="mobile-web-app-capable" content="yes" />
    <!--
    *
    * (c) Copyright Ascensio System SIA 2024
    *
    * Licensed under the Apache License, Version 2.0 (the "License");
    * you may not use this file except in compliance with the License.
    * You may obtain a copy of the License at
    *
    *     http://www.apache.org/licenses/LICENSE-2.0
    *
    * Unless required by applicable law or agreed to in writing, software
    * distributed under the License is distributed on an "AS IS" BASIS,
    * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    * See the License for the specific language governing permissions and
    * limitations under the License.
    *
    -->
    <title>ONLYOFFICE</title>
    <link rel="icon"
          href="images/word.ico"
          type="image/x-icon" />
<!--    <link rel="stylesheet" type="text/css" href="stylesheets/editor.css" />-->

    <style>
        html {
            height: 100%;
            width: 100%;
        }

        body {
            background: #fff;
            color: #333;
            font-family: Arial, Tahoma,sans-serif;
            font-size: 12px;
            font-weight: normal;
            height: 100%;
            margin: 0;
            overflow-y: hidden;
            padding: 0;
            text-decoration: none;
        }

        .form {
            height: 100%;
        }

        div {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div class="form">
    <div id="iframeEditor">
    </div>
</div>
<script type="text/javascript" src="http://103.163.217.6:8080/web-apps/apps/api/documents/api.js"></script>
<script type="text/javascript" language="javascript">

    var docEditor;
    var config;
    let historyObject;

    var innerAlert = function (message, inEditor) {
        if (console && console.log)
            console.log(message);
        if (inEditor && docEditor)
            docEditor.showMessage(message);
    };

    var onAppReady = function () {  // the application is loaded into the browser
        innerAlert("Document editor ready");
    };

    var onDocumentStateChange = function (event) {  // the document is modified
        var title = document.title.replace(/\*$/g, "");
        document.title = title + (event.data ? "*" : "");
    };

    var onRequestClose = function () {  // close editor
        docEditor.destroyEditor();
        innerAlert("Document editor closed successfully");
    };

    var onMetaChange = function (event) {  // the meta information of the document is changed via the meta command
        if (event.data.favorite !== undefined) {
            var favorite = !!event.data.favorite;
            var title = document.title.replace(/^\☆/g, "");
            document.title = (favorite ? "☆" : "") + title;
            docEditor.setFavorite(favorite);  // change the Favorite icon state
        }

        innerAlert("onMetaChange: " + JSON.stringify(event.data));
    };

    var onRequestEditRights = function () {  // the user is trying to switch the document from the viewing into the editing mode
        location.href = location.href.replace(RegExp("mode=\\w+\&?", "i"), "") + "&mode=edit";
    };

    var onRequestHistory = function (event) {  // the user is trying to show the document version history
        const fileName = "new.docx" || null;
        const directUrl = "" || null;
        const data = {
            fileName: fileName,
            directUrl: directUrl
        };
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "historyObj");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            historyObject = JSON.parse(xhr.responseText);
            docEditor.refreshHistory(  // show the document version history
                {
                    currentVersion: historyObject.countVersion,
                    history: historyObject.history
                });
        }
    };

    var onRequestHistoryData = function (event) {  // the user is trying to click the specific document version in the document version history
        const version = event.data;
        docEditor.setHistoryData(historyObject.historyData[version-1]);  // send the link to the document for viewing the version history
    };

    var onRequestHistoryClose = function (event){  // the user is trying to go back to the document from viewing the document version history
        document.location.reload();
    };

    var onRequestRestore = function (event) { // the user is trying to restore file version
        const version = event.data.version;
        const fileName = "new.docx" || null;
        const directUrl = "" || null;
        const restoreData = {
            version: version,
            fileName: fileName,
        };
        let xhr = new XMLHttpRequest();
        xhr.open("PUT", "restore");
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(restoreData));
        xhr.onload = function () {
            const response = JSON.parse(xhr.responseText);
            if (response.success && !response.error) {
                const dataForHistory = {
                    fileName: fileName,
                    directUrl: directUrl
                };
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "historyObj");
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.send(JSON.stringify(dataForHistory));
                xhr.onload = function () {
                    historyObject = JSON.parse(xhr.responseText);
                    docEditor.refreshHistory(  // show the document version history
                        {
                            currentVersion: historyObject.countVersion,
                            history: historyObject.history
                        });
                }
            } else {
                innerAlert(response.error);
            }
        }
    }

    var onError = function (event) {  // an error or some other specific event occurs
        if (event)
            innerAlert(event.data);
    };

    var onOutdatedVersion = function (event) {  // the document is opened for editing with the old document.key value
        location.reload(true);
    };

    // replace the link to the document which contains a bookmark
    var replaceActionLink = function(href, linkParam) {
        var link;
        var actionIndex = href.indexOf("&action=");
        if (actionIndex != -1) {
            var endIndex = href.indexOf("&", actionIndex + "&action=".length);
            if (endIndex != -1) {
                link = href.substring(0, actionIndex) + href.substring(endIndex) + "&action=" + encodeURIComponent(linkParam);
            } else {
                link = href.substring(0, actionIndex) + "&action=" + encodeURIComponent(linkParam);
            }
        } else {
            link = href + "&action=" + encodeURIComponent(linkParam);
        }
        return link;
    }

    var onMakeActionLink = function (event) {  // the user is trying to get link for opening the document which contains a bookmark, scrolling to the bookmark position
        var actionData = event.data;
        var linkParam = JSON.stringify(actionData);
        docEditor.setActionLink(replaceActionLink(location.href, linkParam));  // set the link to the document which contains a bookmark
    };

    var onRequestInsertImage = function(event) {  // the user is trying to insert an image by clicking the Image from Storage button
        var data = {"fileType":"svg","url":"https://doc1.pm33.net/example/images/logo.svg","directUrl":null,"token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmaWxlVHlwZSI6InN2ZyIsInVybCI6Imh0dHBzOi8vZG9jMS5wbTMzLm5ldC9leGFtcGxlL2ltYWdlcy9sb2dvLnN2ZyIsImRpcmVjdFVybCI6bnVsbCwiaWF0IjoxNzMwMDI2NzI3LCJleHAiOjE3MzAwMjcwMjd9.koxxlKfAEml5SuFlon6Oqp81NMAahBzskX3rN-08Hbc"};
        data.c = event.data.c;
        docEditor.insertImage(data);  // insert an image into the file
    };

    var onRequestSelectDocument = function(event) {  // the user is trying to select document by clicking the Document from Storage button
        var data = {"fileType":"docx","url":"https://doc1.pm33.net/example/assets/document-templates/sample/sample.docx","directUrl":null,"token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmaWxlVHlwZSI6ImRvY3giLCJ1cmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9hc3NldHMvZG9jdW1lbnQtdGVtcGxhdGVzL3NhbXBsZS9zYW1wbGUuZG9jeCIsImRpcmVjdFVybCI6bnVsbCwiaWF0IjoxNzMwMDI2NzI3LCJleHAiOjE3MzAwMjcwMjd9.McP-2foOpcvZZAkWggANynWTbt8rtoWXp1N3WEfHqm8"};
        data.c = event.data.c;
        docEditor.setRequestedDocument(data);  // select a document
    };

    var onRequestSelectSpreadsheet = function (event) {  // the user is trying to select recipients data by clicking the Mail merge button
        var data = {"fileType":"csv","url":"https://doc1.pm33.net/example/csv","directUrl":null,"token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJmaWxlVHlwZSI6ImNzdiIsInVybCI6Imh0dHBzOi8vZG9jMS5wbTMzLm5ldC9leGFtcGxlL2NzdiIsImRpcmVjdFVybCI6bnVsbCwiaWF0IjoxNzMwMDI2NzI3LCJleHAiOjE3MzAwMjcwMjd9.SC7Fr5s8cAoGSTjqb2CveKWcZSI3RdVERnsSCpekrJI"};
        data.c = event.data.c;
        docEditor.setRequestedSpreadsheet(data);  // insert recipient data for mail merge into the file
    };

    var onRequestUsers = function (event) {
        if (event && event.data){
            var c = event.data.c;
        }

        switch (c) {
            case "protect":
                var users = [{"id":"uid-2","name":"Mark Pottato","email":"pottato@example.com","group":"group-2","reviewGroups":["group-2",""],"commentGroups":{"view":"","edit":["group-2",""],"remove":["group-2"]},"userInfoGroups":["group-2",""],"favorite":true,"deniedPermissions":[],"descriptions":["Belongs to Group2","Can review only his own changes or changes made by users with no group","Can view comments, edit his own comments and comments left by users with no group. Can remove his own comments only","This file is marked as favorite","Can create new files from the editor","Can see the information about users from Group2 and users who don’t belong to any group","Can’t submit forms","Has an avatar"],"templates":false,"avatar":true,"goback":{"text":"Go to Documents"},"close":{},"image":"https://doc1.pm33.net/example/images/uid-2.png"},{"id":"uid-3","name":"Hamish Mitchell","email":"mitchell@example.com","group":"group-3","reviewGroups":["group-2"],"commentGroups":{"view":["group-3","group-2"],"edit":["group-2"],"remove":[]},"userInfoGroups":["group-2"],"favorite":false,"deniedPermissions":["copy","download","print"],"descriptions":["Belongs to Group3","Can review changes made by Group2 users","Can view comments left by Group2 and Group3 users. Can edit comments left by the Group2 users","This file isn’t marked as favorite","Can’t copy data from the file to clipboard","Can’t download the file","Can’t print the file","Can create new files from the editor","Can see the information about Group2 users","Can’t submit forms","Can’t close history","Can’t restore the file version"],"templates":false,"avatar":false,"goback":null,"close":{},"image":null}];
                break;
            case "info":
                users = [];
                var allUsers = [{"id":"uid-1","name":"John Smith","email":"smith@example.com","group":null,"reviewGroups":null,"commentGroups":{},"userInfoGroups":null,"favorite":null,"deniedPermissions":[],"descriptions":["File author by default","Doesn’t belong to any group","Can review all the changes","Can perform all actions with comments","The file favorite state is undefined","Can create files from templates using data from the editor","Can see the information about all users","Can submit forms","Has an avatar"],"templates":true,"avatar":true,"goback":{"blank":false,"url":"https://doc1.pm33.net/example"},"close":{"visible":false},"image":"https://doc1.pm33.net/example/images/uid-1.png"},{"id":"uid-2","name":"Mark Pottato","email":"pottato@example.com","group":"group-2","reviewGroups":["group-2",""],"commentGroups":{"view":"","edit":["group-2",""],"remove":["group-2"]},"userInfoGroups":["group-2",""],"favorite":true,"deniedPermissions":[],"descriptions":["Belongs to Group2","Can review only his own changes or changes made by users with no group","Can view comments, edit his own comments and comments left by users with no group. Can remove his own comments only","This file is marked as favorite","Can create new files from the editor","Can see the information about users from Group2 and users who don’t belong to any group","Can’t submit forms","Has an avatar"],"templates":false,"avatar":true,"goback":{"text":"Go to Documents"},"close":{},"image":"https://doc1.pm33.net/example/images/uid-2.png"},{"id":"uid-3","name":"Hamish Mitchell","email":"mitchell@example.com","group":"group-3","reviewGroups":["group-2"],"commentGroups":{"view":["group-3","group-2"],"edit":["group-2"],"remove":[]},"userInfoGroups":["group-2"],"favorite":false,"deniedPermissions":["copy","download","print"],"descriptions":["Belongs to Group3","Can review changes made by Group2 users","Can view comments left by Group2 and Group3 users. Can edit comments left by the Group2 users","This file isn’t marked as favorite","Can’t copy data from the file to clipboard","Can’t download the file","Can’t print the file","Can create new files from the editor","Can see the information about Group2 users","Can’t submit forms","Can’t close history","Can’t restore the file version"],"templates":false,"avatar":false,"goback":null,"close":{},"image":null},{"id":"uid-0","name":null,"email":null,"group":null,"reviewGroups":null,"commentGroups":{},"userInfoGroups":[],"favorite":null,"deniedPermissions":["protect"],"descriptions":["The name is requested when the editor is opened","Doesn’t belong to any group","Can review all the changes","Can perform all actions with comments","The file favorite state is undefined","Can't mention others in comments","Can't create new files from the editor","Can’t see anyone’s information","Can't rename files from the editor","Can't view chat","Can't protect file","View file without collaboration","Can’t submit forms"],"templates":false,"avatar":false,"goback":null,"close":null,"image":null}];
                for (var i = 0; i < event.data.id.length; i++) {
                    for (var j = 0; j < allUsers.length; j++) {
                        if (allUsers[j].id == event.data.id[i]) {
                            users.push(allUsers[j]);
                            break;
                        }
                    }
                }
                break;
            default:
                users = [{"email":"pottato@example.com","name":"Mark Pottato"},{"email":"mitchell@example.com","name":"Hamish Mitchell"}];
        }

        docEditor.setUsers({
            "c": c,
            "users": users,
        });
    };

    var onRequestSendNotify = function(event) {  // the user is mentioned in a comment
        event.data.actionLink = replaceActionLink(location.href, JSON.stringify(event.data.actionLink));
        var data = JSON.stringify(event.data);
        innerAlert("onRequestSendNotify: " + data);
    };

    var onRequestOpen = function(event) {  // user open external data source
        innerAlert("onRequestOpen");
        var windowName = event.data.windowName;

        requestReference(event.data, function (data) {
            if (data.error) {
                var winEditor = window.open("", windowName);
                winEditor.close();
                innerAlert(data.error, true);
                return;
            }

            var link = data.link;
            window.open(link, windowName);
        });
    };

    var onRequestReferenceData = function(event) {  // user refresh external data source
        innerAlert("onRequestReferenceData");

        requestReference(event.data, function (data) {
            docEditor.setReferenceData(data);
        });
    };

    var requestReference = function(data, callback) {
        innerAlert(data);

        data.directUrl = !!config.document.directUrl;

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "reference");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            innerAlert(xhr.responseText);
            callback(JSON.parse(xhr.responseText));
        }
    };

    var onRequestReferenceSource = function (event) {
        innerAlert("onRequestReferenceSource");
        let xhr = new XMLHttpRequest();
        xhr.open("GET", "files/");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.send();
        xhr.onload = function () {
            if (xhr.status === 200) {
                innerAlert(JSON.parse(xhr.responseText));
                let fileList = JSON.parse(xhr.responseText);
                let firstXlsxName;
                let file;
                for (var i = 0; i < fileList.length; i++) {
                    file = fileList[i];
                    if (file["title"]) {
                        if (getFileExt(file["title"]) === "xlsx")
                        {
                            firstXlsxName = file["title"];
                            break;
                        }
                    }
                }
                if (firstXlsxName) {
                    let data = {
                        directUrl : "" || false,
                        path : firstXlsxName
                    };
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "reference");
                    xhr.setRequestHeader("Content-Type", "application/json");
                    xhr.send(JSON.stringify(data));
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            docEditor.setReferenceSource(JSON.parse(xhr.responseText));
                        } else {
                            innerAlert("/reference - bad status");
                        }
                    }
                } else {
                    innerAlert("No *.xlsx files");
                }
            } else {
                innerAlert("/files - bad status");
            }
        }
    };

    var onRequestSaveAs = function (event) {  //  the user is trying to save file by clicking Save Copy as... button
        var title = event.data.title;
        var url = event.data.url;
        var data = {
            title: title,
            url: url
        }
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "create");
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            innerAlert(xhr.responseText);
            innerAlert(JSON.parse(xhr.responseText).file, true);
        }
    }

    var onRequestRename = function(event) { //  the user is trying to rename file by clicking Rename... button
        innerAlert("onRequestRename: " + JSON.stringify(event.data));

        var newfilename = event.data;
        var data = {
            newfilename: newfilename,
            dockey: config.document.key,
            ext: config.document.fileType
        };
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "rename");
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify(data));
        xhr.onload = function () {
            innerAlert(xhr.responseText);
        }
    };

    var onDocumentReady = function(){
        fixSize();
    };

    config = {
        "document": {
            "directUrl": "",
            "fileType": "docx",
            "info": {
                "owner": "Me",
                "uploaded": "Sun Oct 27 2024",
                "favorite": null
            },
            "key": "118.71.162.143__127.0.0.1new.docx11730015725555",
            "permissions": {
                "chat": true,
                "comment": false,
                "copy": true,
                "download": true,
                "edit": true,
                "fillForms": false,
                "modifyContentControl": false,
                "modifyFilter": true,
                "print": true,
                "review": false,
                "reviewGroups": null,
                "commentGroups": {},
                "userInfoGroups": null,
                "protect": true
            },
            "referenceData": {
                "fileKey": "{\"fileName\":\"new.docx\",\"userAddress\":\"118.71.162.143__127.0.0.1\"}",
                "instanceId": "https://doc1.pm33.net/example"
            },
            "title": "new.docx",
            "url": "https://doc1.pm33.net/example/download?fileName=new.docx&useraddress=118.71.162.143__127.0.0.1"
        },
        "documentType": "word",
        "editorConfig": {
            "actionLink": null,
            "callbackUrl": "https://doc1.pm33.net/example/track?filename=new.docx&useraddress=118.71.162.143__127.0.0.1",
            "coEditing": null,
            "createUrl": "https://doc1.pm33.net/example/editor?fileExt=docx&userid=uid-1&type=desktop&lang=en",
            "customization": {
                "about": true,
                "comments": true,
                "close": {"visible":false},
                "feedback": true,
                "forcesave": false,
                "goback": {"blank":false,"url":"https://doc1.pm33.net/example"},
                "submitForm": true
            },
            "embedded": {
                "embedUrl": "https://doc1.pm33.net/example/download?fileName=new.docx",
                "saveUrl": "https://doc1.pm33.net/example/download?fileName=new.docx",
                "shareUrl": "https://doc1.pm33.net/example/download?fileName=new.docx",
                "toolbarDocked": "top"
            },
            "fileChoiceUrl": "",
            "lang": "en",
            "mode": "edit",
            "plugins": {"pluginsData":[]},
            "templates": [{"image":"","title":"Blank","url":"https://doc1.pm33.net/example/editor?fileExt=docx&userid=uid-1&type=desktop&lang=en"},{"image":"https://doc1.pm33.net/example/images/file_docx.svg","title":"With sample content","url":"https://doc1.pm33.net/example/editor?fileExt=docx&userid=uid-1&type=desktop&lang=en&sample=true"}],
            "user": {
                "group": "",
                "id": "uid-1",
                "image": "https://doc1.pm33.net/example/images/uid-1.png",
                "name": "John Smith"
            }
        },
        "height": "100%",
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJkb2N1bWVudCI6eyJkaXJlY3RVcmwiOiIiLCJmaWxlVHlwZSI6ImRvY3giLCJpbmZvIjp7Im93bmVyIjoiTWUiLCJ1cGxvYWRlZCI6IlN1biBPY3QgMjcgMjAyNCIsImZhdm9yaXRlIjpudWxsfSwia2V5IjoiMTE4LjcxLjE2Mi4xNDNfXzEyNy4wLjAuMW5ldy5kb2N4MTE3MzAwMTU3MjU1NTUiLCJwZXJtaXNzaW9ucyI6eyJjaGF0Ijp0cnVlLCJjb21tZW50IjpmYWxzZSwiY29weSI6dHJ1ZSwiZG93bmxvYWQiOnRydWUsImVkaXQiOnRydWUsImZpbGxGb3JtcyI6ZmFsc2UsIm1vZGlmeUNvbnRlbnRDb250cm9sIjpmYWxzZSwibW9kaWZ5RmlsdGVyIjp0cnVlLCJwcmludCI6dHJ1ZSwicmV2aWV3IjpmYWxzZSwicmV2aWV3R3JvdXBzIjpudWxsLCJjb21tZW50R3JvdXBzIjp7fSwidXNlckluZm9Hcm91cHMiOm51bGwsInByb3RlY3QiOnRydWV9LCJyZWZlcmVuY2VEYXRhIjp7ImZpbGVLZXkiOiJ7XCJmaWxlTmFtZVwiOlwibmV3LmRvY3hcIixcInVzZXJBZGRyZXNzXCI6XCIxMTguNzEuMTYyLjE0M19fMTI3LjAuMC4xXCJ9IiwiaW5zdGFuY2VJZCI6Imh0dHBzOi8vZG9jMS5wbTMzLm5ldC9leGFtcGxlIn0sInRpdGxlIjoibmV3LmRvY3giLCJ1cmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9kb3dubG9hZD9maWxlTmFtZT1uZXcuZG9jeCZ1c2VyYWRkcmVzcz0xMTguNzEuMTYyLjE0M19fMTI3LjAuMC4xIn0sImRvY3VtZW50VHlwZSI6IndvcmQiLCJlZGl0b3JDb25maWciOnsiYWN0aW9uTGluayI6bnVsbCwiY2FsbGJhY2tVcmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS90cmFjaz9maWxlbmFtZT1uZXcuZG9jeCZ1c2VyYWRkcmVzcz0xMTguNzEuMTYyLjE0M19fMTI3LjAuMC4xIiwiY29FZGl0aW5nIjpudWxsLCJjcmVhdGVVcmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9lZGl0b3I_ZmlsZUV4dD1kb2N4JnVzZXJpZD11aWQtMSZ0eXBlPWRlc2t0b3AmbGFuZz1lbiIsImN1c3RvbWl6YXRpb24iOnsiYWJvdXQiOnRydWUsImNvbW1lbnRzIjp0cnVlLCJjbG9zZSI6eyJ2aXNpYmxlIjpmYWxzZX0sImZlZWRiYWNrIjp0cnVlLCJmb3JjZXNhdmUiOmZhbHNlLCJnb2JhY2siOnsiYmxhbmsiOmZhbHNlLCJ1cmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZSJ9LCJzdWJtaXRGb3JtIjp0cnVlfSwiZW1iZWRkZWQiOnsiZW1iZWRVcmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9kb3dubG9hZD9maWxlTmFtZT1uZXcuZG9jeCIsInNhdmVVcmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9kb3dubG9hZD9maWxlTmFtZT1uZXcuZG9jeCIsInNoYXJlVXJsIjoiaHR0cHM6Ly9kb2MxLnBtMzMubmV0L2V4YW1wbGUvZG93bmxvYWQ_ZmlsZU5hbWU9bmV3LmRvY3giLCJ0b29sYmFyRG9ja2VkIjoidG9wIn0sImZpbGVDaG9pY2VVcmwiOiIiLCJsYW5nIjoiZW4iLCJtb2RlIjoiZWRpdCIsInBsdWdpbnMiOnsicGx1Z2luc0RhdGEiOltdfSwidGVtcGxhdGVzIjpbeyJpbWFnZSI6IiIsInRpdGxlIjoiQmxhbmsiLCJ1cmwiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9lZGl0b3I_ZmlsZUV4dD1kb2N4JnVzZXJpZD11aWQtMSZ0eXBlPWRlc2t0b3AmbGFuZz1lbiJ9LHsiaW1hZ2UiOiJodHRwczovL2RvYzEucG0zMy5uZXQvZXhhbXBsZS9pbWFnZXMvZmlsZV9kb2N4LnN2ZyIsInRpdGxlIjoiV2l0aCBzYW1wbGUgY29udGVudCIsInVybCI6Imh0dHBzOi8vZG9jMS5wbTMzLm5ldC9leGFtcGxlL2VkaXRvcj9maWxlRXh0PWRvY3gmdXNlcmlkPXVpZC0xJnR5cGU9ZGVza3RvcCZsYW5nPWVuJnNhbXBsZT10cnVlIn1dLCJ1c2VyIjp7Imdyb3VwIjoiIiwiaWQiOiJ1aWQtMSIsImltYWdlIjoiaHR0cHM6Ly9kb2MxLnBtMzMubmV0L2V4YW1wbGUvaW1hZ2VzL3VpZC0xLnBuZyIsIm5hbWUiOiJKb2huIFNtaXRoIn19LCJoZWlnaHQiOiIxMDAlIiwidG9rZW4iOiIiLCJ0eXBlIjoiZGVza3RvcCIsIndpZHRoIjoiMTAwJSIsImlhdCI6MTczMDAyNjcyNywiZXhwIjoxNzMwMDI3MDI3fQ.oY9L1ysMVC75ItK_bicA4ReqzJejaJiCi0NLajDMb2w",
        "type": "desktop",
        "width": "100%"

    };
    config.events = {
        "onAppReady": onAppReady,
        "onDocumentReady": onDocumentReady,
        "onDocumentStateChange": onDocumentStateChange,
        "onError": onError,
        "onOutdatedVersion": onOutdatedVersion,
        "onMakeActionLink": onMakeActionLink,
        "onMetaChange": onMetaChange,
        "onRequestInsertImage": onRequestInsertImage,
        "onRequestSelectDocument": onRequestSelectDocument,
        "onRequestSelectSpreadsheet": onRequestSelectSpreadsheet,
        "onRequestOpen": onRequestOpen,
    };

    if ("uid-1" != null) {
        config.events.onRequestClose = onRequestClose;
        config.events.onRequestEditRights = onRequestEditRights;
        config.events.onRequestHistory = onRequestHistory;
        config.events.onRequestHistoryData = onRequestHistoryData;
        config.events.onRequestRename = onRequestRename;
        config.events.onRequestUsers = onRequestUsers;
        config.events.onRequestSaveAs = onRequestSaveAs;
        config.events.onRequestSendNotify = onRequestSendNotify;
        config.events.onRequestReferenceData = onRequestReferenceData;
        config.events.onRequestReferenceSource = onRequestReferenceSource;
        if ("uid-1" != "uid-3") {
            config.events.onRequestHistoryClose = onRequestHistoryClose;
            config.events.onRequestRestore = onRequestRestore;
        }
    }

    try {
        var oformParam = new URL(window.location).searchParams.get("oform");
    } catch (e) {}
    if (oformParam == "false") {
        config.document.options = config.document.options || {};
        config.document.options["oform"] = false;
    }

    var connectEditor = function () {
        docEditor = new DocsAPI.DocEditor("iframeEditor", config);
        fixSize();
    };

    // get the editor sizes
    var fixSize = function () {
        if (config.type !== "mobile") {
            return;
        }
        var wrapEl = document.getElementsByTagName("iframe");
        if (wrapEl.length) {
            wrapEl[0].style.height = screen.availHeight + "px";
            window.scrollTo(0, -1);
            wrapEl[0].style.height = window.innerHeight + "px";
        }
    };

    const getFileExt = function (fileName) {
        if (fileName.indexOf(".")) {
            return fileName.split('.').reverse()[0];
        }
        return false;
    };

    if (window.addEventListener) {
        window.addEventListener("load", connectEditor);
        window.addEventListener("resize", fixSize);
        window.addEventListener("orientationchange", fixSize);
    } else if (window.attachEvent) {
        window.attachEvent("onload", connectEditor);
        window.attachEvent("onresize", fixSize);
        window.attachEvent("orientationchange", fixSize);
    }

</script>
</body>
</html>
