$(function(){
    $(".layui-right-side").click(function () {
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

    $(".layui-center").click(function () {
        var layer = layui.layer;
        var url = $(this).attr('data-action');
        var width = window.innerWidth;
        var height = window.innerHeight;

        var cwidth = $(this).attr("data-width");
        var cheight = $(this).attr("data-height");

        if (cwidth == undefined) {
            width = width * 0.6;
        } else if(cwidth.indexOf("p") >= 0) {
            width =  parseInt(cwidth);
        } else {
            width = width * parseInt(cwidth) /100;
        }

        console.log(cheight);
        if (cheight == undefined) {
            height = height * 0.5;
        } else if(cheight.indexOf("p") >= 0) {
            height = parseInt(cheight);
        } else {
            height = height * parseInt(cheight) /100;
        }

        var closeBtn = $(this).attr("data-close-btn");
        layer.open({
            type: 2,
            title: false,
            content: url,
            area: [width + 'px', height + 'px'],
            closeBtn: closeBtn!=undefined ? 1 : 0
        });
    });

    $(".btn-close").click(function(){
        var layer = layui.layer;
        parent.layer.closeAll();
    });

    $(".toggle-mobile-list").click(function(){
        $(".mobile-list").toggle();
    });

    $(".event-call").click(function(){
        var id =  $(this).attr("data-id");
        layer.confirm('是否拨打电话?',
            {
                icon: 3,
                title:'提示',
                btn: ['手机拨打','座机拨打']
            },
            function(index){
                var params = {
                    id: id,
                    from: 'mobile',
                    field: 'mobile'
                };
                url = '/customer.ring/call.html';
                $.post(url, params, function(res){
                    layer.close(index);

                });
            },

            function(index){
                var params = {
                    id: id,
                    from: 'telephone',
                    field: 'mobile'
                };
                url = '/index/Ring/call.html';
                $.post(url, params, function(res){
                    layer.close(index);
                });
            }
        );
    });

    $(document).on("click", ".btn-ajax-html", function(){
        var url = $(this).attr("data-action");
        var target = $(this).attr("data-target");
        var hinit = $(this).attr("data-confirm");

        if(hinit!=undefined) {
            if(!confirm(hinit)) return false;
        }

        var isRemove = $(this).attr("data-remove");
        var removeTarget = $(this).attr("data-remove-target");
        if(isRemove == 'yes') {
            console.log("remove....");
            $(this).parents("." + removeTarget).remove();
        }

        var isAppend = $(this).attr("data-append");
        $.get(url, function(res){
            if(isAppend == undefined) {
                $(target).html(res);
            } else {
                $(target).append(res);
            }
        });
    });

    $(document).on("change", ".change-ajax-html", function(){
        var url = $(this).attr("data-action");
        var target = $(this).attr("data-target");
        var hinit = $(this).attr("data-confirm");

        if(hinit!=undefined) {
            if(!confirm(hinit)) return false;
        }

        var isRemove = $(this).attr("data-remove");
        var removeTarget = $(this).attr("data-remove-target");
        if(isRemove == 'yes') {
            console.log("remove....");
            $(this).parents("." + removeTarget).remove();
        }

        var id = $(this).val();
        var isAppend = $(this).attr("data-append");
        $.get(url, {id:id}, function(res){
            if(isAppend == undefined) {
                $(target).html(res);
            } else {
                $(target).append(res);
            }
        });
    });


    $(document).on("submit", ".form-ajax", function(){
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.post(url, data, function (res) {
            if(res.code == '200') {
                if(res.redirect == 'dialog') {
                    parent.layer.closeAll();
                    parent.window.location.reload();
                }if(res.redirect == 'reload') {
                    parent.window.location.reload();
                } else {
                    window.location.replace(res.redirect);
                }
            } else {
                alert(res.msg);
            }
        });
        return false;
    });

    $(".form-ajax-search").submit(function () {
        var url = $(this).attr('action');
        var data = $(this).serialize();
        var target = $(this).attr("data-target");
        $.get(url, data, function (res) {
            $(target).html(res);
        });
        return false;
    });

    $(".btn-delete").click(function(){
        if(!confirm('确认删除该行?')) {
            return false;
        }

        var _ = $(this);
        var url = $(this).attr('data-href');
        if(url != undefined) {
            $.get(url, function (res) {
                layer.msg(res.msg);
                if (res.code == '200') {
                    _.parents("tr").remove();
                }
            });
        }
        return false
    });

    $(".lay-delete-api").click(function(){
        if(!confirm('确认删除该行?')) {
            return false;
        }

        var _ = $(this);
        var url = $(this).attr('data-action');
        var fresh = $(this).attr('data-fresh');
        if(url != undefined) {
            $.get(url, function (res) {
                if (res.code == '200') {
                    if(fresh != undefined) {
                        window.location.reload();
                    } else {
                        layer.msg(res.msg);
                        _.parents("tr").remove();
                    }
                }
            });
        }
        return false
    });

    $("select.multiple-select").each(function(i,n) {
        var placeholder = $(n).attr('placeholder');
        $(n).multipleSelect({
            placeholder: placeholder,
            locale: 'zh-CN',
            filter: true
        });
    });

    layui.use('laydate', function(){
        var laydate = layui.laydate;
        $(".date-range").each(function (i, n) {

            var id = $(n).attr("id");
            laydate.render({
                elem: '#' + id
                ,range: '~'
            });
        });

        $(".date").each(function (i, n) {

            var id = $(n).attr("id");
            laydate.render({
                elem: '#' + id
            });
        });

    });

    // 全选
    $("#check-all").click(function(){
        if($(this).prop("checked")) {
            $(".ids").prop('checked', true);
        } else {
            $(".ids").prop("checked", false);
        }
    });

    $(".fast-visit").click(function () {
        var val = $(this).val();
        if(val == 4) {
            $(".fast").hide();
            $(".fast.fast-invalid").show();
        }

        if(val == 7) {
            $(".fast").hide();
            $(".fast.fast-unconnect").show();
        }
    });

    $(".color-group .color-item").click(function () {
        $(".color-group .color-item").removeClass("active");
        $(this).addClass("active");

        var target = $(this).attr("data-target");
        var value = $(this).attr("data-value");
        $(target).val(value);
    });

    $(".fast-group .fast-item").click(function () {
        $(".fast .fast-item").removeClass("active");
        $(this).addClass("active");

        var target = $(this).attr("data-target");
        var value = $(this).attr("data-value");
        $(target).val(value);

        $("#auto-content").val(value);
    });

    $(".fast-group .fast-item").dblclick(function () {
        if($(this).hasClass("active")) {
            $(this).removeClass("active");
            var target = $(this).attr("data-target");
            $(target).val('');
        }
    });

    $(".upload").each(function (i, n) {
        var id = $(n).attr("id");
        var url = $(n).attr("data-action");
        layui.use('upload', function(){
            var upload = layui.upload;
            var uploadInst = upload.render({
                elem: '#' + id
                ,url: url
                ,done: function(res){
                    if(res.code='200') {
                        var img = `<img src="${res.image}" />`;
                        $(n).find(".img-box").html(img);
                    } else {
                        layer.alert("上传失败");
                    }
                }
                ,error: function(){
                    layer.alert("上传失败");
                }
            });
        });
    });
});