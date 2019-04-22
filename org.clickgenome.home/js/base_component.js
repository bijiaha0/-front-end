$(document).ready(function () {

    var token = GetQueryString("utoken");

    if(token){
        addCookie("utoken",token);
    }
    var cookie_token = getCookie("utoken");

    if(cookie_token){
    $("#component_page_header").append('' +
        '<div class="page-header-inner">' +
        '<div class="page-header-logo"><img src="http://www.clickgenome.org/img/logo/logo.gif"/></div>' +
        '<div class="page-header-title">' +
        '<h2>Do genomic analysis by yourself by Mouse-Clicking</h2>' +
        '</div>' +

        '<div class="page-header-entry">' +
        '<div class="page-header-bar">' +
        '<ul style="padding-top: 30px">' +
        '<li id="Logout"><a href="http://portal.clickgenome.org/">Logout</a></li>' +
        '<li><a href="/about/">Contact us</a></li>' +
        '</ul>' +
        '</div>' +
        '</div>' +
        '</div>');

        $("#logout").click(function(){
            deleteCookie("utoken");
        });

    }else {
        $("#component_page_header").append('' +
            '<div class="page-header-inner">' +
            '<div class="page-header-logo"><img src="http://www.clickgenome.org/img/logo/logo.gif"/></div>' +
            '<div class="page-header-title">' +
            '<h2>Do genomic analysis by yourself by Mouse-Clicking</h2>' +
            '</div>' +

            '<div class="page-header-entry">' +
            '<div class="page-header-bar">' +
            '<ul style="padding-top: 30px">' +
            '<li id="Login"><a href="http://portal.clickgenome.org/">Login</a></li>' +
            '<li><a href="/about/">Contact us</a></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>');

    }
    $("#component_nav").append('<div class="navigation-up" xmlns="http://www.w3.org/1999/html"><div class="navigation-inner"><div class="navigation">' +
        '<ul>' +
        '<li id="select_home"><h2><a href="/">Home</a></h2></li>' +


        '<li id="select_data_analysis"><h2><a href="http://analysis.clickgenome.org/">Data Analysis</a></h2>' +
        '<div class="menu col-md-12 clearFix">' +
        '<dl class="dataPlots fl">' +
        '<dt>Data visualization</dt>' +
        '<dd class="clearFix">' +
        '<a href="http://analysis.clickgenome.org/todo/beeswarm/">Bee-swarm plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/mountain/">Mountain plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/manhattan/">Manhattan plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/deflection/">Deflection plot</a>' +
        // '<a class="NoShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Lego plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/volcano/">Volcano plot</a>' +
        // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Heatmap</a>' +
        '<a href="http://analysis.clickgenome.org/todo/mutation/tmb/">Tumor mutational burden</a>' +
        '<a href="http://analysis.clickgenome.org/todo/mutation/ttr/">Transition/Transversion ratio</a>' +
        '</dd>' +
        '</dl>' +
        '<dl class="dataMining fl"><dt>Data Mining</dt>' +
        '<dd class="clearFix"><a href="http://analysis.clickgenome.org/todo/linear_regression/">Linear regression analysis</a>' +
        // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Significance test</a>' +
        // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Survival analysis</a></dd>' +
        '</dl></div>' +
        '</li>' +



        '<li id="select_submit_data"><h2><a href="http://analysis.clickgenome.org/submitIndex.html">Analyze yours</a></h2>' +
        '<div class="menu col-md-12 clearFix">' +
        '<dl class="dataPlots fl">' +
        '<dt>Data visualization</dt>' +
        '<dd class="clearFix">' +
        '<a href="http://analysis.clickgenome.org/todo/fileInput/beeswarmIndex.html">Bee-swarm plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/fileInput/mountainIndex.html">Mountain plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/fileInput/manhattanIndex.html">Manhattan plot</a>' +
        '<a href="http://analysis.clickgenome.org/todo/fileInput/deflectionIndex.html">Deflection plot</a>' +
        // '<a class="disabledShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Lego plot</a>' +
        // '<a class="NoShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Volcano plot</a>' +
        // '<a class="NoShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Heatmap</a></dd>' +
        '</dl>' +
        /*'<dl class="dataMining fl"><dt>Data Mining</dt>' +
        '<dd class="clearFix">' +
        '<a class="NoShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Linear regression analysis</a>' +
        '<a class="NoShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7"">Significance test</a>' +
        '<a class="NoShow" href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Survival analysis</a></dd>' +
        '</dl>' +*/
        '</div>' +
        '</li>' +

        '<li id="select_guide"><h2><a href="http://www.clickgenome.org/guide/">User`s Guide</a></h2></li>' +
        '<li id="select_papers"><h2><a href="http://www.clickgenome.org/papers/">Papers</a></h2></li>' +
        // '<li id="select_forum"><h2><a href="http://forum.clickgenome.org/">Forum</a></h2></li>' +
        '<li id=select_about_us"><h2><a href="http://www.clickgenome.org/about/">About us</a></h2></li>' +
        '</ul>' +
        '</div></div></div>');

    $(".navigation > ul > li").css("width", (1200 / 6) + "px");
    $("#select_" + $("body").attr("page")).addClass("nav-up-selected-inpage");
    $("#component_footer").append('' +
        '<div class="footerinfo">' +
        '<div class="container">' +


        '<ul>' +
        '<li><h2>Quick Entrance</h2></li>' +
        '<li><a href="http://analysis.clickgenome.org/">Data Analysis</a></li>' +
        '<li><a href="http://analysis.clickgenome.org/submitIndex.html">Analyze yours</a></li>' +
        '<li><a href="../guide/">User`s Guide</a></li>' +
        '<li><a href="../papers/">Papers</a></li>' +
        // '<li><a href="http://forum.clickgenome.org/">Forum</a></li>' +
        '<li><a href="/about/">About US</a></li>' +
        '</ul>' +


        '<ul>' +
        '<li><h2>About Data</h2></li>' +
        '<li><a href="https://gdc-portal.nci.nih.gov/">Sources of Data</a></li>' +
        '<li><a href="http://analysis.clickgenome.org/">Data Analysis</a></li>' +
        '</ul>' +


        '<ul>' +
        '<li><h2>Links</h2></li>' +
        '<li><a href="http://www.tju.edu.cn/">Tianjin University</a></li>' +
        '<li><a href="http://www.utsouthwestern.edu/">UT Southwestern</a></li>' +
        '<li><a href="https://gdc.cancer.gov/">GDC</a></li>' +
        '<li><a href="https://www.nih.gov/">NIH</a></li>' +
        '</ul>' +


        '<ul>' +
        '<li><h2>About Click</h2></li>' +
        '<li><a href="/about/">Our Project</a></li>' +
        '<li><a href="/about/">Our Team</a></li>' +
        '<li><a href="/about/">Contact Us</a></li>' +
        '<li><a href="http://forum.clickgenome.org/faqs/">FAQs</a></li></ul>' +
        '</div></div>' +
        '<div class="footerlogo"><div class="container">' +
        '<div class="footerlogo-title"><span>Collaborations</span></div>' +
        '<ul><li><a href="http://www.tju.edu.cn/"><img src="http://analysis.clickgenome.org/img/logo/tianjinuniversity.png"/></a></li>' +
        '<li><a href="http://www.utsouthwestern.edu/"><img src="http://analysis.clickgenome.org/img/logo/ut-southwestern.png"/></a></li></ul>' +
        '</div></div>' +


        '<div class="pagefooter">' +
        '<div class="container">' +
        '<div class="pagefooter-links">' +
        '<ul>' +
        '<li><a href="http://www.tju.edu.cn/">Tianjin University</a></li>' +
        '<li><a href="http://www.utsouthwestern.edu/">UT Southwestern</a></li>' +
        '<li><a href="https://gdc.cancer.gov/">GDC</a></li>' +
        '<li><a href="https://www.nih.gov/">NIH</a></li>' +
        '</ul>' +
        '</div>' +
        '<p>Copyright&copy;2016-2018 clickgenome.org</p>' +
        '<p>Tianjin,China,300350</p>' +
        '</div>' +
        '</div>');


    $("a[href^='http://']").attr("target", "_self");
    $("a[href^='https://']").attr("target", "_self");

});

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if(r != null) return unescape(r[2]);
    return null;
}

function addCookie(name, value, expiresHours) {
    var setcookie = name + "=" + encodeURIComponent(value);
    if(expiresHours > 0) {
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
    for(var i = 0; i < arrcookie.length; i++) {
        var arr = arrcookie[i].split("=");
        arr[0] = arr[0].trim();
        if(arr[0] == name) {
            return decodeURIComponent(arr[1])
        }
    };
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
    for(var i = 0; i < arrcookies.length; i++) {
        var arr = arrcookies[i].split("=");
        document.cookie = arr[0] + "=eg;expires=" + curdate.toGMTString() + ";path=/"
    }
}
