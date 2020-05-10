<?php

/*
 * Fish format!
 *
 * Version 0.2
 *
 */

require "parser2.php";
require "comments.php";
require "embed.php";
require "view.php";
require "edit.php";

function FishFormat($text, $action='markup')
{
    switch($action)
    {
        case "strip":
            $parser = new Parser();
            $parsed = $parser->parse($text);
            $markup = $parser->filter($parsed['tags']);

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
            $parser = new Parser();
            $parsed = $parser->parse($output);
            $output = edit_markup($parsed['text'], $parsed['tags']);

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

            // Links with custom text
            $output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)\|([\w -@\/~]+?)(?:\]\]|}})/', "custom_link", $output);

            // Basic links
            $output = preg_replace_callback('/(?:{{|\[\[)([\w -@\/~]+?)(?:\]\]|}})(s)?/', "basic_link", $output);

            // Replace colon delimited tags with HTML entities (for writing documentation)
            $output = str_replace(array(":{", "}:", ':[', ']:', ':|:'), array("&#123;", "&#125;", "&#91;", "&#93;", "&#124;"), $output);

            // Allow HTML comments
            $output = str_replace(array('&lt;!--', '--&gt;'), array('<!--', '-->'), $output);

            // Remove HTML comments so they aren't parsed for markup
            $output = remove_comments($output);

            // Strip newlines after images
            $output = preg_replace('/((?:img|image)\s*\[[^\]]*?(?:right|left)[^\]]*?\])\n/s', '\\1', $output);

            // Pasrse content for markup
            $parser = new Parser();
            $parsed = $parser->parse($output);

            $output = view_markup($parsed['text'], $parsed['tags']);

            // Put comments back in
            $output = replace_comments($output);

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
            $output = str_replace("    ", "&emsp;&emsp;&emsp;", $output);

            // Strip newlines around comments
            $output = preg_replace('{\n*(<!--|-->)\n*}', "\\1", $output);

            // Strip newlines between images.
            $output = preg_replace('{div>\n+<div}', "div><div", $output);

            // Strip newlines after titles and headings
            $output = preg_replace('{header>\n+}', "header>", $output);
            $output = preg_replace('{<hr /></span>\n+}', '<hr /></span>', $output);
            $output = str_replace("<hr />\n", "<hr />", $output);

            // Strip newlines in tables
            $output = str_replace("</table>\n", "</table>", $output);
            $output = str_replace("</tr>\n", "</tr>", $output);
            $output = str_replace("</td>\n", "</td>", $output);

            // Replace newlines with line breaks
            $output = str_replace("\n", "<br />", $output);
        break;
    }

    return $output;
}

?>
