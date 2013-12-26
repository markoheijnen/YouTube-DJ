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
		return youtubedj.are_you_sure;
	}
};

function youtubedj_get(id) {
	"use strict";
	return jQuery.data(document.body, id);
}

function youtubedj_set(id, object) {
	"use strict";
	jQuery.data(document.body, id, object);
}

function in_array(array, id) {
	for(var i=0;i<array.length;i++) {
		if(array[i] === id) {
			return true;
		}
	}
	return false;
}

var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubePlayerAPIReady() {
	"use strict";
	jQuery('.deck').deck();
	jQuery('.mixer').mixer();
	jQuery('.search').search();
	jQuery('.queue').songqueue();
}

function onStateChange( newState ) {
	if( newState.data == YT.PlayerState.PLAYING || newState.data == YT.PlayerState.BUFFERING ) {
	}
	else if( newState.data == YT.PlayerState.ENDED ) {
		var queue = youtubedj_get( newState.target.a.getAttribute('queue') );
		queue.play_next( newState.target.a.parentNode.getAttribute('id') );
	}
}

function onPlayerReady(event) {
	event.target.playVideo();
	event.target.pauseVideo();
}

(function ($) {
	"use strict";

	$.fn.deck = function () {
		function load_player(id, code) {
			var player = new YT.Player(id, {
				height: '250',
				width: '320',
				playerVars: {
					//controls: 0
				},
				videoId: code
			});

			return player;
		}

		return this.each(function () {
			var deck       = this;
			this.deck      = $(this);
			this.player    = null;
			this.player_id = this.deck.find('.player').attr('id');
			this.code      = this.deck.find('.player').attr('movie');

			youtubedj_set(this.deck.attr('id'), this);

			if (this.code.length > 0) {
				this.player = load_player(this.player_id, this.code);
				this.player.addEventListener("onReady", "onPlayerReady");
				this.player.addEventListener("onStateChange", "onStateChange");
			}

			$('.play', this.deck).click(function () {
				if (deck.player) {
					deck.player.playVideo();
					needToConfirm = true;
				}
			});

			$('.pause', this.deck).click(function () {
				if (deck.player) {
					deck.player.pauseVideo();
				}
			});

			$('.stop', this.deck).click(function () {
				if (deck.player) {
					deck.player.stopVideo();
					needToConfirm = false;
				}
			});

			$('.gain', this.deck).slider({
				orientation: "vertical",
				min: 0,
				max: 100,
				value: 100,
				slide: function (event, ui) {
					if (deck.player) {
						deck.player.setVolume(ui.value);
					}
				}
			});
		});


	};

	$.fn.mixer = function () {
		function crossFade(deckId1, deckId2, fadeLoc, fadeLocOld, animate) {
			animate = animate === 'undefined' ? false : animate;

			var deck1 = youtubedj_get(deckId1);
			var deck2 = youtubedj_get(deckId2);

			var volDeck1 = jQuery('#' + deckId1 + ' .gain').slider('option', 'value');
			var volDeck2 = jQuery('#' + deckId2 + ' .gain').slider('option', 'value');

			if (animate) {
				jQuery({fadeLoc: fadeLocOld}).animate({fadeLoc: fadeLoc}, {
					duration: 500,
					easing: 'linear',
					step: function () {
						deck1.player.setVolume((100 - Math.ceil(this.fadeLoc)) * (volDeck1 / 100));
						deck2.player.setVolume(Math.ceil(this.fadeLoc) * (volDeck2 / 100));
					},
					complete: function () {
						deck1.player.setVolume((100 - fadeLoc) * (volDeck1 / 100));
						deck2.player.setVolume(fadeLoc * (volDeck2 / 100));
					}
				});
			} else {
				deck1.player.setVolume((100 - fadeLoc) * (volDeck1 / 100));
				deck2.player.setVolume(fadeLoc * (volDeck2 / 100));
			}
		}

		return this.each(function () {
			var mixer = $(this);
			var deck1 = $(this).attr('deck1');
			var deck2 = $(this).attr('deck2');

			if (deck1.length > 0 && deck2.length > 0) {
				var defaultvalue = 50;
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
		function load_data(searchresult_box, searchterm, offset, decks, queue) {
			decks = decks.split(',');

			var data = {
				action: 'youtubedj_search',
				searchTerm: searchterm,
				offset: offset
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post(youtubedj.ajax, data, function (response) {
				searchresult_box.html('');

				var buttons = '';
				//buttons += '<a class="fav">Fav</a>';

				if (queue) {
					buttons += '<a class="queue">' + youtubedj.add_to_queue + '</a>';
				}

				$.each(decks, function (key, value) {
					var title = $('#' + value).find('h2').html();
					buttons += '<a class="loadsong" deck="' + value + '">' + title + '</a>';
				});

				var html = '<ul class="videolist">';
				if (response) {
					$.each(response.songs, function (key, value) {
						html += '<li id="' + value.id + '" class="result song">';
						html += '<img src="' + value.thumbnail.normal + '" alt="' + value.title + '">';
						html += '<h5>' + value.title + '</h5>';
						html += '<div class="loadto">';
						html += buttons;
						html += '</div>';
						html += '</li>';
					});
				}

				html += '</ul>';

				searchresult_box.html(html);
			});
		}

		return this.each(function () {
			var search  = $(this);
			var queue   = search.attr('queue');
			var results = $('.searchResults', search);

			$('form', search).click(function (evt) {
				evt.preventDefault();
				var searchTerm = $('.searchTerm', search).val();

				if (searchTerm.length > 0) {
					load_data(results, searchTerm, 1, search.attr('decks'), queue);
				}
			});

			$(search).on("click", '.loadsong', function (evt) {
				evt.preventDefault();

				var deck = youtubedj_get($(this).attr('deck'));
				deck.player.cueVideoById($(this).closest('.song').attr('id'), 0, 'small');
			});

			if (queue) {
				$(search).on("click", '.queue', function (evt) {
					evt.preventDefault();
					youtubedj_get(queue).add($(this).closest('.song').attr('id'));
				});
			}
		});
	};

	$.fn.songqueue = function () {
		return this.each(function () {
			this.queue = $(this);
			youtubedj_set(this.queue.attr('id'), this);

			var songs = new Array();
			var list  = this.queue.find('.queuelist');

			$( 'li', list ).each(function( index ) {
				songs.push($(this).attr('songid'));
			});


			var decks = this.queue.attr('decks');
			decks = decks.split(',');

			//var queue;
			this.add = function (songid) {
				if(!in_array(songs, songid)) {
					songs.push(songid);

					var html = '<li songid="' + songid + '" class="song">';
					html += '<h5>' + $('#' + songid).find('h5').html() + '</h5>';
					html += '</li>';

					list.append(html);
				}
			};

			this.play_next = function (deck) {
				if( songs[0] ) {
					var currentdeck = youtubedj_get( deck );
					currentdeck.player.cueVideoById( songs[0], 0, 'small');

					list.find('li[songid="' + songs[0] + '"]' ).hide( 1000, function() {
						$(this).remove();
					});

					songs.splice(0, 1);

					var _decks = decks.slice(0);

					var deck_index = _decks.indexOf( deck );
					if( deck_index != -1 ) {
						_decks.splice(deck_index, 1);
					}

					if( _decks[0] ) {
						deck = youtubedj_get( _decks[0] );
						deck.player.playVideo();
					}
				}
			}
		});
	};

})(jQuery);