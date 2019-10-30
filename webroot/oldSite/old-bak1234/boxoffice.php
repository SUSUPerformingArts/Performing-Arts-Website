
<?php 

$area="About";
include 'header.php';
?>
<div class="row-fluid">
	<div class="well">
		<h2>Performing Arts Box Office</h2>
	</div>
</div>


<!-- Start Ticket Shop App -->

<div id="embedTS_GKFH" style="width:750px;"></div>
<script type="text/javascript">
  (function() {
    var el = document.createElement("script");
    el.type = "text/javascript";
    el.async = true;
    el.src = "http://www.ticketsource.co.uk/ticketshop/GKFH";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(el, s);
  })();
</script>

<!-- End Ticket Shop App -->


<?php include 'footer.php';
?>

