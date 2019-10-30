$("document").ready(function() {
	fixTopMarginForFullWidthBackgrounds();
	setBackgroundAndNav();
	
	$("body").css("background-color", "#eef9f6");
	
	if ($(".container.theatrical:nth-child(3), .container.music:nth-child(3), .container.dance:nth-child(3), .welcome:nth-child(3), .container.pa_container:nth-child(3)").length!==0) {
		setUpTransparentNav();
		$("#pa_brand img").attr("src", "/img/icons/pa_white.svg");
		$(".navbar-inverse *").css("color", "#ffffff");
	} else {
		setUpPAbrand();
	}
	
	$(document).scroll(setUpTransparentNav);
	$(".navbar-collapse").on('shown.bs.collapse', setUpTransparentNav);
	$(".navbar-collapse").on('hidden.bs.collapse', setUpTransparentNav);
	
	$(window).resize(matchingCols);
	matchingCols();
});



function fixTopMarginForFullWidthBackgrounds() {
	var children = $(".pa-wrapper").children();
	var first = $(children[1]);
	var nav = $(children[0]);
	
	if (first.height() === 0) {
		nav.css('margin-bottom','0px');
		$(".pa-wrapper").css("padding-top", "49px");
	}
}

function getArea() {
	if ($(".welcome, .well-pa, .well-pa-fancy, .container.pa_container").length > 0) return 0;
	if ($(".well-theatrical, .well-theatrical-fancy, .container.theatrical").length > 0) return 1;
	if ($(".well-music, .well-music-fancy, .container.music").length > 0) return 2;
	if ($(".well-dance, .well-dance-fancy, .container.dance").length > 0) return 3;
}

function setBackgroundAndNav() {
	if ($(".welcome, .well-pa, .well-pa-fancy, .container.pa_container").length > 0) {
		setNavColour(0);
		return;
	}
	
	if ($(".well-theatrical, .well-theatrical-fancy, .container.theatrical").length > 0) {
		$("body, .pa-wrapper").css("background-color", "#eef9f6");
		
		setNavColour(1);
		$(".fancyActions > .btn.btn-pa").removeClass("btn-pa").addClass("btn-theatrical");
	}
	
	if ($(".well-dance, .well-dance-fancy, .container.dance").length > 0) {
		$("body, .pa-wrapper").css("background-color", "#fffaed");
		
		setNavColour(3);
		$(".fancyActions > .btn.btn-pa").removeClass("btn-pa").addClass("btn-dance");
	} 
	
	if ($(".well-music, .well-music-fancy, .container.music").length > 0) {
		$("body, .pa-wrapper").css("background-color", "#ecf4f9");
		
		setNavColour(2);
		$(".fancyActions > .btn.btn-pa").removeClass("btn-pa").addClass("btn-music");
	}
}

function setNavColour(area, opacity) {
	if (opacity===undefined)
		opacity=1;
		
	if ($(".navbar-collapse.collapse").attr("aria-expanded")==="true") {
	   opacity=0.7;
	}
	
	opacity2 = opacity+0.4;
	
	if (area===0) {
		$(".navbar-inverse").css("background-color", "rgba(48,29,47,"+opacity+")");
		$(".navbar-inverse").css("background-image", "-moz-linear-gradient(top, rgba(76,44,74,"+opacity2+"), rgba(48,29,47,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-webkit-gradient(linear, 0 0, 0 100%, from(rgba(76,44,74,"+opacity2+")), to(rgba(48,29,47,"+opacity+")))");
		$(".navbar-inverse").css("background-image", "-webkit-linear-gradient(top, rgba(76,44,74,"+opacity2+"), rgba(48,29,47,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-o-linear-gradient(top, rgba(76,44,74,"+opacity2+"), rgba(48,29,47,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "linear-gradient(to bottom, rgba(76,44,74,"+opacity2+"), rgba(48,29,47,"+opacity+"))");
	
      $(".dropdown-menu").addClass("pa");
      $(".navbar-inverse").addClass("pa");
	}
	
	if (area===1) {
		$(".navbar-inverse").css("background-color", "rgba(22,38,29,"+opacity+")");
		$(".navbar-inverse").css("background-image", "-moz-linear-gradient(top, rgba(37,66,50,"+opacity2+"), rgba(22,38,29,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-webkit-gradient(linear, 0 0, 0 100%, from(rgba(37,66,50,"+opacity2+")), to(rgba(22,38,29,"+opacity+")))");
		$(".navbar-inverse").css("background-image", "-webkit-linear-gradient(top, rgba(37,66,50,"+opacity2+"), rgba(22,38,29,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-o-linear-gradient(top, rgba(37,66,50,"+opacity2+"), rgba(22,38,29,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "linear-gradient(to bottom, rgba(37,66,50,"+opacity2+"), rgba(22,38,29,"+opacity+"))");
	
	   $(".dropdown-menu").addClass("theatrical");
	   $(".navbar-inverse").addClass("theatrical");
	}
	
	if (area===2) {
		$(".navbar-inverse").css("background-color", "rgba(28,46,61,"+opacity+")");
		$(".navbar-inverse").css("background-image", "-moz-linear-gradient(top, rgba(39,66,89,"+opacity2+"), rgba(28,46,61,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-webkit-gradient(linear, 0 0, 0 100%, from(rgba(39,66,89,"+opacity2+"), to(rgba(28,46,61,"+opacity+")))");
		$(".navbar-inverse").css("background-image", "-webkit-linear-gradient(top, rgba(39,66,89,"+opacity2+"), rgba(28,46,61,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-o-linear-gradient(top, rgba(39,66,89,"+opacity2+"), rgba(28,46,61,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "linear-gradient(to bottom, rgba(39,66,89,"+opacity2+"), rgba(28,46,61,"+opacity+"))");
	
	   $(".dropdown-menu").addClass("music");
	   $(".navbar-inverse").addClass("music");
	}
	
	if (area===3) {
		$(".navbar-inverse").css("background-color", "rgba(58,45,9,"+opacity+")");
		$(".navbar-inverse").css("background-image", "-moz-linear-gradient(top, rgba(71,55,9,"+opacity2+"), rgba(58,45,9,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-webkit-gradient(linear, 0 0, 0 100%, from(rgba(71,55,9,"+opacity2+")), to(rgba(58,45,9,"+opacity+")))");
		$(".navbar-inverse").css("background-image", "-webkit-linear-gradient(top, rgba(71,55,9,"+opacity2+"), rgba(58,45,9,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "-o-linear-gradient(top, rgba(71,55,9,"+opacity2+"), rgba(58,45,9,"+opacity+"))");
		$(".navbar-inverse").css("background-image", "linear-gradient(to bottom, rgba(71,55,9,"+opacity2+"), rgba(58,45,9,"+opacity+"))");
	
	   $(".dropdown-menu").addClass("dance");
	   $(".navbar-inverse").addClass("dance");
	}
	
	$(".navbar-inverse").css("box-shadow", "0px 0px 5px rgba(255,255,255,"+opacity+")");
}

function setUpPAbrand() {
	$("#pa_brand").hover(function() {
		$("#pa_brand img").attr("src", "/img/icons/pa_white.svg");
	},
	function() {
		$("#pa_brand img").attr("src", "/img/icons/pa_grey.svg");
	});
}

function setUpTransparentNav() {
	var cont = $(".container.theatrical:nth-child(3), .container.music:nth-child(3), .container.dance:nth-child(3), .container.pa_container:nth-child(3), .welcome:nth-child(3)");
	$(".welcome").css("height", "calc(100vh + 2px)");
	
	cont.css("padding-top", "50px");
	cont.css("margin-top", "-50px");
	
	var firstElement = $(cont[0]);
	
	while (firstElement.length <= 1 && $(firstElement.context).prop("tagName") !== undefined) {
		firstElement = $(firstElement[0].firstChild);
	}

	firstElement = $(firstElement.context.parentElement);
	
	setNavColour(getArea(), Math.min($(document).scrollTop()/(firstElement.offset().top+25), 1));
}

function matchingCols() {
   $("#col2").css("height", $("#col1").height());
}

