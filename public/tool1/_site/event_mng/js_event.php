<script>
    function reLearnFace(){

        // alert("Lấy các ảnh ra để học");
        //Lấy ra image_list id trong .input_value_to_post [name=image_list]
        let imgId = document.querySelector('.input_value_to_post[name="image_list"]').value;
        console.log("inputImageList: ", imgId);

        showWaittingIcon();

        // Tạo FormData để post
        let formData = new FormData();
        let imgLink = "https://events.dav.edu.vn/test_cloud_file?fid=" + imgId
        imgLink = imgId
        console.log(" Image link: ", imgLink);
        formData.append('image_list', imgLink);
        formData.append('user_event_id', <?php echo request('id') ?>);

        //Chuyen sang json post:

        //Gọi API fetch để post imageList lên để lấy kết qua ve
        fetch('/api/event-face-info/reLearn', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        }).then( response => {
            hideWaittingIcon();
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        }).then(data => {
            hideWaittingIcon();
            console.log("Relearn data: ", data);
            try {
                data = JSON.parse(data);
            }
            catch (e) {
                console.error("Error parsing JSON: ", e);
                alert("Có lỗi xảy ra khi phân tích dữ liệu trả về:\n\n " + data);
                return;
            }

            if (data.code == 1 ) {
                showToastInfoTop("Cập nhật Face thành công: " + data.payload + "");
                let face_vector = data.vector;
                console.log("RET= ", face_vector);
                // Có thể làm gì đó với dữ liệu trả về
                //Set giá trị này cho textarea.input_value_to_post.text_area_edit
                let textArea = document.getElementById('edit_text_area_face_vector');
                if (textArea) {
                    textArea.value = face_vector; // Giả sử trả về face_vector
                    console.log("Updated face_vector: ", face_vector);
                } else {
                    // alert("Không tìm thấy textarea để cập nhật face_vector.");
                    // return;
                }

                //Triger click id = save-one-data
                // let saveButton = document.getElementById('save-one-data');
                // if (saveButton) {
                //     saveButton.click();
                // } else {
                //     console.error("Không tìm thấy nút lưu để kích hoạt.");
                // }

            } else {
                alert("Học lại thất bại: " + data.message);
            }
        }).catch(error => {
            hideWaittingIcon();
            console.error('There was a problem with the fetch operation:', error);
            // showToastInfoTop(data)
            alert("Có lỗi xảy ra khi học lại: " + error.message);
        })

    }
</script>
