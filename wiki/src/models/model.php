<?php

require_once "page.php";

class Model
{
    // MySQL connection must be passed to the constructor
    function __construct($mysql)
    {
        $this->connection = $mysql;
        $this->page = new Page($mysql);
    }

    // Private function to do string replacements on queries
    private function replace($match)
    {
        $replacement = array_shift($this->replacements);

        // Arrays need to be turned into lists of values
        if(is_array($replacement))
        {
            $output = array();
            
            // If no glue is defined, assume comma
            if(!isset($replacement['__glue']))
            {
                $glue = ',';
            }
            else
            {
                // Don't trust any one or any thing
                $glue = $this->connection->escape_string($replacement['__glue']);
                unset($replacement['__glue']);
            }

            foreach($replacement as $key => $value)
            {
                if(is_string($key))
                {
                    $key = $this->connection->escape_string($key);
                    $value = $this->connection->escape_string($value);
                    
                    $output[] = "`{$key}` = '{$value}'";
                }
                else
                {
                    $value = $this->connection->escape_string($value);

                    if($match[0] == '??')
                    {
                        $output[] = "`{$value}`";
                    }

                    elseif($match[0] == '?')
                    {
                        $output[] = "'{$value}'";
                    }
                }
            }

            return implode($glue, $output);
        }

        // Everything else just gets escaped
        else
        {
            // Column / table names
            if($match[0] == '??')
            {
                return "`".$this->connection->escape_string($replacement)."`";
            }

            // Regular values
            elseif($match[0] == '?')
            {
                return "'".$this->connection->escape_string($replacement)."'";
            }
        }
    }

    // Function to run a query
    public function query($string, $data)
    {
        // Ensure replacements variable is always an array
        $this->replacements = array($data);
        $query = preg_replace_callback('/\?{1,2}/', array($this, 'replace'), $string);

        return $this->connection->query($query);
    }
}

?>
