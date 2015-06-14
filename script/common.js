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
}


$(document).ready(function() {
	stretchPages();

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
		// hide current page
		$(".page.active").removeClass("active");
		// show new page
		var page = $("#" + $(this).attr("data-page"));
		page.addClass("active");
		// focus empty input on page change (mainly for search)
		if (page.find("input").first().val() == "")
			page.find("input").first().focus();
		// replace active tab with current
		$(".page-tab").removeClass("active");
		$(this).addClass("active");
		$(window).scroll();
		stretchPages();
	});

	// change page on swipe left and right
	$(".page-holder").on("swipeleft", function(event) {
		// event.preventDefault();
		$(".page-tab.active").next().click();
	});

	// $(".page-holder").bind("swipeone", function(event, object) {
	// 	event.preventDefault();
	// 	var xDirection = object.description.split(":")[2];
	// 	if (xDirection == "left")
	// 		$(".page-tab.active").next().click();
	// 	if (xDirection == "right")
	// 		$(".page-tab.active").prev().click();
	// });

	// check and uncheck boxes in ul.check-list elements and update list counter
	$("ul.check-list li").click(function() {
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
	})

	// add decoration to hr elements
	$("hr").each(function() {		
		$(this).append("<div class=\"decoration\"><div class=\"circle\"/><div class=\"circle\"/><div class=\"circle\"/></div>");
	})

	// make full recent boxes clickable
	$(".recent-box").click(function() {
		location.href = $(this).find("a").attr("href");
	});
});
$(window).resize(function() {
	stretchPages();
});

// force page backgrounds to stretch to the bottom of the page
function stretchPages() {
	$(".page").each(function() {
		height = $(window).height() - $(this).offset().top - parseFloat($(this).css("padding-top")) - parseFloat($(this).css("padding-bottom"));
		$(this).css("min-height", height);
	});

}