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

    private $text;
    private $tags = array();

    public function parse($input)
    {
        $this->text = $input;
        $start = "\[|{";    // Opening brackets
        $end = "\]|}";      // Closing brackets

        // Regex to match tags
        // Match word characters, or (word characters followed by a comma and an optional space, followed by more word characters with an optional comma) repeating
        $tags = "(?:\w+|(?:\w+(?:, *\w+,?)*))";

        // Regular tags ( big[Some text here!] )
        $regular = "($tags) *(?:$start)([^\[{]*?)(?:$end)";
        
        // Delimited tags ( big x[Other text here!!]x )
        $delimited = "($tags) *([\w\.])(?:$start)(.*)(?:$end)\\2";

        while(preg_match("/$regular/s", $this->text) || preg_match("/$delimited/s", $this->text))
        {
            $this->text = preg_replace_callback("/$regular/s", array($this, 'replace'), $this->text);
            $this->text = preg_replace_callback("/$delimited/s", array($this, 'replace'), $this->text);
        }

        return array('text' => $this->text, 'tags' => $this->tags);
    }

    private function replace($match)
    {
        $replacementID = uuid();

        // Ensure it is unique and doesn't exist in the document
        while($this->markup[$replacementID] || strpos($this->text, $replacementID) !== false)
        {
            $replacementID = uuid();
        }

        $data = array
        (
            'source' => $match[0],
            'tag' => $match[1],
            'content' => ($match[3]) ? $match[3] : $match[2]
        );

        $this->tags[$replacementID] = $data;
        return $replacementID;
    }
}

?>
