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

    public function parse()
    {
        
    }
}

?>
