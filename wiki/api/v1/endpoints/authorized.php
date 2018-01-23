<?php

trait AuthorizedEndpoints
{
    public function status()
    {
        header('Content-Type: application/json');
        return json_encode(['status' => 'success', 'session' => $_SESSION['status']]);
    }

    public function login()
    {
        // Check if post data was submitted
        if(!empty($_POST))
        {
            if(defined('CAPTCHA_BYPASS') && CAPTCHA_BYPASS)
            {
                if(preg_match(CAPTCHA_BYPASS, $_POST["recaptcha_response_field"]))
                {
                    $this->setStatus(true, 10);
                }
            }
            
            if(!isset($_SESSION['bypass']))
            {
                $Resp = recaptcha_check_answer(RECAPTCHA_PRIVATE,
                                                $_SERVER["REMOTE_ADDR"],
                                                $_POST["recaptcha_challenge_field"],
                                                $_POST["recaptcha_response_field"]);

                if($Resp->is_valid)
                {
                    $this->setStatus(true, 10);
                }
            }
        }
        
        // Otherwise return a captcha
        $script = "<script>var RecaptchaOptions = {theme : 'blackglass'}</script>";
        $form = "<form method='post'>" . recaptcha_get_html(RECAPTCHA_PUBLIC, null, 1) . "</form>";
        return $script . $form;
    }

    public function logout()
    {
        session_destroy();
    }

    // Function to list every page on the wiki (requires auth)
    public function pages()
    {
        if($this->isAuthenticated())
        {
            $this->useCredits(4);

            $result = $this->model->page->get(['1' => 1], 'Path, Title');
            $pages = [];

            while($page = $result->fetch_object())
            {
                array_push($pages, array
                (
                    'path' => '/' . $page->Path,
                    'title' => $page->Title
                ));
            }

            header('Content-Type: application/json');
            return json_encode(['status' => 'success', 'session' => $_SESSION['status'], 'data' => $pages]);
        }
    }

    // Function to list every tag on the wiki (requires auth)
    public function tags()
    {
        if($this->isAuthenticated())
        {
            $this->useCredits(4);

            $result = $this->model->tags->stats(['1' => 1], 'tag, count, views');
            $tags = [];

            while($tag = $result->fetch_object())
            {
                array_push($tags, (array)$tag);
            }

            header('Content-Type: application/json');
            return json_encode(['status' => 'success', 'session' => $_SESSION['status'], 'data' => $tags]);
        }
    }
}
