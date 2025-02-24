/*
 * jQuery.liveFilter
 *
 * Copyright (c) 2009 Mike Merritt
 *
 * Forked by Lim Chee Aun (cheeaun.com)
 * 
 */
 
(function($){
	$.fn.liveFilter = function(inputEl, filterEl, options){
		var defaults = {
			filterChildSelector: null,
			filter: function(el, val){
				return $(el).text().toUpperCase().indexOf(val.toUpperCase()) >= 0;
			},
			before: function(){},
			after: function(){}
		};
		var options = $.extend(defaults, options);
		
		var el = $(this).find(filterEl);
		if (options.filterChildSelector) el = el.find(options.filterChildSelector);

		var filter = options.filter;

		$(inputEl).on("change keyup", function(){
			options.before.call(this, contains, containsNot);

			$(filterEl).show();
			var filterGroupName = $(inputEl).data('searchers-group') || 'g_searchers';
			console.log('filterGroupName=', filterGroupName, $(inputEl).val());
			var searchers = window[filterGroupName];
			console.log('searchers=', searchers);
			if (searchers) {
				for (var inputNum in searchers) {
					var inputVal = searchers[inputNum];
					if (inputVal) {
						var filterChildSelector = '.live-filter-data-block-filterinfo-' + inputNum;

						var el = $(document).find(filterEl);
						el = el.find(filterChildSelector);

						var contains = el.filter(function () {
							return filter(this, inputVal);
						});
						var containsNot = el.not(contains);

						contains = contains.parents(filterEl);
						containsNot = containsNot.parents(filterEl).hide();
					}
				}
			}

			options.after.call(this, contains, containsNot);
		});
	}
})(jQuery);
