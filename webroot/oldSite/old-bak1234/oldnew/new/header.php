<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SUSU Performing Arts - <?php echo $area; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="//use.typekit.net/czx6utv.js"></script>
    <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="./css/bootstrap.css" rel="stylesheet" type="text/css" />    
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
  <link href="http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
  <link href="./css/custom.css" rel="stylesheet" type="text/css" />
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->

    <link rel="apple-touch-startup-image" href="startup.png">
<link rel="apple-touch-icon-precomposed" href="touch-icon-iphone.png" />
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="touch-icon-ipad.png" />
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="touch-icon-iphone-retina.png" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="touch-icon-ipad-retina.png" />

  </head>

  <body>
  <!-- Facebook API -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=451768998216844";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="http://perform.susu.org/new/">Performing Arts</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li <?php if($area=="Home"){echo 'class="active"';} ?>><a href="http://perform.susu.org/new">Home</a></li>
              <!--<li <?php if($area=="Get Involved"){echo 'class="active"';} ?>><a href="getinvolved.php">Get Involved</a></li>-->
              <li class="dropdown <?php if($area=="About"){echo 'active';} ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">About Us<b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/new/about.php#aboutpa">About PA</a></li>
                  <li><a href="/new/about.php#pacard">The PA Card</a></li>
                  <li><a href="/new/about.php#committee">Your Committee</a></li>
                </ul>
              </li>
              <li class="dropdown <?php if($area=="Societies"){echo 'active';} ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Societies<b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/new/societies.php#theatrical">Theatrical</a></li>
                  <li><a href="/new/societies.php#music">Music</a></li>
                  <li><a href="/new/societies.php#dance">Dance</a></li>
                  <li><a href="/new/societies.php#tech">Tech</a></li>
                </ul>
              </li>
              <li><a href="https://docs.google.com/folder/d/0B8QnE0Kdupy-ektjS1ZtTGV6akU/edit?pli=1">Resources</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
