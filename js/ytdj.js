/**
 * Fix needToConfirm to check all players
 */

var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubePlayerAPIReady() {
	jQuery('.deck').deck();
	jQuery('.mixer').mixer();
	jQuery('.search').search();
}

(function( $ ){

	$.fn.deck = function() {
		return this.each(function() {
			var _this  = $(this);
			var player = null;
			var id     = _this.find('.player').attr('id');
			var code   = _this.find('.player').attr('movie');

			if( code.length > 0 ) {
				player = load_movie( id, code );
			}

			$( '.play', _this ).click(function() {
				if( player ) {
					player.playVideo();
					needToConfirm = true;
				}
			});

			$( '.pause', _this ).click(function() {
				if( player ) {
					player.pauseVideo();
				}
			});

			$( '.stop', _this ).click(function() {
				if( player ) {
					player.stopVideo();
					needToConfirm = false;
				}
			});

			$( '.gain', _this ).slider({ 
				orientation: "vertical",
				min: 0,
				max: 100,
				value: 100,
				slide: function(event, ui) {
					if( player ) {
						player.setVolume(ui.value);
					}
				}
			});
		});

		function load_movie( id, code ) {
			player = new YT.Player( id, {
				height: '250',
				width: '300',
				playerVars: {
					//controls: 0
				},
				videoId: code
			});

			$.data( document.body, id, player );

			return player;
		};
	}

	$.fn.mixer = function() {
		return this.each(function() {
			var _this = $(this);
			var deck1 = $(this).attr('deck1');
			var deck2 = $(this).attr('deck2');

			if( deck1.length > 0 && deck2.length > 0 ) {
				var defaultvalue = 60;
				var oldvalue = defaultvalue;

				var slider = $( '.crossfader', _this ).slider({ 
					min: 0,
					max: 100,
					value: defaultvalue,
					animate: 500,
					slide: function(event, ui) {
						crossFade( deck1, deck2, ui.value, oldvalue );
						oldvalue = ui.value;
					},
					change: function(event, ui) {
						oldvalue = ui.value;
					}
				});

				$( '.mixer-button-a', _this ).click(function () {
					crossFade( deck1, deck2, 0, oldvalue, true );
					slider.slider('value', 0 );
				});	
				
				$( '.mixer-button-b', _this ).click(function () {
					crossFade( deck1, deck2, 100, oldvalue, true );
					slider.slider('value', 100 );
				});
			}
		});

		function crossFade(deckId1, deckId2, fadeLoc, fadeLocOld, animate ) {
			animate = typeof animate !== 'undefined' ? animate : false;

			var deck1 = $( '#' + deckId1 );
			var deck2 = $( '#' + deckId2 );

			var volDeck1 = jQuery( '.gain', deck1 ).slider('option', 'value');
			var volDeck2 = jQuery( '.gain', deck2 ).slider('option', 'value');

			if( animate ) {
				jQuery({fadeLoc: fadeLocOld}).animate({fadeLoc: fadeLoc}, {
					duration: 500,
					easing: 'linear',
					step: function() {
						$.data( document.body, deck1.find('.player').attr('id') ).setVolume( ( 100 - Math.ceil( this.fadeLoc ) ) * ( volDeck1 / 100 ) );
						$.data( document.body, deck2.find('.player').attr('id') ).setVolume( Math.ceil( this.fadeLoc ) * ( volDeck2 / 100 ) );
					},
					complete: function() {
						$.data( document.body, deck1.find('.player').attr('id') ).setVolume( ( 100 - fadeLoc ) * ( volDeck1 / 100 ) );
						$.data( document.body, deck2.find('.player').attr('id') ).setVolume( fadeLoc * ( volDeck2 / 100 ) );
					}
				});
			}
			else {
				$.data( document.body, deck1.find('.player').attr('id') ).setVolume( ( 100 - fadeLoc ) * ( volDeck1 / 100 ) );
				$.data( document.body, deck2.find('.player').attr('id') ).setVolume( fadeLoc * ( volDeck2 / 100 ) );
			}
		}
	}

	$.fn.search = function() {
		return this.each(function() {
			var _this = $(this);

			$( 'form', _this ).click(function( evt ) {
				evt.preventDefault();
				load_data( $( '.searchResults', _this ), $( '.searchTerm', _this ).val(), 1 );
			});
		});

		function load_data( searchresult_box, key, offset ) {
			var data = {
				action: 'youtubedj_search',
				searchTerm: key,
				offset: offset
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.post( youtubedj.ajax, data, function(response) {
				searchresult_box.html( '' );

				var html = '<ul class="videolist">';

				$.each(response.songs, function(key, value) { 
					html += '<li id="' + value.id + '" class="videoresult">';
					html += '<img src="' + value.thumbnail.default + '" alt="' + value.title + '">';
					html += '<h5>' + value.title + '</h5>';
					html += '<div class="loadto">';
					html += '<a title="How to give the perfect man hug" class="fav" href="#" mediaid="JUdWApwbudQ">Fav</a>';
					html += '<a class="queue">Add to queue</a>';
					html += '<a class="load loadTo0" href="#" mediaid="JUdWApwbudQ">Deck 1</a>';
					html += '<a class="load loadTo1" href="#" mediaid="JUdWApwbudQ">Deck 2</a>';
					html += '</div>';
					html += '</li>';
				});

				html += '</ul>';

				searchresult_box.html( html );
			});
		}
	}

})( jQuery );


//onError
needToConfirm = false;
window.onbeforeunload = askConfirm;

function askConfirm(){
	if (needToConfirm){
		return "ARE YOU SURE!?";
	}    
}


function deckLoad(deckId, videoId) {
	//alert(videoId + ' ' + deckId);
	var startSeconds = 0;
	var suggestedQuality = "small";
	ytplayers[deckId].cueVideoById(videoId, startSeconds, suggestedQuality);
}


function getUrlVars()
{
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
}