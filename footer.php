      <hr>

      <footer>
        <p>&copy; SUSU Performing Arts & Joe Hart 2012</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35100441-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
    <script type="text/javascript">

    google.load("feeds", "1");

    function initialize() {
      var feed = new google.feeds.Feed("http://www.susu.tv/series/performing-arts/feed/");
      feed.load(function(result) {
        if (!result.error) {
          var container = document.getElementById("feed");
          for (var i = 0; i < result.feed.entries.length; i++) {
            var entry = result.feed.entries[i];
            var div = document.createElement("div");
            var h = document.createElement("h4");
            var p = document.createElement("p");
            var link = document.createElement("a");
            var video ='cock';
            if(i==0)
            {

              var embed = document.createElement("div");
              embed.setAttribute("id", "video");
              embed.appendChild(document.createTextNode(video));
              $.get('http://www.guardian.co.uk/culture/2012/jun/21/jimmy-carr-apologises-error-tax', 
function(data) {
   $(data).find('meta[name=adescription]').attr("content");
   $(embed).text("cunt");
   
});
              //container.appendChild(embed);


            }

            link.setAttribute('href', entry.link);
            link.appendChild(document.createTextNode(entry.title));
            h.appendChild(link);
            p.appendChild(document.createTextNode(entry.contentSnippet));
            //p.appendChild(document.createTextNode(video.toString()));
            //p.appendChild(document.createTextNode(data));
            div.appendChild(h);
            div.appendChild(p);
            container.appendChild(div);
          }
        }
      });
    }
    google.setOnLoadCallback(initialize);

    </script>
<script>
  (function(d, t) {
    var e = d.createElement(t);
    e.src = d.location.protocol + '//www.browserawarenessday.com/widget/50f48dbc9c1b7ef263000072';
    e.async = true;
    var n = d.getElementsByTagName(t)[0]; n.parentNode.insertBefore(e, n);
  })(document, 'script');  
</script>

  </body>
</html>