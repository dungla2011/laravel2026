var jclsVmCtrl = function () {
    currentVm : null;
}

jclsVmCtrl.vmPowerReset = function() {
    if (!jclsVmCtrl.currentVm) {
        alert("NOT select VM?");
        return;
    }

    if(!confirm("Are you sure to Reset Vps?")){
        return;
    }

    jclsVmCtrl.wmks.destroy();
    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=resetvm&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {
        if(!ClassApi.checkReturnApi(data)){
            return;
        }
        setTimeout(function () {
            jclsVmCtrl.updateNewVM();
        }, 2000)
    });
}

jclsVmCtrl.vmPowerOff = function() {

    if (!jclsVmCtrl.currentVm) {
        alert("NOT select VM?");
        return;
    }

    if(!confirm("Are you sure to Power Off Vps?")){
        return;
    }

    $("#wmksContainer").text(' --- OFF VM ---');

    jclsVmCtrl.wmks.destroy();

    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=offvm&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {
        ClassApi.checkReturnApi(data);
    });
}

jclsVmCtrl.vmPowerOn = function() {
    if (!jclsVmCtrl.currentVm) {
        alert("NOT select VM?");
        return;
    }

    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=onvm&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {
        if(ClassApi.checkReturnApi(data)){
            jclsVmCtrl.wmks.destroy();
        }
    });
}



jclsVmCtrl.updateSc = function() {
    jclsVmCtrl.wmks.updateScreen();
}

jclsVmCtrl.sendCtrlAltDel = function() {
    jclsVmCtrl.wmks.sendKeyCodes([17, 18, 46]);
}

jclsVmCtrl.sendWindowU= function() {
    jclsVmCtrl.wmks.sendKeyCodes([91, 85]);
}

jclsVmCtrl.vmSetBootFromCD = function(ok) {
    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=vmSetBootFromCD&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {

    });
}

jclsVmCtrl.vmSetBootFromDisk = function() {
    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=vmSetBootFromDisk&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {

    });
}

jclsVmCtrl.vmSetEnableDisableNic = function(enable) {
    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=vmSetEnableNic&enable=" + enable + "&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {

    });
}


jclsVmCtrl.updateNewVM = function(vmId, elmId) {

    if(!$('#vmGlxContainer').length){
        alert("Error: Not define id: vmGlxContainer?");
        return;
    }

    if (typeof jclsVmCtrl.wmks === 'undefined')
    {
        jclsVmCtrl.wmks = WMKS.createWMKS("vmGlxContainer", {})
            .register(WMKS.CONST.Events.CONNECTION_STATE_CHANGE, function (event, data) {
                if (data.state == WMKS.CONST.ConnectionState.CONNECTED) {
                    console.log("connection state change : connected");
                }
            });
    }

    if(!vmId || typeof vmId === "undefined" || vmId.length == 0){

    }else
        jclsVmCtrl.currentVm = vmId;

    var urlGet = "/a_p_i/member-hosting/get-console-ticket?cmd=getTicket&vmid=" + jclsVmCtrl.currentVm;
    $.get(urlGet, function (data, status) {


        // if(!ClassApi.checkReturnApi(data)){
        //     $("#vmGlxContainer").html('<div style="text-align: center"> <br> <br> <h2>VM is OFF?</h2> <br> Server return: ' + data + '</div>');
        //     return;
        // }

        $("[id^='vm_id_in_div_']").css("color", '#337ab7');

        if(elmId)
            $("#" + elmId).css("color", 'red');

        if (!jctool.checkJsonDecode(data)) {

            //alert("Not valid return json? " + data);

            var ret1 = data.payload;
            // if(data.payload != 'undefined')
            //     ret1 = data.payload;

            var ret2 = JSON.stringify(data);

            $("#vmGlxContainer").html('<div style="text-align: center; "> <br> <br> <h2>Status: </h2> <br> <b style="color: red" data-code-pos="qqq1708996234791">Server return: ' + ret1 + ' </b> <br>' +  ret2 +'</div>');
            jclsVmCtrl.wmks.destroy();
            jclsVmCtrl.wmks = WMKS.createWMKS("vmGlxContainer", {})
                .register(WMKS.CONST.Events.CONNECTION_STATE_CHANGE, function (event, data) {
                    if (data.state == WMKS.CONST.ConnectionState.CONNECTED) {
                        console.log("connection state change : connected");
                    }
                });

            return;
        }
        else{
            //alert(" Not valid return json? ");
            //return;
        }

        $("#vmGlxContainer").text('');
        jclsVmCtrl.wmks.destroy();
        jclsVmCtrl.wmks = WMKS.createWMKS("vmGlxContainer", {})
            .register(WMKS.CONST.Events.CONNECTION_STATE_CHANGE, function (event, data) {
                if (data.state == WMKS.CONST.ConnectionState.CONNECTED) {
                    console.log("connection state change : connected");
                }
            });

        var mm = JSON.parse(data);

        if (!Array.isArray(mm)) {
            alert("Not valid info vm?");
            return;
        }

        if (!mm[0] || !mm[1]) {
            alert("Error: " + data);
            return;
        }

        //$("td[id^=" + value + "]")

        var domainPort = mm[0];
        var ticket = mm[1];




        jclsVmCtrl.wmks.connect("wss://" + domainPort + "/ticket/" + ticket);

        console.log(" domainPort = " + domainPort);
        console.log("wss://" + domainPort + "/ticket/" + ticket)
        //alert("Data: " + data + "\nStatus: " + status);
    });
}
