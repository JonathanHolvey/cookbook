Array.prototype.unique = function() {
    var unique = [];
    for (var i = 0; i < this.length; i++) {
        if (unique.indexOf(this[i]) == -1) {
            unique.push(this[i]);
        }
    }
    return unique;
};

// convert strings such as tomato(es) and leaf/leaves to tomatoes and leaves
String.prototype.pluralise = function() {
	return this.replace(/(\w+)\((\w*s)\)|(\w+)\/(\w+s)/g, "$1$2$4");
};


$(document).ready(function() {
	// hide ingredient info bubbles when clicking away
	$("body").click(function(event) {
		$(".recipe-instructions .bubble, .recipe-instructions .ingredient").removeClass("active");
	});
	// position and show ingredient info bubbles
	$(".recipe-instructions .ingredient").click(function(event) {
		event.stopPropagation();
		// hide open bubbles and reset positioning
		$(".recipe-instructions .ingredient").removeClass("active").find(".bubble").css("left", 0);
		// set maximum bubble width
		$(this).find(".bubble").css("max-width", $("body").width() - 2 * parseFloat($(this).find(".bubble").css("margin-left")));
		// calculate available space for centring bubble over ingredient
		margin = parseFloat($(this).find(".bubble").css("box-shadow").match(/(\d+px)/g)[2]) / 3;
		leftShift = ($(this).find(".bubble").outerWidth() - $(this).innerWidth()) / 2;
		leftSpace = $(this).offset().left - parseFloat($(this).find(".bubble").css("margin-left"));
		rightSpace = $(this).offset().left + $(this).find(".bubble").outerWidth() + parseFloat($(this).find(".bubble").css("margin-left")) - $("body").width();
		// move bubble as close to the centre of ingredient as possible, without running off the page
		$(this).find(".bubble").css("left", -1 * Math.max(Math.min(leftShift, leftSpace - margin), rightSpace + margin));
		// show bubble
		$(this).addClass("active");
	});

	// show pages
	$(".page-tab").click(function () {
		setPage($(this).attr("data-page"));
	});

	// load correct page tab on load or history
	parseURL();
	// run stretchPages() - must have an active page available with position
	stretchPages();

	// change page on swipe left and right
	$(".page-holder").on("swipeleft", function(event) {
		$(".page-tab.active").next().click();
	});
	$(".page-holder").on("swiperight", function(event) {
		$(".page-tab.active").prev().click();
	});

	// check and uncheck boxes in ul.checklist elements and update list counter
	$("ul.checklist li").click(function() {
		if ($(this).hasClass("checked")) {
			$(this).removeClass("checked");
			adjust = 1;
		}
		else {
			$(this).addClass("checked");
			adjust = 0;
		}
		// update list counter with number of unchecked items
		count = $(this).siblings("li").not(".checked").length + adjust;
		$(this).parent().parent().find(".list-count .counter").html(count);
		if (count == 1)
			$(".list-count h2").html("item remaining");
		else
			$(".list-count h2").html("items remaining");
	});

	// add decoration to hr elements
	$("hr").each(function() {		
		$(this).append("<div class=\"decoration\"><div class=\"circle\"/><div class=\"circle\"/><div class=\"circle\"/></div>");
	});

	// make full recent boxes clickable
	$(".recent-box").click(function() {
		location.href = $(this).find("a").attr("href");
	});

	window.addEventListener("popstate", function(e) {
		parseURL();
	});
});
$(window).resize(function() {
	stretchPages();
});

// force page backgrounds to stretch to the bottom of the page
function stretchPages() {
	page = $(".page.active");
	height = $(window).height() - page.offset().top - parseInt(page.css("padding-top")) - parseInt(page.css("padding-bottom"));
	$(".page").css("min-height", height);
}

// load correct page tab from current url
function parseURL() {
	if (pageRoot != location.href.replace(/\/$/, "")) {
		pageInfo = location.href.replace(pageRoot + "/", "");
		pageName = pageInfo.replace(/\/.*$/, "");
		showPage(pageName);
	}
	else
		showPage("");
}

// change page and add tab navigation to history
function setPage(pageName) {
	showPage(pageName);
	history.pushState(null, null, pageRoot + "/" + pageName);
}

function showPage(pageName) {
	// hide current page
	$(".page,.page-tab").removeClass("active");
	// show new page
	if (pageName !== "")
		page = $("#" + pageName);
	else
		page = $(".page").first();
	page.addClass("active");
	// replace active tab with current
	$(".page-tab[data-page=\"" + $(page).attr("id") + "\"]").addClass("active");
	// focus empty input on page change (mainly for search)
	if (page.find("input").first().val() === "")
		page.find("input").first().focus();
	$(window).scroll();
}