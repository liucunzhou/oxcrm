layui.config({
    base: '/assets/'
}).extend({
    index: 'lib/index',
}).use(['index', 'table'], function () {
    var admin = layui.admin
            ,table = layui.table
            ,$ = layui.jquery;

    table.render({
        elem: '#table',
        url: tableUrl,
        height: 'full-100',
        defaultToolbar: ['filter'],
        toolbar: '#table-toolbar',
        title: '门店管理',
        cols:  [
            [
                {
                    "field": "id",
                    "title": "ID",
                    "width": 80
                }, {
                    "field": "title",
                    "title": "门店名称"
                }, 
                {
                    "field": "sort",
                    "title": "排序",
                    "width": 60
                }, 
                {
                    "field": "is_valid",
                    "title": "状态", 
                    "width": 60
                }, {
                    "field": "create_time",
                    "title": "创建时间",
                    "width": 165
                }, {
                    "type": "tool",
                    "title": "操作",
                    "toolbar": "#table-tool",
                    "width": 300
                }
            ]
        ],
        page: true
    });

    var height = window.innerHeight;
    var width = window.innerWidth * 0.6;
    var url = '';
    //头工具栏事件
    table.on('toolbar(table)', function (obj) {
        var checkStatus = table.checkStatus(obj.config.id);
        var url = '';
        switch (obj.event) {
            case 'addStore':
                var data = checkStatus.data;
                url = '/index/store/addstore.html';
                layer.open({
                    type: 2,
                    title: '添加',
                    content: url,
                    area: [width + 'px', height + 'px'],
                    offset: 'rb'
                });
                break;
        }
        ;
    });

    //监听行工具事件
    table.on('tool(table)', function (obj) {
        var id = obj.data.id;
        switch (obj.event) {
            case 'editStore':
                url = '/index/dictionary.store/editstore.html?id=' + id;
                layer.open({
                    type: 2,
                    title: '编辑门店信息',
                    content: url,
                    area: [width + 'px', height + 'px'],
                    offset: 'rb'
                });
                break;
            case 'hall':
                url = '/admin/store.hall/index.html?store_id=' + id;
                layer.open({
                    type: 2,
                    title: '婚宴厅管理',
                    content: url,
                    area: [width + 'px', height + 'px'],
                    offset: 'rb'
                });
                break;

            case 'staff':
                url = '/admin/store.staff/staffs.html?store_id=' + id;
                layer.open({
                    type: 2,
                    title: '酒店客服管理',
                    content: url,
                    area: [width + 'px', height + 'px'],
                    offset: 'rb'
                });
                break;

            case 'deleteStore':
                url = '/index/dictionary.store/deletestore.html';
                var params = {id:id};
                $.post(url, params, function (res) {
                    if (res.code == '200') {
                        table.reload("table");
                        layer.closeAll();
                    } else {
                        layer.msg(res.msg);
                    }
                });
                break;
        }
        ;
    });

});