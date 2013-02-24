var jInsertEditorText = function(tag, field)
{

	var path = tag.replace(/^.+src=\"/, '');

	//Remove the media files root
	path = path.replace(/^images\/?/, ''); //TODO : change here the root if moved
	path = path.replace(/\".+$/, '');

	var fieldData = jInsertFields[field];
	var src = fieldData.url + path;

	if (fieldData.size)
		src += '&size=' + fieldData.size;

	if (fieldData.attrs)
		src += '&attrs=' + fieldData.attrs;

	var fieldObj;
	var previewObj;
	if (typeof(jQuery) == 'undefined')
	{
		//MooTools fallback
		previewObj = $("_" + field + "_preview");
		$(field).value = '[IMAGES]' + path;
	}
	else
	{
		previewObj = jQuery('#_' + field + "_preview")[0];
		jQuery('#' + field).val('[IMAGES]' + path);
	}

	if (fieldData.preview)
		previewObj.innerHTML = '<img src="' + src + '" alt=""/>'
	else
		previewObj.innerHTML = path;

};


var jInsertFields = {};