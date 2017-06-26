<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Actualizar extends CI_Controller {


	//OBTIENE LOS DATOS DE LA WEB DBIZI//
	private function getDatosFromdBizi(){

		$html = file_get_contents('https://www.dbizi.com/map/'); //Convierte la información de la URL en cadena

		$posicion_datos_inicio_de_estaciones = strpos($html, '{"stations":');	
		$posicion_datos_fin_de_estaciones = strpos($html, "}]}');")+3;
		$length = $posicion_datos_fin_de_estaciones - $posicion_datos_inicio_de_estaciones;
		$datos = substr ( $html , $posicion_datos_inicio_de_estaciones , $length  );
		$datos_json = json_decode($datos,true);
		return $datos_json;


	}
	
	public function actualizarEstaciones(){		

	$estaciones = $this->getDatosFromdBizi();
	$this->load->model('Estaciones');

	//ACTUALIZA LOS DATOS DE LA BBDD//
	foreach( $estaciones['stations'] as $estacion ) {
		//actualizar cada estacion
			$this->input->post('estacion');
			$query_res = $this->Estaciones->actualizar_estacion($estacion);
		}


	}
	

}
