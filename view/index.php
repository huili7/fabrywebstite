<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>International Fabry Disease Genotype-Phenotype Database (dbFGP)</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../public/css/style.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.12.4.js"></script>
  <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>

</head>
<style>

</style>

<body>
<script>
//js code starts executing
$(document).ready(function(){

   $.ajax({
        url: "../model/count.php",
		dataType : 'text',
        success: function (data){
			$("#visitor").php(data);
		}

	});
});


</script>

<div class="container">
<?php include"header.php"; ?>

	<div class="content" style="height:700px">
		<h2 style="font-family: 'Helvetica Neue', sans-serif; font-size: 18px; color:#3D1C00;font-weight: 700; line-height: 50px; letter-spacing: 1px;">
			International Fabry Disease Genotype-Phenotype Database (dbFGP)
		</h2>
		<P style="text-align: center; color: #292929; display: inline-block; font-family: 'Georgia', serif; font-style: italic; font-size: 16px; line-height: 22px; margin: 0 0 20px 18px; padding: 1px 12px 8px; border-bottom: double #69D2E7;">
			Established and maintained by <br>
			The International Center for Fabry Disease <br>
			The Icahn School of Medicine at Mount Sinai<br>
			Department of Genetics and Genomic Sciences<br>
			New York, New York
		</P>
		<br>
		<P style="text-align: center; color: #292929; display: inline-block; font-family: 'Georgia', serif; font-style: italic; font-size: 16px; line-height: 22px;font-weight:800">
			A web-based database of genotype and phenotype information consolidating <br> data from peer-reviewed publications,
			known databases, and available patient clinical<br> and biochemical findings to provide healthcare providers and <br>
			patients with comprehensive information about<br> Fabry disease.

		</P>
	</div>

<div id="visitor"></div>
<?php include"footer.php"; ?>
</div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-92177106-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
