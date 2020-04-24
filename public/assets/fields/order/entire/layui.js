layui.config({
    base: '/assets/' //静态资源所在路径
}).extend({
    index: 'lib/index' //主入口模块
}).use(['index', 'table', 'laydate'], function () {
    var $ = layui.jquery
        ,table = layui.table
        ,laydate = layui.laydate;

    laydate.render({
        elem: '#date_range',
        type: 'date',
        range: '~'
    });

    table.render({
        elem: '#table'
        ,url: tableRequestUrl
        ,height: 'full-100'
        ,defaultToolbar: ['filter']
        // ,toolbar: '#table-toolbar'
        ,toolbar: false
        ,title: tableTitle
        ,cols: _cols
        ,page: true,
        limit: 30,
        limits: [20, 30]
    });

    var height = window.innerHeight;
    var width = window.innerWidth * 0.6;
    var url = '';
    //头工具栏事件
    table.on('toolbar(table)', function (obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        var url = '';
        switch (obj.event) {
            case "searchCustomer":
                if($("#advance-search").hasClass("layui-hide")) {
                    $("#advance-search").removeClass("layui-hide");
                } else {
                    $("#advance-search").addClass("layui-hide");
                }
                break;
        }
    });

    //监听行工具事件
    table.on('tool(table)', function (obj) {
        var id = obj.data.id;
        switch (obj.event) {
            case 'editOrder':
                url = '/index/order.Order/editorder.html?id=' + id;
                top.layui.index.openTabsPage(url, '编辑订单');
                break;

            case 'sourceConfirm':
                url = '/index/order/sourceconfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '来源审核');
                break;

            case 'scoreConfirm':
                url = '/index/order/scoreconfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '积分审核');
                break;

            case 'fianceConfirm':
                url = '/index/order/fianceconfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '财务订单审核');
                break;

            case 'accountingPaymentConfirm':
                url = '/index/order/accountingpaymentconfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '会计付款审核');
                break;

            case 'cashierPaymentConfirm':
                url = '/index/order/cashierpaymentconfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '出纳付款审核');
                break;

            case 'fiancePaymentConfirm':
                url = '/index/order/fiancepaymentconfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '财务主管付款审核');
                break;

            case 'cashierReceviablesConfirm':
                url = '/index/order/cashierReceivablesConfirm.html?id=' + id;
                top.layui.index.openTabsPage(url, '出纳收款审核');
                break;

            case 'showOrder':
                url = '/index/order.order/showorder.html?id=' + id;
                top.layui.index.openTabsPage(url, '查看订单详情');
                break;

            case 'checkResult':
                url = '/index/order/checkresult.html?id=' + id;
                top.layui.index.openTabsPage(url, '查看审核结果');
                break;
        }
    });

});