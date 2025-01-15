<?php

class Page extends Model
{
    // MySQL connection must be passed to the constructor
    function __construct($mysql)
    {
        $this->connection = $mysql;
    }

    // Get information about pages
    public function get($select, $from = "*")
    {
        // Select statement must be an array!
        if(!is_array($select))
            return;

        // Default glue should be 'and'
        if(!isset($select['__glue']))
            $select['__glue'] = 'and';
        
        return $this->query("Select $from from `Wiki_Pages` where ?", $select);
    }
}

?>
