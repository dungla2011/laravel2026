
class clsTableMngJs {

    static getMaxRowColCell1(){
        let maxRow = -1, maxCol = -1;
        $('.divTable2Cell').each(function (){
            if(maxRow < parseInt($(this).attr('data-tablerow')))
                maxRow = parseInt($(this).attr('data-tablerow'))
            if(maxCol < parseInt($(this).attr('data-tablecol')))
                maxCol = parseInt($(this).attr('data-tablecol'))
        })

        return [maxRow, maxCol];
    }

    static getSelectingCheckBox = function (){
        let mIdSelected = []
        $(".select_one_check:checked").each(function (){
            // console.log(" Check id ", this.getAttribute('data-id'));
            if(this.getAttribute('data-id'))
                mIdSelected.push(this.getAttribute('data-id'))
        })
        return mIdSelected
    }


    static updateListIdInsert(idList){

        for(let id1 in idList) {
            let newId = idList[id1];
            console.log(" ID1 / Val = ", id1, newId);


            let objDiv = $("div.divTable2Row[data-id=" + id1 + "]");
            let cloneMe = objDiv.clone();

            let [maxR, maxC] = clsTableMngJs.getMaxRowColCell1();


            objDiv.find("*[data-id=" + id1 + "]").prop("data-id", newId)
            objDiv.find("*[data-id=" + id1 + "]").attr("data-id", newId)
            objDiv.find("*[data-id=" + newId + "]").attr("data-id", newId)
            objDiv.find("*[data-id=" + newId + "]").prop("data-id", newId)


            objDiv.find("div.id_data").text(newId);
            objDiv.find("div.id_data").css('color', 'inherit');

            objDiv.find("input").each(function () {
                //data-autocomplete-id=
                if ($(this).attr('data-field') == 'id') {
                    $(this).val(newId)
                }

                if ($(this).attr('data-autocomplete-id')) {
                    // console.log(" INPUT ", this);
                    let oldId = $(this).attr('data-autocomplete-id');
                    let newAuto = oldId.replace(id1, newId);
                    $(this).attr('data-autocomplete-id', newAuto);
                    $(this).prop('data-autocomplete-id', newAuto);
                }
            });

            objDiv.attr("data-id", newId)
            objDiv.prop("data-id", newId);

            //Clone ra 1 div mới
            //divTable2Body
            cloneMe.attr('data-id', id1);
            cloneMe.prop('data-id', id1);

            cloneMe.find("div").attr('data-tablerow' , maxR+1);
            cloneMe.find("div").prop('data-tablerow' , maxR+1);

            cloneMe.appendTo(".divTable2Body");
            cloneMe.find("input").each(function (){
                $(this).val('');
            })
        }
    }

    /**
     * Format lai de Save len sv
     */
    static formatDateTimeVn(dataId0, field){
        let newValFormat = '';
        let field1 = field;
        //Format lại giá trị để gửi lên dateformat phù hợp trong db
        let that = $("input.input_value_to_post[data-field=" + field + "][data-id='" + dataId0 + "']")

        console.log("formatDateTimeVn  ", field1, dataId0);

        // let dataId = $(this).attr('data-id');
        // if(dataId0)
        //     if(dataId !== dataId0)
        //         return;

        // let field2 = $(this).attr('data-field');

        console.log("Change ...", $(that).val(), field);

        let newVal = $(that).val();

        if(!newVal || newVal[0] == '_'){
            // $(this).attr('value','')
            // $(this).prop('value','')
            return '';
        }

        //Nếu 4 số đầu là số, nghĩa là năm thì ko cần format lại
        if(!isNaN(newVal.substr(0,4))){
            console.log(" 4 số đầu là năm, ko cần format lại : ", newVal);
            return newVal;
        }

        if(newVal){
            let date = newVal.split(" ")[0];
            let time = newVal.split(" ")[1];

            newValFormat = date.split("/")[2] + "-" + date.split("/")[1] + '-' + date.split("/")[0]
            if(time)
                newValFormat += " " + time;

            // $(this).attr('value',newValFormat)
            // $(this).prop('value',newValFormat)
            // return newValFormat;
        }

        return newValFormat;
    }

    static saveOneIdTable(dataId, callBack){
        let user_token = jctool.getCookie('_tglx863516839');



        var allData = {};
        let isEmpty = 1;
        $("input.input_value_to_post[data-id='" + dataId + "'], textarea.input_value_to_post[data-id='" + dataId + "']").each(function (){
            if($(this).hasClass('input_value_to_post')){
                // console.log(" FIELD = " + $(this).data('field') + " / val = " +  $(this).val());
                if($(this).attr('data-edit-able') == 1){
                    let value = $(this).val();
                    let field = $(this).data('field');
                    // console.log(" Editable data-edit-able: ", $(this).data('data-edit-able'));
                    console.log("Editable field1: ", field, value, $(this).attr('value'));
                    if(value || value === 0 || value === '0')
                        isEmpty = 0;

                    if($(this).hasClass('edit_date_time') || $(this).hasClass('edit_date')){
                        let value0 = value
                        value = clsTableMngJs.formatDateTimeVn(dataId, field);
                        console.log(" Oldval/newVal = ",value0, "/" , value);
                    }

                    allData[field] = value;
                }
            }
        })

        //
        if(isEmpty && dataId < 0){
            console.log("Không update vì ko có giá trị insert nào!")
            return;
        }

        console.log("allData2: ", allData);

        let urlOrg = $('#div_container').attr("data-api-url-update-one");

        //urlOrg nếu không có /update ở cuoi thi them vao
        if(!urlOrg.endsWith("/update"))
            urlOrg += "/update";

        let urlPost = urlOrg + "/" + dataId ;

        console.log("DataApi1 = "+ urlPost);
        showWaittingIcon();

        $.ajax({
            url: urlPost,
            type: 'POST',
            data: allData,
            // async: async1,
            headers: {
                'Authorization': 'Bearer ' + user_token
                // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {
                hideWaittingIcon();
                console.log(" RET212 = ", result);

                if(result.payload){
                    showToastInfoTop(result.payload);
                    console.log(" result.payload1 = ", result.payload);
                    if(result.payload.insert_list){
                        clsTableMngJs.updateListIdInsert(result.payload.insert_list)
                    }
                    if(callBack)
                        callBack(1)
                }
                else{
                    if(callBack)
                        callBack(0)
                    alert("Có lỗi update: \r\n" + JSON.stringify(result))
                }



                //showToastInfoTop(" Done ?");


                // showToastInfoTop(" Done ?");
                // if(result.payload && result.payload.insert_list){
                //     clsTableMngJs.updateListIdInsert(result.payload.insert_list)
                // }

            },
            error: function (result) {
                if(callBack)
                    callBack(0)
                hideWaittingIcon();
                console.log(" RET313 = ", result);
                if(result?.responseJSON?.message){
                    alert("Error: " + result?.responseJSON?.message)
                }
                else
                    alert("Error: " + JSON.stringify(result))
            },
        });


    }

    static saveOneDataTable(async1 = true, showToastDone = true){

        // clsTableMngJs.formatDateTimeVn()

        let user_token = jctool.getCookie('_tglx863516839');

        let allData = $("#form_save_one").serializeArray();

        //Phải chỉnh lại ở đây, vì datetimepicker có lỗi với serializeArray ở trên
        allData = [];
        $("#form_save_one").find('input.input_value_to_post, textarea.input_value_to_post').each(function (){
            let dataField = $(this).attr('data-field')
            let dataId = $(this).attr('data-id')
            let name = $(this).prop('name')
            let val = $(this).prop('value')

            if($(this).is(':disabled'))
                return;

            console.log(" check field: " , dataField, name , val );


            if($(this).hasClass('edit_date_time') || $(this).hasClass('edit_date')){
                console.log(" Set new val date0: " , val);
                if(val[0] == '_'){
                    console.log(" Case 1");
                    val = '';
                }
                else{
                    console.log(" Case 2");
                    val = clsTableMngJs.formatDateTimeVn(dataId, dataField);
                }
                console.log(" Set new val date1: " , val);
            }

            allData.push({'name': name, 'value': val})
        });

        console.log(" AllData5 = ", allData);
        // console.log(" AllData2 = ", all2);

        let dataId = $("#form_save_one").data("id");

        let isAdd = 0;

        let param1 = ''
        //Nếu là update, thì có dataId
        if(dataId)
            param1 = "/update/"+ dataId;
        else
            param1 = "/add";

        console.log(" Rich20 " , allData);
        //Lấy thêm các trường của tinymce:
        $("[data-type='rich_text']").each(function (){

            console.log(" Rich1");
            let field1 = $(this).attr('data-field')
            if($(this).hasClass('_read_only_')){
                console.log("Ignore _read_only_ field: ", field1);
                return;
            }

            if(tinymce && tinymce.editors) {
                console.log(" Rich200-" , field1);
                let content = tinymce.get(this.id).getContent()
                // console.log(" Rich210-", content);
                // for(let tmp of allData)
                // {
                //     if(tmp.name == field1)
                //         tmp.value = content
                // }

                allData.push({name: field1, value: content})

                console.log(" Rich211 " , allData);

                allData = allData.filter(object => {
                    return object.name !== 'edit_rich_text_' + field1;
                });
            }
        })


        console.log(" Rich2 " , allData);

        let urlOrg = $('#div_container').attr("data-api-url-update-one");
        let urlPost = urlOrg + param1 ;

        //Dành cho sync
        let retSave = 0

        if(param1 == '/add'){
            const url = new URL( window.location.href);
            // Create a URLSearchParams object from the query string
            const params = new URLSearchParams(url.search);
            console.log("params = ", params);
            const variables = {};
            params.forEach((value, key) => {
                variables[key] = value;
            });
            let __cmd_post  = variables['__cmd_post']
            let __cmd_param = variables['__cmd_param']

            console.log(" __cmd_post = ",  __cmd_post, __cmd_param);

            if(__cmd_post && __cmd_param)
                urlPost += "?__cmd_post=" + __cmd_post + "&__cmd_param=" + __cmd_param;

        }

        console.log("DataApi = "+ urlPost);
        showWaittingIcon();
        $.ajax({
            url: urlPost,
            type: 'POST',
            data: allData,
            async : async1,
            headers: {
                'Authorization': 'Bearer ' + user_token
                // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {
                hideWaittingIcon();
                console.log(" Add RET220 = ", result);
                if(!result.code){
                    alert("Có lỗi api 1:\n" + (result));
                }else{

                    retSave = result
                    if(showToastDone)
                        showToastInfoTop(" Done ?");
                    //Nếu là Add thì foreward sang edit
                    if(!dataId){
                        console.log("Url forward: ");
                        let curl = window.location.href;
                        curl = curl.split("?")[0];
                        curl = curl.replace("/create", '/edit/');
                        window.location.href = curl + result.payload;
                        // window.location.href = window.location.href.replace("/create", '/edit/') + result.payload;
                    }

                }
            },
            error: function (result) {
                hideWaittingIcon();
                alert("Error: " + result.responseJSON.message)
                console.log(" RET33 = ", result);
            },
        });

        return retSave
    }
}





$(function () {

    let user_token = jctool.getCookie('_tglx863516839');

    // $(".search-auto-complete-tbl").focusout(function(){
    //     console.log(" focusout ... search-auto-complete-tbl ");
    // });

    $(document).keyup(function (e){


        if($("div[data-tablerow] > input:focus").length){
            let inputF = $("div[data-tablerow] > input:focus")
            let data_opt_field = inputF.attr('data-opt-field');
            if(data_opt_field == 3){
                let data_autocomplete_id = inputF.attr('data-autocomplete-id')
                console.log(" 3333 - field /Val = " , data_autocomplete_id, inputF.val());
                let sl = "input.input_value_to_post[data-autocomplete-id='"+ data_autocomplete_id+ "']";
                $(sl).attr('value', inputF.val());

                let code = e.keyCode || e.which;
                if (code == 13) {
                    let dataId = $(sl).attr('data-id');
                    console.log(" Save now: dataId = ", dataId);
                    if (!dataId) {
                        console.log("Have not data id, return");
                        return;
                    }
                    $("i.save_one_item[data-id=" + dataId + "]").trigger("click");
                    $("div[data-join-val="+ data_autocomplete_id +"]").text("");
                }

            }
        }
    })

    function decodeHTMLEntities(text) {
        var entities = [
            ['amp', '&'],
            ['apos', '\''],
            ['#x27', '\''],
            ['#x2F', '/'],
            ['#39', '\''],
            ['#47', '/'],
            ['lt', '<'],
            ['gt', '>'],
            ['nbsp', ' '],
            ['quot', '"']
        ];

        for (var i = 0, max = entities.length; i < max; ++i)
            text = text.replace(new RegExp('&' + entities[i][0] + ';', 'g'), entities[i][1]);

        return text;
    }

    //https://github.com/scottgonzalez/jquery-ui-extensions/blob/master/src/autocomplete/jquery.ui.autocomplete.html.js
    (function( $ ) {

        var proto = $.ui.autocomplete.prototype,
            initSource = proto._initSource;

        function filter( array, term ) {
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );
            return $.grep( array, function(value) {
                return matcher.test( $( "<div>" ).html( value.label || value.value || value ).text() );
            });
        }

        $.extend( proto, {
            _initSource: function() {
                if ( this.options.html && $.isArray(this.options.source) ) {
                    this.source = function( request, response ) {
                        response( filter( this.options.source, request.term ) );
                    };
                } else {
                    initSource.call( this );
                }
            },

            _renderItem: function( ul, item) {
                return $( "<li></li>" )
                    .data( "item.autocomplete", item )
                    .append( $( "<a></a>" )[ this.options.html ? "html" : "text" ]( item.label ) )
                    .appendTo( ul );
            }
        });

    })( jQuery );



    //Auto complete search Field-Has-Api-URL
    //For ex: search email to fill userid to InputForm
    // Data respond {value, label}
    $(".search-auto-complete-tbl").autocomplete({
        source: function (request, response) {
            $.ajax({
                //Đưa vào đây mới nhận data-api-search
                url: this.element.attr("data-api-search"),
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + user_token
                },
                data: {
                    data_autocomplete_id: this.element.attr("data-autocomplete-id"),
                    search_str: request.term,
                    field: this.element.attr("data-api-search-field")
                },
                success: function (data) {
                    console.log("DATA autocom = ", data);
                    response(data.payload)
                    //https://stackoverflow.com/questions/5077409/what-does-autocomplete-request-server-response-look-like
                    // response($.map(data.payload, function (item) {
                    //     return {  label: item.email, value: item.user_id }
                    // }));
                },

                error: function (data) {
                    alert('Error call api: ' + this.url + "\n\n" + JSON.stringify(data).substr(0,1000));
                }
            });
        },
        minLength: 1,
        html: true,
        select: function (event, ui) {
            console.log(" RET1 = ", ui);
            // console.log(" EVEL = " , event);
            //Xóa label đi, vì input hiden ở dưới nhận nó
            $(this).val(ui.item.label);
            let data_autocomplete_id = $(this).attr('data-autocomplete-id');
            let data_opt_field = $(this).attr('data-opt-field');

            console.log(" data_autocomplete_id1 = ", data_autocomplete_id , data_opt_field);
            // $(this).next('input').attr('value', ui.item.value);

            let multiVal = $(this).closest('.divTable2Cell').attr('data-multi-value');
            if(multiVal == undefined)
                multiVal = $(this).attr('data-multi-value');

            console.log(" multiVal123 = " + multiVal);
            let inputSelect = "input.input_value_to_post[data-autocomplete-id='" + data_autocomplete_id + "']";
            let spanItem = "<span data-autocomplete-id='" + data_autocomplete_id + "' " +
                "class='span_auto_complete' data-item-value='" + ui.item.value + "' " +
                "title='Remove this item'>" + ui.item.label + " [x] </span>";

            $("input[data-autocomplete-id='" + data_autocomplete_id + "']").val('');

            ////////////////
            //Add a span of autocoplete value (for ex: userid by find email)
            if (multiVal == 1) {
                console.log(" MultiVal , so cont..., ui.item.value = " + ui.item.value);
                let currentVal = $(inputSelect).attr('value');
                // let allVal = currentVal + "," + ui.item.value;

                let allVal = jctool.addNumberInStringComma(currentVal, ui.item.value);

                console.log("currentVal = " + currentVal + " / All Val2 = " + allVal);
                if (allVal != currentVal) {

                    //Nếu là top filter, thì chỉ search 1 giá trị
                    if($(this).attr('data-is-top-filter') == 1){
                        $(inputSelect).attr('value', ui.item.value);
                        $(inputSelect).prop('value', ui.item.value);
                    }
                    else{
                        $(inputSelect).attr('value', allVal);
                        $(inputSelect).prop('value', allVal);
                    }

                    $(this).siblings("div.search-auto-complete-tbl").append(spanItem);


                }
            } else {
                console.log(" Set value for input post: " +  ui.item.value);
                console.log(" inputSelect: " +  inputSelect);
                $(inputSelect).attr('value', ui.item.value);
                $(inputSelect).val(ui.item.value);
                $(this).siblings("div.search-auto-complete-tbl").html(spanItem)
            }
            event.preventDefault();
        }
    }).focus(function(event){
        console.log("Focus...1");
        $(this).autocomplete("search");
    })

    //Remove item autocomlete, set empty value of hidden relate Input
    $(document).on("click", ".span_auto_complete", function () {

        let valRemove = $(this).attr("data-item-value");
        let data_autocomplete_id = $(this).attr('data-autocomplete-id');
        let editable = $(this).parents('.divTable2Cell').attr("data-edit-able")
        if(editable == 0){
            console.log(" Not editable : " + data_autocomplete_id + " / Edit = " + editable);
            return;
        }
        console.log(" Click remove1 " + $(this).attr("data-item-value"));
        //$(this).parent().parent().find("input[value='" + valInputRemove + "']").attr('value', "");

        console.log(" data_autocomplete_id2=", data_autocomplete_id);



        let inputSelect = "input.input_value_to_post[data-autocomplete-id='" + data_autocomplete_id + "']";
        let multiVal = $(this).closest('.divTable2Cell').attr('data-multi-value');
        console.log(" MutlVal = " + multiVal);

        $("input[data-autocomplete-id='" + data_autocomplete_id + "']").val('');

        if (multiVal == 1) {
            console.log(" MutlVal1 = " + multiVal);
            let currentVal = $(inputSelect).attr('value');

            console.log(" currentVal = " + currentVal);
            let newVal = jctool.removeNumberInStringComma(currentVal, valRemove);
            console.log("newVal2 = ", newVal);

            $(inputSelect).attr('value', newVal);
            $(inputSelect).prop('value', newVal);

            let allVal = currentVal + "," + valRemove;
        } else{
            $(inputSelect).attr('value', '');
            $(inputSelect).prop('value', '');
        }
        $(this).siblings('.span_auto_complete1').remove();
        $(this).remove();
    });

    //Lưu lại giá trị cũ, nếu post có lỗi thì trở lại
    var previousValue, selectedIndex;
    //Select option in grid, change next input form
    $("select.sl_option[data-field]").on('focus', function (e) {
        previousValue = $(this).val();
        selectedIndex = $(this).prop('selectedIndex');
        console.log(" sl_option val0 = " + previousValue);
    }).on('change', function () {

        console.log(" sl_option change to ... " + this.value);

        //Set value of next input
        $(this).next('input').attr('value', this.value);

        //////////////////
        //không tiếp tục update trực tiếp nữa
        return;

    });

    //Change status On/Off, change next input form 0/1
    $(".change_status_item").on('click', function () {
        console.log("Change status item ...");
        var setVal;
        if ($(this).hasClass('fa-toggle-on')) {
            $(this).parent().next('input').attr('value', 0);
            $(this).parent().next('input').prop('value', 0);
            setVal = 0;
        } else {
            $(this).parent().next('input').attr('value', 1);
            $(this).parent().next('input').prop('value', 1);
            setVal = 1;
        }
        $(this).toggleClass("fa-toggle-on fa-toggle-off");


        //////////////////
        //không tiếp tục update trực tiếp nữa
        return;

    })



    //Thêm để các item thêm mới có thể hoạt động
    $(document).keyup(function (e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            //Nếu đang có 1 focus, thì bỏ qua
            if ($("div[data-tablerow] > input:focus").length) {
                let dataId = $("div[data-tablerow] > input:focus").attr('data-id');
                console.log(" Save now: dataId = ", dataId);
                if (!dataId) {
                    console.log("Have not data id, return");
                    return;
                }
                console.log("Trigger click..." + dataId);
                $("i.save_one_item[data-id=" + dataId + "]").trigger("click");
            }
        }
    });

    //Enter input, call api save cell
    $("div[data-tablerow] > input").on('keyup', function (e)
    {

        //Sử dụng $(document).keyup(f
        return;



    });



    $(document).on('click', ".save_one_item", function () {

        let dataId = ($(this).attr("data-id"));

        console.log("save_one_item IDxa = " , dataId);

        clsTableMngJs.saveOneIdTable(dataId)


    })


    $("#save-one-data").on("click", function () {
        clsTableMngJs.saveOneDataTable()
    });

    $("#delete_one_item").on("click", function () {


        let api = $(this).attr("data-api");
        let idListSelecting =  $(this).attr("data-id");

        console.log(" API = ", api, idListSelecting);

        if(!idListSelecting)
            return;

        if (confirm("Bạn có chắc chắn muốn xóa vào thùng rác?") == true) {
        } else
            return

        let urlPost = api + "/delete/?id=" + idListSelecting;
        // if(this.id == 'un_delete_item_multi')
        //     urlPost = api + "/un-delete/?id=" + idListSelecting;

        // urlPost = urlPost.replace(":8002/", ":8001/");
        console.log(" API URL " + urlPost);

        showWaittingIcon();
        $.ajax({
            url: urlPost,
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + user_token
                // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {

                hideWaittingIcon();
                console.log(" RET221 = ", result);
                if(!result.code){
                    alert("Có lỗi api 2:\n" + (result));
                }else
                    showToastInfoTop(" Done ?");
            },
            error: function (result) {

                hideWaittingIcon();
                alert("Error: " + result.responseJSON.message)
                console.log(" RET33 = ", result);
            },
        });

    });


    $("#save-all-data").on("click", function () {


        let allData = $("#form_data").serializeArray();

        console.log(" AllData1 = ", allData);
        //let urlPost = $(this).data("api");

        let urlPost = $('#div_container').attr("data-api-url-update-multi");

        // urlPost = urlPost.replace(":8002/", ":8001/");


        allData = [];
        let haveOneData = 0;
        $('input.input_value_to_post, textarea.input_value_to_post').each(function (){

            let dataField = $(this).attr('data-field');
            if($(this).is(":disabled")){
                console.log(" Disable ignore: " , dataField);
                return;
            }

            haveOneData = 1;

            //Chỉ lấy prop, là giá trị sau khi thay đổi, còn attr là giá trị load ban đầu
            //Nên mọi chỗ thay đổi = js , sẽ phải set prop
            let value = $(this).prop('value');

            //Lỗi này: có lúc xóa empty input, mà vẫn nhận old value của attr
            // if(!value)
            //     value = $(this).attr('value');

            let name = $(this).attr('name');
            let dataId = $(this).attr('data-id');
            console.log(" xData ID = " , dataField , dataId, "VAL = '", value ,"' ", );
            if(dataId){

                if($(this).hasClass('edit_date_time') || $(this).hasClass('edit_date')){
                    value = clsTableMngJs.formatDateTimeVn(dataId, dataField);
                    console.log(" Set new val date2: " + value);
                }

                if(dataField == 'ide__' || dataField == 'id' || dataField == '_id' || $(this).attr('data-edit-able') == 1)
                    allData.push({name: name, value: value});
            }
        })

        console.log(" haveOneData = " , haveOneData);


        // $('textarea.input_value_to_post').each(function (){
        //     let dataField = $(this).attr('data-field');
        //     let value = $(this).prop('value');
        //     if(!value)
        //         value = $(this).attr('value');
        //     let name = $(this).attr('name');
        //     let dataId = $(this).attr('data-id');
        //     // console.log(" xData ID = " , dataId, "VAL = '", value ,"' ", dataField);
        //     if(dataId){
        //         if(dataField == 'id' || dataField == '_id' || $(this).attr('data-edit-able') == 1)
        //             allData.push({name: name, value: value});
        //     }
        // })

        console.log("All Data 2 = ", allData);
        // urlPost = "/test01";
        // console.log(" API URL " + urlPost);


        showWaittingIcon();
        $.ajax({
            url: urlPost,
            type: 'POST',
            data: allData,
            headers: {
                'Authorization': 'Bearer ' + user_token
                // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {
                hideWaittingIcon();
                console.log(" RET222 = ", result);
                if(!result.code){
                    alert("Có lỗi api 3:\n" + (result));
                }else{
                    if(result.payload){
                        showToastInfoTop(result.payload);
                        console.log(" result.payload = ", result.payload);
                        if(result.payload.insert_list){
                            clsTableMngJs.updateListIdInsert(result.payload.insert_list)
                        }
                    }
                    else
                        showToastInfoTop("Done?");
                    //
                }
            },
            error: function (result) {

                hideWaittingIcon();
                alert("Error: " + result.responseJSON.message)
                console.log(" RET33 = ", result);
            },
        });

    });



    $("input.select_one_check").click(function (){

        var totalCheck = 0;
        $("input.select_one_check:not(.select_all_check)").each(function (){
            if(this.checked){
                $(this).closest(".divTable2Cell ").css("background-color", 'gray')
                totalCheck++;
            }
            else{
                $(this).closest(".divTable2Cell ").css("background-color", 'white')
            }
        });

        console.log("TotalCheck1 = " + totalCheck);
        if(totalCheck)
            $("#show_action_multi_item").show();
        else
            $("#show_action_multi_item").hide();

        $(".status_delete").html(totalCheck + " Selected")

    });

    $("input.select_all_check").click(function (){
        if(this.checked) {
            $("input.select_one_check").prop('checked', true);
            $("#show_action_multi_item").show();
        }
        else{
            $("#show_action_multi_item").hide();
            $("input.select_one_check").prop('checked', false);
        }
        var totalCheck = 0;
        $("input.select_one_check:not(.select_all_check)").each(function (){
            if(this.checked){
                $(this).closest(".divTable2Cell ").css("background-color", 'gray')
                totalCheck++;
            }
            else{
                $(this).closest(".divTable2Cell ").css("background-color", 'white')
            }
        });
        $(".status_delete").html(totalCheck + " Selected")
    });






    $("#update_parent_list, #delete_item_multi , #un_delete_item_multi").click(function (){


        if(this.id == 'delete_item_multi')
            if (confirm("Bạn có chắc chắn muốn xóa?\n(Sau khi xoá Bạn có thể phục hồi trong Thùng rác)") == true) {
            } else
                return

        let mmIdList = [];
        let idListSelecting = '';
        $("input.select_one_check").each(function (){
            if(this.checked && $(this).attr("data-id")){
                idListSelecting +="," + $(this).attr("data-id")
                mmIdList.push($(this).attr("data-id"));
            }
        });
        console.log("idListSelecting = " + idListSelecting);
        console.log("ID = " + this.id);

        let method = "GET";
        let urlPost = $('#div_container').attr("data-api-url") + "/delete/?id=" + idListSelecting;
        if(this.id == 'un_delete_item_multi')
            urlPost = $('#div_container').attr("data-api-url") + "/un-delete/?id=" + idListSelecting;
        if(this.id == 'update_parent_list'){
            urlPost = $('#div_container').attr("data-api-url") + "/update-multi?id=" + idListSelecting + "&___cmd=update_parent_list";
            method = "POST";
        }

        // urlPost = urlPost.replace(":8002/", ":8001/");
        console.log(" API URL ", method , urlPost);

        showWaittingIcon();
        $.ajax({
            url: urlPost,
            type: method,
            headers: {
                'Authorization': 'Bearer ' + user_token
                // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {

                hideWaittingIcon();
                console.log(" RET221 = ", result);
                if(!result.code){
                    alert("Có lỗi api 4:\n" + JSON.stringify(result));
                }else {
                    showToastInfoTop(" Delete Done! " + result.message);
                    mmIdList.forEach(function (item) {
                        $("input.select_one_check[data-id='" + item + "']").closest(".divTable2Row").remove();
                    });
                }
            },
            error: function (result) {

                hideWaittingIcon();
                alert("Error: " + (result.responseText))
                console.log(" RET33 = ", result);
            },
        });
    });

    $("#close_multi_action").on("click", function (){
        $("#show_action_multi_item").hide();
        $(".divTable2Cell input.select_one_check").prop( "checked", false );
    })

});

//https://stackoverflow.com/questions/23532729/submit-only-non-empty-fields-from-form
$(document).ready(function(){
    $("form").submit(function(){
        $("input").each(function(index, obj){
            if($(obj).val() == "") {
                $(obj).remove();
            }
        });
        $("select").each(function(index, obj){
            if($(obj).val() == "") {
                $(obj).remove();
            }
        });
    });
});


//For Dialog Mutil set value
$(function () {

    $('.divTable2Cell[data-edit-able="0"]').find(".one_node_name.fa-times").hide()
    $('.input_open_tree_select').on('click', function (){

        $("#common_dialog2").dialog("open");
        let api_data1 = $(this).attr('data-api-search');
        let dataTypeField = $(this).attr('data-type-field');
        let dataField = $(this).attr('data-field-filter');

        $("#tree_root_to_do2").show();
        console.log("api_data1: " + api_data1);
        clsTreeJsV2.clearAllTreeInstance();
        let treeFolder1 = new clsTreeJsV2();
        treeFolder1.bind_selector = "#tree_root_to_do2"
        treeFolder1.opt_open_all_first = 1;
        // treeFolder1.data = data1
        treeFolder1.api_data = api_data1;
        treeFolder1.hide_root_node = 0; //6.9.24: tai sao hide?
        treeFolder1.disable_menu = 1;
        treeFolder1.disable_drag_drop = 1;

        console.log(" dataField = " , dataField);

        $("#btn_set_filter_by_selecting_tree").prop("data-field", dataField);
        $("#btn_set_filter_by_selecting_tree").attr("data-field", dataField);

        if(dataTypeField == 26 || dataTypeField == 25){
            treeFolder1.radio1 = true;
        }

        treeFolder1.showTree();
    })

    $("#btn_set_filter_by_selecting_tree").click(function () {

        console.log(" Click ..." + this.id);
        let dtField = $(this).attr('data-field');
        console.log(" dt field1: " , dtField);

        let elm = $(".cls_root_tree input.radio_box_node1:checked")[0]
        console.log("ELM checked0: ", elm);
        let nodeId = '';
        if (elm) {
            console.log(" Found elm ... parent: ", elm);
            nodeId = $(elm).parent(".real_node_item").attr('data-tree-node-id');

            if(nodeId){
                $("input.search_top_grid[data-field-filter='" + dtField + "']").attr('value', nodeId);
                $("input.search_top_grid[data-field-filter='" + dtField + "']").prop('value', nodeId);
            }
        }

        $("#common_dialog2").dialog("close");

    });

    $("#btn_set_value_all_item_selecting").click(function () {

        console.log(" Click ..." + this.id);
        let dtField = $(this).attr('data-field');
        let cmd = $(this).prop('data-cmd');
        console.log(" dt field1: " , dtField);
        console.log(" dt cmd: " , cmd);

        if(cmd == 'cmd-set-value'){
            let newVal = $("#input_set_this_value_to_all_item_field").prop('value');
            console.log("--- New Val Set: " , newVal);
            $("input.select_one_check").each(function () {
                if (this.checked && $(this).attr("data-id")) {
                    // console.log(" Set value for " + $(this).attr("data-id"));
                    let selector = "input.input_value_to_post[data-field='"+dtField+"'][data-id='" + $(this).attr("data-id") + "']";
                    console.log("selector : " + selector);

                    if(newVal == undefined)
                        newVal = '';
                    // console.log("--- New Val Set: " , newVal);
                    $(selector).attr('value', newVal);
                }
            });
        }

        if(cmd == 'cmd-move-item'){
            console.log(" Click ..." + this.id);

            // let dtField = $(this).prop('data-field');
            //
            // console.log(" dt field6: " , dtField);

            let elm = $(".cls_root_tree input.radio_box_node1:checked")[0]
            console.log("ELM checked0: ", elm);

            //Có case xóa rỗng all
            let nodeId = '';
            if (elm) {
                console.log(" Found elm ... parent: ", elm);
                nodeId = $(elm).parent(".real_node_item").attr('data-tree-node-id');
            }

            console.log(" nodeId = ", nodeId);
            if (!nodeId || nodeId == undefined)
                nodeId = '';

            //Đặt các parent input value với nodeId vừa chọn
            $("input.select_one_check:checked").each(function () {
                if (this.checked && $(this).attr("data-id")) {
                    console.log("Set new val " + nodeId);
                    // idListSelecting +="," + $(this).attr("data-id")
                    $("input.input_value_to_post[data-field='"+dtField+"'][data-id='" + $(this).attr("data-id") + "']").attr('value', nodeId);
                }
            });

        }

        if(cmd == 'cmd-add-item-multi-value'){

            // let dtField = $(this).prop('data-field');
            // console.log(" dt field65: " , dtField);

            let elms = $(".cls_root_tree input.check_box_node1:checked");
            console.log(" ELM All = ", elms);
            console.log(" n elm checked = ", elms.length);
            //Xóa value của các hàng nếu không có check box nào chọn
            if(!elms.length){
                console.log("Delete value now...");
                $(".divTable2Cell input.select_one_check:checked").each(function () {

                    console.log(`Set empty $(this).attr(data-id) /Field= ${dtField} ` + $(this).attr("data-id"));
                    let selector = ".divTable2Cell input.input_value_to_post[data-field='"+dtField+"'][data-id='" + $(this).attr("data-id") + "']";
                    if (!($(selector).length))
                        console.log(" Error not Found selector" , selector);
                    $(selector).attr('value', '');
                    $(selector).prop('value', '');
                    $(selector).val('');
                })
            }
            else
                for (let elm of elms) {
                    console.log(" Found elm ... parent: ", elm);
                    let nodeId = $(elm).parent(".real_node_item").attr('data-tree-node-id');
                    console.log(" nodeId = ", nodeId);
                    if (nodeId && !Number.isNaN(nodeId))
                    {
                        //Đặt các parent input value với nodeId vừa chọn
                        $(".divTable2Cell input.select_one_check:checked").each(function () {
                            if (this.checked && $(this).attr("data-id")) {
                                // idListSelecting +="," + $(this).attr("data-id")
                                // console.log(" IDselect = " , $(this).attr("data-id"));
                                // console.log("Selector: " + "input.input_value_to_post[data-field='parent'][data-id='"+ $(this).attr("data-id") +"']");
                                let selector = ".divTable2Cell input.input_value_to_post[data-field='"+dtField+"'][data-id='" + $(this).attr("data-id") + "']";
                                let oldVal = $(selector).attr('value');
                                console.log(" Old Val: " + oldVal);
                                let newVal = jctool.addNumberInStringComma(oldVal, nodeId);
                                console.log(" new Val: " + newVal);
                                $(selector).attr('value', newVal);
                            }
                        });
                    }
                }
        }

        $("#common_dialog").dialog("close");

        // alert("Click nút Save All để ghi lại các dữ liệu đã thay đổi")
        //Kích hoạt save All
        // document.getElementById("save-all-data").click();
    });

    $("#btn_close_select_tree").click(function () {
        $("#common_dialog").dialog("close");
    })
    $("#btn_close_select_tree2").click(function () {
        $("#common_dialog2").dialog("close");
    })

    // $("#btn_clear_value_item_selecting").click(function () {
    //
    //
    //     console.log(" Click ..." + this.id);
    //     let dtField = $(this).prop('data-field');
    //     console.log(" dt field: " , dtField);
    //
    //     $("input.select_one_check").each(function () {
    //         if (this.checked && $(this).attr("data-id")) {
    //             let selector = "input.input_value_to_post[data-field='"+dtField+"'][data-id='" + $(this).attr("data-id") + "']";
    //             $(selector).attr('value', '');
    //         }
    //     });
    //
    //     $("#common_dialog").dialog("close");
    //
    //     alert("Click nút Save All để ghi lại các dữ liệu đã thay đổi")
    //     //Kích hoạt save All
    //     //document.getElementById("save-all-data").click();
    // });

    $("#common_dialog, #common_dialog2").dialog({
        width: 500,
        position: {my: "center top+50", at: "center top+50", of: window},
        autoOpen: false,
        modal: true,
        open: function (event, ui) {
            $('.ui-widget-overlay').bind('click', function () {
                $("#common_dialog").dialog('close');
                $("#common_dialog2").dialog('close');
            });
        }
    });

    $(document).on('click', "#found_search_autocomplete_this_value_to_all_item_field", function (){
        $("#input_set_this_value_to_all_item_field").attr("value", "")
        $("#input_set_this_value_to_all_item_field").prop("value", "")
        $(this).html("<span style='float: right'>x</span>");
        $(this).hide();
    })

    if (typeof $.contextMenu == 'function')
    $.contextMenu({
        // selector: '.real_node_item',
        selector: '.icon_tool_for_field', //Với menu có thể trigger Left
        trigger: 'left',
        callback: function (key, options) {

            $("#show_action_multi_item").hide();

            let user_token = jctool.getCookie('_tglx863516839');
            console.log("CMD Click : " + key);
            let that = options.$trigger;
            console.log("options.$trigger = ", that);
            let field = $(that).attr('data-field');
            let dataTypeField = $(that).attr('data-type-field');
            let dataSearchFieldIfHave = $(that).attr('data-search-field-if-have');
            console.log(" Field: " + field);
            console.log(" dataTypeField: " + dataTypeField);
            console.log(" dataSearchFieldIfHave: " + dataSearchFieldIfHave);


            $("#tree_root_to_do").hide();
            $("#input_set_this_value_to_all_item_field").hide();


            $("#btn_set_value_all_item_selecting").attr('data-field', field);

            $("#found_search_autocomplete_this_value_to_all_item_field").hide();
            $("#search_autocomplete_this_value_to_all_item_field").hide();


            let api_data1 = $(that).attr('data-api-if-have');

            if (key == 'set_value_multi_item') {

                console.log(" xxx dataTypeField " , dataTypeField);

                if(!$(".divTable2Cell input.select_one_check:checked").length){
                    alert("You have not selected any item!\nPlease check items you want to do!")
                    return;
                }

                $("#common_dialog").dialog("open");

                let nCheck = $(".divTable2Cell input.select_one_check:checked:not(.select_all_check)").length;
                $("#number_of_item_selected").html(" Set value for <b> " + nCheck + " </b> item selected! <br><br><i> " +
                    "Không chọn giá trị, để trống là xóa hết giá trị trên cột '"+ field + "' của các hàng đã chọn! </i>");

                //Kiểu thông thường
                //3 = string
                if (!dataTypeField || dataTypeField == 1 || dataTypeField == 3 || dataTypeField == 2) {
                    $("#input_set_this_value_to_all_item_field").show();
                    $("#btn_set_value_all_item_selecting").prop("data-cmd", 'cmd-set-value');
                }

                if(api_data1)
                    if (dataTypeField == 2 || dataTypeField == 12) {

                        console.log(" Field 2/12");

                        $("#btn_set_value_all_item_selecting").prop("data-cmd", 'cmd-set-value');
                        // $("#btn_set_value_all_item_selecting").show();

                        $("#search_autocomplete_this_value_to_all_item_field").show();
                        $("#search_autocomplete_this_value_to_all_item_field").attr('data-api-search', api_data1);
                        $("#search_autocomplete_this_value_to_all_item_field").autocomplete({
                            source: function (request, response) {
                                $.ajax({
                                    //Đưa vào đây mới nhận data-api-search
                                    url: this.element.attr("data-api-search"),
                                    type: 'POST',
                                    headers: {
                                        'Authorization': 'Bearer ' + user_token
                                    },
                                    data: {
                                        search_str: request.term,
                                        field: dataSearchFieldIfHave
                                    },
                                    success: function (data) {
                                        console.log("DATA = ", data);
                                        response(data.payload)
                                        //https://stackoverflow.com/questions/5077409/what-does-autocomplete-request-server-response-look-like
                                        // response($.map(data.payload, function (item) {
                                        //     return {  label: item.email, value: item.user_id }
                                        // }));
                                    },
                                    error: function (data) {
                                        alert('Error call api: ' + this.url + "\n\n" + JSON.stringify(data).substr(0, 1000));
                                    }
                                });
                            },
                            minLength: 2,
                            select: function (event, ui) {
                                console.log(" RET1 = ", ui.item.value);
                                console.log(" RET1 = ", ui.item.label);
                                // $("#input_set_this_value_to_all_item_field").val(ui.item.value)
                                $("#found_search_autocomplete_this_value_to_all_item_field").show();

                                let oldVal =  $("#input_set_this_value_to_all_item_field").attr("value");
                                let newVal = '';

                                console.log("Old Val = " + oldVal);

                                if(dataTypeField == 12){
                                    $("#found_search_autocomplete_this_value_to_all_item_field").append("<span class='span_item_select_auto'>" + ui.item.label + "</span>")
                                    newVal = jctool.addNumberInStringComma(oldVal, ui.item.value)
                                }
                                if(dataTypeField == 2) {
                                    newVal = ui.item.value
                                    $("#found_search_autocomplete_this_value_to_all_item_field").html("<span style='span_item_select_auto'>" + ui.item.label + "</span>")
                                }

                                $("#input_set_this_value_to_all_item_field").attr("value", newVal)
                                $("#input_set_this_value_to_all_item_field").prop("value", newVal)

                                $(this).val();
                                $("#search_autocomplete_this_value_to_all_item_field").val();
                            }
                        });
                    }

                //TreeSingle Radio check: 25
                //Tree MultiCheck:" 26
                if (dataTypeField == 25 || dataTypeField == 26) {
                    $("#tree_root_to_do").show();
                    console.log("api_data1: " + api_data1);
                    clsTreeJsV2.clearAllTreeInstance();
                    let treeFolder1 = new clsTreeJsV2();
                    treeFolder1.bind_selector = "#tree_root_to_do"
                    treeFolder1.opt_open_all_first = 1;
                    // treeFolder1.data = data1
                    treeFolder1.api_data = api_data1;
                    treeFolder1.hide_root_node = 0; //6.9.24: tai sao hide?
                    treeFolder1.disable_menu = 1;
                    treeFolder1.disable_drag_drop = 1;

                    if(dataTypeField == 26){
                        treeFolder1.checkbox1 = true;
                        $("#btn_set_value_all_item_selecting").prop("data-cmd", 'cmd-add-item-multi-value');
                    }

                    if(dataTypeField == 25) {

                        treeFolder1.radio1 = true;
                        $("#btn_set_value_all_item_selecting").prop("data-cmd", 'cmd-move-item');
                    }
                    treeFolder1.showTree();
                }
            }

        },
        items: {
            // "edit_name": {
            //     name: "Rename", icon: "edit", visible: function () {
            //         return $(this).parent().attr('data-tree-node-id') > 0;
            //     }
            // },
            "set_value_multi_item": {name: "Set value selected multi items", icon: "edit"},
            "sep1": "---------",
            "quit": {
                name: "Quit", icon: function () {
                    return 'context-menu-icon context-menu-icon-quit';
                }
            }
        }
    });

    $("#add_field_btn_filter").on('click',function (){
        if ($(this).text().indexOf("More") > 0) {
            $(".div_filter_item1").css('display', 'inline-block');
            $(this).removeClass("btn-primary").addClass("btn-warning");
            $(this).html("<i style='color: ' class=\"fa fa-minus-square\"></i> Less")
        }
        else {
            $(".div_filter_item1").hide();
            $(this).removeClass("btn-warning").addClass("btn-primary");
            $(this).html("<i class=\"fa fa-plus-square mx-1\"></i> More");
        }

        $(".div_filter_item1[data-field-filter='id']").show();
        $(".div_filter_item1[data-field-filter='name']").show();
    })

    $(".divTable2Cell > textarea").on('keyup',function(e) {
        console.log("xxx ", $(this).val());
        let idF = $(this).attr('data-textarea-id');
        $("input[data-autocomplete-id="+ idF +"]").attr('value', $(this).val());
    });
//optional - one line but wrap it
    $("#textarea").on('keypress',function(e) {
        if(e.which == 13) { //on enter
            //e.preventDefault(); //disallow newlines
            // here comes your code to submit
        }
    });

    $(".div_filter_item select").on("change", function (){
        console.log(" change value: " , this.value, this.name);
        if(this.value == 'N' || this.value == 'E'){
            $(".div_filter_item input[data-field-s=" + this.getAttribute('data-field-sl') + "]").val(this.getAttribute('data-field-sl')  + "_null");
        }
    })


    $("#form_save_one .for_up_down_key").keyup(function (e){

        let mField = [];
        $("input.for_up_down_key:visible").each(function (){
            mField.push($(this).attr('data-field'))
        })

        let dataField = $(this).attr('data-field');
        if(!dataField || mField.length <=0)
            return;

        let nextInp = null;
        let prevInp = null;

        for(let i = 0; i< mField.length; i++)
            if(dataField == mField[i]){
                if(i < mField.length - 1)
                    nextInp = $("input.for_up_down_key[data-field="+ mField[i+1] +"]")
                if(i > 0)
                    prevInp = $("input.for_up_down_key[data-field="+ mField[i-1] +"]")
                break;
            }

        console.log(" next, prev = ", nextInp, nextInp?.attr("data-field"), prevInp, prevInp?.attr("data-field"));

        if(e.which == 13){
            console.log(" Enter ..." , dataField);
            if(nextInp) {
                nextInp.focus();
                nextInp.select();
            }
        }
        if(e.which == 38){
            console.log(" Up ..." , dataField);
            if(prevInp) {
                console.log("up to ", prevInp.attr("data-field"));
                prevInp.focus();
                prevInp.select();
            }
        }
        if(e.which == 40){
            console.log(" Down ..." , dataField);
            if(nextInp) {
                nextInp.focus();
                nextInp.select();
            }
        }
    })



})

$(function () {
    console.log("onload00...");


    //Khi hover qua divTable2Row, show con div.mau_copy neu co
    $(".divTable2Row").hover(function () {
        $(this).find(".mau_copy").show();
    }, function () {
        $(this).find(".mau_copy").hide();
    });

    $(".divTable2Cell textarea[data-type=\"text_area\"]").each(function () {
        var text = this.value;
        var byteCount = new Blob([text]).size;
        console.log('Byte count:', byteCount);
        let field = this.name;
        $("div[data-namex2='" + field + "']").html( "Number char: " + byteCount + " / " + text.length + $("div[data-namex2='" + field + "']").html());
    });


    $('.all_check_many input').on('change', function() {
        // Lấy mảng các giá trị đã được check
        let dataField = $(this).closest('.all_check_many').data('field');
        var checkedValues = [];
        // Tìm tất cả input checkbox đã được check trong .all_check_many
        $('.all_check_many input:checked').each(function() {
            checkedValues.push($(this).val());
        });
        // Đa ra 1 chuỗi cách nhau dâu phẩy
        var newVal = checkedValues.join(',');
        console.log("Các giá trị đã chọn:", newVal);
        $(".input_value_to_post[data-field='" + dataField + "']").val(newVal);
        $(".input_value_to_post[data-field='" + dataField + "']").attr(newVal);
        $(".input_value_to_post[data-field='" + dataField + "']").prop(newVal);
    });


})

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        console.log('Copying to clipboard was successful!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
