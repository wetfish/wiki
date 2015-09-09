<?php

header('Content-type: text/css');
echo <<<CSS
body { margin:0 4% 64px 4%; background-image:url('missingno.png'); color: #FFFFFF; font-family: Tahoma,Helvetica,Sans-serif; font-size:10pt; overflow-x: hidden; z-index: 0; min-height: 100vh; }
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

a.broken { color:hsla(336, 97%, 55%, 1); }

span.title { font-size: 18pt; font-weight:bold; }
.strike { text-decoration:line-through; }
.big { font-size: 160%; font-weight: bold; }
.medium { font-size: 130%; font-weight: bold; }
.small { font-size: 80%; }
.warning { background:yellow; color:black; font-weight:bold; padding:0px 4px 0px 4px; }
.error { background:red; color:black; font-weight:bold; padding:0px 4px 0px 4px; }
.error a { color: yellow; }


.ui-draggable { z-index: 3; }
.super { background-image:url('super.gif'); opacity: 0; transition: opacity 0.8s }
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
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
}

.fishwrap {
    position: absolute; top: 0; bottom: 0; left: 0; right: 0; overflow: hidden;
    z-index: -1;
}

.swimming { transition: transform 0.5s ease; }

.wiki-box, .title-box {
    border:1px solid rgba(0,0,0,0.3);
    background-color: rgba(0,0,0,0.2);
    padding:8px;
}

.wiki-box-title, .title-box h1 {
    border:1px solid rgba(0,0,0,0.3);
    background-color: rgba(0,0,0,0.2);
    padding:2px 8px;
    font-size:14pt;
    border-radius:2px;
    font-weight:bold;
}

.title-box h1 {
    margin: 0;
    display: inline-block;
}

.title-box span {
    display: inline-block;
    padding: 0 24px;
    transform: translateY(-2px);
}

img {
    max-width:100%;
}

video {
    max-width: 100%;
    max-height: 100%;
}

pre {
    max-width: 100%;
}

.fishbux {
    color: yellow;
    position: relative;
    display: inline-block;
}

.fishbux .wrap {
    position: absolute;
    top: -10px;
    right: -50px;
}

.fishbux img {
    margin: 0px 8px;
    display: inline-block;
    max-width: 100%;
}

.nsfw {
    display: inline-block;
    position: relative;
}

.nsfw .message {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color:#000;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    padding-top: 22%;
    font-weight: bold;
}

.nsfw .message p {
    font-size: 60%;
    font-weight: normal;
}

.nsfw.show .message {
    display: none;
}

.snip {
    display: inline;
    margin-left: 1em;
}

.snip .message {
    cursor: pointer;
    font-size:75%;
}

.snip .stuff {
    display: none;
    margin-left: 1em;
}

.snip.show .stuff {
    display: inline;
}

.dragon {
    z-index: 1337;
    transition: transform 0.5s;
}

.dragging {
    transition: transform 0s;
}

CSS;

?>
