<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datos extends CI_Controller {



	/* DEPRECATED - TASK DONE WITH CRON AT LINUX
	ACTUALIZA LOS DATOS SI TIENE MÁS DE UN MINUTO DE ANTGÜEDAD
	private function comparar_tiempo_y_actualizar(){
		$this->load->model('Estaciones');

		$ultimo_tiempo =  $this->Estaciones->ultima_actualizacion();

		$difference = time() - strtotime($ultimo_tiempo);

		
		if ($difference > 60 ){
			$this->actualizarEstaciones();
		}
	}*/
	

	//Publica los datos en modo JSON
	public function publicarDatos(){
		
		//$this->comparar_tiempo_y_actualizar();
		
		$this->load->model('Estaciones');
		$estaciones = $this->Estaciones->obtener_estaciones();
		
		print json_encode($estaciones);
	}
	
	public function publicarEstadoBicis(){
		$this->load->model('EstadoBicis');
		$estadoBicis = $this->EstadoBicis->obtener_estadoBicis();
		
		print json_encode($estadoBicis);
	}
	
	
}
