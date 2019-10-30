<?php
    /* Header file for the PA website
        Require this file and call:
            printHeader($title[, $scripts[, $css]]);
                Taking the page title, and optionally the additional scripts and css in the page

        Prints the meta information and the navbar
        also opens the main content tag (closed in footer.php)
    */

namespace PA\Snippets;

use \PA\Auth\Auth;


class Header implements SnippetInterface {

    public static function printSnippet($options = null){
        $title = isset($options['title']) ? $options['title'] : null;
        $scripts = isset($options['scripts']) ? $options['scripts'] : null;
        $css = isset($options['css']) ? $options['css'] : null;

        self::printHeader($title, $scripts, $css);
    }

    public static function printHeader($title = "SUSU Performing Arts", $scripts = null, $css = null, $jqueryVersion = null){
        self::printHead($title, $scripts, $css, $jqueryVersion);
        self::printNav();
    }









    /** Functions for printing each of the header sections **/
    /** Meta information **/
    public static function printHead($title = "SUSU Performing Arts", $scripts = null, $css = null, $jqueryVersion = null){
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='utf-8'>
            <title>$title</title>";
        	
			self::print_compatCode();
			
            self::print_icons();

            self::print_descriptors();

            self::print_openGraph($title);

            self::print_mainCSS();

            self::print_customCSS($css);
			
            self::print_mainJS($jqueryVersion);

            self::print_customJS($scripts);
        ?>


        </head>
        <body>

        <?php
    }



    public static function print_compatCode(){
    ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    }

    public static function print_icons(){
    ?>
        <link rel="manifest" href="/manifest.json">

        <meta name="application-name" content="SUSU Performing Arts">
        <meta name="apple-mobile-web-app-title" content="SUSU Performing Arts">

        <meta name="theme-color" content="#222222">
        <meta name="msapplication-navbutton-color" content="#222222">
        <meta name="apple-mobile-web-app-status-bar-style" content="#222222">

        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="apple-touch-startup-image" href="/img/webapp/startup.png">

        <link rel="apple-touch-icon" href="/img/webapp/icon.png">

        <link rel="apple-touch-icon-precomposed" href="/img/webapp/icon.png">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="/img/webapp/icon.png">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="/img/webapp/icon.png">
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="/img/webapp/icon.png">

    <?php
    }

    public static function print_descriptors(){
    ?>
        <meta name="description" content="The home of the University of Southampton Students' Union's Performing Arts Societies. Whether it's dance, theatre or music as long as its performing you'll find it here.">
        <meta name="author" content="SUSU Performing Arts">

    <?php
    }

    public static function print_openGraph($title = "SUSU Performing Arts"){
    ?>
        <meta property="og:title" content="<?php echo $title; ?>">
        <meta property="og:url" content="https://perform.susu.org<?php echo $_SERVER['REQUEST_URI']; ?>">
        <meta property="og:image" content="https://perform.susu.org/img/opengraph.png">
        <meta property="og:type" content="website">

    <?php
    }

    public static function print_mainCSS(){
    ?>
        <link href="/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="/libs/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
        <link href="/css/main.css" rel="stylesheet" type="text/css">
        <link href="/libs/d.css" rel="stylesheet" type="text/css">

    <?php
    }

    public static function print_mainJS($jqueryV = null){
		if (!isset($jqueryV))
			echo "<script src='/libs/jquery-1.11.3.min.js'></script>";
		else
			echo "<script src='/libs/jquery-$jqueryV.min.js'></script>";
    }

    public static function print_customCSS($css = null){
        if(isset($css)){
            foreach ($css as $sheet){
                echo "  <link href=\"$sheet\" rel=\"stylesheet\" type=\"text/css\">\n";
            }
        }
    }

    public static function print_customJS($scripts = null){
        if(isset($scripts)){
            foreach ($scripts as $script){
                echo "  <script src=\"$script\"></script>\n";
            }
        }
    }




    /** Print the visual naviagtion bar ***/
    public static function printNav(){
    ?>
    <div class="pa-wrapper">

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <span class="navbar-brand"><a class="susu" href="http://www.susu.org"><img src="/img/logos/susu.png" alt="SUSU Logo"></a> <a class="navbar-brand brand-pa" href="/">Performing Arts</a></span>
                </div>

                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class=""><a href="/">Home</a></li>

                        <li class="dropdown">
                            <a href="/about" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">About Us <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/about">About PA</a></li>
                                <li><a href="/about#pacard">The PA Card</a></li>
                                <li><a href="/about#committee">Your Committee</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="/societies" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Societies <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/societies#theatrical">Theatrical</a></li>
                                <li><a href="/societies#music">Music</a></li>
                                <li><a href="/societies#dance">Dance</a></li>
                                <li><a href="/societies#tech">Tech</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="/calendar" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">What's on <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/calendar">Shows Calendar</a></li>
                                <li><a href="/schedule">Rehearsals and Meetings Schedule</a></li>
                                <li><a href="/archive/shows">PA Archive</a></li>
                                <li><a href="/boxoffice">Box Office</a></li>
                            </ul>
                        </li>

                        <!--<li><a href="/archive">Archive</a></li>-->

                        <li><a href="/resources">Resources</a></li>

                        <li><a href="/blogs">Blog</a></li>
                        

                        <!--<li><a href="http://www.annextheatre.co.uk/">The Annex</a></li>-->

                        <?php self::printUserNavMobile(); ?>

                    </ul>

                    <?php self::printUserNavNormal(); ?>

                </div>


            </div>
        </nav>


        <main class="container pa-main-content">

    <?php
    }


    /** Utils for the nav in normal and mobile mode **/
    public static function printUserNavNormal(){
        $archiveBase = "/archive";
        $uri = $_SERVER["REQUEST_URI"];
        ?>
            <ul class="nav navbar-nav navbar-right hidden-xs">
                <?php if(Auth::isLoggedIn()){ ?>
                <li class="dropdown pull-right">
                    <a href="<?php echo $archiveBase; ?>/me" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo Auth::getUserInfo("preferredName"); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $archiveBase; ?>/me">Profile</a></li>
                        <li><a href="<?php echo $archiveBase; ?>/members/edit">Edit Profile</a></li>
                        <?php if(Auth::canEditShows() || Auth::canEditSocieties() || Auth::isOnPACommittee()){ ?>
                            <li><a href="<?php echo $archiveBase; ?>/admin">Admin Page</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $archiveBase; ?>/logout<?php if(isset($uri)){echo '?continue=' . $uri;} ?>">Logout</a></li>
                    </ul>
                </li>
                <?php }else{ ?>
                    <li><a href="<?php echo $archiveBase; ?>/login<?php if(isset($uri)){echo '?continue=' . $uri;} ?>">Login</a></li>
                <?php } ?>

            </ul>
        <?php
    }

    public static function printUserNavMobile(){
        $archiveBase = "/archive";
        $uri = $_SERVER["REQUEST_URI"];
        if(Auth::isLoggedIn()){
            ?>
                <li class="dropdown visible-xs-block">
                    <a href="<?php echo $archiveBase; ?>/me" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo Auth::getUserInfo("preferredName"); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $archiveBase; ?>/me">Profile</a></li>
                        <li><a href="<?php echo $archiveBase; ?>/members/edit">Edit Profile</a></li>
                        <?php if(Auth::canEditShows() || Auth::canEditSocieties() || Auth::isOnPACommittee()){ ?>
                            <li><a href="<?php echo $archiveBase; ?>/admin">Admin Page</a></li>
                        <?php } ?>
                        <li><a href="<?php echo $archiveBase; ?>/logout<?php if(isset($uri)){echo '?continue=' . $uri;} ?>">Logout</a></li>
                    </ul>
                </li>
            <?php }else{ ?>
                <li><a class="visible-xs-block" href="<?php echo $archiveBase; ?>/login<?php if(isset($uri)){echo '?continue=' . $uri;} ?>">Login</a></li>
            <?php 
        }
    }







    /** Archive nav printing **/
    public static function printArchiveNav(){
        $archiveBase = "/archive";
    ?>
    <div class="pa-wrapper">

        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!--<span class="pull-left"><a class="navbar-brand pa-archive-brand" href="/">Performing Arts</a> <a class="navbar-brand pa-archive-brand" href="<?php echo $archiveBase; ?>">Archive</a></span>-->
                    <span class="navbar-brand"><a class="susu" href="/"><img style="max-width: 50px; vertical-align: text-bottom;" src="/img/logos/pa_gray.png" alt="SUSU Logo"></a> <a class="navbar-brand brand-pa" href="<?php echo $archiveBase; ?>">Archive</a></span>
                </div>

                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="/">&laquo; Main site</a></li>

                        <!--<li><a href="<?php echo $archiveBase; ?>">Home</a></li>-->

                        <?php /* <li class="dropdown">
                            <a href="/about" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Shows <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/about">About PA</a></li>
                                <li><a href="/about#pacard">The PA Card</a></li>
                                <li><a href="/about#committee">Your Committee</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="/societies" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Societies <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/societies#theatrical">Theatrical</a></li>
                                <li><a href="/societies#music">Music</a></li>
                                <li><a href="/societies#dance">Dance</a></li>
                                <li><a href="/societies#tech">Tech</a></li>
                            </ul>
                        </li> */ ?>

                        <li><a href="<?php echo $archiveBase; ?>/shows">Shows</a></li>

                        <li><a href="<?php echo $archiveBase; ?>/societies">Societies</a></li>

                        <li><a href="<?php echo $archiveBase; ?>/members">Members</a></li>

                        <!--<li><a href="<?php echo $archiveBase; ?>/venues">Venues</a></li>-->

                        <!--<li><a href="/">&laquo; Main site</a></li>-->


                        <?php self::printUserNavMobile(); ?>

                    </ul>


                    <?php self::printUserNavNormal(); ?>

                </div>

            </div>
        </nav>


        <main class="container pa-main-content">

    <?php
    }


}



