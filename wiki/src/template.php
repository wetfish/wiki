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
        <link href="/colorbox.css" rel="stylesheet" type="text/css" />
        <link href="/src/css/diff.css" rel="stylesheet" type="text/css" />
        <!--[if IE]>
                <link href="/styleie.css" rel="stylesheet" type="text/css" media="screen" />
        <![endif]-->

        <script type="text/javascript" src="/src/js/basic.js"></script>
        <script type="text/javascript" src="/src/js/wiki2.js"></script>
        <script type="text/javascript" src="/js/jquery.min.js"></script>
        <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/jquery.colorbox.js"></script>
        <script type="text/javascript" src="/jquery.json-2.2.min.js"></script>
        <!-- <script type="text/javascript" src="https://www.cornify.com/js/cornify.js"></script>  -->
        
        <script type="text/javascript" src="/jquery-fieldselection.js"></script>
        <script type="text/javascript" src="/js/window.js"></script>
        <script src='/src/js/wetki.js'></script>
        <!-- <script src='/src/js/jquery.transit.js'></script> -->
        <link href="/window.css" rel="stylesheet" type="text/css" />
        
        <script type="text/javascript" src="/js/wiki.js"></script>
        <script type="text/javascript" src="/js/swim.js"></script>
        
        <link rel="icon" type="image/png" href="/favzz.png"/>
    </head>
    <body>
        <div class="bodyborder">
            <div class="body">
                <div class="header">
                    <a class='header' href='https://wiki.wetfish.net/' onclick='cornify_add(); setTimeout("Jump()", 5000); return false;'><img src='/thisiswetfish.png' border='0'></a>
                </div>

                <div class="navigation">
                    <div class="navbox" onClick="Jump('/news')">
                        <a class="nav exempt" href="/news">News</a>
                    </div>

                    <div class='navbox' onClick="Jump('/popular')">
                        <a class='nav exempt' href='/popular'>Popular</a>
                    </div>
                    
                    <div class="navbox" onClick="Jump('/browse')">
                        <a class="nav exempt" href="/browse">Browse</a>
                    </div>
                    
                    <div class="navbox" onClick="Jump('/?random')">
                        <a class="nav exempt" href="/?random">Random</a>
                    </div>
                    
                    <div class="navbox" onClick="Jump('/search')">
                        <a class="nav exempt" href="/search">Search</a>
                    </div>
                    <div class='navbox' onClick="Jump('/tags')">
                        <a class='nav exempt' href='/tags'>Tags</a>
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
