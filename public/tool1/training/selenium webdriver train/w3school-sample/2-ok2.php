<div id="assignmenttext" style=''>
    <p>Specify that the animation of the &lt;div&gt; element should have a "ease-in-out" speed curve.</p>

<pre id="assignmentcontainer" style="overflow:auto"></pre>



<div id="assignmentcode">
    &lt;style&gt;
    div {
    width: 100px;
    height: 100px;
    position: relative;
    background-color: red;
    animation-name: example;
    animation-duration: 4s;
    @(25): @(11);
    }

    @keyframes example {
    0%   {background-color: red; left:0px;}
    50%  {background-color: yellow; left:200px;}
    100% {background-color: red; left:0px;}
    }
    &lt;/style&gt;

    &lt;body&gt;
    &lt;div&gt;This is a div&lt;/div&gt;
    &lt;/body&gt;
</div>
<style>

    #assignmentcontainer, #showcorrectanswercontainer {
        background-color:#E7E9EB;
        padding:30px;
        padding-top:0;
        /*padding-bottom:90px;*/
        /*min-height:250px;*/
        font-family:Consolas,Menlo,"Courier New", Courier, monospace;
        font-size:120%;
        line-height:1.7em;
        border-radius:5px;
    }
    #assignmentcontainer[contenteditable], #assignmentcontainer[contenteditable]~#showcorrectanswercontainer {
        background-color:#fff;
        outline:10px solid #f1f1f1;
    }

    #showcorrectanswercontainer {
        /*display:none;*/
    }
    .editablesection {
        background-color:#ffffff;
        display:inline-block;
        border:none;
        height:1.3em;
        padding:1px 2px;
        /*
          height:1.2em;
          padding:0;
          outline-offset:0;*/
    }
    /*
    .editablesection:focus {
      outline:2px solid #ffffff;
    }
    */
    .meassureInputWidth {
        display:inline-block;
        position:absolute;
    }

    .w3-codespan {
        color:#000000;
    }

    [id^="correctcode"] {
        display:none;
    }

    #showcorrectanswercontainer input {
        color:mediumblue;
    }

    .phonebr {
        display:none;
    }

    .showanswerbutton, .hideanswerbutton {
        background-color:#282A35;
        position:absolute;
        right:20px;
        bottom:20px;
    }
    .showanswerbutton:hover,.showanswerbutton:active, .hideanswerbutton:hover,.hideanswerbutton:active {
        background-color:#000!important;
    }
    @media screen and (max-width: 899px) {
        #answerbuttoncontainer {
            position:fixed;
            bottom:0;
            background-color:rgba(85,85,85,0.9);
            background-color:#D9EEE1;
            padding:20px;
            width:100%;
            left:0;
        }
        #assignmentcontainer, #showcorrectanswercontainer {
            min-height:150px;
            padding-bottom:60px;
            margin-left:-20px;
            margin-right:-20px;
            border-radius:0;
        }
        #assignmentNotCorrect, #assignmentCorrect {
            padding:20px;
        }
    }

    @media screen and (max-width: 475px) {
        .phonebr {
            display:initial;
        }
    }

    @media (max-width:899px){.hide-from-small{display:none!important}}
    @media (min-width:900px) {.hide-from-large{display:none!important}}
    @media only screen and (max-device-width: 480px) {
        #assignmentcontainer, #showcorrectanswercontainer {
            font-family:'Source Code Pro',Menlo,Consolas,"Courier New", Courier, monospace;
        }
        #assignmentcontainer input, #showcorrectanswercontainer input {padding:0;height:1.5em}


</style>


<script>

    var formanswers = [];
    var editable = false;
    var trimcheck = true;
    var invalue = false;
    formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');formanswers.push('');
    var originalassignmentcode;

    function initAssignment() {
        var x, y, txt, i, newtxt, c, cc, n, numberofchar, j, inputs, templates, l, inputcount = -1, endpos, endpos2, inputvalue;
        document.getElementById("assignmenttext").style.display = "block";
        x = document.getElementById("assignmentcode");
        x.style.display = "none";
        txt = x.innerHTML;
        originalassignmentcode = txt;
        if (x.getAttribute("trimcheck") == "false") {trimcheck = false;}
        if (x.getAttribute("contenteditable") == "true") {
            editable = true;
            document.getElementById("assignmentcontainer").innerHTML = txt;
            document.getElementById("assignmentcontainer").setAttribute("contenteditable", "true");
            document.getElementById("assignmentcontainer").addEventListener("keydown", function(e) {
                if(e.keyCode == 9){
                    e.preventDefault();
                    var doc = document.getElementById("assignmentcontainer").ownerDocument.defaultView;
                    var sel = doc.getSelection();
                    var range = sel.getRangeAt(0);
                    var blankspaces = document.createTextNode("    ");
                    range.insertNode(blankspaces);
                    range.setStartAfter(blankspaces);
                    range.setEndAfter(blankspaces);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            });
        } else {
            newtxt = "";
            for (i = 0; i < txt.length; i++) {
                c = txt[i]
                numberofchar = 0;
                if (c == "@") {
                    inputcount++
                    inputvalue = "";
                    if (txt[i + 1] == "(") {
                        startpos = i + 2;
                        endpos = txt.indexOf(")", startpos);
                        //if (txt.indexOf("(", startpos) > -1) {
                        //  if (txt.indexOf("(", startpos) < endpos) {
                        //    endpos = txt.indexOf(")", endpos + 1);
                        //  }
                        //}
                        //endpos2 = txt.indexOf(",", startpos);
                        //if (endpos2 > -1) {
                        //  invalue = true;
                        //  inputvalue = txt.substring(endpos2 + 1, endpos);
                        //  n = txt.substring(startpos, endpos2)
                        //  if (!isNaN(n)) {numberofchar = Number(n) + inputvalue.length;}
                        //} else {
                        n = txt.substring(startpos, endpos)
                        if (!isNaN(n)) {numberofchar = n;}
                        //}
                    }
                    if (numberofchar > 0) {
                        i = endpos;
                        c = "<pre class='meassureInputWidth'>"
                        for (j = 0; j < numberofchar; j++) {
                            c += " ";
                        }
                        c += "</pre>"
                        c += "<input spellcheck='false' class='editablesection' onkeypress='checkKey(event)' oninput='writinginput(this, " + inputcount + ")' maxlength='" + numberofchar + "'>"
                        //c += "<input value='" + inputvalue + "' spellcheck='false' class='editablesection' onkeypress='checkKey(event)' oninput='writinginput(this, " + inputcount + ")' maxlength='" + numberofchar + "'>"
                    }
                }
                newtxt += c;
            }
            document.getElementById("assignmentcontainer").innerHTML = newtxt;
            inputs = document.getElementsByClassName("editablesection");
            templates = document.getElementsByClassName("meassureInputWidth");
            for (i = 0; i < inputs.length; i++) {
                inputs[i].style.width = ((templates[i].offsetWidth) + 4) + "px";
                templates[i].style.display = 'none';
                templates[i].innerHTML = "w3exercise_input_no_" + i;
                //if (inputs[i].value == "") {
                cc = formanswers[i];
                cc = cc.replace(/&apos;/g, "'");
                cc = cc.replace(/&quot;/g, '"');
                inputs[i].value = cc;
                //}
            }
        }
        //window.setTimeout(function () {inputs[0].focus()}, 800);
    }

    function checkKey(event) {
        if (event.keyCode == 13) {
            checkassignmentcode();
            uic_r_p();
        }
    }



    initAssignment();


</script>
