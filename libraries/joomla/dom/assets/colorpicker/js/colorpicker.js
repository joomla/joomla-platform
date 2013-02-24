/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <  Generated with Cook       (by Jocelyn HUARD) |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo----------------------------------------------------- +
* @version		1.5
* @package		Joomla
* @subpackage	Cook
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
var JDomColorpickerCLASS =
{
	Extends: JDom,


	wrapper:null,		// Asset global wrapper
	domInput:null,		// Asset DOM input

	color:null,			// Current color value
	domSelected:null,	// DOM object displaying current color


	init: function()
	{
		this.domInput = $(this.params.name);


		if (this.domInput)
			this.color = $(this.domInput).value;


		if (this.params.selection)
			this.domSelected = $(this.params.selection);


	},

	getInstance: function()
	{

		switch(this.params.variant)
		{
			case 'rvb':
			default:
				var klass = 'JDomColorpickerRvb';

				break;
		}

		var jdom = new window[klass](this.wrapper, this.params);

		return jdom;
	},

	choose: function(color)
	{
		this.color = color;
		this.populate(color);

		this.refreshSelection();

	},

	populate: function(value)
	{
		this.domInput.value = value;
	},

	refreshSelection: function()
	{
		if (this.domSelected)
			this.domSelected.setStyle('background-color', this.color);
	}

};


if (requiredMooToolsVersion('1.2.0'))
	var JDomColorpicker = new Class(JDomColorpickerCLASS);
else
	var JDomColorpicker = JDom.extend(JDomColorpickerCLASS);



var JDomColorpickerRvbCLASS =
{
	Extends: JDomColorpicker,

	colorsWrapper:null,
	highlightColor:'#FF0000',
	overColor:'#FFFFFF',
	highlightBorder:2, //px

	pickW:10,
	pickH:10,



	render: function()
	{
		var thisp = this;


		var colorsWrapper = new Element('div').inject(this.wrapper);
		this.colorsWrapper = colorsWrapper;


		this.refreshSelection();
		this.highlight(this.color);



		var tabk = new Array('FF','CC','99','66','33','00');
		var tabj = tabk;
		var tabi = new Array('CC', '66', '00');
		//var tabi = new Array('CC', '88', '44', '00');
		var tabi2 = new Array('00','33','66','99','CC','FF');
		var color="";
		var cmp = 0;


		for(var k = 0 ; k < tabk.length ; k++)
		{


			var line = new Element('div').inject(colorsWrapper);
			line.setStyle('clear','left');
			for(var i = 0 ; i < tabi.length ; i++)
			{
				if (i == 1)
					tabj = tabi2;
				else
					tabj = Array('FF','CC','99','66','33','00');

				for(var j=0;j<6;j++)
				{
					color="#" + tabi[i] + tabk[k] + tabj[j];

					var pick = new Element('div', {'rel':color, 'class':'pick'}).inject(line);
					pick.setStyles({
									'float': 'left',
									'background-color': color,
									'width':this.pickW - (this.highlightBorder *2),
									'height':this.pickH - (this.highlightBorder *2),
									'border': this.highlightBorder + 'px solid',
									'border-color':color,
									'cursor':'pointer',
									'margin':0
									});


					if (this.color == color)
					{
						pick.setStyles({'border-color':this.highlightColor});
					}


					pick.addEvent('mouseover', function()
					{
						if (thisp.overColor != '')
							this.setStyle('border-color',thisp.overColor);
					});

					pick.addEvent('mouseleave', function()
					{
						var color = this.getProperty('rel');
						if (thisp.color != color)
							this.setStyle('border-color',color);
						else
							this.setStyle('border-color',thisp.highlightColor);
					});

					pick.addEvent('mousedown', function()
					{
						var color = this.getProperty('rel');
						this.setStyle('border-color',thisp.highlightColor);

						thisp.choose(color);

						thisp.clearSelection();

					});
				}
			}

			cmp = cmp + 1;
			if (cmp == 6)
			{
				var tabi = new Array('FF', '99', '33');
				var tabk = tabi2;
				k = -1;
			}

		}



		if (this.domInput)
			this.domInput.addEvent('change', function()
			{
				thisp.color = this.value
				thisp.highlight();
				thisp.refreshSelection();
			});

	},



	clearSelection: function()
	{
		var thisp = this;
		this.colorsWrapper.getElements('.pick').each(function(pick)
		{
			var color = pick.getProperty('rel');

			if (thisp.color != color)
				pick.setStyle('border-color',color);

		});
	},

	highlight: function(color)
	{
		if (color == undefined)
			color = this.color;


		var thisp = this;
		this.colorsWrapper.getElements('.pick').each(function(pick)
		{
			var pickColor = pick.getProperty('rel');

			if (pickColor == color)
				pick.setStyle('border-color',thisp.highlightColor);

		});

		this.clearSelection();
	}

};

if (requiredMooToolsVersion('1.2.0'))
	var JDomColorpickerRvb = new Class(JDomColorpickerRvbCLASS);
else
	var JDomColorpickerRvb = JDomColorpicker.extend(JDomColorpickerRvbCLASS);
