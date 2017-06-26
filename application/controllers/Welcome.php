<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	public function estaciones()
	{
		$markers = new stdClass();
		//$markers-> label = "Marcadores de estaciones dBizi";
		$markers = array(
			array("numero"=>'1',"nombre"=>'Ayuntamiento',"lat"=>'43.321800',"long"=>'-1.985146'),
			array("numero"=>'2',"nombre"=>'Andia',"lat"=>'43.320547',"long"=>'-1.982459'),
			array("numero"=>'3',"nombre"=>'Arrasate',"lat"=>'43.318285',"long"=>'-1.981574'),
			array("numero"=>'4',"nombre"=>'Paseo Francia',"lat"=>'43.317488',"long"=>'-1.977250'),
		);

		print json_encode($markers);

	}
		
}
