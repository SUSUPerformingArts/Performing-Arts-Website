<?php
    /* Footer for the PA website
        Call the printFooter function to print out the footer
        Adds a copywrite footer & closes the main content tag
        Includes required javascript, for performance reasons
    */

namespace PA\Snippets;

use \PA\Auth\Auth;


class Footer implements SnippetInterface {


    public static function printSnippet($options = null){
        $scripts = isset($options['scripts']) ? $options['scripts'] : null;

        self::printFooter($scripts);
    }

    public static function printFooter($scripts = null){
        self::printVisualFooter();
        self::printFooterScripts($scripts);
    }



    public static function printVisualFooter(){
        $archiveBase = "/archive";
        $uri = $_SERVER["REQUEST_URI"];
    ?>

        </main>


        <footer class="pa-main-footer">
            <div class="container">
                <div class="row">
					<div class="col-md-4 col-md-offset-4">
                        <a href="/"><img src="/img/logos/pa_white_centered.svg" alt="PA logo" style="width: 100%"></a>
					</div>
				</div>
				
				<div class="row" style="height: 20px" > </div>
				
				<div class="row text-center">
                        <div class="col-md-1">
							<a href="mailto:pao@susuperformingarts.org"><img class="img-responsive" src="/img/logos/social/email.svg" /></a>
						</div>
						<div class="col-md-1">
							<a href="https://fb.com/SUSUPerformingArts"><img class="img-responsive" src="/img/logos/social/facebook.svg" /></a>
						</div>
						<div class="col-md-1">
							<a href="https://twitter.com/SUSUPerform"><img class="img-responsive" src="/img/logos/social/twitter.svg" /></a>
						</div>
						<div class="col-md-1">
							<a href=""><img class="img-responsive" src="/img/logos/social/instagram.svg" /></a>
						</div>
						<div class="col-md-1">
							<a href="http://www.snapchat.com/add/susuperform"><img class="img-responsive" src="/img/logos/social/snapchat.svg" /></a>
						</div>
				</div>

                <hr class="hr-smaller" style="margin-top: 20px; margin-bottom: 20px; ">
                <p class="text-center" style="color: #ffffff">
                    &copy; SUSU Performing Arts &amp; Corin Chaplin, 2015; Simeon Brooks 2017
                    <br>
                    Design &copy; Joe Hart, 2012 &amp; Robin Johnson, 2014.
                    <br>
                    <a href="/feedback">Leave website feedback here.</a>
                </p>
            </div>
        </footer>

    </div>

    <?php
    }


    public static function printFooterScripts($scripts = null){
    ?>
        <script src="/libs/moment.js"></script>
        <script src="/libs/bootstrap/js/bootstrap.min.js"></script>
        <script src="/libs/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>

    <?php self::printFooter_customJS($scripts) ?>

        <script>
          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-35100441-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();
        </script>


    </body>
    </html>

    <?php
    }


    public static function printFooter_customJS($scripts = null){
        if(isset($scripts)){
            foreach ($scripts as $script){
                echo "  <script src=\"$script\"><script>\n";
            }
        }
    }

}
