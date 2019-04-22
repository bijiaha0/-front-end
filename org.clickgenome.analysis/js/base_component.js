$(document).ready(function() {

	var token = GetQueryString("utoken");

	if(token){
		addCookie("utoken",token);
	}
	var cookie_token = getCookie("utoken");

	if(cookie_token){
		$(".navigation").append('<div id="header"><div class="col-md-1"></div>' +
            '<div class="col-md-12 nav clearFix">'+
            '<ul class="col-md-12 nav-l fl"><li><a href="http://www.clickgenome.org/">Home</a></li><li><a href="http://analysis.clickgenome.org/">Data Analysis</a>' +

            '<div class="menu col-md-12 clearFix">' +
            '<dl class="dataPlots fl">' +
            '<dt>Data visualization</dt>' +
            '<dd class="clearFix"><a href="http://analysis.clickgenome.org/todo/beeswarm/">Bee-swarm plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/mountain/">Mountain plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/manhattan/">Manhattan plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/deflection/">Deflection plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Lego plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/volcano/">Volcano plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Heatmap</a></dd>' +
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

            '<li><a href="http://analysis.clickgenome.org/submitIndex.html">Analyze yours</a>' +


            '<div class="menu col-md-12 clearFix">' +
            '<dl class="dataPlots fl">' +
            '<dt>Data visualization</dt>' +
            '<dd class="clearFix">' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/beeswarmIndex.html">Bee-swarm plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/mountainIndex.html">Mountain plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/manhattanIndex.html">Manhattan plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/deflectionIndex.html">Deflection plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Lego plot</a>' +
            // '<a href="javascript:return false;" style= "cursor:default;color: #a7a7a7">Volcano plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Heatmap</a></dd>' +
            '</dl>' +
            // '<dl class="dataMining fl"><dt>Data Mining</dt>' +
            // '<dd class="clearFix">' +
            // '<a href="javascript:return false;" style= "cursor:default;color: #a7a7a7">Linear regression analysis</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Significance test</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Survival analysis</a></dd>' +
            // '</dl>' +
            '</div>' +
            '</li><li><a href="http://www.clickgenome.org/guide/">User`s Guide</a></li><li><a href="http://www.clickgenome.org/papers/">Papers</a></li>' +
            '<li><a href="http://forum.clickgenome.org?utoken='+cookie_token+'">Forum</a></li><li><a href="http://www.clickgenome.org/about/">About us</a></li>' +
            '<li id="logout"><a href="http://analysis.clickgenome.org">Logout</a></li></ul></div></div>');
		$("#logout").click(function(){
			deleteCookie("utoken");
		});
	} else {
		$(".navigation").append('<div id="header">' +
            '<div class="col-md-12 nav clearFix">' +
            '<ul class="col-md-12 nav-l fl"><li style="margin-left: 8%"><a href="http://www.clickgenome.org/">Home</a></li><li><a href="http://analysis.clickgenome.org/">Data Analysis</a>' +


            '<div class="menu col-md-12 clearFix">' +
            '<dl class="dataPlots fl">' +
            '<dt>Data visualization</dt>' +
            '<dd class="clearFix"><a href="http://analysis.clickgenome.org/todo/beeswarm/">Bee-swarm plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/mountain/">Mountain plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/manhattan/">Manhattan plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/deflection/">Deflection plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Lego plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/volcano/">Volcano plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Heatmap</a></dd>' +
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

            '<li><a href="http://analysis.clickgenome.org/submitIndex.html">Analyze yours</a>' +

            '<div class="menu col-md-12 clearFix">' +
            '<dl class="dataPlots fl">' +
            '<dt>Data visualization</dt>' +
            '<dd class="clearFix">' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/beeswarmIndex.html">Bee-swarm plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/mountainIndex.html">Mountain plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/manhattanIndex.html">Manhattan plot</a>' +
            '<a href="http://analysis.clickgenome.org/todo/fileInput/deflectionIndex.html">Deflection plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Lego plot</a>' +
            // '<a href="javascript:return false;" style= "cursor:default;color: #a7a7a7">Volcano plot</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Heatmap</a></dd>' +
            '</dl>' +
            // '<dl class="dataMining fl"><dt>Data Mining</dt>' +
            // '<dd class="clearFix">' +
            // '<a href="javascript:return false;" style= "cursor:default;color: #a7a7a7">Linear regression analysis</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Significance test</a>' +
            // '<a href ="javascript:return false;" style= "cursor:default;color: #a7a7a7">Survival analysis</a></dd>' +
            // '</dl>' +
            '</div>' +
            '</li><li><a href="http://www.clickgenome.org/guide/">User`s Guide</a></li><li><a href="http://www.clickgenome.org/papers/">Papers</a></li>' +
            '<!--<li><a href="http://forum.clickgenome.org/">Forum</a></li>--><li><a href="http://www.clickgenome.org/about/">About us</a></li>' +
            '<li id="login"><a href="http://portal.clickgenome.org/">Login</a></li></ul></div></div>');
	}

	$("#" + $("body").attr("active")).addClass("active");
	$(".footer").append('<div class="row"><div class="col-md-1"></div><div class="col-md-2 brand"><a href="#"><img src="http://analysis.clickgenome.org/img/logo/clickgenome_logo_blue.png"/></a><small><span>&copy;2016-2018 clickgenome.org</span></small></div><div class="col-md-8"><div class="info"><h4 class="title">About Data</h4><div class="con"><a href="/#analysis_function">Analysis</a><a href="http://analysis.clickgenome.org/submitIndex.html">Submit</a></div></div><div class="info"><h4 class="title">Quick Entrance</h4><div class="con"><a href="http://www.clickgenome.org/newsandupdates/">News&Updates</a><a href="http://www.clickgenome.org/documents/">Documents</a><a href="http://www.clickgenome.org/papers/">Papers</a><!--<a href="http://forum.clickgenome.org/">Forum</a>--></div></div><div class="info"><h4 class="title">About</h4><div class="con"><a href="http://www.clickgenome.org/about/">Our Project</a><a href="http://www.clickgenome.org/about/">Our Team</a><a href="http://www.clickgenome.org/about/">Contact US</a><a href="http://forum.clickgenome.org/faqs/">FAQs</a></div></div><div class="info"><h4 class="title">Links</h4><div class="con"><a href="http://www.clickgenome.org/">www.clickgenome.org</a><a href="http://www.tju.edu.cn/">Tianjin University</a><a href="http://www.utsouthwestern.edu/">UT Southwestern</a><a href="https://gdc.cancer.gov/">GDC</a><a href="https://www.nih.gov/">NIH</a></div></div></div><div class="col-md-1"></div></div>');
	$("a[href^='http://']").attr("target", "_self");
	$("a[href^='https://']").attr("target", "_self");
	$("#logout").attr("target","");
	$("#login").attr("target","");
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
