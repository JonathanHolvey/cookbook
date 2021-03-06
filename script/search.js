function recipeSearchCompare(a, b) {
	return b.score - a.score;
}

// scroll down when searching on small screens
function scrollToSearch() {
	if ($(window).scrollTop() < $("#search").offset().top && window.matchMedia("(max-width: 40em)").matches)
	$(window).scrollTop($("#search").offset().top);
}

// finds whether a word or words can be found in a child property of a recipe
function matchProperties(query, recipe, property, pluralise) {
	// check if one or multiple words are supplied
	var multi = false;
	if (typeof(query) == "object")
		multi = true;
	else
		query = [query];

	var found = false;
	var matches = [];
	// loop through all words
	query.forEach(function(queryWord) {
		// check word is allowed
		if (ignore.indexOf(queryWord) == -1 && queryWord.length >= minMatch) {
			// loop through matching recipe properties
			recipeProperty = typeof(recipe[property]) == "object" ? recipe[property] : [recipe[property]];
			recipeProperty.forEach(function(value) {
				if (value !== null) {
					var string = pluralise ? value.pluralise() : value;
					// find word in text of selected propery
					if (string.toLowerCase().indexOf(queryWord) != -1) {
						matches.push(string);
						found = true;
					}
				}
			});
		}
	});
	// return array of matched strings
	if (multi !== false && found)
		return matches.unique();
	// return boolean stating whether matches were found
	else
		return found;
}

function resetSearch() {
	// show placeholder text when input empty
	if ($("#search-box input").val().length === 0)
		$("#search-box").removeClass("active");
}

var ignore = ["and", "the"]; // words to exclude from search query
var minMatch = 3; // minimum number of characters required for a match

$(document).ready(function() {
	// load recipe index using ajax
	$.ajax({url: "recipe-index.json", dataType: "json"}).done(function(data) {
		var recipeIndex = data;

		// run search when typing occurs
		$("#search-box input").keyup(function(event) {
			// add search query to history on enter press
			if (event.keyCode == 13) {
				history.pushState(null, null, pageRoot + "/search/" + $(this).val().replace(/ /g, "+"));
				if (touchDevice)
					$(this).blur();
			}
			resetSearch();

			// extract search query from input element and split into array of words
			var query = $(this).val().replace(/[,;]/g, "").trim().toLowerCase().split(" ");
			var matches = [];
			// loop through all recipes, looking for matches
			recipeIndex.forEach(function(recipe) {
				var score = 0; // score is used to rank matching recipes
				// search through recipes for each query word
				query.forEach(function(queryWord) {
					var found = false;
					if (score !== false && queryWord.length >= minMatch && ignore.indexOf(queryWord) == -1) {
						// search in recipe title
						if (matchProperties(queryWord, recipe, "t") === true) {
							found = true;
							score += 1.0;
						}
						// search in recipe description
						if (matchProperties(queryWord, recipe, "a") === true) {
							found = true;
							score += 0.3;
						}
						// search in genres
						if (matchProperties(queryWord, recipe, "g") === true) {
							found = true;
							score += 0.5;
						}
						// search in ingredients
						if (matchProperties(queryWord, recipe, "i", true) === true) {
							found = true;
							score += 0.4;
						}
						// reject recipe if word cannot be found
						if (!found)
							score = false; 
					}
				});
				// save search ranking to recipe property and append to array
				recipe.score = score;
	 			if (score !== false && score > 0)
	 				matches.push(recipe);
			});
			
			$("#search-results").empty(); // reset results
			// print out results, if any
			if (matches.length > 0)	{
				// sort array of recipes by search ranking
				matches.sort(recipeSearchCompare);

				// hide search hint and error when showing results
				$("#search-hint, #search-no-match").hide();
				// create html for matched recipes
				matches.forEach(function(match) {
					// create surrounding hyperlink
					$("#search-results").append($("<a>").addClass("result").attr("href", "recipes/" + match.f.replace(".json", "")));
					// create title and description
					$("#search-results .result").last().append($("<h2>").text(match.t));
					$("#search-results .result").last().append($("<p>").text(match.a + "."));
					// create matched ingredients
					var matchedIngredient = matchProperties(query, match, "i", true);
					if (matchedIngredient !== false) {
						$("#search-results .result").last().append($("<p>").addClass("details").append($("<span>").addClass("no-highlight title").text("Ingredients: ")));
						$("#search-results p").last().append(matchedIngredient.sort().join(", ") + "&ensp;");
					}
					// create matched genres
					var matchedGenre = matchProperties(query, match, "g");
					if (matchedGenre !== false) {
						$("#search-results .result").last().append($("<p>").addClass("details").append($("<span>").addClass("no-highlight title").text("Genres: ")));
						$("#search-results p").last().append(matchedGenre.join(", ").replace(/\//g, "&nbsp;&rsaquo; "));
					}
				});
				// highlight each match in resulting html
				query.forEach(function(queryWord) {
					if (queryWord.length >= minMatch && ignore.indexOf(queryWord) == -1) {
						$("#search-results *").not(".no-highlight").highlight(queryWord, "match");
					}
				});
			}
			else {
				// check for non-ignored words with required characters
				searchFail = false;
				query.forEach(function(queryWord) {
					if (queryWord.length >= minMatch && ignore.indexOf(queryWord) == -1)
						searchFail = true;
				});
				// show 'no matches'
				if (searchFail)
					$("#search-no-match").show();
				else
					$("#search-no-match").hide();
				$("#search-hint").show(); // show search hint again if no results
			}
			scrollToSearch();
		}).keyup();
	});

	// hide placeholder text and show clear button when typing occurs
	$("#search-box input").keydown(function() {
		$("#search-box").addClass("active");
	});

	// try to scroll down on search box focus
	$("#search-box input").focus(scrollToSearch);

	// re-show placeholder text if input is empty
	if ($("#search-box input").val() !== "") {
		$("#search-box").addClass("active");
	}

	// clear search query when button is clicked
	$("#search-box .search-icon").click(function() {
		$("#search-box input").val("").keyup().focus();
		history.pushState(null, null, pageRoot + "/search");
	});

	// focus search input on page load
	$("#search-box input").focus();

	// pin search box at top of page when scrolling
	$(window).scroll(function() {
		if ($(window).scrollTop() >= $("#search").offset().top)
			$("#search").addClass("fixed");
		else
			$("#search").removeClass("fixed");			
	});

});