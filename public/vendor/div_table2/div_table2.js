/**
 * A table-grid-cell like Excel (Sheet), build from DIVs
 * When user press key Up, down, cursor will Jump up down (like Excel)
 * Press Table, ShiftTable, cursor will next, back...
 */

$(function () {

    /**
     *
     * @returns {*|jQuery|HTMLElement}
     */
    function findSelectingCell(){
        return $("div[data-selecting-keyboard='1']");
    }
    function setSelectingCell(col, row){


        console.log("setSelectingCell Set col row = ", col, row);

        if(!col || !row)
            return;

        $("div[data-tablerow=" + (row) + "][data-tablecol=" + col + "]").css("background-color", 'lavender');

        $("div[data-tablerow=" + row + "][data-tablecol=" + col + "]").attr('data-selecting-keyboard', 1);
        $("div[data-tablerow=" + row + "][data-tablecol=" + col + "]").prop('data-selecting-keyboard', 1);
    }

    function clearAllSelectingCell(notBlur = 0){
        console.log("clearAllSelectingCell ... ");
        if(notBlur){
        }else{
            console.log(" focus out ???");
            // $("div[data-tablerow] > input[type=text]:focus").blur();
        }

        $('.divTable2Cell').css("background-color", 'white');
        $("div[data-selecting-keyboard='1']").attr('data-selecting-keyboard', 0);
        $("div[data-selecting-keyboard='1']").prop('data-selecting-keyboard', 0);
    }

    function getMaxRowColCell(){
        let maxRow = -1, maxCol = -1;
        $('.divTable2Cell').each(function (){
            if(maxRow < parseInt($(this).attr('data-tablerow')))
                maxRow = parseInt($(this).attr('data-tablerow'))
            if(maxCol < parseInt($(this).attr('data-tablecol')))
                maxCol = parseInt($(this).attr('data-tablecol'))
        })

        return [maxRow, maxCol];
    }

    $(document).on('click', function (e) {

        if($("div[data-tablerow] > input:focus").length)
        {
            let inputF = $("div[data-tablerow] > input:focus")
            console.log("OnClick inputF ");

            let cRow = inputF.parents('.divTable2Cell').data('tablerow');
            let cCol = inputF.parents('.divTable2Cell').data('tablecol');

            if(!cRow || !cCol)
                return;

            clearAllSelectingCell()
            setSelectingCell(cCol, cRow)
            console.log(" Focus input ...");
            // inputF.focus();
        }

    })

    $(document).on('click', function (e){

        if(!$(e.target).hasClass('input_value_to_post')){

            console.log("Not selecting cell");
            clearAllSelectingCell();
        }

    })

    $(document).keyup(function (e) {

        let currentRow
        let currentCol
        currentRow = findSelectingCell().data('tablerow')
        currentCol = findSelectingCell().data('tablecol')

        var code = e.keyCode || e.which;
        if (code == 13) {
            //Nếu đang có 1 focus, thì sẽ chuyển thành phím đi xuống, 1 dòng
            if($("div[data-tablerow] > input:focus").length){
                //Đang có forus, unfocus
                console.log("Enter Đang focus...");
                clearAllSelectingCell()
                setSelectingCell(currentCol, currentRow)
                code = 40;
            }
            //Nếu chưa có thì focus vào
            else{
                if(currentRow !== undefined && currentCol !== undefined){
                    $("div[data-tablerow=" + (currentRow) + "][data-tablecol=" + currentCol + "]").children("input:first").focus();
                    return;
                }
            }
        }

        //Esc
        if (code == 27) {
            //nếu có forus thì blur
            if($("div[data-tablerow] > input:focus").length){
                $("div[data-tablerow] > input:focus").blur();
            }
            return;
        }

        let upDown = 0
        let leftRight = 0

        if (code == 37) {
            if($("div[data-tablerow] > input:focus").length){
                console.log("Đang focus, bỏ qua2");
                return;
            }
            leftRight = -1
        }

        if (code == 39) {
            if($("div[data-tablerow] > input:focus").length){
                console.log("Đang focus, bỏ qua1");

                // let inputF = $("div[data-tablerow] > input:focus")
                // if(inputF)
                // {
                //     let cRow = inputF.parents('.divTable2Cell').data('tablerow');
                //     let cCol = inputF.parents('.divTable2Cell').data('tablecol');
                //     clearAllSelectingCell()
                //     setSelectingCell(cCol, cRow);
                // }

                return;
            }
            leftRight = 1
        }

        if (code == 38) {
            e.view.event.preventDefault();
            upDown = -1
        }

        if (code == 40) {
            e.view.event.preventDefault();
            upDown = 1
        }

        if(upDown || leftRight){
            let [maxR, maxC] = getMaxRowColCell()
            console.log("updown key...goto RC: ",maxR, maxC);
            let currentRow
            let currentCol
            currentRow = findSelectingCell().data('tablerow')
            currentCol = findSelectingCell().data('tablecol')
            // console.log("currentRow,  currentCol ", currentRow , currentCol);
            if(currentRow === undefined && currentCol === undefined){
                let inputF = $("div[data-tablerow] > input:focus")
                if(inputF)
                {
                    currentRow = inputF.parents('.divTable2Cell').data('tablerow');
                    currentCol = inputF.parents('.divTable2Cell').data('tablecol');
                }
            }

            // console.log("currentRow,  currentCol ", currentRow , currentCol);

            if(currentRow !== undefined && currentCol !== undefined)
            {
                console.log(" InputF ok: " , currentRow ,  currentCol);

                if(upDown)
                if(currentRow + upDown >= 0 && currentRow + upDown <= maxR) {
                    clearAllSelectingCell()
                    setSelectingCell(currentCol, currentRow + upDown);
                }

                // else
                //     console.log(" Not set rc because max R");
                if(leftRight){
                    if(currentCol + leftRight >= 0 && currentCol + leftRight <= maxC) {
                        clearAllSelectingCell()
                        //Tìm next right left xem có phù hợp không

                        let nexCol = currentCol

                        if(leftRight > 0){
                            for(let c = currentCol + leftRight; c<=maxC; c++){
                                if($('div[data-tablerow='+ currentRow +'][data-tablecol='+ c +'][data-edit-able=1]').length > 0){
                                    console.log(" Next Col = " , c);
                                    nexCol = c;
                                    break;
                                }
                            }
                        }
                        else{
                            for(let c = currentCol + leftRight; c>=0; c--){
                                if($('div[data-tablerow='+ currentRow +'][data-tablecol='+ c +'][data-edit-able=1]').length > 0){
                                    console.log(" Next Col = " , c);
                                    nexCol = c;
                                    break;
                                }
                            }
                        }


                        setSelectingCell(nexCol, currentRow);

                    }
                    else{
                        console.log(" MaxCol Reach: ", maxC);
                    }
                }
            }
            return;
        }

        //Nếu đang không focus mà phím bất kỳ khác, thì focus
        if($("div[data-tablerow] > input:focus").length == 0)
            if(code == 46){
                console.log("Key 46. del");
                findSelectingCell().find('input.input_value_to_post[data-edit-able=1]').val('');
            }else
                findSelectingCell().find('input.input_value_to_post[data-edit-able=1]').focus().val(String.fromCharCode( e.which ));

    });


    //Key up, down for input in divTable
    $("div[data-tablerow] > input").on('keyup', function (e) {

        console.log("Key up...");

        let tableRowNum = $(this).parents('.divTable2Cell').data('tablerow');
        let tableColNum = $(this).parents('.divTable2Cell').data('tablecol');


        var code = e.keyCode || e.which;

        if (code == 13) {
            return;
        }
        if (e.ctrlKey && code == 37) {
            console.log("CTRL ok + left ...");
        }

        console.log(" Enter tableRowNum = " , tableRowNum , tableColNum, e.keyCode);


        if (code == 9) {
            clearAllSelectingCell(1)
            setSelectingCell(tableColNum, tableRowNum)
        }

        if (code == 38) {
            //$("div[data-tablerow=" + (tableRowNum - 1) + "][data-tablecol=" + tableColNum + "] > :first-child").focus();
            $("div[data-tablerow=" + (tableRowNum - 1) + "][data-tablecol=" + tableColNum + "]").children("input:first").focus();
            $(':focus').select();
            console.log("upkey...");
        }

        if (e.ctrlKey && code == 39) {
            console.log("CTRL ok + right ...");
        }

        if (code == 40) {
            console.log("downkey...goto: " + (tableRowNum + 1) + " / "  + tableColNum);



            // $("div[data-tablerow=" + (tableRowNum + 1) + "][data-tablecol=" + tableColNum + "]").children("input:first").focus();
            // $(':focus').select();
        }

    });
})


// JavaScript to add the hover effect
document.addEventListener('DOMContentLoaded', function() {
    const cells = document.querySelectorAll('.divTable2Cell');

    cells.forEach(cell => {
        if (cell.classList.contains('cellHeader'))
            return;

        cell.addEventListener('mouseenter', function() {
            const siblings = this.parentElement.querySelectorAll('.divTable2Cell');
            siblings.forEach(sibling => sibling.classList.add('hovered'));
        });
        cell.addEventListener('mouseleave', function() {
            const siblings = this.parentElement.querySelectorAll('.divTable2Cell');
            siblings.forEach(sibling => sibling.classList.remove('hovered'));
        });
    });
});



document.addEventListener("DOMContentLoaded", () => {
    let isResizing = false;
    let startX = 0;
    let startWidth = 0;
    let targetCell = null;

    // Khi nhấn chuột vào vùng kéo
    document.addEventListener("mousedown", (e) => {
        if (e.target.classList.contains("resize-handle")) {
            isResizing = true;
            targetCell = e.target.parentElement; // Lấy cell liên quan
            startX = e.clientX;
            startWidth = targetCell.offsetWidth;

            // Thêm lớp 'resizing' vào body
            document.body.classList.add("resizing");
            e.preventDefault();
        }
    });

    // Khi kéo chuột
    document.addEventListener("mousemove", (e) => {
        if (isResizing && targetCell) {
            const newWidth = startWidth + (e.clientX - startX);
            if (newWidth > 50) { // Đảm bảo cell không nhỏ hơn chiều rộng tối thiểu
                targetCell.style.width = `${newWidth}px`;
            }
            e.preventDefault();
        }
    });

    // Khi thả chuột
    document.addEventListener("mouseup", () => {
        if (isResizing) {
            isResizing = false;
            targetCell = null;

            // Loại bỏ lớp 'resizing' khỏi body
            document.body.classList.remove("resizing");
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.for_seperatorHeader').forEach(function(header) {
        header.addEventListener('click', function() {
            let nextElement = header.nextElementSibling;
            let cc = 0;
            while (nextElement && !nextElement.classList.contains('for_seperatorHeader')) {
                cc = cc + 1;
                if (cc > 200) {
                    break;
                }
                //dummy_item  bo qua cac class dummy_item
                if (nextElement.classList.contains('dummy_item')) {

                }else
                if (nextElement.classList.contains('divTable2Row')) {
                    console.log(" 000x0 ", '"' + $(nextElement).css('display') + '"', $(nextElement).attr('data-field'));
                    nextElement.style.display = ($(nextElement).css('display') === 'none' || !$(nextElement).css('display')) ? 'table-row' : 'none';
                    console.log(" 001x2 " , '"' + nextElement.style.display + '"');
                }
                nextElement = nextElement.nextElementSibling;
            }
        });
    });
});
