<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EstadoBicis extends CI_Model {

 
	
		public function obtener_estadoBicis()
        {

				
			$query = $this->db->query("SET @currcount = NULL, @currvalue = NULL;");
			$sql ="
				SELECT `numeroBici`,`estadoBici`,`informacionBici`,`posted` FROM(
					SELECT `numeroBici` , `posted`,`informacionBici`,`estadoBici`,
						@currcount := IF(@currvalue = `numeroBici`, @currcount + 1, 1) AS rank,
						@currvalue := `numeroBici` AS aux
					FROM reportBicicletas
					ORDER BY `numeroBici`,`posted` DESC
				)AS aux WHERE rank <= 5";

			$query = $this->db->query($sql);
			$estadoBicis = array();
			
			foreach ($query->result_array() as $row){
				$row_array['numeroBici'] = $row['numeroBici'];
				$row_array['estadoBici'] = $row['estadoBici'];
				$row_array['informacionBici'] = $row['informacionBici'];
				$row_array['posted'] = $row['posted'];
				array_push($estadoBicis,$row_array);
			}
			
			return $estadoBicis;
		}

}
?>