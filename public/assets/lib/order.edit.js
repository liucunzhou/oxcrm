layui.config({
    base: '/assets/' //静态资源所在路径
}).extend({
    index: 'lib/index' //主入口模块
}).use(['index', 'form', 'laydate', 'element'], function () {
    var $ = layui.jquery
        ,laydate = layui.laydate
        ,form = layui.form
        ,element = layui.element;

    var layid = location.hash.replace(/^#form-tab=/, '');
    element.tabChange('form-tab', layid);

    form.render(null, 'component-form-group');
    lay('.layui-date').each(function() {
        laydate.render({
            elem: this,
            type: 'date',
            format: 'yyyy-MM-dd HH:mm'
        });
    });

    /* 监听提交 */
    form.on('submit(form-submit)', function (data) {
        var url = data.form.action;
        var params = data.field;
        $.post(url, params, function (res) {
            if(res.code == '200') {
                parent.location.reload();
            } else {
                layer.alert(res.msg);
            }
        });
        return false;
    });

    var bindInputs = 'input[name="table_amount"],' +
        'input[name="table_price"],' +
        'input[name="service_fee"],' +
        'input[name="wine_fee"],' +
        'input[name="banquet_update_table"],' +
        'input[name="wedding_total"],' +
        'input[name="banquet_discount"],' +
        'input[name="banquet_ritual_hall"],' +
        'input[name="banquet_other"]' +
        'input[name="wedding_room_amount"]' +
        'input[name="wedding_room"]' +
        'input[name="part_amount"]' +
        'input[name="part"]' +
        'input[name="champagne_amount"]' +
        'input[name="champagne"]' +
        'input[name="tea_amount"]' +
        'input[name="tea"]' +
        'input[name="cake_amount"]' +
        'input[name="cake"]';

    /* 监控桌数的输入 */
    $(bindInputs).bind("input", function () {
        var form = $(this).parents("form");

        var totals = 0;
        var banquetTotals = 0;
        var serviceFee = $('input[name="service_fee"]').val();
        if(serviceFee!='' && serviceFee!=undefined) {
            banquetTotals = banquetTotals + parseFloat(serviceFee);
        }

        var wineFee = $('input[name="wine_fee"]').val();
        if(wineFee!='' && wineFee!=undefined) banquetTotals = banquetTotals + parseFloat(wineFee);

        var banquetRitualHall = $('input[name="banquet_ritual_hall"]').val();
        if(banquetRitualHall!='' && banquetRitualHall!=undefined) banquetTotals = banquetTotals + parseFloat(banquetRitualHall);

        var banquetOther = $('input[name="banquet_other"]').val();
        if(banquetOther!='' && banquetOther!=undefined) banquetTotals = banquetTotals + parseFloat(banquetOther);

        // 餐标
        var tableAmount = $('input[name="table_amount"]').val();
        var tablePrice = $('input[name="table_price"]').val();
        if(tablePrice != '' && tableAmount!=undefined && tableAmount!='' && tablePrice!=undefined) {
            tableAmount = parseFloat(tableAmount);
            tablePrice = parseFloat(tablePrice);
            banquetTotals = banquetTotals + tableAmount * tablePrice;
        }

        // 婚房
        var weddingRoomAmount = $('input[name="wedding_room_amount"]').val();
        var weddingRoom = $('input[name="wedding_room"]').val();
        if(weddingRoomAmount != '' && weddingRoomAmount!=undefined && weddingRoom!='' && weddingRoom!=undefined) {
            weddingRoomAmount = parseFloat(weddingRoomAmount);
            weddingRoom = parseFloat(weddingRoom);
            banquetTotals = banquetTotals + weddingRoomAmount * weddingRoom;
            console.log(banquetTotals);
        } else {
            console.log(weddingRoomAmount);
        }

        // 停车位
        var partAmount = $('input[name="part_amount"]').val();
        var part = $('input[name="part"]').val();
        if(partAmount != '' && partAmount!=undefined && part!='' && part!=undefined) {
            partAmount = parseFloat(partAmount);
            part = parseFloat(part);
            banquetTotals = banquetTotals + partAmount * part;
        }

        // 香槟
        var champagneAmount = $('input[name="champagne_amount"]').val();
        var champagne = $('input[name="champagne"]').val();
        if(champagneAmount != '' && champagneAmount!=undefined && champagne!='' && champagne!=undefined) {
            champagneAmount = parseFloat(champagneAmount);
            champagne = parseFloat(champagne);
            banquetTotals = banquetTotals + champagneAmount * champagne;
        }

        // 茶歇
        var teaAmount = $('input[name="tea_amount"]').val();
        var tea = $('input[name="tea"]').val();
        if(teaAmount != '' && teaAmount!=undefined && tea!='' && tea!=undefined) {
            teaAmount = parseFloat(teaAmount);
            tea = parseFloat(tea);
            banquetTotals = banquetTotals + teaAmount * tea;
        }

        // 蛋糕
        var cakeAmount = $('input[name="cake_amount"]').val();
        var cake = $('input[name="cake"]').val();
        if(cakeAmount != '' && cakeAmount!=undefined && cake!='' && cake!=undefined) {
            cakeAmount = parseFloat(cakeAmount);
            cake = parseFloat(cake);
            banquetTotals = banquetTotals + cakeAmount * cake;
        }

        var banquetUpdateTable = $('input[name="banquet_update_table"]').val();
        if(banquetUpdateTable!='' && banquetUpdateTable != undefined) {
            banquetUpdateTable = parseFloat(banquetUpdateTable);
            banquetTotals = banquetTotals + banquetUpdateTable;
        }

        var banquetDiscount = $('input[name="banquet_discount"]').val();
        if(banquetDiscount!='' && banquetDiscount!=undefined) banquetTotals = banquetTotals - parseFloat(banquetDiscount);
        var weddingTotals = $('input[name="wedding_total"]').val();
        if(weddingTotals!='' &&weddingTotals!=undefined) {
            weddingTotals = parseFloat(weddingTotals);
        } else {
            weddingTotals = 0;
        }

        totals = banquetTotals + weddingTotals;

        // 定金
        if(form.hasClass("form-hotel-protocol")) {
            var earnest = totals * 0.2;
            earnest = earnest.toFixed(2);
            $('input[name="earnest_money"]').val(earnest);
        } else {
            var earnest = totals * 0.3;
            earnest = earnest.toFixed(2);
            $('input[name="earnest_money"]').val(earnest);
        }

        // 中款
        var middle = totals * 0.5;
        middle = middle.toFixed(2);
        $('input[name="middle_money"]').val(middle);

        // 尾款
        if (form.hasClass("form-hotel-protocol")) {
            var tail = totals * 0.3;
            tail = tail.toFixed(2);
            $('input[name="tail_money"]').val(tail);
        } else {
            var tail = totals * 0.2;
            tail = tail.toFixed(2);
            $('input[name="tail_money"]').val(tail);
        }

        if(banquetTotals > 0) {
            banquetTotals = banquetTotals.toFixed(2);
        }
        if(totals > 0) {
            totals = totals.toFixed(2);
        }
        $('input[name="banquet_totals"]').val(banquetTotals);
        $('input[name="contract_totals"]').val(totals);
    });

    $(document).on("click", ".append_wedding_item", function () {
        var tbody = $(this).parents("tbody");
        var row = $(this).parents("tr").clone();
        tbody.append(row);
        form.render()
    });

    $(document).on("click", ".delete_wedding_item", function () {
        var row = $(this).parents("tr").remove();
    });

    $(document).on("click", ".tbody-append", function () {
        var table = $(this).parents("table");
        var tbody = $(this).parents("tbody").clone();
        table.append(tbody);
        form.render()
    });

    $(document).on("click", ".tbody-delete", function () {
        $(this).parents("tbody").remove();
    });
});