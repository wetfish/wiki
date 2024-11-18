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

function SelectAction(Type)
{
    var Form = document.getElementById('TheInternet');
    Form.action = Form.action + Type;

    // Save current name in local storage
    var name = $('#Name').value();
    localStorage.setItem('name', name);
    
    Form.submit();
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

function hasChanges(changed, formData) {
    var Form = document.getElementById('TheInternet');
    return changed || (new FormData(Form) !== formData);
}

$(document).ready(function()
{
    // Populate saved name from local storage
    var name = localStorage.getItem('name');
    // Check if the title is "Preview:", which means changes are likely pending
    var title = document.querySelector('div.title');
    var changed = title && title.textContent.trim().startsWith('Preview:');
    // Stash the form so we can use it to compare for changes later
    var Form = document.getElementById('TheInternet');
    var formData = new FormData(Form);

    if(name)
    {
        $('#Name').value(name);
    }

    $('a').on('click', function(e) {
        if (hasChanges(changed, formData)) {
            if (!confirm('You have unsaved changes. Are you sure you want to leave?')) {
                e.preventDefault();
            } else {
                // Override function to make sure it doesn't fire anyways
                $(window).on('beforeunload', function (e) {});
                changed = false;
            }
        }
    });

    $(window).on('beforeunload', function(e) {
        if (hasChanges(changed, formData)) {
            var confirmationMessage = 'You have unsaved changes. Are you sure you want to leave?';
            (e || window.event).returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });


    $('#TheInternet').on('change input', function () {
        changed = true;
    });

    $('#TheInternet').on('submit', function() {
        // Update our stashed form data, and remove the `changed` guard
        var Form = document.getElementById('TheInternet');
        formData = new FormData(Form);
        changed = false;

        Form.action = Form.action + 'edit';
        // Save current name in local storage
        var name = $('#Name').value();
        localStorage.setItem('name', name);
        return true;
    });

    // Preview should justwerk
    $('input[value="Preview"]').on('click', function(e) {
        changed = false;
        return true;
    });

    $('.navbox').on('click', function(event)
    {
        var src = $(this).find('a').attr('href');
        var target = $(this).find('a').attr('target');

        if(target == '_blank')
        {
            window.open(src, '_blank'); 
        }
        else
        {
            window.location = src;
        }
    });

    $('.navbox a').on('click', function(event)
    {
        event.stopPropagation();
    });
    
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
