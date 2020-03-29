$(function () {
    $(".form-ajax").submit(function () {
        var url = $(this).attr("action");
        var data = $(this).serialize();

        $.post(url, data, function(res){
            alert(res);
        });

        return false;
    });


    $(document).on("change", "#area", function () {
        var id = $(this).val();
        var text = $("#area option:selected").text();
        if(id == '0') return false;

        var zone = $("#zone").val();

        if(zone== '') {
            zone = text;
        } else {
            var arr = zone.split(',');
            if(arr.indexOf(text) > -1) return false;

            zone = zone + ',' + text;
        }
        $("#zone").val(zone);

        var html = `<div class="selected-item">${text}</div>`;
        $("#zone-box").append(html);

       //  $(".selected-item-list .selected-item").each()
    })

    $(document).on("dblclick", ".selected-item", function () {
        $(this).remove();
    });

    $(document).on("submit", ".form-assign", function(){
        $('input[name="staff_id[]"]').each(function(i,n){
            if($(n).is(':checked')) {
                var label = $(n).parent("label");
                label.find("input").remove();
                label.appendTo(".selected-box");
            }
        });

        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.get(url, data, function (res) {
            if(res == '') {
                layer.alert("分配失败");
            } else {
                $(".panel-store-sales-assigned-body").append(res);
            }
        });
        return false;
    });
})