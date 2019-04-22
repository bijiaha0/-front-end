$(document).ready(function() {
	$(".skip").animateCss("pulse");
	$(".skip > a,.skip > em > stop").bind("mouseover", function() {
		$(this).animateCss("pulse")
	});
	$(".skip > em > stop").bind("click", function() {
		STOP = false;
	});
	$(window).load(function() {
		$('#loading').fadeOut(500);
		title();
		lasttime(6);
	});
});

$.fn.extend({
	animateCss: function(animationName) {
		var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
		this.addClass('animated ' + animationName).one(animationEnd, function() {
			$(this).removeClass(animationName);
		});
	}
});

var STOP = true;

function lasttime(t) {
	if(!STOP) {
		$(".skip > em").text("Do not automatically skips!");
		return;
	}
	if(t < 0) {
		$(".skip > em").text("Enjoy your data analysis journey.");
		setTimeout(function() {
			addCookie("isnew", "0", "");
			window.location.href = "../";
		}, 1000);
	} else {
		$(".skip > em > lasttime").text(t);
		setTimeout(function() {
			lasttime(t - 1);
		}, 1000);
	}
}

$("#skipnow").bind('click', function() {
	addCookie("isnew", "0", "");
	window.location.href = "../";
});

function title() {
	var container = $(".title");
	var title = "Click:Do genomic analysis by yourself by Mouse-Clicking";
	var index = 0;
	var length = title.length;
	container.text("");
	var tId = setInterval(function() {
		container.append(title.charAt(index));
		if(index++ === length) {
			clearInterval(tId);
		}
	}, 60);

	var count = 0;
	var pId = setInterval(function() {
		if(count % 2 == 0) {
			$("p:eq(" + count + ")").animateCss("slideInLeft")
		} else {
			$("p:eq(" + count + ")").animateCss("slideInRight")
		}
		if(count++ === 4) {
			clearInterval(pId);
			$(".title").animateCss("tada");
		}
	}, 600);

	var total = 0;
	var iId = setInterval(function() {
		if(total % 2 == 0) {
			$("img:eq(" + total + ")").animateCss("flipInX")
		} else {
			$("img:eq(" + total + ")").animateCss("flipInY")
		}

		if(total++ === 7) {
			clearInterval(iId);
		}
	}, 300);
}