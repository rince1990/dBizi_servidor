<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estaciones extends CI_Model {

        public $title;
        public $content;
        public $date;

        public function actualizar_estacion($estacion)
        {
   	
				$sql = 'INSERT INTO estacion (id, latitude, longitude, name, free ,locked, light, active, number, address, ultima_actualizacion)
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)
							ON DUPLICATE KEY UPDATE 
								latitude=VALUES(latitude), 
								longitude=VALUES(longitude), 
								name=VALUES(name),
								free=VALUES(free), 
								locked=VALUES(locked), 
								light=VALUES(light),
								active=VALUES(active),
								number=VALUES(number),
								address=VALUES(address),
								ultima_actualizacion= VALUES(ultima_actualizacion)';
			
				$query = $this->db->query($sql, array( $estacion['id'],
													$estacion['latitude'], 
													$estacion['longitude'], 
													$estacion['name'],
													$estacion['free'] , 
													$estacion['lock'],
													$estacion['light'], 
													$estacion['active'], 
													$estacion['number'], 
													$estacion['address'],
													date('Y-m-d H:i:s')
													));
				return $query;
        }
		
		public function ultima_actualizacion()
        {
		$this->db->select_min('ultima_actualizacion'); //obtiene la fecha mas antigua
		$query = $this->db->get('estacion');
		$row = $query->row();
		return $row->ultima_actualizacion;
			
		}
	
		public function obtener_estaciones()
        {
			$query = $this->db->get('estacion');
				$estaciones = array();
			
			
			foreach ($query->result_array() as $row){
				$row_array['id'] = $row['id'];
				$row_array['latitude'] = $row['latitude'];
				$row_array['longitude'] = $row['longitude'];
				$row_array['name'] = $row['name'];
				$row_array['free'] = $row['free'];
				$row_array['locked'] = $row['locked'];
				$row_array['light'] = $row['light'];
				$row_array['active'] = $row['active'];
				$row_array['number'] = $row['number'];
				$row_array['address'] = $row['address'];
				$row_array['ultima_actualizacion'] = $row['ultima_actualizacion'];
				array_push($estaciones,$row_array);
			}
			
			return $estaciones;
		}


}
?>