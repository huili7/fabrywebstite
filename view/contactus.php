<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Mutation Search</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../public/css/style.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
 	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/jquery.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js"></script>
	<script src="../js/ui/jquery.ui.core.js"></script>
  <script src="../js/ui/jquery.ui.widget.js"></script>
  <script src="../js/ui/jquery.ui.mouse.js"></script>
  <script src="../js/ui/jquery.ui.draggable.js"></script>
  <script src="../js/ui/jquery.ui.droppable.js"></script>
</head>
<style>

label {
  display: inline-block;
  width: 140px;
  text-align: right;
}​
input[type="text"] {
	display: inline;
	width: 400px;
  font-size: 14px;
	background-color:#9EcDDF;
  font-family: 'Lato';
  color: white;
	padding: 3px 0px 3px 3px;
	margin: 1px 0px 1px 0px;
	border-radius: 3px;
	-moz-border-radius: 5px;
}

.inputfield {
  padding-top: 2px;
  font-weight: 400;
}

::-webkit-input-placeholder {
  color: white;
  font-weight: 300;
}

textarea {
	display: inline;
  width: 400px;
	max-width: 400px;
	height: 100px;
  max-height: 200px;
  font-size: 14px;
	background-color:#9EcDDF;
	color:#fff;
  padding: 5px 0px 5px 10px;
	margin: 5px 0px 0px 0px;
	border-radius: 3px;
	-moz-border-radius: 5px;
}

</style>
<?php
error_reporting(0);
$subject="dbFGP Feedback: Searching for ".$_GET["subject"]." yields no results";
?>
<script>

	$(document).ready(function(){
	var subject="<?php echo $subject; ?>";
	$("#subject").val(subject);
   	$.ajax({
        url: "../data/server.txt",
        success: function (data){
		var darray=data.split("\n");
        	autoFill(darray);
		}

	});
});


function showContribution(){
	$( "#noresults" ).dialog({
         autoOpen: true,

         minHeight: 185,
         width: 1000,
         modal: true,

         position: 'center'
    });
}
function exportPDF(divId){
 	var printContents = document.getElementById(divId).innerHTML;
	$.ajax({
			type : 'POST',
			url : '../model/writefile.php',
			dataType : 'text',
			data: {
				name:printContents

			},
			success : function(data1){

				window.location.href="../model/generatePDF.php";

	       },
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);

			}
		});
}
function insertContact(){

	name=$("#name").val();
	email=$("#email").val();
	phone=$("#phone").val();
	subject=$("#subject").val();
	message=$("#message").val();
	institute=$("#institute").val();
	street=$("#street").val();
	city=$("#city").val();
	var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if (!filter.test(email)) {
    	alert("Your email format is incorrect, please check it.")
    	return false;
}
	$.ajax({
			type : 'POST',
			url : '../model/pass.php',
			dataType : 'text',
			data: {
				name:name,
				email:email,
				phone:phone,
				subject:subject,
				message:message,
				institute:institute,
				street:street,
				city:city

			},
			success : function(data1){
				alert("Thanks contribution! your variant have been uploaded successfully.");
                   return false;
	       },
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);

			}
		});


}
function displayNoresult(keyword){
 	alert("Sorry, your variant is not included in our current database, please complete the Contact information from. The dbGFP team will contact you soon");

 	window.location.href = "http://www.dbfgp.org/dbFgp/fabry/contactus.php";

}
function displayresult(){

  	$("#noresults").hide();
   	$("#haveresult").show();

}
function checkData(data){

 	var keyword=$("#tags").val();
 	if($.inArray( keyword, data)!==-1){
 		getAlldata(keyword);
 		displayresult();
 	}else{
   		displayNoresult(keyword);
 	}
}
function mysearch(){

$.ajax({
            url: "../data/server.txt",
            async: false,
            success: function (data){
				var darray=data.split("\n");
            	checkData(darray);

			}

		});


}
function filldata(data){
	$.each(data, function(key,value ) {
		if(key=="Likely_phenotype"){
		  	if(value){
				var Likely_phenotype_html=value.replace(/##/g, "<br>BAIMUDAN");
				$("#"+key).php(Likely_phenotype_html);
		  	}
		}
		else if(key=="references"){
			var ref_array=value.split("##");
			var ref_html="";
			for(i=0;i<ref_array.length;i++){
			       var url_text=ref_array[i].split("!!");
				   ref_html=ref_html+"<a target='_blank' href='"+url_text[1]+"' >"+url_text[0]+"</a><br>";
			}
			$("#"+key).php(ref_html);
		}
		else{
			$("#"+key).php(value);
		}

	});
}
function getAlldata(keyword){
	$.ajax({
			type : 'POST',
			url : '../model/getmutilpledata7_new.php',
			dataType : 'json',
			data: {
			   gene:keyword
			},
			success : function(data){

				if(data){
				 filldata(data);

				}


	       },
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);

			}
		});

}

function printDiv(divId) {
      	var printContents = document.getElementById(divId).innerHTML;
       	var originalContents = document.body.innerHTML;
       	document.body.innerHTML = "<html><head><title></title></head><body>" + printContents + "</body>";
       	window.print();
       	document.body.innerHTML = originalContents;
   }

function autoFill(data){

 	 $( "#tags" ).autocomplete({
		source: function( request, response ) {
			var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
			response( $.grep( data, function( item ){
            return matcher.test( item );
            }) );
		},
    	minLength: 1,
    	select: function(event, ui) {
	       	event.preventDefault();
	      	$("#tags").val(ui.item.label);
	       	$("#selected-tag").val(ui.item.label);
		   	getAlldata(ui.item.label);
	    },
	   focus: function(event, ui) {
	       	event.preventDefault();
	      	$("#tags").val(ui.item.label);
		  	 $("#selected-tag").val(ui.item.label);
	   }
 	});

}



</script>
<body>


<div class="container">
<?php include"header.php"; ?>
	<div class="content" style="height:auto;">

		<h2 style="font-family: 'Helvetica Neue', sans-serif; font-size: 18px; color:#3D1C00;font-weight: 700; line-height: 50px; letter-spacing: 1px;">
			Contact dbFGP
		</h2>
			<P style="text-align: left; color: #292929; display: inline-block; font-family: 'Georgia', serif; font-style: italic; font-size: 16px; line-height: 22px; margin: 0 0 20px 18px; padding: 1px 12px 8px; border-bottom: double #69D2E7;">
			For questions  regarding the information <br>
			presented in this database or questions / problems<br>
			regarding the website, please contact:<br>
			dbFGP Team<br>
			dbFGP@mssm.edu
			</p>
			<p class="inputfield"><label for="name">Name*:   &nbsp;</label>
    			<input type="text" id="name" name="name" placeholder="Your first and last names" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="1" />
			</p>
			<p class="inputfield"><label for="email">Email*: &nbsp;</label>
    			<input type="text" id="email" name="email" placeholder="yourname@domain.com" required tabindex="2" />
			</p>
			<p class="inputfield"><label for="phone">Phone*: &nbsp;</label>
    			<input type="text" id="phone" name="email"  required tabindex="3" />
			</p>
			<p class="inputfield"><label for="subject">Subject*: &nbsp;</label>
    			<input type="text" id="subject" name="email"   required tabindex="4" readonly />
			</p>
    	<p class="inputfield" ><label for="message" style="vertical-align: top;">Message*:&nbsp; </label>
    			<textarea name="message" id="message" ></textarea>
    	</p>
        <p class="inputfield"><label for="institution">Institution:</label>
    			<input type="text" id="institute" name="name" placeholder="First and last name" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="5" />
    	</p>
      	<p class="inputfield"><label for="addres">Address: &nbsp;</label>
    			<input type="text" id="street" name="street" placeholder="Street address,apartment, suite, room" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="5" />
    	</p>
		<p class="inputfield"><label for="addres"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; </label>
    			<input type="text" id="city" name="name" placeholder="City, state,Postal / zip code, country" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="5" />
    	<br>* Required&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;<button onclick="insertContact();return false;"  />submit</button></p>

	</div>
<?php include"footer.php"; ?>
</div>
</body>
</html>
