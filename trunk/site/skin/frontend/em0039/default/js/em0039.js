/**
 * Javascript library for template ExtremeMagento
 * @copyright 2007 Quick Solution LTD. All rights reserved.
 * @author Giao L. Trinh <giao.trinh@quicksolutiongroup.com>
 */

(function() {
	
// EM.tools {{{
	
if (typeof BLANK_IMG == 'undefined') 
	var BLANK_IMG = '';

// declare namespace() method
String.prototype.namespace = function(separator) {
  this.split(separator || '.').inject(window, function(parent, child) {
    var o = parent[child] = { }; return o;
  });
};


'EM.tools'.namespace();


// EM0008 {{{

function menu()
{
	var Width_ul=960;
	var Width_li=0;
	var Width_before=0;
	var Width_div=0;
	var Width=0;
	
	$$("#nav").each(function(elem) {
	elem.childElements().each(function(li) {
		li.addClassName('submenu');
	});
		
	});
	
	
    $$(".menu").each(function(elem) {
		elem.childElements().each(function(li) {
			li.addClassName('submenu');
            Width_li=li.getWidth();
			Width=Width_ul-Width_before;
			Width_before+=Width_li;
			$div=li.select('div')[0];
			if(typeof $div != 'undefined'){
				Width_div=$div.getWidth();
				sub=Width_div-Width;
				if(sub>0){
					$div.addClassName(' position-right')
					li.addClassName('position-right-li')
				}
			}
        });
		
	});
}

function decorateSlideshow_moreview() {
	var $$li = $$('#slideshow_moreviews ul li');
	if ($$li.length > 0) {
		
		// reset UL's width
		var ul = $$('#slideshow_moreviews ul')[0];
		var w = 0;
		$$li.each(function(li) {
			w += li.getWidth() + 10;
		});
		ul.setStyle({'width':w+'px'});
		
		// private variables
		var previous = $$('#slideshow_moreviews a.previous')[0];
		var next = $$('#slideshow_moreviews a.next')[0];
		var num = 1;
		var width = ul.down().getWidth() * num;
		var slidePeriod = 3; // seconds
		var manualSliding = false;
		
		// next slide
		function nextSlide() {
			new Effect.Move(ul, { 
				x: -width,
				mode: 'relative',
				queue: 'end',
				duration: 1.0,
				//transition: Effect.Transitions.sinoidal,
				afterFinish: function() {
					for (var i = 0; i < num; i++)
						ul.insert({ bottom: ul.down() });
					ul.setStyle('left:0');
				}
			});
		}
		
		// previous slide
		function previousSlide() {
			new Effect.Move(ul, { 
				x: width,
				mode: 'relative',
				queue: 'end',
				duration: 1.0,
				//transition: Effect.Transitions.sinoidal,
				beforeSetup: function() {
					for (var i = 0; i < num; i++)
						ul.insert({ top: ul.down('li:last-child') });
					ul.setStyle({'position': 'relative', 'left': -width+'px'});
				}
			});
		}
		
		function startSliding() {
			sliding = true;
		}
		
		function stopSliding() {
			sliding = false;
		}
		
		// bind next button's onlick event
		next.observe('click', function(event) {
			Event.stop(event);
			manualSliding = true;
			nextSlide();
		});
		
		// bind previous button's onclick event
		previous.observe('click', function(event) {
			Event.stop(event);
			manualSliding = true;
			previousSlide();
		});
		
		
		// auto run slideshow
		/*new PeriodicalExecuter(function() {
			if (!manualSliding) previousSlide();
			manualSliding = false;
		}, slidePeriod);*/
		
		
	}
}


Event.observe(window, 'load', function() {
	decorateSlideshow_moreview();
});

document.observe("dom:loaded", function() {
	menu();
});

// }}}

})();