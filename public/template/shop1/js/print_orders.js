$("svg.bar_code").each(function (){
    let idx = $(this).attr('data-tracking-id');
    // console.log("That = ", that);
    JsBarcode('svg[data-tracking-id="'+ idx +'"]', idx, {
        // format: "pharmacode",
        // lineColor: "#0aa",
        width: 1.25,
        fontSize: 15,
        height: 30,
        // displayValue: false
    });
})
