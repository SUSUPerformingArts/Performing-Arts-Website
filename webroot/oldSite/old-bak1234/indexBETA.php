<?php 
$area="Home";
  include 'header.php'; 

?>
<div class="carousel slide">
          <div class="carousel-inner">
            <!-- Carousel items -->
            <div class="carousel-inner">
              <div class="active item">
                <img src="./img/carousel/1.jpg" alt="Theatre" />
                <div class="carousel-caption">
                  <h4>Theatrical</h4>
                  <p>Interested in Acting? Directing? Magic? Juggling? Comedy? Musical Theatre? Then have a look at our theatrical societies</p>
                  <p><a href="societies.php#theatrical" class="btn btn-primary btn-large">Get Involved &raquo;</a></p>

                </div>
              </div>
              <div class="item">…</div>
              <div class="item">…</div>
            </div>
            <!-- Carousel nav -->
            <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
            <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
          </div>
        </div>
      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="row-fluid">
        <div class="span12">
          <!--<div class="hero-unit">
            <div class="row-fluid">
              <div class="span4">
                <img src="img/pa.png" alt="SUSU Performing Arts" width="100%" />
              </div>
              <div class="span8">
                <div class="row-fluid">
                  <p class="lead">Dance. Music. Theatre. Tech.</p>
                  <p>With over 30 societies ranging from Theatre Group to Folk Soc there's something for pretty much everyone who's passionate about performing.</p>
                </div>
                <p><a href="societies.php" class="btn btn-primary btn-large">Get Involved &raquo;</a></p>
              </div>
            </div>
          </div>-->
        
        
        </div>
        <!--<div class="span4">
          <h2>Blogs</h2>
        </div>  -->
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span3">
          <h2>32 Societies</h2>
          <p> Our societies are divided into Musical, Theatrical and Dance societies. And of course there is StageSoc as well for those interested in tech.</p>
          <p><a class="btn btn-primary" href="societies.php">Learn more &raquo;</a></p>
        </div>
        <div class="span3">
          <h2>Latest Media</h2>
          <div id="feed"></div>
          <p><a class="btn btn-primary" href="#">View details &raquo;</a></p>
       </div>
        <div class="span6">
          <h2>Follow Us</h2>
          <div class="row-fluid">
            <div class="span6">
          <div class="fb-like-box" data-href="http://www.facebook.com/SUSUPerformingArts" data-height="600" data-show-faces="true" data-colorscheme="dark" data-stream="true" data-header="true"></div>
          </div>
          <div class="span6">
          <a class="twitter-timeline" href="https://twitter.com/SUSUPerform" data-widget-id="277923812060823555">Tweets by @SUSUPerform</a>
          <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
          </div>
          </div>
        </div>
      </div>
<?php include 'footer.php'; ?>