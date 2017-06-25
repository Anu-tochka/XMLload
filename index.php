<?php
header('Content-type: text/html; charset=windows-1251');
require_once ('config.php');
require_once ('functions.php');

$folder =  'files/';//для загрузки

/* загрузка файлов
*/
if (isset($_FILES)) { 
	$file_xml = $folder . basename($_FILES['xml']['name']);
	$file_pdf = $folder . basename($_FILES['pdf']['name']);
	if (is_executable($file_xml)) {
    echo $file_xml.' является исполняемым!';
}
	if (is_executable($file_pdf)) {
    echo $file_pdf.' является исполняемым!';
}
	if (move_uploaded_file($_FILES['xml']['tmp_name'], $file_xml)) {
		echo "файл корректен и был успешно загружен.\n";
	} else {
		echo "возможная атака с помощью загрузки!\n";
	}
	if (move_uploaded_file($_FILES['pdf']['tmp_name'], $file_pdf)) {
		echo "файл корректен и был успешно загружен.\n";
	} else {
		echo "возможная атака с помощью загрузки!!\n";
	}
}

/* чтение xml
*/
	$xml = simplexml_load_file($file_xml, $class_name = "SimpleXMLElement");
	$date = $xml->Date;
	$dateSign = (string)$xml->Date; // дата подписания вида 09.02.2015
	$dateSign = chDate($dateSign); // преобразуем
	
	$number = $xml->Number;//  номер НПА
	$number =iconv("UTF-8","windows-1251", $number);
	
	$name = (string)$xml->Name;// название НПА
	$name =iconv("UTF-8","windows-1251", $name);
	$keys = forSearch($name); // формируем слова для поиска
	echo $name.': '.$keys.'</li>';
	
	$docType = $xml->Kind;// тип НПА
	$docType =iconv("UTF-8","windows-1251", $docType);
	if ($docType == 'Постановление') $docType=1;
	if ($docType == 'Распоряжение') $docType=2;
	if ($docType == 'Приказ') $docType=3;
	if ($docType == 'Закон') $docType=4;
	if ($docType == 'Мониторинг правоприменения') $docType=5;
	if ($docType == 'Обзор федерального и областного законодательства') $docType=6;
	
	$authority = $xml->SignatoryAuthorities[0]->SignatoryAuthority['Name'];// орган власти
	$authority =iconv("UTF-8","windows-1251", $authority);
	if ($authority == 'орган власти 1') $authority='00';
	if ($authority == 'орган власти 2') $authority='01';
	
	$datePub = date("Ymd H:i:s"); // дата публикации

	$pubID = PubID($authority, $conn); // ID публикации
	
	$signerName =  trim($xml->Signer); // подписант
	$signerName =iconv("UTF-8","windows-1251", $signerName);
	if ($signerName) $signerID = signer($signerName, $conn); // находим ID подписанта в базе
	if ($signerID) { // если подписант есть
		$pdf = file_get_contents($file_pdf);
		$pdf = pdfstr($pdf); // преобразуем файл pdf в бинарную строку для записи в БД
		
		$query =  "INSERT into Acts (PubID, DatePub, DateSign, Number, Name, DocTypeID, AuthorityID, PdfFile, SignerID, IsPublished, NameForSearch) values ('".$pubID."', '".$datePub."', '".$dateSign."', '".$number."', '".$name."', ".$docType.", '".$authority."', 0x".$pdf.", ".$signerID.", 1, '".$keys."');";//
		sqlsrv_query( $conn, $query);
	}
	
// Close the connection.
	sqlsrv_close( $conn);
?>
 </body>
</html>
