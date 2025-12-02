$(function () {
    $('.checkbox_wrapper').on('click', function () {

        $(this).parents('.inside_a_parent').find('.checkbox_child_route').prop("checked", $(this).prop("checked"));

    });

    $(".checkall").on('click',function (){
        $(this).parents('.parent_all_check_per').find('input').prop('checked',  $(this).prop("checked"));
    })
})
