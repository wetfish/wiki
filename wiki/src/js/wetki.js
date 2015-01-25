function parseURL(url) {
    var a =  document.createElement('a');
    a.href = url;
    return {
        source: url,
        protocol: a.protocol.replace(':',''),
        host: a.hostname,
        port: a.port,
        query: a.search,
        params: (function(){
            var ret = {},
                seg = a.search.replace(/^\?/,'').split('&'),
                len = seg.length, i = 0, s;
            for (;i<len;i++) {
                if (!seg[i]) { continue; }
                s = seg[i].split('=');
                ret[s[0]] = s[1];
            }
            return ret;
        })(),
        file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
        hash: a.hash.replace('#',''),
        path: a.pathname.replace(/^([^\/])/,'/$1'),
        relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
        segments: a.pathname.replace(/^\//,'').split('/')
    };
}

$(document).ready(function()
{	
	$('.navbox, .paginate').hover(function()
	{
		var superDuper = $("<div class='super ninja absolutely transparent'></div>");
		
		if($(this).is('.navbox'))
			superDuper.addClass('nav');
		
		superDuper.offset( $(this).offset() );
		superDuper.height( $(this).outerHeight() );
		superDuper.width( $(this).outerWidth() );
		
		$(this).parent().append(superDuper);
		superDuper.stop().fadeIn();
	},
	function()
	{
		$(this).parent().find('.super.ninja').stop().fadeOut(function() { $(this).remove(); });
	});	
	
	$('.glitchme').each(function()
	{
		var image = $(this);
		
		setInterval(function()
		{
			imageSrc = parseURL(decodeURIComponent(image.attr('src')));
			imageSrc.params.rand = Math.random();
			
			
			image.attr('src', imageSrc.protocol + "://" + imageSrc.host + imageSrc.path + "?" + $.param(imageSrc.params, true));
		}, 1234);
	});
	
	
	$('a.brew').each(function()
	{
		var newLink = '';
		var linkSparkle = $(this).text();
		linkSparkle.split('');

		
		$.each(linkSparkle, function(index, value)
		{
			newLink += "<span class='magic'>" + value + "</span>";
		});
		
		$(this).html(newLink);
	});
	
	/*
	
	function linkBlink(link, center)
	{
		var length = link.parent().find('.magic').length;
		
		for(sparkle = 0; sparkle < length; sparkle++)
		{
			if(sparkle > 0 && link.parent().find('.magic').eq(sparkle).css('text-decoration') == 'none')
				link.parent().find('.magic').eq(sparkle - 1).css('text-decoration', 'none');
				
			link.parent().find('.magic').eq(sparkle).css('text-decoration', 'underline');
		}
		
	}
	*/
	$('body').on('mouseenter', '.magic', function ()
	{
		$(this).css('text-decoration', 'overline');
		$(this).parent().css('text-decoration', 'none');
		

		alert('hi');
		
		var length = link.parent().find('.magic').length;	
		
		console.log($(this).index());
	});
	
	$('body').on('mouseleave', '.magic', function ()
	{

		$(this).parent().find('.magic').css('text-decoration', 'underline');
	});
	
});
