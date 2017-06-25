<?php

/*** ������� �������� ���������� ***/

function signer($signerName, $conn){
	$query =  "SELECT * FROM Signers where Signers.FIO like '%".$signerName."%' and Signers.IsActive=1";
	$sign = '';
	$sql = sqlsrv_query( $conn, $query);
	
	if( $sql === false ) {
		$sign = sqlsrv_errors();
	}	
	else
	{
		while( $row = sqlsrv_fetch_array( $sql, SQLSRV_FETCH_ASSOC) ) {
			$sign = $row['ID']; // ID ����������
		}
	}
	
	// ���� ID ���������� �� �����, ���� �� ���� ��������
	if( !$sign ) {
		mail("���@���.ru", "My Subject", "���������� .$signerName. � ���� ���. ����������, ���������� � ��������������", "From: admin@���.ru"); 
	}

	return $sign;
}

/*** ������� �������������� ���� ��� �� ***/

function chDate($oldd){
	$yy = '';
	$dd = '';$mm = '';
	$dd = substr($oldd, 0, 2);
	$mm = substr($oldd, 3, 2);
	$yy = substr($oldd, 6, 4);
	$date = $yy.$mm.$dd;

	return $date;
}	


/*** ������� �������������� ����� pdf � �������� ������ (��� ������ � ��) ***/

function pdfstr($pdf){
	$h=[];// ������ hex-��������
	$pdfstr = ''; // ������� ������
	
	for($i=0;$i<strlen($pdf);$i++){
		$ascii = ord($pdf[$i]); //  ascii-��� �������(dec)
		$h[$i] = sprintf("%'.02x", $ascii); //������ � ������ hex-�������� ����������� �� 2-� ����, ����� ����� ��������� ���� �� ���������
		$pdfstr = $pdfstr.(string)$h[$i];
	}

	return $pdfstr;
}

/*** ������� ������������ ���� ��� ������ ***/

function forSearch($name){
	$low = mb_strtolower($name); // ��������� �� � ������ ������� 
	$rep = str_replace(' ','',$low); // � ������� ������� 
	return $rep;
}	

/*** ������� ������������ PubID ***/

function PubID($authority, $conn){ // 
	$dateN = date("Ymd"); // ����������� ����
	$count = 0;
	$query =  "SELECT count(*) as c FROM Acts where AuthorityID='$authority' and DatePub>='$dateN'";
	$sql = sqlsrv_query( $conn, $query);
	$row = sqlsrv_fetch_array( $sql, SQLSRV_FETCH_ASSOC);
	$count = $row['c']; 
	if (!$count) $count = 0;
	$count++;
	settype($dateN, "string");
	$ID = $dateN.'0000';
	$ID = $count + $ID;
	$pubID = '35'.$authority.$ID;
	return $pubID;
}	

	
	
?>