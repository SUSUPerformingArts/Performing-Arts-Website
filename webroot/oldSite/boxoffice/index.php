<?php

    // Box Office
    $root = $_SERVER["DOCUMENT_ROOT"];
    require_once $root . "/compose.php";

    \PA\Snippets\Header::printHeader("SUSU Performing Arts - Box Office");

?>

<div class="well well-pa">
	<h1>Performing Arts Box Office</h1>
</div>


<!-- Start Ticket Shop App -->

<div id="embedTS_GKFH"></div>
<script type="text/javascript">
  (function() {
    var el = document.createElement("script");
    el.type = "text/javascript";
    el.async = true;
    el.src = "//www.ticketsource.co.uk/ticketshop/GKFH";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(el, s);
  })();
</script>

<!-- End Ticket Shop App -->


<?php 
    \PA\Snippets\Footer::printFooter();
