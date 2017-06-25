<?php

/*** функция проверки подписанта ***/

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
			$sign = $row['ID']; // ID подписанта
		}
	}
	
	// если ID подписанта не нашли, надо об этом сообщить
	if( !$sign ) {
		mail("ххх@ххх.ru", "My Subject", "подписанта .$signerName. в базе нет. Пожалуйста, обратитесь к администратору", "From: admin@ххх.ru"); 
	}

	return $sign;
}

/*** Функция преобразования даты для БД ***/

function chDate($oldd){
	$yy = '';
	$dd = '';$mm = '';
	$dd = substr($oldd, 0, 2);
	$mm = substr($oldd, 3, 2);
	$yy = substr($oldd, 6, 4);
	$date = $yy.$mm.$dd;

	return $date;
}	


/*** Функция преобразования файла pdf в бинарную строку (для записи в БД) ***/

function pdfstr($pdf){
	$h=[];// массив hex-символов
	$pdfstr = ''; // будущая строка
	
	for($i=0;$i<strlen($pdf);$i++){
		$ascii = ord($pdf[$i]); //  ascii-код символа(dec)
		$h[$i] = sprintf("%'.02x", $ascii); //запись в массив hex-символов обязательно из 2-х цифр, иначе потом скаченный файл не прочитать
		$pdfstr = $pdfstr.(string)$h[$i];
	}

	return $pdfstr;
}

/*** Функция формирования слов для поиска ***/

function forSearch($name){
	$low = mb_strtolower($name); // переводим всё в нижний регистр 
	$rep = str_replace(' ','',$low); // и убираем пробелы 
	return $rep;
}	

/*** функция формирования PubID ***/

function PubID($authority, $conn){ // 
	$dateN = date("Ymd"); // сегодняшняя дата
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