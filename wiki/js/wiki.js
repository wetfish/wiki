function Wiki(Type)
{
    var Output = new Object;

    switch(Type)
    {
        case "Bold":
            Output.start = " b {";
            Output.end = "} ";
        break;
        
        case "Italics":
            Output.start = " i {";
            Output.end = "} ";
        break;
        
        case "Underline":
            Output.start = " u {";
            Output.end = "} ";
        break;
        
        case "Strike":
            Output.start = " s {";
            Output.end = "} ";
        break;
        
        case "Big":
            Output.start = " big {";
            Output.end = "} ";
        break;
        
        case "Medium":
            Output.start = " med {";
            Output.end = "} ";
        break;
        
        case "Small":
            Output.start = " small {";
            Output.end = "} ";
        break;
        
        case "Rainbow":
            Output.start = " rainbow {";
            Output.end = "} ";
        break;
        
        case "Internal":
            Output.start = "{{";
            Output.end = "}}";
        break;
        
        case "External":
            Output.start = " url {";
            Output.end = "} ";
        break;
        
        case "Image":
            Output.start = " image {";
            Output.end = "} ";
        break;
        
        case "Video":
            Output.start = " video {";
            Output.end = "} ";
        break;
        
        case "Music":
            Output.start = " music {";
            Output.end = "} ";
        break;
    }
    
    var Text = $('#Editbox').getSelection().text;
    $('#Editbox').replaceSelection(Output.start + Text + Output.end);
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
    $('#TheInternet').submit(function() {
        var Form = document.getElementById('TheInternet');
        Form.action = Form.action + 'edit';
    });
}			

var RecaptchaOptions = {
    theme : 'blackglass'
};
