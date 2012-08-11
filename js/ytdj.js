/*jslint browser:true */
/*global jQuery, YT, youtubedj */

/**
 * Fix needToConfirm to check all players
 */


//onError
var needToConfirm = false;
window.onbeforeunload = function askConfirm() {
	"use strict";

	if (needToConfirm) {
		return "ARE YOU SURE!?";
	}
};


var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubePlayerAPIReady() {
	"use strict";

	jQuery('.deck').deck();
	jQuery('.mixer').mixer();
	jQuery('.search').search();
}

(function ($) {
	"use strict";

	$.fn.deck = function () {
		function load_movie(id, code) {
			var player = new YT.Player(id, {
				height: '250',
				width: '300',
				playerVars: {
					//controls: 0
				},
				videoId: code
			});

			$.data(document.body, id, player);

			return player;
		}

		return this.each(function () {
			var deck   = $(this);
			var player = null;
			var id     = deck.find('.player').attr('id');
			var code   = deck.find('.player').attr('movie');

			if (code.length > 0) {
				player = load_movie(id, code);
			}

			$('.play', deck).click(function () {
				if (player) {
					player.playVideo();
					needToConfirm = true;
				}
			});

			$('.pause', deck).click(function () {
				if (player) {
					player.pauseVideo();
				}
			});

			$('.stop', deck).click(function () {
				if (player) {
					player.stopVideo();
					needToConfirm = false;
				}
			});

			$('.gain', deck).slider({
				orientation: "vertical",
				min: 0,
				max: 100,
				value: 100,
				slide: function (event, ui) {
					if (player) {
						player.setVolume(ui.value);
					}
				}
			});
		});
	};

	$.fn.mixer = function () {
		function crossFade(deckId1, deckId2, fadeLoc, fadeLocOld, animate) {
			animate = animate === 'undefined' ? false : animate;

			var deck1 = $('#' + deckId1);
			var deck2 = $('#' + deckId2);

			var volDeck1 = jQuery('.gain', deck1).slider('option', 'value');
			var volDeck2 = jQuery('.gain', deck2).slider('option', 'value');

			if (animate) {
				jQuery({fadeLoc: fadeLocOld}).animate({fadeLoc: fadeLoc}, {
					duration: 500,
					easing: 'linear',
					step: function () {
						$.data(document.body, deck1.find('.player').attr('id')).setVolume((100 - Math.ceil(this.fadeLoc)) * (volDeck1 / 100));
						$.data(document.body, deck2.find('.player').attr('id')).setVolume(Math.ceil(this.fadeLoc) * (volDeck2 / 100));
					},
					complete: function () {
						$.data(document.body, deck1.find('.player').attr('id')).setVolume((100 - fadeLoc) * (volDeck1 / 100));
						$.data(document.body, deck2.find('.player').attr('id')).setVolume(fadeLoc * (volDeck2 / 100));
					}
				});
			} else {
				$.data(document.body, deck1.find('.player').attr('id')).setVolume((100 - fadeLoc) * (volDeck1 / 100));
				$.data(document.body, deck2.find('.player').attr('id')).setVolume(fadeLoc * (volDeck2 / 100));
			}
		}

		return this.each(function () {
			var mixer = $(this);
			var deck1 = $(this).attr('deck1');
			var deck2 = $(this).attr('deck2');

			if (deck1.length > 0 && deck2.length > 0) {
				var defaultvalue = 60;
				var oldvalue = defaultvalue;

				var slider = $('.crossfader', mixer).slider({
					min: 0,
					max: 100,
					value: defaultvalue,
					animate: 500,
					slide: function (event, ui) {
						crossFade(deck1, deck2, ui.value, oldvalue);
						oldvalue = ui.value;
					},
					change: function (event, ui) {
						oldvalue = ui.value;
					}
				});

				$('.mixer-button-a', mixer).click(function () {
					crossFade(deck1, deck2, 0, oldvalue, true);
					slider.slider('value', 0);
				});

				$('.mixer-button-b', mixer).click(function () {
					crossFade(deck1, deck2, 100, oldvalue, true);
					slider.slider('value', 100);
				});
			}
		});
	};

	$.fn.search = function () {
		function load_data(searchresult_box, decks, searchterm, offset) {
			decks = decks.split(',');

			var data = {
				action: 'youtubedj_search',
				searchTerm: searchterm,
				offset: offset
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(youtubedj.ajax, data, function (response) {
				searchresult_box.html('');

				var html_decks = '';
				$.each(decks, function (key, value) {
						html_decks += '<a class="load" deck="'+ value +'">' + value + '</a>';
					});

				var html = '<ul class="videolist">';

				$.each(response.songs, function (key, value) {
					html += '<li id="' + value.id + '" class="result song">';
					html += '<img src="' + value.thumbnail.normal + '" alt="' + value.title + '">';
					html += '<h5>' + value.title + '</h5>';
					html += '<div class="loadto">';
					//html += '<a class="fav">Fav</a>';
					//html += '<a class="queue">Add to queue</a>';
					html += html_decks;
					html += '</div>';
					html += '</li>';
				});

				html += '</ul>';

				searchresult_box.html(html);
			});
		}

		return this.each(function () {
			var search = $(this);

			$('form', search).click(function (evt) {
				evt.preventDefault();
				var searchTerm = $('.searchTerm', search).val();

				if (searchTerm.length > 0) {
					load_data($('.searchResults', search), search.attr('decks'), searchTerm, 1);
				}
			});
		});
	};

})(jQuery);


/*
function deckLoad(deckId, videoId) {
	"use strict";

	var startSeconds = 0;
	var suggestedQuality = "small";
	ytplayers[deckId].cueVideoById(videoId, startSeconds, suggestedQuality);
}


function getUrlVars()
{
	"use strict";

	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
}
*/