function Window(Data){    $('body').prepend("<div class='border'><div class='window'>" + Data + "</div></div>");        var dragged = false;    var offset = {};    offset.top = 16;    offset.left = $('body').width() - $('.border').width() + 44;    $('.border').style({top:$(window).scroll().top + offset.top + 'px', left:$(window).scroll().left + offset.left + 'px'});    $('.border').dragondrop({ignore: 'a'});    $('.border').on('dragend', function(event)    {        offset = $('.border').position();                offset.top -= $(window).scroll().top;        offset.left -= $(window).scroll().left;        dragged = true;    });        $(window).on('scroll', function()    {        if(!dragged)        {            $('.border').transform('translate', $(window).scroll().left + offset.left + 'px',  $(window).scroll().top + offset.top + 'px');        }    });}