var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

function onYouTubePlayerAPIReady() {
	jQuery('.deck').deck();
}

(function( $ ){

	$.fn.deck = function() {
		return this.each(function() {
			var _this = $(this);
			player    = _this.find('.player');
			var id    = player.attr('id');
			var code  = player.attr('movie');

			if( code.length > 0 ) {
				load_movie( id, code );
			}
		});

		function load_movie( id, code ) {
			player = new YT.Player( id, {
				height: '250',
				width: '300',
				videoId: code
			});
		};
	}

})( jQuery );






var params = { allowScriptAccess: "always" };

		
		function deckLoad(deckId, videoId) {
			//alert(videoId + ' ' + deckId);
			var startSeconds = 0;
			var suggestedQuality = "small";
			ytplayers[deckId].cueVideoById(videoId, startSeconds, suggestedQuality);
		}
		
		function deckPlay(deckId) {
		  if (ytplayers[deckId]) {
			ytplayers[deckId].playVideo();
			 var needToConfirm = true;
		  }
		}
				
		function deckPause(deckId) {
		  if (ytplayers[deckId]) {
			ytplayers[deckId].pauseVideo();
		  }
		}
		
		function deckStop(deckId) {
		  if (ytplayers[deckId]) {
			ytplayers[deckId].stopVideo();
		  }
		}
		
		function crossFade(deckId1, deckId2, fadeLoc) {
			var volDeck1 = jQuery('#deck0 .gain').slider('option', 'value');
			var volDeck2 = jQuery('#deck1 .gain').slider('option', 'value');
			ytplayers[deckId1].setVolume( ( 100 - fadeLoc ) * ( volDeck1 / 100 ) );
			ytplayers[deckId2].setVolume(fadeLoc * ( volDeck2 / 100 ) );
		}
		
		//onError
			
		needToConfirm = false;
		window.onbeforeunload = askConfirm;
		
		function askConfirm(){
			if (needToConfirm){
				return "ARE YOU SURE!?";
			}    
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
			//alert (vars[1]);
		}
		
		//getUrlVars();
			
		// jQuery Shizzle
		jQuery(function( $ ) {
			
			$("#crossfader").slider({ 
				//orientation: "vertical",
				//range: "min",
				min: 0,
				max: 100,
				value: 60,
				slide: function(event, ui) {
					//$("#amount").val(ui.value);
					crossFade(0, 1, ui.value);
				}
			});
				
			$("#deck0 .gain").slider({ 
				orientation: "vertical",
				//range: "min",
				min: 0,
				max: 100,
				value: 100,
				slide: function(event, ui) {
					//$("#amount").val(ui.value);
					ytplayers[0].setVolume(ui.value);
				}
			});
				
			$("#deck1 .gain").slider({ 
				orientation: "vertical",
				//range: "min",
				min: 0,
				max: 100,
				value: 100,
				slide: function(event, ui) {
					//$("#amount").val(ui.value);
					ytplayers[1].setVolume(ui.value);
				}
			});

			
			$('#cfToA').click(function () {
				$('#crossfader').slider('option', 'value', 0);
				crossFade(0, 1, 0);
			});	
			
			$('#cfToB').click(function () {
				$('#crossfader').slider('option', 'value', 100);
				crossFade(0, 1, 100);
			});
			
		});
