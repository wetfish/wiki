function Wiki(Type)
{
    var Output = new Object;

    switch(Type)
    {
        case "Bold":
            Output.start = " b [";
            Output.end = "] ";
        break;
        
        case "Italics":
            Output.start = " i [";
            Output.end = "] ";
        break;
        
        case "Underline":
            Output.start = " u [";
            Output.end = "] ";
        break;
        
        case "Strike":
            Output.start = " s [";
            Output.end = "] ";
        break;
        
        case "Big":
            Output.start = " big [";
            Output.end = "] ";
        break;
        
        case "Medium":
            Output.start = " med [";
            Output.end = "] ";
        break;
        
        case "Small":
            Output.start = " small [";
            Output.end = "] ";
        break;
        
        case "Rainbow":
            Output.start = " rainbow [";
            Output.end = "] ";
        break;
        
        case "Internal":
            Output.start = "[[";
            Output.end = "]]";
        break;
        
        case "External":
            Output.start = " url [";
            Output.end = "] ";
        break;
        
        case "Image":
            Output.start = " image [";
            Output.end = "] ";
        break;
        
        case "Video":
            Output.start = " video [";
            Output.end = "] ";
        break;
        
        case "Music":
            Output.start = " music [";
            Output.end = "] ";
        break;
    }

    var value = $('#Editbox').value();
    $('#Editbox').value(value + Output.start + Output.end);
}

function Jump(URL)
{
    if(URL == undefined)
        URL = 'https://wiki.wetfish.net/';
    
    window.location.href = URL; 
}

function SuperJump(URL)
{
    if(URL == undefined)
        URL = 'https://wiki.wetfish.net/';
    
    window.open(URL, '_blank'); 
}

function SelectAction(Type)
{
    var Form = document.getElementById('TheInternet');
    Form.action = Form.action + Type;
    Form.submit();
}

window.onload = function(){
    $('#TheInternet').on('submit', function() {
        var Form = document.getElementById('TheInternet');
        Form.action = Form.action + 'edit';
    });
}

var RecaptchaOptions = {
    theme : 'blackglass'
};

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

function buildQuery(input)
{
    var output = [];
    
    for(var key in input)
    {
        var value = input[key];
        output.push(key + '=' + value);
    }

    return output.join('&');
}

$(document).ready(function()
{
    $('.navbox, .paginate').on('mouseenter', function()
    {
        var id = Math.random().toString(36).slice(2).replace(/^[0-9]+/, '');
        $(this).data('id', id);
        
        var superDuper = document.createElement('div');
        $(superDuper).addClass('absolutely super');
        $(superDuper).attr('id', id);
        
        if($(this).hasClass('navbox'))
            $(superDuper).addClass('nav');

        var body = $('body').position();
        var position = $(this).position();
        var size = $(this).size();

        $(superDuper).style({'top': position.top - body.top + 'px', 'left': position.left - body.left + 'px'});
        $(superDuper).style({'height': size.height + 'px', 'width': size.width + 'px'});

        $(this).parent().append(superDuper);

        setTimeout(function()
        {
            $('#' + id).style({'opacity': 0.24});
        }, 10);
    });

    $('.navbox, .paginate').on('mouseleave', function()
    {
        var id = $(this).data('id');

        if(id)
        {
            var superDuper = $('#' + id).el[0];
            $(superDuper).style({'opacity': 0});
            $(superDuper).on('transitionend', function()
            {
                $(this).remove();
            });
        }
    });
    
    $('.glitchme').each(function()
    {
        var image = this;
        
        setInterval(function()
        {
            imageSrc = parseURL(decodeURIComponent(image.src));
            imageSrc.params.rand = Math.random();

            image.src = imageSrc.protocol + "://" + imageSrc.host + imageSrc.path + "?" + buildQuery(imageSrc.params);
        }, 3000);
    });
});
