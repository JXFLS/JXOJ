var codes=document.getElementsByTagName("pre");

for (var i = 0; i < codes.length; i++) {
    codes[i].style="position:relative;";
    codes[i].innerHTML="<button class=\"btn btn-primary btn-xs sample-copy\" style=\"right:0;position: absolute;top: 0;\">复制</button>"+codes[i].innerHTML;
}

$(".sample-copy").click(function() {
    var element = $(this).parent().find("code");
    var text = $(element).text();
    var $temp = $("<textarea>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
    $(this).text("复制成功").addClass("disabled");
    
    var e=this;
    setTimeout(function() {
        $(e).text("复制").removeClass("disabled");
    }, 500);
});
