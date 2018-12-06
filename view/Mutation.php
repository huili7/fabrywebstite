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
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js" type="text/javascript"></script>
	<script src="../js/ui/jquery.ui.core.js"></script>
    	<script src="../js/ui/jquery.ui.widget.js"></script>
    	<script src="../js/ui/jquery.ui.mouse.js"></script>
    	<script src="../js/ui/jquery.ui.draggable.js"></script>
    	<script src="../js/ui/jquery.ui.droppable.js"></script>
</head>
<style>



</style>
<script>
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
	apt=$("#apt").val();
	city=$("#city").val();
	state=$("#state").val();
	postal=$("#postal").val();
	country=$("#country").val();
	manifestations=$("#manifestations").val();
	phenotype=$("#phenotype").val();
	var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
	if (!filter.test(email)) {
   		alert("Your email format is incorrect, please check it.")
    	return false;
	}

 	filter = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
    if (!filter.test(phone)) {
	  	alert("Your phone format is incorrect, please check it.")
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
				apt:apt,
				city:city,
				state:state,
				postal:postal,
				country:country,
				phenotype:phenotype,
				manifestations:manifestations

			},
			success : function(data1){

				alert("Thanks contribution! your variant have been uploaded successfully.");
				if ($("#noresults").hasClass("ui-dialog-content")&&$("#noresults").dialog("isOpen")) {
				$("#noresults").dialog("close");
				$("#noresults").hide();
                   return;
              }
			  else{
			  $("#noresults").hide();
			  return;
			  }


	       },
			error : function(XMLHttpRequest, textStatus, errorThrown) {
				alert(textStatus);

			}
		});


}
function displayNoresult(keyword){
 	alert("Sorry, your variant is not included in our current database, please complete the contact information form. The dbGFP team will contact you soon.");

 	window.location.href = "contactus.php?subject="+keyword;

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
	if($("#tags").val()==""){
 		return false;
	}

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
				$("#"+key).html(Likely_phenotype_html);
		  	}
		}
		else if(key=="references"){
			var ref_array=value.split("##");
			var ref_html="";
			for(i=0;i<ref_array.length;i++){
			    var url_text=ref_array[i].split("!!");
				ref_html=ref_html+"<a target='_blank' href='"+url_text[1]+"' >"+url_text[0]+"</a><br>";
			}
			$("#"+key).html(ref_html);
		}
		else{
			$("#"+key).html(value);
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
				//alert("good job");
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
$(document).ready(function(){
   $.ajax({
            url: "../data/server.txt",
            success: function (data){
			  	var darray=data.split("\n");
              	autoFill(darray);
			}

	});

	$("#tags").keypress(function(event){
  		var keycode = (event.keyCode ? event.keyCode : event.which);
  		if (keycode == '13') {
    		event.preventDefault();
    		event.stopPropagation();
  		}
	});
});


</script>

<body>
<div class="container">

<?php include"header.php"; ?>
		<br>
		<br><br><br>


	<div class="content">
		<h2 style="font-family: 'Helvetica Neue', sans-serif; font-size: 18px; color:#3D1C00;font-weight: 700; line-height: 50px; letter-spacing: 1px;">
			International Fabry Disease Genotype-Phenotype Database (dbFGP)
		</h2>
		<form >
		<P style="text-align: left;color: #292929; display: inline-block; font-style: italic;font-family: 'Georgia', serif; font-size: 16px; font-weight: 500; line-height: 22px; margin: 0 100px 10px 100px; padding: 1px 10px 1px 10px;  border: 1px solid #fff">
		Type the mutation you are searching for in the box. You may use codon change, amino acid change, nucleotide, or protein nomenclature. Do not include spaces. Then click on “SEARCH”.
		</p>
		 <label for="tags"> </label><br>
  		<input id="tags" placeholder="Enter mutation here..." style="-moz-box-sizing: content-box;height: 38px;background-color:#fff;	width:400px;  padding: 0 36px 0 10px; border: 2px solid #3299BB ;text-transform: uppercase;  border-radius: 10px; /* (height/2) + border-width */; margin: 15px 0 15px 0;  color: #000000;font-size: 14px;	-webkit-appearance: none;	-moz-appearance: none;">
		<a href="#" class="btn blue" onclick="mysearch();">
		SEARCH</a>
		</form>
	 </div>

	<div class="results" id="haveresult" style="display:none;" >
	<span style="text-align:center;margin-left:100px;width:100px; padding:10px;background: #0e83cd;" class="botn botn-1"><a href="#" onclick="exportPDF('results');">Export</a></span> <span style="text-align:center;width:100px; padding:10px;background: #0e83cd;" class="botn botn-1"><a href="#" onclick="printDiv('results');" >Print</a></span>
		<div id="results">
	<table cellpadding='10';  style="border-collapse: collapse; margin-left:10%; margin-right:10%;margin-bottom:30px; padding-bottom:30px;">
	<tr>
		<td>
			<img src="../public/images/logo.png" height="170px">
		</td>
		<td>
			<h3 style="font-family: 'Molengo', Georgia, Times, serif;font-size: 22px;line-height: 55px;font-weight: 800; margin: 0 0 24px; text-align: left;color:#3D1C00">
				International Fabry Disease Genotype-Phenotype Database (dbFGP)</h3>
		</td>
	</tr>
		<td colspan="2" align="center">
			<P style="text-align: center; color: #292929; display: inline-block; font-family: 'Georgia', serif; font-style: italic; font-size: 16px; line-height: 22px; margin: 0 0 20px 18px; padding: 1px 12px 8px; border-bottom: double #69D2E7;">
			established and maintained by <br>
			The Icahn School of Medicine at Mount Sinai, Department of Genetics and Genomic Sciences<br>
			New York, New York 10029<br>
			Phone: 866-322-7963; Fax: 212-659-6780; Email: dbFGP@mssm.edu; URL: dbFGP.org

		</p>
		<P style="text-align: center; color: #292929; display: inline-block; font-family: 'Georgia', serif; font-style: italic; font-size: 16px; line-height: 22px; margin: 0 0 20px 18px; padding: 1px 12px 8px; font-weight:600;">
		A web-based database of genotype and phenotype information consolidating  data from peer-reviewed publications, known databases,<br>
 		and available patient clinical and biochemical findings to provide healthcare providers and patients with <br>
		comprehensive information about Fabry disease.
				</p>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="font-family: 'Helvetica Neue', sans-serif; text-align:center;font-size: 18px; color:#3D1C00;font-weight: 700; line-height: 50px; letter-spacing: 1px;">
				Results of Mutation Search

		</td>
	</tr>
		<td colspan="2">
			<ol style="list-style-type: none;list-style-position: inside; padding-left:0;">
				<li style="padding: 8px;text-align:left;"><label style="font-size:15px;font-weight:700;"> Genotype information:</label>
				</li>
				<ul style="list-style-type:dot;list-style-position: inside; padding-left:0;">
					<li style="padding: 8px;text-align:left;margin-left: 15px;">Nucleotide change:
						<label style="text-decoration: underline;" id="Nucleotide_change">
						</label>
				</li>
				<li style="padding: 8px;text-align:left;margin-left: 15px;">Exon(s)/Intron(s) involved:
						<label style="text-decoration: underline;" id="Exon_Intron">
						</label>
					</li>
					<li style="padding: 8px;text-align:left;margin-left: 15px;">Amino acid change:
						<label style="text-decoration: underline;" id="Amino_acid_change">
						</label>
					</li>
					<li style="padding: 8px;text-align:left;margin-left: 15px;">HGMD accession:
						<label style="text-decoration: underline;" id="HGMD_accession">
						</label>
					</li>
					<li style="padding: 8px;text-align:left;margin-left: 15px;">Mutation Type:
						<label style="text-decoration: underline;" id="Mutation_Type">
						</label>
					</li>
				</ul>
				<li style="padding: 8px;text-align:left;"><label style="font-size:15px;font-weight:700;">Likely phenotype:</label>
					<label style="text-decoration: underline;" id="Likely_phenotype">
					</label>
				</li>
				<li style="padding: 8px;text-align:left;"><label style="font-size:15px;font-weight:700;">Important Mutation-Specific References:<br>
				</label><br>

					<label  id="references"></label>
				</li>
				<li style="padding: 8px;text-align:left;"><label style="font-size:15px;font-weight:700;">α-Galactosidase A Activity in Affected Males and Heterozygous Females: <br></label>

			<br>

<div  id="clinInfor">

</div>

</li>
				<li style="padding: 8px;text-align:left;"><label style="font-size:15px;font-weight:700;">Phenotype-Specific Clinical Manifestations:</label><br>
					<div id="clinPic"></div>
				</li>
				<li style="padding: 8px;text-align:left;"><label style="font-size:15px;font-weight:700;"> Phenotype-Specific Recommendations for Medical Management and Testing At-Risk Family Members:</label>
					<div id="clinRec"></div>					<li>
			</ol>
		</td>
	</tr>
</table>
		</div>
	</div>
	<div class="noresults" id="noresults" style="display:none;">
				<P style="text-align: center; color: #292929; display: inline-block; font-family: 'Georgia', serif; font-style: italic; font-size: 16px; line-height: 22px; margin: 0 0 20px 18px; padding: 1px 12px 8px; border-bottom: double #69D2E7;">
			For questions  regarding the information
			presented in this database or questions / problems
			regarding the website, please contact:
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
    			<input type="text" id="email" name="email"  required tabindex="3" />
			</p>
			<p class="inputfield"><label for="subject">Subject*: &nbsp;</label>
    			<input type="text" id="email" name="email"  required tabindex="4" />
			</p>
    		<p class="inputfield" ><label for="message" style="vertical-align: top;">Message*: </label>
    			<textarea name="message" id="message" tabindex="2"></textarea>
    		</p>
      		<p class="inputfield"><label for="institution">Institution:</label>
    			<input type="text" id="name" name="name" placeholder="First and last name" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="5" />
    		</p>
      		<p class="inputfield"><label for="addres">Address: &nbsp;</label>
    			<input type="text" id="name" name="name" placeholder="Street address,apartment, suite, room" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="5" />
    		</p>
			<p class="inputfield"><label for="addres"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; </label>
    			<input type="text" id="name" name="name" placeholder="City, state,Postal / zip code, country" style="color: #fff;" onfocus="if (this.value == '90') {this.value=''; this.style.color='#000';}" required tabindex="5" />
    		<br>* Required&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp;&nbsp;<input name="submit" type="submit" id="submit" tabindex="5" value="Submit" /></p>

	</div>

<?php include"footer.php"; ?>
</div>
</body>
</html>
