<?php

// Required functions for displaying wiki markup
require "../../functions.php";
require "../../src/markup/fishformat.php";

// Recaptcha library for logging in
require "../../recaptchalib.php";

// Traits for loading endpoints
require "endpoints/public.php";
require "endpoints/authorized.php";

class API
{
    use PublicEndpoints;
    use AuthorizedEndpoints;
    
    // The model must be passed when the API is constructed
    function __construct($model)
    {
        $this->model = $model;
        session_start();

        if(!isset($_SESSION['status']))
        {
            $_SESSION['status'] =
            [
                'authed' => false,
                'credits' => 0
            ];
        }
    }

    // Private function to set the session data for a user
    private function setStatus($status, $credits)
    {
        $status = (bool)$status;
        $credits = (int)$credits;
        
        if($status)
        {
            // Set legacy session values (required for editing posts)
            $_SESSION['bypass'] = true;
            $_SESSION['api'] = true;
        }
        
        $_SESSION['status']['authed'] = $status;
        $_SESSION['status']['credits'] = $credits;
    }

    // Private function to check if requests are authenticated
    private function isAuthenticated()
    {
        if($_SESSION['status']['authed'])
        {
            return true;
        }

        header('Content-Type: application/json');
        echo json_encode(array('status' => 'error', 'message' => 'You must be authenticated to perform this action.'));
        exit;
    }

    // Function to process requests
    public function request($url)
    {
        $url = parse_url($url);

        // Remove leading and trailing slashes, then explode the path into an array
        $path = explode('/', trim($url['path'], '/'));

        // Ignore the first two segments (/api/v1)
        $path = array_slice($path, 2);
        $method = array_shift($path);

        if(method_exists($this, $method))
        {
            return call_user_func(array($this, $method), $path);
        }
        else
        {
            $response = array
            (
                'status' => 'error',
                'code' => 400,
                'message' => 'Invalid method.'
            );

            header('Content-Type: application/json');
            return json_encode($response);
        }
    }
}

?>
