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
        var title = $(this).attr('title');
        var percent = $(this).attr("data-width");
        if(percent != undefined) {
            percent = parseInt(percent)/100;
        } else {
            percent = 0.6;
        }

        var width = window.innerWidth * percent;
        var height = window.innerHeight * .5;
        
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