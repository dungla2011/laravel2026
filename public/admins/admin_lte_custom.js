dinamicMenu = function() {
    let uriFull = window.location.href.split('?')[0];
    uriFull = "/" + jctool.getUriFull().split('?')[0];
    //var url = window.location;
    console.log(" ...... " + uriFull);
    // Will only work if string in href matches with location
    // $('.nav-treeview li a[href="' + url + '"]').parent().addClass('active');




    let found = 0;
    // Will also work for relative and absolute hrefs
    $('.nav-treeview li a').filter(function() {

        // console.log("prop = " , $(this).prop('href'), this.innerText);
        // console.log("attr = " , $(this).attr('href'), this.innerText);

        if($(this).attr('href').split('?')[0] == uriFull)
            found = 1
        return $(this).attr('href').split('?')[0] == uriFull;
    }).addClass('active');
    $('.nav-treeview li a').filter(function() {

        if($(this).attr('href').split('?')[0] == uriFull)
            found = 1
        return $(this).attr('href').split('?')[0] == uriFull;
    }).closest('ul').closest('li').addClass('menu-open').children('a').addClass('active');

    if(false)
    if(!found){
        $('.nav-treeview li a').filter(function() {
            // console.log(" $(this).attr('href') = " + $(this).attr('href') , this.innerText);
            return uriFull.indexOf($(this).attr('href').split('?')[0]) >=0 ;
        }).addClass('active');
        $('.nav-treeview li a').filter(function() {
            // console.log(" $(this).attr('href') = " + $(this).attr('href') , this.innerText);
            return uriFull.indexOf($(this).attr('href').split('?')[0]) >=0 ;
        }).closest('ul').closest('li').addClass('menu-open').children('a').addClass('active');
    }
};

$(function (){
    dinamicMenu();
})
