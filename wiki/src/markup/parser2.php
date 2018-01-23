<?php

function replace_once($search, $replacement, $string)
{
    // We have to use preg_replace instead of str_replace to ensure this match is only replaced once
    return preg_replace("/" . preg_quote($search, "/") . "/", $replacement, $string, 1);
}

class Parser
{
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

    public function filter($input)
    {
        $output = array();
        
        foreach($input as $object)
        {
            if(is_array($object['content']))
            {
                $filtered = filter_markup($object['content']);

                foreach($filtered as $markup)
                {
                    $text .= $markup['text'];
                }
            }
            else
            {
                $text = explode('|', $object['content']);
                $text = array_pop($text);
            }

            $output[] = array
            (
                'source' => $object['source'],
                'text' => $text
            );
        }

        return $output;
    }

    private function replace($match)
    {
        $replacementID = uuid();

        // Ensure it is unique and doesn't exist in the document
        while($this->tags[$replacementID] || strpos($this->text, $replacementID) !== false)
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
