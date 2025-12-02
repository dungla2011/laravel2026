<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>



<svg id="barcode"></svg>

<script>
    JsBarcode("#barcode", "Hello123", {
        // format: "pharmacode",
        lineColor: "#0aa",
        width: 2,
        height: 40,
        // displayValue: false
    });
</script>



<div id="qrcode"></div>
<script type="text/javascript">
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: "http://jindo.dev.naver.com/collie",
        width: 128,
        height: 128,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>



<svg class="barcode"
     jsbarcode-format="upc"
     jsbarcode-value="123456789012"
     jsbarcode-textmargin="0"
     jsbarcode-fontoptions="bold">
</svg>

<script>

    JsBarcode(".barcode").init();
</script>
