<?php

/*
 * Fish format!
 * 
 * Version 0.1
 * 
 */

require "parser.php";
require "parser2.php";
require "comments.php";
require "embed.php";
require "view.php";
require "edit.php";

if(!class_exists(Benchmark))
{
    include_once(__DIR__ . "/../benchmark.php");
    $benchmark = new Benchmark;
}

function FishFormat($text, $action='markup')
{
    global $benchmark;

    switch($action)
    {
        case "strip":
            $parsed = parse_markup($text);
            $markup = filter_markup($parsed['markup']);

            foreach($markup as $filtered)
            {
                $text = replace_once($filtered['source'], $filtered['text'], $text);
            }

            // Remove newlines
            $text = str_replace("\n", "", $text);

            // Filter links
            $text = preg_replace("/(?:\[\[|{{)([^\]\|}]+)(?:\]\]|}})/", "\\1", $text);
            $text = preg_replace("/(?:\[\[|{{)[^\]}]+\|([^\]}]+)(?:\]\]|}})/", "\\1", $text);

            $output = $text;
        break;

        case "edit":
            $output = str_replace("\t", "    ", $text);

            while(preg_match('/^ *:+/m', $output)) {
                $output = preg_replace('/^( *):/m','\1    ', $output); }

            // Filter links
            $output = str_replace(array('[[', ']]', '{{', '}}'), array('&91;&91;', '&93;&93;', '&123;&123;', '&125;&125;'), $output);

            // Allow HTML comments
            $output = str_replace(array('&lt;!--', '--&gt;'), array('<!--', '-->'), $output);

            // Remove HTML comments so they aren't parsed for markup
            $output = remove_comments($output);

            // Rewrite specific tags (images, soundcloud, date)
            $parsed = parse_markup($output);
            $output = edit_markup($parsed['input'], $parsed['markup']);

            // Put comments back in
            $output = replace_comments($output);

            // Re-filter HTML comments
            $output = str_replace(array('<!--', '-->'), array('&lt;!--', '--&gt;'), $output);
            
            // Un-filter links
            $output = str_replace(array('&91;&91;', '&93;&93;', '&123;&123;', '&125;&125;'), array('[[', ']]', '{{', '}}'), $output);
        break;
        
        case "format":
            $output = preg_replace('/(\w{14})/', "$1&#8203;", $text);
        break;

        default:            
            $output = $text;
            $output = str_replace("    ", "&emsp;&emsp;&emsp;", $output);
            $benchmark->log('Spaces Replaced');


            // Links with custom text
            $output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)\|([\w -@\/~]+?)(?:\]\]|}})/', "custom_link", $output);
            $benchmark->log('Custom Links Replaced');

            // Basic links
            $output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)(?:\]\]|}})(s)?/', "basic_link", $output);
            $benchmark->log('Basic Links Replaced');

            // Replace semicolon tags with HTML entities (for writing documentation)
            $output = str_replace(array(":{", "}:", ':[', ']:'), array("&#123;", "&#125;", "&#91;", "&#93;"), $output);
            $benchmark->log('Braces Replaced');

            // Allow HTML comments
            $output = str_replace(array('&lt;!--', '--&gt;'), array('<!--', '-->'), $output);
            $benchmark->log('Comments Replaced');

            // Remove HTML comments so they aren't parsed for markup
            $output = remove_comments($output);
            $benchmark->log('Comments Removed');

            // Pasrse content for markup
            if($_SESSION['admin'])
            {
                $parser = new Parser();
                $parsed = $parser->parse($output);

                print_r($parsed);
            }
            else
            {
                $parsed = parse_markup($output);
            }
            $benchmark->log('Markup Parsed');
            
            $output = view_markup($parsed['input'], $parsed['markup']);
            $benchmark->log('Markup Formatted');

            // Put comments back in
            $output = replace_comments($output);
            $benchmark->log('Comments Returned');

            // Random replacements / emoticon type things
            $Search[':Z'] = "<span class='warning'>:Z</span>";
            $Search[':downy:'] = "<span class='warning medium' style='font-family:helvetica'>.'<u>/</u>)</span>";
            $Search[':dunno'] = "<span class='warning'>¯\(º_o)/¯</span>";
            
            foreach($Search as $Key => $Value)
            {
                $output = str_replace($Key, $Value, $output);
            }
            
            $output = str_replace("&lt;3", "<span class='error'>&lt;3</span>", $output);

            // Ordered and unordered lists
            $output = preg_replace_callback("/(?:(?:^|\n)\s*(\*|\-|\#)\s+[^\n]+(?:\n|$))+/m", 'replace_lists', $output);
            $benchmark->log('Lists Replaced');

            // Strip newlines around comments
            $output = preg_replace('{\n*(<!--|-->)\n*}', "\\1", $output);            
            
            // Strip newlines between images.
            $output = preg_replace('{div>\n+<div}', "div><div", $output);
            
            // Strip newlines after titles and headings
            $output = preg_replace('{header>\n+}', "header>", $output);
            $output = preg_replace('{<hr /></span>\n+}', '<hr /></span>', $output);
            $output = str_replace("<hr />\n", "<hr />", $output);

            // Replace newlines with line breaks
            $output = str_replace("\n", "<br />", $output);
        break;
    }
    
    return $output;
}

?>
