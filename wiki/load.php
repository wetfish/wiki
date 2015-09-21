<?php

$URL = parse_url($_GET['url']);
require('simple_html_dom.php');
require('cssparser.php');

function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
}

if(preg_match('/^.*\.?wetfish.net$/i', $URL['host']))
{
    echo file_get_contents($_GET['url']);
    return false; 
    $HTML = str_get_html(file_get_contents($_GET['url']."&load=true"));
    $Unique = uuid('yay');
    $CSS = new CSS;
    $Data = array();
    
    foreach($HTML->find('style') as $Style)
    {
        $CSS->Import($Style->innertext);
        $Data = array_merge($Data, $CSS->Export('raw'));
        $Style->outertext = '';
    }
    
    foreach($HTML->find('link') as $Style)
    {
        $CSS->Import(file_get_contents($URL['scheme']."://".$URL['host']."/".$Style->href));
        $Data = array_merge($Data, $CSS->Export('raw'));
        $Style->outertext = '';
    }
    
    foreach($Data as $Tag => $Styles)
    {
        if($Styles['position'] == "relative")
        {
            $Data['#'.$_GET['id']] = array('height'=> $Styles['height'], 'width'=> $Styles['width']);
        }
    
        switch($Tag{0})
        {
            default:
                foreach($HTML->find($Tag) as $Derp)
                {
                    $Class = $Derp->class;
                        
                    if($Class)
                        $Derp->class = "$Class $Unique";
                    else
                        $Derp->class = "$Unique";
                }
                    
                $Data["$Tag.$Unique"] = $Data[$Tag];
                unset($Data[$Tag]);
            break;
                
            case '.':
                foreach($HTML->find($Tag) as $Derp)
                {
                    $TheTag = substr($Tag, 1);
                    $Class = $Derp->class;
                    $Derp->class = str_replace($TheTag, "{$TheTag}_{$Unique}", $Class);
                }
                
                $Data["{$Tag}_{$Unique}"] = $Data[$Tag];
                unset($Data[$Tag]);
            break;
                
            case '#':
                foreach($HTML->find($Tag) as $Derp)
                {
                    $TheTag = substr($Tag, 1);
                    $ID = $Derp->id;
                    $Derp->id = str_replace($TheTag, "{$TheTag}_{$Unique}", $ID);
                }

                $Data["{$Tag}_{$Unique}"] = $Data[$Tag];
                unset($Data[$Tag]);
            break;
        }
    }
                
    $CSS->Import($Data);
    $HTML->find('head', 0)->innertext .= "<style type='text/css'>".$CSS->Export()."</style>";

    foreach($HTML->find('img') as $Image)
    {
        $ImageURL = parse_url($Image->src);
        
        if(empty($ImageURL['scheme']))
            $Image->src = "{$URL['scheme']}://{$URL['host']}/{$ImageURL['path']}?{$ImageURL['query']}";
    }
    
    var_dump($HTML->find('body'));
    
#	var_dump($HTML);
#	var_dump($HTML->find('html'));
    
    if($HTML->find('html'))
    {
        echo $HTML->find('html', 0)->innertext;
    }else{ 
        echo $HTML->innertext;
    }
}

?>
