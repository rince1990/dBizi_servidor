<?php
include_once "config.php"; 

$host = 'localhost'; //host
$port = '9000'; //port
$null = NULL; //null var


//Create TCP/IP sream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//reuseable port
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

//bind socket to specified host
socket_bind($socket, 0, $port);

//listen to port
socket_listen($socket);

//create & add listning socket to the list
$clients = array($socket);


//start endless loop, so that our script doesn't stop
while (true) {
	//manage multipal connections
	$changed = $clients;
	//returns the socket resources in $changed array
	socket_select($changed, $null, $null, 0, 10);
	
	//check for new socket
	if (in_array($socket, $changed)) {
		$socket_new = socket_accept($socket); //accpet new socket
		$clients[] = $socket_new; //add socket to client array
		
		$header = socket_read($socket_new, 1024); //read data sent by the socket
		perform_handshaking($header, $socket_new, $host, $port); //perform websocket handshake
		
		socket_getpeername($socket_new, $ip); //get ip address of connected socket
		//$response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' connected'))); //prepare json data
		//send_message($response); //notify all users about new connection
		
		//Display old messages
		send_old_messages($socket_new);
		
		
		//make room for new socket
		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}
	
	//loop through all connected sockets
	foreach ($changed as $changed_socket) {	
		
		//check for any incomming data
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
		{
			$received_text = unmask($buf); //unmask data
			if(json_decode($received_text) != null){//null happens when someone disconnect from the chat
				$tst_msg = json_decode($received_text); //json decode 
				$user_name = $tst_msg->name; //sender name
				$posted = date('Y-m-d H:i:s');
				$conn = new mysqli(HOST, USER, PASS, DB);
				
				if ($tst_msg->type == "userMessage"){
						
					$user_message = $tst_msg->message; //message text
					$user_color = $tst_msg->color; //color
					
					

					//Update DB
					$sql = "INSERT INTO wsMessages (name, msg, posted) VALUES ('".$user_name."', '".$user_message."','".$posted."')";
					$conn->query($sql);
					$conn->close();
					
						
						
					//prepare data to be sent to client
					$response_text = mask(json_encode(array('type'=>'usermsg', 'name'=>$user_name, 'message'=>$user_message, 'color'=>$user_color, 'posted'=>$posted )));
					send_message($response_text); //send data			
				}
				else if ($tst_msg->type == "report"){
					$numeroBici = $tst_msg->numeroBici; 
					$estadoBici = $tst_msg->estadoBici; 
					$informacionBici = $tst_msg->informacionBici; 
					
					
					//Update DB
					$sql = "INSERT INTO reportBicicletas (reporter, posted, numeroBici, estadoBici, informacionBici) VALUES ('".$user_name."','".$posted."', '".$numeroBici."', '".$estadoBici."', '".$informacionBici."')";
					$conn->query($sql);
					$conn->close();
					
					//prepare data to be sent to client
					$response_text = mask(json_encode(array('type'=>'system', 'message'=>'La bici '.$numeroBici.' ha sido marcada en el estado "'.$estadoBici.'" por '.$user_name)));
					send_message($response_text); //send data


				}
				break 2; //exit this loop
			}
		}
		
		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
		if ($buf === false) { // check disconnected client
			// remove client for $clients array
			$found_socket = array_search($changed_socket, $clients);
			socket_getpeername($changed_socket, $ip);
			unset($clients[$found_socket]);
			
			//notify all users about disconnected connection
			//$response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
			//send_message($response);
		}
	}
}
// close the listening socket
socket_close($socket);

function send_message($msg)
{
	global $clients;
	
	foreach($clients as $changed_socket)
	{
		@socket_write($changed_socket,$msg,strlen($msg));
	}
	return true;
}

function send_old_messages($client)
{
	
	$conn = new mysqli(HOST, USER, PASS, DB);
	$sql = "SELECT * FROM (SELECT * FROM wsMessages ORDER BY posted desc LIMIT 10) as mensajes ORDER BY POSTED ASC";
	$old_messages = $conn->query($sql);
	if ($old_messages->num_rows > 0) {
		while($message = $old_messages->fetch_assoc()) {
			$msg = mask(json_encode(array('type'=>'usermsg', 'name'=>$message["name"], 'message'=>$message["msg"], 'color'=>'d3d3d3', 'posted'=>$message["posted"])));
			@socket_write($client,$msg,strlen($msg));
		}
	} else {
		$msg = mask(json_encode(array('type'=>'system', 'message'=>'No existen mensajes anteriores')));	
		@socket_write($client,$msg,strlen($msg));
	}
	$msg = mask(json_encode(array('type'=>'system', 'message'=>'↑↑↑Mensajes Antiguos↑↑↑')));
	@socket_write($client,$msg,strlen($msg));	
	$conn->close();

	return true;
}


//Unmask incoming framed message
function unmask($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

//Encode message for transfer to client.
function mask($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);
	
	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

//handshake new client.
function perform_handshaking($receved_header,$client_conn, $host, $port)
{
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
}
