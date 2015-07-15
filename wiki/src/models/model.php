<?php

require_once "page.php";

class Model
{
    function __construct()
    {
        $this->page = new Model\Page();
    }
}

?>
