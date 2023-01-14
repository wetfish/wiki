<?php

// Copy this file to "config.php" and change the following values

define("MYSQL_USER", getenv("DB_USERNAME"));
define("MYSQL_PASSWORD", getenv("DB_PASSWORD"));
define("MYSQL_DATABASE", getenv("DB_DATABASE"));

// Password used to avoid captchas via the login page
define("LOGIN_PASSWORD", "password"); 

// Password for admins to do admin things via the login page
define("ADMIN_PASSWORD", "password");

// Password used to ban users
define("BAN_PASSWORD", "password");

// reCAPTCHA options
define("RECAPTCHA_PUBLIC", "public api key");
define("RECAPTCHA_PRIVATE", "private api key");

// Secret regex to bypass captchas
define("CAPTCHA_BYPASS", false);

// Leave this as is so the docker containers integrate correctly
define("MYSQL_HOST", "db");

?>
