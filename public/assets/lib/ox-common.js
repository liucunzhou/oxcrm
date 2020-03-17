$(function(){
    $(".layui-right-side").click(function () {
        var layer = layui.layer;
        var url = $(this).attr('data-action');
        var title = $(this).attr('title');
        var height = window.innerHeight;
        var percent = $(this).attr("data-width");
        if(percent != undefined) {
            percent = parseInt(percent)/100;
        } else {
            percent = 0.6;
        }
        var width = window.innerWidth * percent;

        layer.open({
            type: 2,
            title: title,
            content: url,
            area: [width + 'px', height + 'px'],
            offset: 'rb'
        });
    });

    $(".layui-center").click(function () {
        var layer = layui.layer;
        var url = $(this).attr('data-action');
        var width = window.innerWidth;
        var height = window.innerHeight;

        var cwidth = $(this).attr("data-width");
        var cheight = $(this).attr("data-height");

        if(cwidth.indexOf("p") >= 0) {
            width = cwidth
        } else if(cwidth != undefined) {
            width = width * parseInt(cwidth) /100;
        } else {
            width = width * 0.6;
        }

        if(cheight.indexOf("p") >= 0) {
            height = parseInt(cheight);
        } else if(cheight != undefined) {
            height = height * parseInt(cheight) /100;
        } else {
            height = height * 0.5;
        }
        
        layer.open({
            type: 2,
            title: false,
            shadeClose: true,
            content: url,
            area: [width + 'px', height + 'px'],
            closeBtn: 0
        });
    });

    $(".btn-close").click(function(){
        var layer = layui.layer;
        parent.layer.closeAll();
    })
})