var jctool = {

}

jctool.deleteRemoveLocalStorage = function (key){
    window.localStorage.removeItem(key);
}
jctool.setLocalStorage = function (key, val){
    window.localStorage.setItem(key, val);
}

jctool.getLocalStorage = function (key){
    return window.localStorage.getItem(key);
}


jctool.deleteCookie = function(cname) {
    document.cookie = cname +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

jctool.setCookie = function(cname, cvalue, exdays = 1) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

jctool.getCookie = function(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
/* Get to one object Object
    var mpar = jctool.getUrlVars();
    for (var prop in mpar) {
            // skip loop if the property is from prototype
            if (!mpar.hasOwnProperty(prop)) continue;
            console.log(prop + " = " + mpar[prop]);
        }
* */
jctool.getUrlVars = function () {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

jctool.getUrlParam = function (parameter, defaultvalue) {
    var urlparameter = defaultvalue;
    if(window.location.href.indexOf(parameter) > -1){
        urlparameter = jctool.getUrlVars()[parameter];
    }
    if(!urlparameter || urlparameter === NaN)
        return null;
    return urlparameter;
}

jctool.getUrlVarsOfTopWindow = function () {
    var vars = {};
    var parts = top.window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

jctool.getUrlParamOfTopWindow = function (parameter, defaultvalue) {
    var urlparameter = defaultvalue;
    if(top.window.location.href.indexOf(parameter) > -1){
        urlparameter = jctool.getUrlVarsOfTopWindow()[parameter];
    }
    if(!urlparameter || urlparameter === NaN)
        return null;
    return urlparameter;
}

jctool.getUriFull = function(){
    var urlFull = location.href;
    if(location.port){
        var ret = location.href.split(location.hostname + ":" + location.port + "/");
    }
    else
        var ret = location.href.split(location.hostname + "/");
    return ret[1];
}

/**
 * setUrlParamString("https://abc.com", 'a',1)      => https://abc.com?a=1
 * setUrlParamString("https://abc.com?", 'a',1)     => https://abc.com?a=1
 * setUrlParamString("https://abc.com?x=2", 'a',1)  => https://abc.com?x=2&a=1
 *
 * @param s
 * @param key
 * @param value
 * @returns {string}
 */
jctool.setUrlParamString = function(s, key,value)
{

    //If it has /
    // if(s.indexOf("?") > 0 && s.lastIndexOf("/") > 0){
    //     s = s.split("?")[1];
    //     console.log(" *** News = " + s);
    // }


    //if(value ==  null || !value)
    //  return s;

    // if(value === null)
    //     alert("NULL1 ");
    // if(value == null)
    //     alert("NULL2 ");

    key = encodeURIComponent(key); value = encodeURIComponent(value);

    console.log(" Change from: " + s , ", key = " + key, " / value = " + value);


    var kvp = key+"="+value;

    var r = new RegExp("(&|\\?)"+key+"=[^\&]*");

    if(s=='#')
        s = '';

    if(s && s.length)
        s = s.replace(r,"$1"+kvp);
    else{

        return "?" + key + '=' + value;
    }

    if(s.indexOf("?") == -1){
        s = s + '?';
    }

    if(!RegExp.$1) {s += (s.length>0 ? '&' : '?') + kvp;};

    //again, do what you will here


    //Them key vao neu chua co:
    if(s.indexOf(key + "=") <=0){
        if(s.indexOf("?") >= 0)
            s = s + '&' + key + '=' + value;
        else
            s = s + '?' + key + '=' + value;
    }

    if(value == null || value === 'null' || !value){

        console.log(" ---- delete key: " + key);
        if(s){
            s = s.replace("&" + key + "=null", '');
            s = s.replace("&" + key + "=", '');
            s = s.replace("?" + key + "=null", '?');
            s = s.replace("?" + key + "=", '?');
        }
    }

    s = s.replace("?&", '?');

    console.log(" Change link to: " + s);

    return s;
}

jctool.setCurrentUrlParamAndGo = function(key,value, returnStr = 0)
{

    console.log(" KEy/Val: " + key + " / " + value);

    key = encodeURIComponent(key); value = encodeURIComponent(value);

    var s = document.location.search;
    var kvp = key+"="+value;

    var r = new RegExp("(&|\\?)"+key+"=[^\&]*");

    console.log(" document.location.search: " + document.location.search);

    s = s.replace(r,"$1"+kvp);

    if(!RegExp.$1) {s += (s.length>0 ? '&' : '?') + kvp;};

    //again, do what you will here

    console.log(" Change page param: " + s);

    //Them key vao neu chua co:
    if(s.indexOf(key + "=") <=0){
        if(s.indexOf("?") >= 0)
            s = s + '&' + key + '=' + value;
        else
            s = s + '?' + key + '=' + value;
    }

    if(returnStr)
        return s;
    //document.location.search = s;

    var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + s;
    window.history.pushState({ path: newurl }, '', newurl);

}

jctool.checkIdInStringComma = function(str, num) {
    var str1 = ","  + str + ',';

    if(str1.indexOf("," + num + ',') != -1){
        return 1;
    }

    return 0
}

jctool.removeNumberInStringComma = function(str, num) {
    var str1 = "," + str + ',';

    //khong co thi bo qua
    if (str1.indexOf("," + num + ',') == -1) {
        return str;
    }

    str1 = str1.replace("," + num + ',' , ",");
    str1 = jctool.trimLeftRightAndRemoveDoubleComma(str1);
    return str1;
}
//Neu da co roi thi khong add:
jctool.addNumberInStringComma = function(str, num) {

    if(num == undefined || Number.isNaN(num))
        return str;
    if(str == undefined)
        return num

    var str1 = ","  + str + ',';
    //co roi thi ko them
    if(str1.indexOf("," + num + ',') != -1){
        return str;
    }

    str1 += "," + num;
    str1 = jctool.trimLeftRightAndRemoveDoubleComma(str1);

    return str1;
}


// Chuẩn hóa lại chuỗi comma id...
jctool.trimLeftRightAndRemoveDoubleComma = function(str) {
    var ret = jctool.trimLeftRight(str, ',');
    ret = ret.replace(',,',',').replace(',,',',').replace(',,',',');
    if(ret == ',')
        ret = '';
    return ret;
}

jctool.trimLeftRight = function(str, ch) {
    var start = 0,
        end = str.length;

    while(start < end && str[start] === ch)
        ++start;

    while(end > start && str[end - 1] === ch)
        --end;
    return (start > 0 || end < str.length) ? str.substring(start, end) : str;
}

jctool.getUrlNotParam = function(){
    return window.location.href.split('?')[0];
}

jctool.checkJsonDecode = function (str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

//https://www.aliciaramirez.com/closing-tags-checker/
jctool.isValidHtmlCheck = function () {

}

jctool.nowy = function () {
    var d = new Date(),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;
    return [year, month, day].join('-');
}

/**
 * Convert a string to HTML entities
 */
jctool.toHtmlEntities = function(string) {
    return string.replace(/./gm, function(s) {
        // return "&#" + s.charCodeAt(0) + ";";
        return (s.match(/[a-z0-9\s]+/i)) ? s : "&#" + s.charCodeAt(0) + ";";
    });
};

/**
 * Create string from HTML entities
 */
jctool.fromHtmlEntities = function(string) {
    return (string+"").replace(/&#\d+;/gm,function(s) {
        return String.fromCharCode(s.match(/\d+/gm)[0]);
    })
};


//Resize image before upload js:
//https://pqina.nl/blog/compress-image-before-upload/
jctool.compressImage = async (file, {maxSize = 800, quality = 0.95, type = file.type}) => {
    // Get as image data
    const imageBitmap = await createImageBitmap(file);

    // Draw to canvas
    const canvas = document.createElement('canvas');
    // canvas.width = imageBitmap.width;
    // canvas.height = imageBitmap.height;
    let width = imageBitmap.width;
    let height = imageBitmap.height;

    if (width > height) {
        if (width > maxSize) {
            height *= maxSize / width;
            width = maxSize;
        }
    } else {
        if (height > maxSize) {
            width *= maxSize / height;
            height = maxSize;
        }
    }

    canvas.width = width;
    canvas.height = height;


    const ctx = canvas.getContext('2d');
    ctx.drawImage(imageBitmap, 0, 0, width, height);

    // Turn into Blob
    const blob = await new Promise((resolve) =>
        canvas.toBlob(resolve, type, quality)
    );

    // Turn Blob into File
    return new File([blob], file.name, {
        type: blob.type,
    });
};

function byteSize2(bytes, decimal = 2) {
    var marker = 1024; // Change to 1000 if required
    var kiloBytes = marker; // One Kilobyte is 1024 bytes
    var megaBytes = marker * marker; // One MB is 1024 KB
    var gigaBytes = marker * marker * marker; // One GB is 1024 MB
    var teraBytes = marker * marker * marker * marker; // One TB is 1024 GB

    // return bytes if less than a KB
    if (bytes < kiloBytes) return bytes + " Bytes";
    // return KB if less than a MB
    else if (bytes < megaBytes) return (bytes / kiloBytes).toFixed(decimal) + " KB";
    // return MB if less than a GB
    else if (bytes < gigaBytes) return (bytes / megaBytes).toFixed(decimal) + " MB";
    // return GB if less than a TB
    else return (bytes / gigaBytes).toFixed(decimal) + " GB";
}

function showWaittingIcon() {
    $("#waitting_icon").show();
}

function hideWaittingIcon() {
    $("#waitting_icon").hide();
}
