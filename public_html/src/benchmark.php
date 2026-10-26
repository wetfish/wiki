<?php

// A little helper class to benchmark PHP execution in microseconds

class Benchmark
{
    private $name;
    private $start;
    private $log;

    // Start a new log
    public function start($name)
    {
        $this->name = $name;
        $this->start = microtime(true);
        $this->log = array();
    }

    // Add an event to the current log
    public function log($message)
    {
        $event = array
        (
            'message' => $message,
            'difference' => microtime(true) - $this->start
        );
        
        array_push($this->log, $event);
    }

    // Save the current log as session data
    public function save()
    {
        if(!is_array($_SESSION['benchmark']))
        {
            $_SESSION['benchmark'] = array();
        }

        $details = array
        (
            'name' => $this->name,
            'start' => $this->start,
            'end' => microtime(true),
            'log' => $this->log,
        );

        $details['difference'] = $details['end'] - $details['start'];
        
        array_push($_SESSION['benchmark'], $details);
    }

    // Display information about the most recent log, or a specific one
    public function display($index = false)
    {
        if($index)
        {
            $details = $_SESSION['benchmark'][$index];
        }
        else
        {
            $details = end($_SESSION['benchmark']);
        }

        echo "<div style='background-color: #000;'>";
        echo "<h1>Benchmark for {$details['name']}</h1>";
        echo "<b>Total time: {$details['difference']}</b>";
        echo "<hr>";
        echo "<pre>";
        print_r($details['log']);
        echo "</pre>";
        echo "</div>";
    }

    // Clear all saved benchmarks
    public function clear()
    {
        $_SESSION['benchmark'] = array();
    }
}

?>
