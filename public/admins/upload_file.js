class clsUploadV2 {

    url_server = '_need_set_';
    bind_selector_upload = null;

    upload_queue = 0;
    uploading = 0;
    upload_done = 0;
    upload_total = 0;
    upload_error = 0;
    maxFileCC = 2;
    set_parent_id = 0; //thư mục cha khi upload lên nếu có
    mFileUpload = [];
    maxSizeUpload = 0;

    dropAreaUpload = null;

    //Nếu có hàm này, sẽ được gọi sau khi upload done, để làm tiếp các task nếu cần...
    upload_done_call_function = '';

    //Hàm sễ gọi trước khi đưa file vào cloud, có the là không đưa vào cloud, mà chỉ để xử lý trước đó
    upload_before_call_function = '';

    //nếu có
    bearerToken = '';


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
        console.log(" Clear all Tree instance1...");
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


    preventDefaults(e){
        e.preventDefault()
        e.stopPropagation()
    }

    highlight(e) {
        if(this.dropAreaUpload && this.dropAreaUpload.classList)
            this.dropAreaUpload.classList.add('highlight')
    }

    unhighlight(e) {
        if(this.dropAreaUpload && this.dropAreaUpload.classList)
            this.dropAreaUpload.classList.remove('active')
    }

    handleDrop(e) {
        var dt = e.dataTransfer
        var files = dt.files

        console.log(" FIles : " + files);

        clsUploadV2.handleFiles(this,files)
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

    static handleFiles(obj, files, maxSize = '') {

        let idParent = $(obj).parents(".upload_zone_glx").attr("id")

        console.log("IDP = " + idParent);

        files = [...files]

        console.log(" handleFiles files : ", files);

        //Get file size, filetype: to limit size, filetype here

        //initializeProgress(files.length)

        let objUpload = clsUploadV2.getInstanceTreeBySelector(idParent);

        console.log(" ObjUpload = ", objUpload);

        if(maxSize)
            maxSize = parseInt(maxSize);

        if(objUpload?.maxSizeUpload && objUpload?.maxSizeUpload > 0)
            maxSize = objUpload.maxSizeUpload

        // files.forEach(objUpload.addFileToUpload)

        let i = 0;
        for (let file1 of files) {
            console.log(" file1.size  maxSize ", file1.size  , maxSize);
            if(maxSize)
            if(parseInt(file1.size) > maxSize){
                alert("Cỡ file quá giới hạn:\n" + file1.name + " | " + byteSize2(file1.size,1) + " > " + byteSize2(maxSize,1))
                return;
            }


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

        if(this.bearerToken){
            console.log("Bear: "  + this.bearerToken);
            xhr.setRequestHeader('Authorization', 'Bearer ' + this.bearerToken);
        }


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



                // $("#result-area-upload").show();

                $("#" +that.bind_selector_upload).find('.upload_result_all').show()

                if (that.upload_done_call_function) {
                    //Create this function on production:
                    window[that.upload_done_call_function](xhr.response, that);
                }

                let link1 = 'Error: not found link!';

                try{
                    let json1 = JSON.parse(xhr.response);

                    link1 = json1.payload.link
                }catch (e) {

                }
                //$("#" + that.bind_selector_result).prepend(xhr.response + "<br>")
                $("#" +that.bind_selector_upload).find('.upload_result_all').prepend(" <a target='_blank' href='" + link1 +"'> " + link1 + "</a>" + "<br>")

                that.upload_done++;
                that.excuteUploadFile();


            } else if (xhr.readyState == 4 && xhr.status != 200) {

                if(xhr.status == 413){
                    alert("File quá kích thước?");
                }
                else
                    alert("Có lỗi upload ");

                // Error. Inform the user
                that.showStatusUpload();
                console.log(" Error ? uploadIdLad = ", xhr.uploadIdLad);
                console.log(" server return1: ", xhr.response);



                let svMess = " Lỗi chưa định nghĩa!"
                if(xhr.response){
                    try {
                        let ret = JSON.parse(xhr.response)
                        if (ret.message)
                            svMess = ret.message;
                    }
                    catch (e){

                    }
                }

                $("#status_upload_error_" + file.upload_id_lad).html(" * ERROR Upload, Code: " + xhr.status + " , " + svMess)

                file.upload_status_lad = 'error';
                that.upload_error++;

                that.excuteUploadFile();
            }
        })

        formData.append('upload_preset', 'ujpu6gyk')
        formData.append('set_parent_id', this.set_parent_id)
        formData.append('file_data', file)

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

        let uploadZone = document.getElementById(this.bind_selector_upload)

        //document.querySelector('[data-id="' + this.bind_div_upload_status_all + '"]').insertAdjacentHTML('beforeend',
        uploadZone.querySelector('.upload_status_some').insertAdjacentHTML('beforeend',
            '<div class="upload_status_one" style="" ' +
            'id="div_upload_status_one_' + i + '"> ' + file.name + ' (' + byteSize2(file.size) + ') <br/> ' +
            '<span title="cancel upload" class="cancel_upload" id="cancel_upload_' + i + '" style=""> Cancel </span>' +
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
        let uploadZone = document.getElementById(this.bind_selector_upload)

        if(!uploadZone){
            alert("Not found upload zone id? " + this.bind_selector_upload)
            throw "Not found upload zone id? " + this.bind_selector_upload
        }

        this.dropAreaUpload = uploadZone.querySelector('.drop_area')

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

    }


}

$(document).ready(function () {
    $(document).on('click', "[id^=cancel_upload_]", function () {

        console.log("Cancel upload ...");

        let idParent = $(this).parents(".upload_zone_glx").attr("id")

        console.log("IDP = " + idParent);

        if(!idParent){
            //Nếu bị gọi nhiều lần thì sẽ kko cón parent
            return;
        }

        let objUpload = clsUploadV2.getInstanceTreeBySelector(idParent);
        var id = this.id.replace('cancel_upload_', '')
        console.log(" cancel_upload_ id = " + id);
        //Stop Upload:
        objUpload.mFileUpload[id].xhrLad.abort();
        $("#div_upload_status_one_" + id).remove();
        objUpload.showStatusUpload();
    })
});
