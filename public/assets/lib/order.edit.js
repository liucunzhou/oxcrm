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
        'input[name="banquet_discount"]';

    /* 监控桌数的输入 */
    $(bindInputs).bind("input", function () {
        var totals = 0;
        var banquetTotals = 0;
        var serviceFee = $('input[name="service_fee"]').val();
        if(serviceFee!='') {
            banquetTotals = banquetTotals + parseFloat(serviceFee);
        }

        var wineFee = $('input[name="wine_fee"]').val();
        if(wineFee!='') banquetTotals = banquetTotals + parseFloat(wineFee);

        var tableAmount = $('input[name="table_amount"]').val();
        var tablePrice = $('input[name="table_price"]').val();
        if(tablePrice != '' && tableAmount!='') {
            tableAmount = parseFloat(tableAmount);
            tablePrice = parseFloat(tablePrice);
            banquetTotals = banquetTotals + tableAmount * tablePrice;
        }

        var banquetUpdateTable = $('input[name="banquet_update_table"]').val();
        if(banquetUpdateTable!='') {
            banquetUpdateTable = parseFloat(banquetUpdateTable);
            banquetTotals = banquetTotals + banquetUpdateTable;
        }

        var banquetDiscount = $('input[name="banquet_discount"]').val();
        if(banquetDiscount!='') banquetTotals = banquetTotals - parseFloat(banquetDiscount);
        var weddingTotals = $('input[name="wedding_total"]').val();
        if(weddingTotals!='') {
            weddingTotals = parseFloat(weddingTotals);
        } else {
            weddingTotals = 0;
        }

        totals = banquetTotals + weddingTotals;
        // 定金
        var earnest = totals * 0.3;
        earnest = earnest.toFixed(2);
        $('input[name="earnest_money"]').val(earnest);
        // 中款
        var middle = totals * 0.5;
        middle = middle.toFixed(2);
        $('input[name="middle_money"]').val(middle);
        // 尾款
        var tail = totals * 0.2;
        tail = tail.toFixed(2);
        $('input[name="tail_money"]').val(tail);

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
});