<?php
$choice =$_POST["query"];

$bikes = array("Ducaite", "Royal Enfield" , "Harley Davidson");

$markers = new stdClass();
//$markers-> label = "Marcadores de estaciones dBizi";
$markers = array(
    array("numero"=>'1',"nombre"=>'Ayuntamiento',"lat"=>'43.321800',"long"=>'-1.985146'),
    array("numero"=>'2',"nombre"=>'Andia',"lat"=>'43.320547',"long"=>'-1.982459'),
	array("numero"=>'3',"nombre"=>'Arrasate',"lat"=>'43.318285',"long"=>'-1.981574'),
	array("numero"=>'4',"nombre"=>'Paseo Francia',"lat"=>'43.317488',"long"=>'-1.977250'),
);


if($choice == "markers")
	print json_encode($markers);







?>
