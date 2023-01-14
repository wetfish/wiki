<?php

function Template($File)
{
    # Remember kids,
    # this function does not support array variables. (Yet? :)
    
    $Template = file_get_contents($File);
    $Template = preg_replace_callback('/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', # PHP's variable matching pattern (from the manual)
                                        create_function('$Matches', 'return $GLOBALS[$Matches[1]];'), # Grab variable from the global scope
                                        $Template);
    return $Template;
}

# wetfish.net, bringing you the best in default values
function Clean($Input, $Type="dicks")
{
    if($Type == "textarea")
        return str_replace(array("<", ">", "\"", "'", "\\", "`", "\r"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", ""), stripslashes($Input));
    else
        return trim(str_replace(array("<", ">", "\"", "'", "\\", "`", "\r", "\n"), array("&lt;", "&gt;", "&#34;", "&#39;", "&#92;", "&#96;", "", "$Break"), stripslashes($Input)));
}

?>
