dd.ready(function() {
    // dd.ready参数为回调函数，在环境准备就绪时触发，jsapi的调用需要保证在该回调函数触发后调用，否则无效。
    dd.runtime.permission.requestAuthCode({
        corpId: "ding0576d5ce9051a1d635c2f4657eb6378f",
        onSuccess: function(result) {
            dd.biz.navigation.setMenu({
                backgroundColor : "#ADD8E6",
                textColor : "#ADD8E611",
                items : [
                    {
                        "id":"1",
                        "iconId":"photo",
                        "text":"我的客资"
                    },
                    {
                        "id":"1",
                        "iconId":"group",
                        "text":"我的客资",
                        "url" : 'http://121.42.184.177/index/customer/mine.html'
                    },
                    {
                        "id":"2",
                        "iconId":"create",
                        "text":"我的订单",
                        "url" : 'http://121.42.184.177/index/order/index.html'
                    },
                    {
                        "id":"3",
                        "iconId":"forward",
                        "text":"个人中心",
                        "url" : 'http://121.42.184.177/index/user/info.html'
                    }
                ],
                onSuccess: function(data) {
                    var str = '';
                    for (i in  data){
                        str += i + ':' + data[i] + "\n";
                    }

                    alert(data.id);
                    var url = '';
                    switch (data.id) {
                        case "1":
                            url = 'http://121.42.184.177/index/customer/index.html';
                            break;

                        case "2":
                            url = 'http://121.42.184.177/index/customer/mine.html';
                            break;

                        case "3":
                            url = 'http://121.42.184.177/index/order/index.html';
                            break;
                    }

                    alert(url);
                    dd.biz.navigation.replace({
                        url: url,// 新的页面链接
                        onSuccess : function(result) {
                            /*
                            {}
                            */
                        },
                        onFail : function(err) {}
                    });
                },
                onFail: function(err) {
                }
            });
        },
        onFail : function(err) {}

    });
});