var BASE_URL = "http://47.88.77.83:8080/admin/";
var SSO_BASE_URL = "http://47.88.77.83:8080/sso/thrid/";
var uid = "";
var uname = "";
var token = "";
var state = 0;
$(document).ready(function () {
//验证管理员身份
    token = GetQueryString("utoken");

    if (token) {
        addCookie("utoken", token);
    }
    var cookie_token = getCookie("utoken");
    if (cookie_token) {
        $.get(SSO_BASE_URL + cookie_token + "/authAdmin", function (data) {
            if (data.state == false) {
                alert(data.msg);
                deleteAllCookie();
                window.location.href = SSO_BASE_URL + "ebdf83c12f8fb6ef684b7fdc148d79ca" + "/getLogin";
            } else {
                uid = data.data.id;
                uname = data.data.username;
                token = data.data.token;
                show();//画图表
            }
        });
    } else {
        alert("Please Login First!");
        window.location.href = SSO_BASE_URL + "ebdf83c12f8fb6ef684b7fdc148d79ca" + "/getLogin";
    }
});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

function addCookie(name, value, expiresHours) {
    var setcookie = name + "=" + encodeURIComponent(value);
    if (expiresHours > 0) {
        var curdate = new Date();
        curdate.setTime(curdate.getTime() + expiresHours * 3600 * 1000);
        setcookie = setcookie + ";expires=" + curdate.toGMTString()
    }
    setcookie = setcookie + ";path=/";
    document.cookie = setcookie;
    return true
}

function getCookie(name) {
    var strcookie = document.cookie;
    var arrcookie = strcookie.split(";");
    for (var i = 0; i < arrcookie.length; i++) {
        var arr = arrcookie[i].split("=");
        arr[0] = arr[0].trim();
        if (arr[0] == name) {
            return decodeURIComponent(arr[1])
        }
    }
    ;
    return ""
}

function deleteCookie(name) {
    var curdate = new Date();
    curdate.setTime(curdate.getTime() - 10000);
    document.cookie = name + "=eg;expires=" + curdate.toGMTString() + ";path=/"
}

function deleteAllCookie() {
    var curdate = new Date();
    curdate.setTime(curdate.getTime() - 10000);
    var delcookies = document.cookie;
    var arrcookies = delcookies.split(";");
    for (var i = 0; i < arrcookies.length; i++) {
        var arr = arrcookies[i].split("=");
        document.cookie = arr[0] + "=eg;expires=" + curdate.toGMTString() + ";path=/"
    }
}

function Logout() {
    deleteCookie("utoken");
    alert("Logout Successfully!");
}
