$(function () //Wait until document finishes loading
{
	//Decode email addresses encoded in 'single' mode:
	if (CloakEmailOptions['mode'] == 'simple')
	{
		$('span.simple-cloak').html(function (index,old_html)
		{
			return MultiReplace([CloakEmailOptions['dot'],CloakEmailOptions['at']], ['.','@'], old_html);
		});
	}
	
	//Decode email addresses encoded in 'hard' mode
	else if (CloakEmailOptions['mode'] == 'hard')
	{
		$('span.hard-cloak').css('display','inline').html(function (index,old_html)
		{
			var result	= '';
			var chars	= old_html.split('-');
			for (var i in chars)
			{
				var char = chars[i];
				result += '&#'+char+';';
			}
			var insert_link = $(this).hasClass('insert-link');
			if (insert_link) result = '<a href="mailto:'+result+'">'+result+'</a>';
			return result;
		});
	}
});

function MultiReplace(array_find, array_replace, value)
{
	for (i in array_find)
	{
		find	= array_find[i];
		replace	= array_replace[i];
		value	= value.split(find).join(replace);
	}
	return value
}
