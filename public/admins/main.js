function actionDelete(){
    event.preventDefault();

    let urlRequest = $(this).data('url');
    let that = $(this);

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {

            console.log("Confirm delete...");

            $.ajax({
                type: 'GET',
                url: urlRequest,
                success: function (data){
                    console.log(data);

                    if(data.code == 200){
                        console.log(" Rmove ok!");

                        that.parent().parent().remove();
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                    else
                        if(data.code < 0){
                        Swal.fire(
                            'Có lỗi xảy ra, mã lỗi: ' + data.code,
                            data.message,
                            'error'
                        )
                    }
                    else{
                        Swal.fire(
                            'No information?',
                            '',
                            'error'
                        )
                    }
                },
                error: function (data){
                    console.log(data);
                    Swal.fire(
                        'Có lỗi server:',
                        data.message,
                        'error'
                    )
                }
            })

        }
    })

}

$(function (){
    $(document).on('click', '.action_delete', actionDelete);
})
