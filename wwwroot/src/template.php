<?xml version="1.0" encoding="utf-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <title><?php echo $Title; ?></title>

        <?php echo $Head; ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="google-site-verification" content="lj_UCeIzlK8MDZyzJ-73XUUZHgroWS_1kQ6kkNar0Vg" />
        <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
        <link href="/style.php" rel="stylesheet" type="text/css" />
        <link href="/src/css/diff.css" rel="stylesheet" type="text/css" />
        <link href="/window.css" rel="stylesheet" type="text/css" />
        <!--[if IE]>
                <link href="/styleie.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->

        <script type="text/javascript" src="/src/js/vendor.js"></script>
        <script type="text/javascript" src="/src/js/basic.js"></script>
        <script type="text/javascript" src="/src/js/dragondrop.js"></script>
        <script type="text/javascript" src="/src/js/load.js"></script>
        <script type="text/javascript" src="/src/js/wiki.js"></script>
        <script type="text/javascript" src="/src/js/wiki2.js"></script>
        <script type="text/javascript" src="/src/js/window.js"></script>
        <script type="text/javascript" src="/src/js/swim.js"></script>
        <script type="text/javascript" src="/src/js/captcha.js"></script>
        
        <link rel="icon" type="image/png" href="/favzz.png"/>
        <script src="/src/node_modules/@ruffle-rs/ruffle/ruffle.js"></script>
    </head>
    <body>
        <div class="bodyborder">
            <div class="body">
                <div class="header">
		<a class='header' href="https://<?php echo $Site?>" onclick='cornify_add(); setTimeout("Jump()", 5000); return false;'><img src='/thisiswetfish.png' border='0'></a>
                </div>

                <div class="navigation">
                    <div class="navbox">
                        <a class="nav exempt" href="/news">News</a>
                    </div>

                    <div class='navbox'>
                        <a class='nav exempt' href='/popular'>Popular</a>
                    </div>
                    
                    <div class="navbox">
                        <a class="nav exempt" href="/browse">Browse</a>
                    </div>
                    
                    <div class="navbox">
                        <a class="nav exempt" href="/?random">Random</a>
                    </div>
                    
                    <div class="navbox">
                        <a class="nav exempt" href="/search">Search</a>
                    </div>
                    <div class='navbox'>
                        <a class='nav exempt' href='/tags'>Tags</a>
                    </div>
                    <div class='navbox'>
                        <a class='nav exempt' href='https://chat.wetfish.net' target="_blank">Contact</a>
                    </div>
                </div>

                <div class="subnav">
                    <hr />
                    <div class="usernav">
                        <?php echo $Content['UserNav']; ?>
                    </div>

                    <div class="pagenav">
                        <?php echo $Content['PageNav']; ?>
                    </div>
                    <hr />
                </div>

                <?php echo $Content['ExtraNav']; ?>

                <div class="title"><?php echo $Content['Title']; ?></div>
                
                <br />
                
                <div class="content"><?php echo $Content['Body']; ?></div>

                <div style='clear:both;'></div>
                
                <?php echo $Content['Tags']; ?>
                
                <hr />
                <center><iframe id='leader-friend' src='https://ads.wetfish.net/friendship/leader.html' style='width:750px; height:115px; border:0; outline:0; overflow:hidden;' scrolling="no"></iframe></center>
            </div>
        </div>

        <div class="footer">
            <div class="footerborder">
                <div class="footerbox">
                    <?php echo $Content['Footer']; ?>
                </div>
            </div>
        </div>		
        <div class="fishwrap"></div>
    </body>
</html>
