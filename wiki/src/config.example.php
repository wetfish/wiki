<?php

// Copy this file to "config.php" and change the following values

define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "username");
define("MYSQL_PASSWORD", "password");
define("MYSQL_DATABASE", "database");

// Password used to avoid captchas via the login page
define("LOGIN_PASSWORD", "password"); 

// Password for admins to do admin things via the login page
define("ADMIN_PASSWORD", "password");

// Password used to ban users
define("BAN_PASSWORD", "password");

// reCAPTCHA options
define("RECAPTCHA_PUBLIC", "public api key");
define("RECAPTCHA_PRIVATE", "private api key");

?>
