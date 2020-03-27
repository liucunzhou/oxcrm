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
        var zone = $("#zone").val();

        if(zone== '') {
            zone = id;
        } else {
            var arr = zone.split(',');
            if(arr.indexOf(id) > -1) return false;

            zone = zone + ',' + id;
        }
        $("#zone").val(zone);

        var text = $("#area option:selected").text();
        var html = '<div class="selected-item">' + text + '</div>';
        $("#zone-box").append(html);
    })

    $(document).on("dblclick", ".selected-item", function () {
        $(this).remove();
    });
})