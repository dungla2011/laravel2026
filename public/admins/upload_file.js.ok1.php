function byteSize2(bytes) {
    var marker = 1024; // Change to 1000 if required
    var decimal = 3; // Change as required
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

class clsUploadV2 {

    //Dùng để chứa mọi instance của class này, mục đích cho trường hợp multi-upload trên 1 trang
    //mỗi uploadZone có một instance, mảng này để truy ngược các phần tử trong uploadZone được quản lý bởi instance nào
    //Từ một DOM bất kỳ, truy ngược đến gốc  từ đó ra instance tương ứng trong all[] này
    //để lấy ra các setting, như api, ...
    static all = [];
    static countId = 0;

    constructor() {
        clsUploadV2.all.push(this);
        console.log("clsUploadV2.constructor: ", clsUploadV2.all);
    }
    destroy() {
        console.log(" clsUploadV2.destroy");
        let i = clsUploadV2.all.indexOf(this);
        clsUploadV2.all.splice(i, 1);
    }
    static clearAllTreeInstance(){
        console.log(" Clear all Tree instance...");
        clsUploadV2.all = []
    }

    static getStaticCount(){
        clsUploadV2.countId += 1
        return clsUploadV2.countId;
    }

    /**
     * @return clsUploadV2
     */
    static getInstanceTreeBySelector(bind_selector) {
        for (let obj of clsUploadV2.all) {
            if (obj.bind_selector_upload == bind_selector) {
                // console.log(" Found selector = ", tree);
                return obj;
            }
        }
        return null
    }

    url_server = '_need_set_';
    bind_selector_upload = null;
    bind_selector_result = null;
    bind_div_upload_status_all = null;
    bind_file_elm_id = null;

    upload_queue = 0;
    uploading = 0;
    upload_done = 0;
    upload_total = 0;
    upload_error = 0;
    maxFileCC = 2;
    set_parent_id = 0; //thư mục cha khi upload lên nếu có
    mFileUpload = [];

    dropAreaUpload = null;

    //Nếu có hàm này, sẽ được gọi sau khi upload done, để làm tiếp các task nếu cần...
    upload_done_call_function = '';

    preventDefaults(e){
        e.preventDefault()
        e.stopPropagation()
    }

    highlight(e) {
        this.dropAreaUpload.classList.add('highlight')
    }

    unhighlight(e) {
        this.dropAreaUpload.classList.remove('active')
    }

    handleDrop(e) {
        var dt = e.dataTransfer
        var files = dt.files

        console.log(" FIles : " + files);

        this.handleFiles(files)
    }

    updateProgress(fileNumber, percent, loaded) {
        let progressBarI = document.getElementById('progress_bar' + fileNumber);
        progressBarI.value = percent;
        var speedAll = (loaded / (Date.now() - this.mFileUpload[fileNumber].startTimeUploadLad) / 1000).toFixed(2);
        var speed = ((loaded - this.mFileUpload[fileNumber].lastByteUploadLad) / (Date.now() - this.mFileUpload[fileNumber].lastTimeUploadLad) / 1000).toFixed(2);
        if (isNaN(speed)) {
            return;
        }
        this.mFileUpload[fileNumber].lastTimeUploadLad = Date.now();
        this.mFileUpload[fileNumber].lastByteUploadLad = loaded;
        document.getElementById('percent_upload_' + fileNumber).innerText = percent.toFixed(2) + " % (" + speed + " MB/s)";
    }

    static handleFiles(obj, files) {
        let idParent = $(obj).parents(".upload_area").attr("id")
        console.log("IDP = " + idParent);

        files = [...files]
        console.log(" handleFiles files : ", files);

        //Get file size, filetype: to limit size, filetype here

        //initializeProgress(files.length)

        let objUpload = clsUploadV2.getInstanceTreeBySelector(idParent);

        console.log(" ObjUpload = ", objUpload);

        // files.forEach(objUpload.addFileToUpload)

        let i = 0;
        for (let file1 of files) {
            objUpload.addFileToUpload(file1,i)
            i = i + 1
        }

        //files.forEach(previewFile)
    }

    countUploadInfo() {
        var ret = 0;
        this.upload_queue = 0;
        this.uploading = 0;
        this.upload_done = 0;
        this.upload_total = 0;

        for(let idFile in this.mFileUpload ){
            let fileUp = this.mFileUpload[idFile];
            if (fileUp.upload_status_lad == 'doing')
                this.uploading++;
            if (fileUp.upload_status_lad == 0)
                this.upload_queue++;
            if (fileUp.upload_status_lad == 'error')
                this.upload_error++;
            if (fileUp.upload_status_lad == 'done')
                this.upload_done++;
        }

        // for (var i = 0; i < this.mFileUpload.length; i++) {
        //     if (this.mFileUpload[i].upload_status_lad == 'doing')
        //         this.uploading++;
        //     if (this.mFileUpload[i].upload_status_lad == 0)
        //         this.upload_queue++;
        //     if (this.mFileUpload[i].upload_status_lad == 'error')
        //         this.upload_error++;
        //     if (this.mFileUpload[i].upload_status_lad == 'done')
        //         this.upload_done++;
        // }

        return this.uploading;
    }

    showStatusUpload() {
        console.log(" showStatusUpload ... ");
        $("span[data-id='upload_info']").html("Doing: " + this.countUploadInfo() + ", done: " + this.upload_done + ', queue: ' + this.upload_queue + '');
    }

    startUploadOne(file) {

        file.upload_status_lad = 'doing';
        file.startTimeUploadLad = Date.now();
        file.lastTimeUploadLad = Date.now();
        file.lastByteUploadLad = 0;

        console.log(" Continue upload : ", file.upload_id_lad, " = ", file);

        var url = this.url_server;
        var xhr = new XMLHttpRequest()

        file.xhrLad = xhr;

        var i = file.upload_id_lad;

        console.log("Upload now: ", file);

        console.log("countFile = " + i);

        this.showStatusUpload();

        var formData = new FormData()
        xhr.open('POST', url, true)
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest')


        let that = this

        // Update progress (can be used to show progress indicator)
        xhr.upload.addEventListener("progress", function (e) {
            //console.log("addEventListener Update progressI: " , i);
            that.updateProgress(i, (e.loaded * 100.0 / e.total) || 100, e.loaded)
        })

        xhr.addEventListener('readystatechange', function (e) {
            if (xhr.readyState == 4 && xhr.status == 200) {
                that.updateProgress(i, 100) // <- Add this
                file.upload_status_lad = 'done';

                $("#div_upload_status_one_" + file.upload_id_lad).remove();
                that.showStatusUpload();
                console.log(" DONE ? uploadIdLad = ", file.upload_id_lad);
                console.log(" server return: ", xhr.response);


                $("#result-area-upload").show();

                if (that.upload_done_call_function) {
                    //Create this function on production:
                    window[that.upload_done_call_function](xhr.response, that);
                }

                $("#" + that.bind_selector_result).prepend(xhr.response + "<br>")

                that.upload_done++;
                that.excuteUploadFile();


            } else if (xhr.readyState == 4 && xhr.status != 200) {
                // Error. Inform the user
                that.showStatusUpload();
                console.log(" Error ? uploadIdLad = ", xhr.uploadIdLad);
                console.log(" server return: ", xhr.response);

                $("#status_upload_error_" + file.upload_id_lad).html(" * ERROR Upload, Code: " + xhr.status)

                file.upload_status_lad = 'error';
                that.upload_error++;

                that.excuteUploadFile();
            }
        })

        formData.append('upload_preset', 'ujpu6gyk')
        formData.append('set_parent_id', this.set_parent_id)
        formData.append('file', file)

        xhr.send(formData)
    }

    excuteUploadFile() {

        console.log(" excuteUploadFile ... ", this.mFileUpload );
        for(let idFile in this.mFileUpload ){
            let fileUp = this.mFileUpload[idFile];

            console.log(" fileUp now " , fileUp);

            if (fileUp.upload_status_lad == 0) {
                if (this.countUploadInfo() >= this.maxFileCC) {
                    console.log(" Number thread ", this.maxFileCC, " ...");
                    return;
                }
                this.startUploadOne(fileUp);
            }
        }

        // for (var k = 0; k < this.mFileUpload.length; k++) {
        //     if (this.mFileUpload[k].upload_status_lad == 0) {
        //         if (this.countUploadInfo() >= this.maxFileCC) {
        //             console.log(" Number thread ", this.maxFileCC, " ...");
        //             return;
        //         }
        //         var file = this.mFileUpload[k];
        //         this.startUploadOne(file);
        //     }
        // }
    }

    addFileToUpload(file) {


        file.upload_status_lad = 0;
        file.upload_id_lad = this.bind_selector_upload + "_" + clsUploadV2.getStaticCount();

        console.log(" Thisx = ", this);
        console.log(" Filex = ", file);

        //this.mFileUpload.push(file);
        this.mFileUpload[file.upload_id_lad] = file;
        this.upload_total++;
        let i = file.upload_id_lad;
        document.querySelector('[data-id="' + this.bind_div_upload_status_all + '"]').insertAdjacentHTML('beforeend',
            '<div style="margin-bottom:5px; background-color: snow; padding: 5px 8px; border: 1px solid #ccc; border-radius: 5px" ' +
            'id="div_upload_status_one_' + i + '"> ' + file.name + ' (' + byteSize2(file.size) + ') <br/> ' +
            '<button title="cancel upload" id="cancel_upload_' + i + '" style="margin-top: 5px; font-size: smaller; border-radius: 5px"> Cancel </button>' +
            '<progress class="progress_bar" id="progress_bar' + i + '" max=100 value=0></progress> <span id="percent_upload_' + i + '"> ... </span> ' +
            '<span style="color: red" id="status_upload_error_' + i + '"></span>' +
            '</div>');
        this.showStatusUpload();

        //Excute Upload right after add files, or add a button to start upload with this command:
        this.excuteUploadFile()
    }

    //Call In document Ready:
    initUpload(){
        // ************************ Drag and drop ***************** //
        this.dropAreaUpload = document.getElementById(this.bind_selector_upload)

// Prevent default drag behaviors
        if (this.dropAreaUpload) {
            ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                this.dropAreaUpload.addEventListener(eventName, this.preventDefaults, false)
                document.body.addEventListener(eventName, this.preventDefaults, false)
            })

            // Highlight drop area when item is dragged over it
            ;['dragenter', 'dragover'].forEach(eventName => {
                this.dropAreaUpload.addEventListener(eventName, this.highlight, false)
            })

            ;['dragleave', 'drop'].forEach(eventName => {
                this.dropAreaUpload.addEventListener(eventName, this.unhighlight, false)
            })

            // Handle dropped files
            this.dropAreaUpload.addEventListener('drop', this.handleDrop, false)

        }

        $(document).on('click', "[id^=cancel_upload_]", function () {
            let idParent = $(this).parents(".upload_area").attr("id")
            console.log("IDP = " + idParent);
            let objUpload = clsUploadV2.getInstanceTreeBySelector(idParent);
            var id = this.id.replace('cancel_upload_', '')
            console.log(" cancel_upload_ id = " + id);
            //Stop Upload:
            objUpload.mFileUpload[id].xhrLad.abort();
            $("#div_upload_status_one_" + id).remove();
            objUpload.showStatusUpload();
        })
    }
}

$(document).ready(function () {

});
