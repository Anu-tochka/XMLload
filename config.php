<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// здесь реальные значения заменены
$server = 'server';
$connectionInfo = array('UID' => 'user', 'PWD' => 'password', 'Database'=>'DB');
// соединяемся с MSSQL 
$conn = sqlsrv_connect( $server, $connectionInfo);
if( $conn === false ) {
	die( print_r( sqlsrv_errors(), true));
}

?>
