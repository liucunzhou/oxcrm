$(function () {
    $(document).on("click", ".btn-ajax", function () {
        var target = $(this).attr("data-target");
        var url = $(this).attr("data-action");
        $.get(url, function(result){
            $(target).html(result)
        })
    });

    $(document).on("click", ".btn-delete-department", function () {
        if(!confirm("确定要删除该部门么？")) {
            return false;
        }

        var target = $(this).attr("data-target");
        var url = $(this).attr("data-action");
        $.get(url, function(result){
            if(typeof result == 'string') {
                layer.msg('删除成功');
                $(target).html(result);
            } else {
                layer.msg(result.msg);

            }
        })
    });

    $(document).on("click", ".btn-right-side", function () {
        var layer = layui.layer;
        var url = $(this).attr('data-action');
        var is_options = $(this).attr("data-is-option");
        var title = $(this).attr('title');
        var height = window.innerHeight;
        var percent = $(this).attr("data-width");
        if(percent != undefined) {
            percent = parseInt(percent)/100;
        } else {
            percent = 0.6;
        }
        var width = window.innerWidth * percent;

        if(is_options!=undefined) {
            var ids = '';
            $(".ids:checked").each(function (i, n) {
                if(i==0) {
                    ids = $(n).val();
                } else {
                    ids += ',' + $(n).val();
                }
            })
            url += '?ids=' + ids;
        }


        layer.open({
            type: 2,
            title: title,
            content: url,
            area: [width + 'px', height + 'px'],
            offset: 'rb'
        });
    });

})