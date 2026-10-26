<?php

// Copy this file to "config.php" and change the following values

// Password used to avoid captchas via the login page
define("LOGIN_PASSWORD", getenv("LOGIN_PASSWORD")); 

// Password for admins to do admin things via the login page
define("ADMIN_PASSWORD", getenv("ADMIN_PASSWORD"));

// Password used to ban users
define("BAN_PASSWORD", getenv("BAN_PASSWORD"));

// reCAPTCHA options
define("RECAPTCHA_PUBLIC", getenv("RECAPTCHA_PUBLIC"));
define("RECAPTCHA_PRIVATE", getenv("RECAPTCHA_PRIVATE"));

// Secret regex to bypass captchas
define("CAPTCHA_BYPASS", getenv("CAPTCHA_BYPASS"));

// Leave this as is so the docker containers integrate correctly
define("MYSQL_HOST", getenv("DB_HOSTNAME"));
define("MYSQL_USER", getenv("DB_USERNAME"));
define("MYSQL_PASSWORD", getenv("DB_PASSWORD"));
define("MYSQL_DATABASE", getenv("DB_DATABASE"));

?>
