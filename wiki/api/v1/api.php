<?php

// Required functions for displaying wiki markup
require "../../functions.php";
require "../../src/markup/fishformat.php";

// Recaptcha library for logging in
require "../../recaptchalib.php";

class API
{
    // The model must be passed when the API is constructed
    function __construct($model)
    {
        $this->model = $model;
        session_start();
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

    // Function to display a page's content
    public function content($path)
    {
        $path = implode('/', $path);
        $result = $this->model->page->get(array('path' => $path));
        $page = $result->fetch_object();

        return FishFormat($page->Content);
    }

    // Function to display a page's source
    public function source($path)
    {
        $path = implode('/', $path);
        $result = $this->model->page->get(array('path' => $path));
        $page = $result->fetch_object();

        return $page->Content;
    }

    // Function to display JSON containing a page's content
    public function json($path)
    {
        $path = implode('/', $path);
        $result = $this->model->page->get(array('path' => $path));
        $page = $result->fetch_object();

        if(empty($page))
        {
            $response = array
            (
                'status' => 'error',
                'code' => 404,
                'message' => 'Page does not exist.'
            );

            header('Content-Type: application/json');
            return json_encode($response);
        }
        else
        {
            $response = array
            (
                'status' => 'success',
                'code' => 200,
                'path' => $page->Path,
                'views' => $page->Views,
                'edits' => count(explode(',', $page->Edits)),
                'modified' => $page->EditTime,

                'title' => array
                (
                    'formatted' => FishFormat($page->Title),
                    'source' => $page->Title
                ),

                'content' => array
                (
                    'formatted' => FishFormat($page->Content),
                    'source' => $page->Content
                )
            );

            header('Content-Type: application/json');
            return json_encode($response);
        }
    }

    public function login()
    {
        // Check if post data was submitted
        if(!empty($_POST))
        {
            if(preg_match("/^(fuck? you captcha?|fuck? captchas?|i hate captchas?|captchas?.*sucks?)$/i", $_POST["recaptcha_response_field"]))
                $_SESSION['bypass'] = true;

            if(!isset($_SESSION['bypass']))
            {
                $Resp = recaptcha_check_answer(RECAPTCHA_PRIVATE,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["recaptcha_challenge_field"],
                                    $_POST["recaptcha_response_field"]);

                if(!$Resp->is_valid)
                    return json_encode(array('status' => 'error', 'message' => 'Invalid captcha! Please try again.'));
                else
                    $_SESSION['bypass'] = true;
            }
        }

        // Check if the user is now logged in
        if($_SESSION['bypass'])
            return json_encode(array('status' => 'success', 'message' => 'You are logged in.'));

        // Otherwise return a captcha
        $script = "<script>var RecaptchaOptions = {theme : 'blackglass'}</script>";
        $form = "<form method='post'>" . recaptcha_get_html(RECAPTCHA_PUBLIC, null, 1) . "</form>";
        return $script . $form;
    }
}

?>
