/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <  Generated with Cook       (by Jocelyn HUARD) |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo----------------------------------------------------- +
* @version		1.5
* @package		JDom
* @subpackage	Searchbox
* @copyright	Copyright 2011 - 100% vitamin
* @author		100% Vitamin - www.cpcv.net - info@cpcv.net
*
* /!\  Joomla! is free software.
* This version may have been modified pursuant to the GNU General Public License,
* and as distributed it includes or is derivative of works licensed under the
* GNU General Public License or other free or open source software licenses.
*
*             .oooO  Oooo.     See COPYRIGHT.php for copyright notices and details.
*             (   )  (   )
* -------------\ (----) /----------------------------------------------------------- +
*               \_)  (_/
*/

(function($) {
	$.fn.jdomSearchBox = function(options)
	{
		var domInput = $(this);
		var id = domInput.attr('id');

		var domSearch = $('#search_input_' + id);
		var domEmpty = $('#search_empty_' + id);;
		var domLabel = $('#search_label_' + id);;

	//Merge the options
	    var opts = $.extend({}, $.fn.jdomSearchBox.defaults, options);

		var fctChange = function()
		{
			if (domEmpty.length && domLabel.length)
			{
				if ((domSearch.val().trim() != domLabel.val().trim())
				&& (domSearch.val().trim() != ""))
					domEmpty.val(0);

				if ((domEmpty.val() == 1))
					domInput.val('');
				else
					domInput.val(domSearch.val());
			}
			else
				domInput.val(domSearch.val());

		};

		var fctRefreshLabel = function()
		{
			if (domEmpty.val() == 1)
			{
				domSearch.val("");
				domSearch.removeClass('search_default');
			}
		};


		var emptySearchText = function()
		{
			if (!domEmpty)
				return;

			var thisp = this;

			domSearch.mousedown(function()
			{
				fctRefreshLabel();
			});

			domSearch.blur(function()
			{
				if (domInput.val().trim() == "")
				{
					domSearch.val(domLabel.val());
					domEmpty.val(1);
					domSearch.addClass('search_default');
				}
				else
				{
					domEmpty.val(0);
				}
			});
		};


		domSearch.keypress(function()
		{
			fctChange();
		});

		domSearch.change(function()
		{
			fctChange();
		});


		emptySearchText();
	    return this;
	};


// Config : default options (not used yet)
	$.fn.jdomSearchBox.defaults = {};
})(jQuery);