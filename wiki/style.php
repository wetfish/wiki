<?php

session_start();
if($_SESSION['Background'] == 'Frozen' or true)
	$Background = "url('missingno.png')";
else
	$Background = "url('missingno.php')";
	
header('Content-type: text/css');
echo <<<CSS
body { margin:0 4% 64px 4%; background-image:$Background; color: #FFFFFF; font-family: Tahoma,Helvetica,Sans-serif; font-size:10pt; overflow-x: hidden; z-index: 0; }
hr { height: 0px;  border: solid #98b3cd; border-width: 1px 0px 0px 0px; margin: 4px 0px; }

p { padding:0px; margin:0px }

.toggle { background:rgba(99, 72, 177, 0.5); }
.littleborder { border-bottom:1px solid rgba(99, 72, 177, 0.5); }
td { color: #FFFFFF; font-family: Tahoma,Helvetica,Sans-serif; font-size:10pt; padding: 0px 6px 0px 6px; }

a { color:#E87EE1; text-decoration: none; }
a:hover { text-decoration: underline; }

a.nav { color:#98b3cd; font-size:12pt; font-weight:bold; 
	text-shadow: 4px 4px 4px #0A002F;
 }
a.nav:hover { text-decoration:none; }

a.title { color:#FFFFFF; font-weight:bold; }
a.title:hover { text-decoration: underline; }

a.header { color:#FFFFFF; font-weight:bold; font-size:22pt; float:right; padding:4px 24px 0px 0px; }
a.header:hover { text-decoration:none; }

span.title { font-size: 18pt; font-weight:bold; }
.strike { text-decoration:line-through; }
.big { font-size: 160%; font-weight: bold; }
.medium { font-size: 130%; font-weight: bold; }
.small { font-size: 80%; }
.warning { background:yellow; color:black; font-weight:bold; padding:0px 4px 0px 4px; }
.error { background:red; color:black; font-weight:bold; padding:0px 4px 0px 4px; }

.ui-draggable { z-index: 3; }
.super { background-image:url('super.gif'); }
.duper { position:absolute; top:0; bottom:0; right:0; left:0; display:none; }
.nav { -webkit-border-top-left-radius: 8px;
-webkit-border-top-right-radius: 8px;
-moz-border-radius-topleft: 8px;
-moz-border-radius-topright: 8px;
border-top-left-radius: 8px;
border-top-right-radius: 8px;
}
.absolutely { position: absolute; }
.transparent { opacity:0.24; }
.ninja { display: none; }

div.bodyborder {
    background-image:url('body.png');
    border: 1px solid #000000;
    border-top:none;
    -moz-border-radius-bottomleft:16px;
    -webkit-border-bottom-left-radius:16px;
    border-bottom-left-radius: 16px;
    position: relative;
    z-index: 1;
}

div.body {
    overflow:hidden;
    background-image:url('supergay.png');
    background-repeat:no-repeat;
    padding: 0px 8px 8px 8px;
    border: 1px solid #98b3cd;
    border-top:none;
    -webkit-border-bottom-left-radius: 16px;
    -moz-border-radius-bottomleft: 16px;
    border-bottom-left-radius: 16px;
}

div.content { overflow:hidden;}
div.header { height:80px; padding-top:8px; }
div.navigation { }
div.navbox { position:relative; z-index:1;padding:8px 16px 4px 16px; margin:1px; float:left; text-align:center; }
div.navbox:hover { background-image:url('mostlytransparent.png'); margin:0px; border-top: 1px solid #98b3cd; border-right: 1px solid #98b3cd; border-left: 1px solid #98b3cd;
-webkit-border-top-left-radius: 8px;
-webkit-border-top-right-radius: 8px;
-moz-border-radius-topleft: 8px;
-moz-border-radius-topright: 8px;
border-top-left-radius: 8px;
border-top-right-radius: 8px;
 }
 
 .navbox a { z-index:1; }
 
div.subnav { clear:both; }
div.usernav { float:right; padding-right:23px; }
div.pagenav { padding-left:23px; }
div.extranav { float:right; font-size:8pt; }
div.title {
	padding-left:23px; font-size: 42pt; font-weight:bold;
	font-family: 'Lobster', serif;
	text-shadow: 4px 4px 4px #0A002F;
}

div.footer { position:relative; }
div.footerborder {
    position:absolute;
    top:0px;
    right:0px;
    border: 1px solid #000000;
    border-top-style:none;
    -moz-border-radius-bottomright:16px;
    -moz-border-radius-bottomleft:16px;
    -webkit-border-bottom-left-radius:16px;
    -webkit-border-bottom-right-radius:16px;
    border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
}

div.footerbox {
    background-image:url('body.png');
    padding:8px;
    text-align:center;
    font-size: 8pt;
    font-style: italic;
    border: 1px solid #98b3cd;
    border-top-style:none;
    -moz-border-radius-bottomright:16px;
    -moz-border-radius-bottomleft:16px;
    -webkit-border-bottom-left-radius:16px;
    -webkit-border-bottom-right-radius:16px;
    border-bottom-right-radius: 16px;
    border-bottom-left-radius: 16px;
}

div.GalleryContainer { display:inline-block; height:170px; width:170px; margin:10px; }
img.GalleryImage {
    display:block;
    margin:0 auto;
    border:1px solid #000000;
    -moz-border-radius:8px;
    -webkit-border-radius:8px;
    border-radius: 8px;
    padding:10px;
}

img.GalleryImage:hover { background-image:url('hover.png'); }

span.del{background:#B51010}
span.ins{background:#6348b1}


#jetpack
{	position:absolute;
	display:none;
	top:128px;
	left:128px;
	border:1px solid #FFF;
	font-family:Tahoma, Helvetica, Arial;
	font-size:10pt;
	color: #FFF;
	padding:8px;
	background-image:url('bg.png');
	z-index:999;
	-moz-border-radius:8px;
	-webkit-border-radius:8px;
    border-radius: 8px;
}

#jetpack #title
{	font-weight:bold; }

#input { border:1px #fff dashed; outline:0; margin:4px; padding:4px; }
#form { padding-top:4px; margin:0; }

iframe[src="about:blank"]{display:none;}


.history td {
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.multi-line {
	overflow: auto !important;
	white-space: normal !important;
}

.date:hover {
	text-decoration: underline;
	cursor:help;
}


/*help! */
.clear { clear:both; }
.right { float:right; }
.left { float:left; }
.paginate {  margin:4px; display:inline-block; position: relative; z-index:1; }
.paginate a { padding:4px 8px; display:inline-block; }
.paginate:hover { margin:3px; background:rgba(99, 72, 177, 0.3); border:1px solid rgba(255, 255, 255, 0.54); }

.page-navigation { margin:8px 0px; border-top:1px solid rgba(0, 0, 0, 0.2); border-bottom:1px solid rgba(0, 0, 0, 0.2) }
.page-navigation.bottom { border-bottom: 0; margin-bottom: 0; }

#kristyfish {
	position: absolute;
    -webkit-transform-style: preserve-3d;
    backface-visibility: visible;
}

.fishwrap { position: absolute; top: 0; bottom: 0; left: 0; right: 0; overflow: hidden; }
.swimming { transition: all 0.5s; }

.wiki-box {
	border:1px solid rgba(0,0,0,0.3);
	background-color: rgba(0,0,0,0.2);
	padding:8px;
}

.wiki-box-title {
	border:1px solid rgba(0,0,0,0.3);
	background-color: rgba(0,0,0,0.2);
	padding:2px 8px;
	font-size:14pt;
	border-radius:2px;
	font-weight:bold;
}

img {
	max-width:100%;
}

CSS;

?>
