<?php

class Parser
{
    private $markup = array
    (
        // Text formatting
        'color',
        'bold', 'b',
        'italic', 'italcs', 'i',
        'underline', 'u',
        'strike', 's',
        'big',
        'medium', 'med',
        'small', 'sml',
        'rainbow', 'doublerainbow', 'dblrainbow', 'rainbow2',
        'glitch',

        // Section formatting
        'heading', 'subheading',
        'box',
        'infobox',
        'title',
        'titlebox',

        // Alignment
        'center',
        'right',
        'left',

        // Embedded content
        'image', 'img',
        'flash',
        'video', 'youtube', 'vimeo', 'vine', 'ted',
        'soundcloud',
        'load', 'embed',
        'music',
        'playlist',
        'Codepen',

        // Miscellaneous
        'ad', 'ads',
        'pre',
        'url',
        'redirect',
        'snow',
        'style',
        'total',
        'anchor',
        'fb', 'fishbux',
        'nsfw',
        'snip', 'hide',
        'date',
    );

    private $replacements = array();

    public function parse($input)
    {
        $start = "\[|{";    // Opening brackets
        $end = "\]|}";      // Closing brackets

        // Regular tags ( big[Some text here!] )
        $regular = "([\w, ]+) *(?:$start)(.*?)(?:$end)";

        // Delimited tags ( big x[Other text here!!]x )
        $delimited = "([\w, ]+) *([\w\.)(?:$start)(.*)(?:$end)\\4";

        return preg_replace_callback("/\b(?:$regular|$delimited)\b/is", array($this, 'replace'), $input);
    }

    private function replace()
    {
        print_r(func_get_args());
    }
}

?>
