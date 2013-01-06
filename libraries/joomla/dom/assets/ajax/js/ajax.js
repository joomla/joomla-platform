/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <  Generated with Cook       (by Jocelyn HUARD) |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo----------------------------------------------------- +
* @version		1.0
* @package		Cook Self Service
* @subpackage	JDom
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
	$.fn.jdomAjax = function(options)
	{
		var thisp = this;

	//Merge the options
	    var opts = $.extend({}, $.fn.jdomAjax.defaults, options);
		if (!opts.data)
			opts.data = new Object();


	//Data Object init
		var data = opts.data;
		if (typeof(opts.token) != 'undefined')
			data.token = opts.token;


	//Use a dotted namespace to find the MVC context (Cook Self Service)
		if (typeof(opts.namespace) != 'undefined')
		{
			var urlParts = opts.namespace.split('.');

			data.option = 'com_' + urlParts[0];
			data.view = urlParts[1];
			data.layout = urlParts[2];
			data.render = (urlParts[3]?urlParts[3]:'');
		}

	//Merge the vars with data
		if (typeof(opts.vars) != 'undefined')
		{
			for (var key in opts.vars)
			{
				data[key] = opts.vars[key];
			}
		}

	//getHTML
		var getHTML = function(opts)
		{
			$.ajax({
				'type': opts.method,
				'url': opts.url,
				'data': opts.data,
				'success': function(data, textStatus, jqXHR){
					if (typeof(opts.successHTML) != 'undefined')
						opts.successHTML(thisp, data, textStatus, jqXHR);

				},
				'error' : function(jqXHR, textStatus, errorThrown){
					if (typeof(opts.error) != 'undefined')
						opts.error(thisp, textStatus, errorThrown);
				}

			});

		};

		var getJSON = function(opts)
		{
			$.getJSON(opts.url, opts.data, function(data) {

				if (typeof(opts.successJSON) != 'undefined')
					opts.successJSON(thisp, data);
			});
		};


	//END OF THE FUNCTION, RETURN SCRIPT

		return this.each(function()
		{
			//Execute for all instances

			//Loading scripts (spinner eventually)
			if (typeof(opts.loading) != 'undefined')
				opts.loading(thisp);

			//Choose the type of query (JSON, or HTML)
			if (opts.result == 'JSON')
				getJSON(opts);
			else
				getHTML(opts);
		});



	}


//CONFIGURATION

//Defines the defaults for your component
	$.fn.jdomAjax.defaults =
	{
		url:'index.php?tmpl=component',
		method:'POST',
		data:null,
		dom:null,
		token:parseInt(Math.random() * 9999999999),

		loading: function(object)
		{
			$('<div/>', {'class':'jdom-ajax-spinner'}).appendTo($(object));
		},

		successHTML: function(object, data, textStatus, jqXHR)
		{
			var thisp = this;

			//fill the object with the returned html
			$(object).html('').html(data);

			$(object).ready(function()
			{
				if (typeof(callback['_' + thisp.token]) == 'function')
				{
					(callback['_' + thisp.token])();
					callback['_' + thisp.token] = null;
				}
			});
		},


		successJSON: function(object, data)
		{

		},

		error: function(object, jqXHR, textStatus, errorThrown)
		{

		},
	};
})(jQuery);



var callback = {};
var registerCallback = function(token, fct)
{
	callback['_' + token] = fct;
}
