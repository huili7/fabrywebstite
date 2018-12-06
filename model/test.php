<?php
$myresult = array();
$myresult["Nucleotide_change"]="not available";
$myresult["Exon_Intron"]="not available";
$myresult["Amino_acid_change"]="not available";
$myresult["HGMD_accession"]="not available";
$myresult["Mutation_Type"]="not available";
$myresult["Likely_phenotype"]="not available";
$myresult["Mutation_Description"]="not available";
$myresult["references"]="not available";
$myresult["clinInfor"]="G20";
$myresult["clinRec"] = "not available";
$myresult["clinPic"]="not available";


echo json_encode($myresult);
