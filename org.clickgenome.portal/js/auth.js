

var Utils = new Object({
    getQueryString: function(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
});




var Auth = new Object({

    URL: {

        BASE_URL: "http://54.193.40.253:8080/sso/sso",

        login: function() {

            return Auth.URL.BASE_URL + "/" + $("#email").val() + "/" + $("#password").val() + "/" + Utils.getQueryString("ticket") + "/auth";

        },

        app_info: function() {
            return
        }
    },
    Utils: {

        md5Encode: function() {
            return
        }

    },

    prevLink:{

       link:function () {
           return document.referrer;
       }
    },

    Operate: {

        get_app_info: function() {
            return
        },

        login: function() {

            console.log(Auth.URL.login());

            $.get(Auth.URL.login(), {}, function(data, status) {//ajax请求，返回结果

                Auth.View.login(data);

            });
        }
    },

    View: {
        init: function() {
            $("#appname").html(Utils.getQueryString("appname"));
            $("#appavatar").attr("src", Utils.getQueryString("appavatar"));
        },

        login: function(data) {

            if (data.state == false) {
                alert(data.msg);
                $.get("http://54.193.40.253:8080/admin/log/login/log?account="+$("#email").val()+"&time="+Date.parse(new Date())/1000+"&isSuccess=0");
            } else {
                console.log(data);
                $.get("http://54.193.40.253:8080/admin/log/login/log?account="+$("#email").val()+"&time="+Date.parse(new Date())/1000+"&isSuccess=1");

                if(Auth.prevLink.link().indexOf('fileInput') != -1) {//来自上传数据页面

                    window.location.href = 'http://analysis.clickgenome.org/submitIndex.html' + "?&utoken=" + data.data.token
                }
                else {

                        window.location.href = 'http://analysis.clickgenome.org' + "?&utoken=" + data.data.token;

                }
                //刷新页面
                // window.location.href = 'http://analysis.clickgenome.org' + "?&utoken=" + data.data.token;

            }
        }
    }
});
