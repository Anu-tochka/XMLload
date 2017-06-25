<?php
header('Content-type: text/html; charset=windows-1251');
require_once ('config.php');
require_once ('functions.php');

$folder =  'files/';//��� ��������

/* �������� ������
*/
if (isset($_FILES)) { 
	$file_xml = $folder . basename($_FILES['xml']['name']);
	$file_pdf = $folder . basename($_FILES['pdf']['name']);
	if (is_executable($file_xml)) {
    echo $file_xml.' �������� �����������!';
}
	if (is_executable($file_pdf)) {
    echo $file_pdf.' �������� �����������!';
}
	if (move_uploaded_file($_FILES['xml']['tmp_name'], $file_xml)) {
		echo "���� ��������� � ��� ������� ��������.\n";
	} else {
		echo "��������� ����� � ������� ��������!\n";
	}
	if (move_uploaded_file($_FILES['pdf']['tmp_name'], $file_pdf)) {
		echo "���� ��������� � ��� ������� ��������.\n";
	} else {
		echo "��������� ����� � ������� ��������!!\n";
	}
}

/* ������ xml
*/
	$xml = simplexml_load_file($file_xml, $class_name = "SimpleXMLElement");
	$date = $xml->Date;
	$dateSign = (string)$xml->Date; // ���� ���������� ���� 09.02.2015
	$dateSign = chDate($dateSign); // �����������
	
	$number = $xml->Number;//  ����� ���
	$number =iconv("UTF-8","windows-1251", $number);
	
	$name = (string)$xml->Name;// �������� ���
	$name =iconv("UTF-8","windows-1251", $name);
	$keys = forSearch($name); // ��������� ����� ��� ������
	echo $name.': '.$keys.'</li>';
	
	$docType = $xml->Kind;// ��� ���
	$docType =iconv("UTF-8","windows-1251", $docType);
	if ($docType == '�������������') $docType=1;
	if ($docType == '������������') $docType=2;
	if ($docType == '������') $docType=3;
	if ($docType == '�����') $docType=4;
	if ($docType == '���������� ���������������') $docType=5;
	if ($docType == '����� ������������ � ���������� ����������������') $docType=6;
	
	$authority = $xml->SignatoryAuthorities[0]->SignatoryAuthority['Name'];// ����� ������
	$authority =iconv("UTF-8","windows-1251", $authority);
	if ($authority == '����� ������ 1') $authority='00';
	if ($authority == '����� ������ 2') $authority='01';
	
	$datePub = date("Ymd H:i:s"); // ���� ����������

	$pubID = PubID($authority, $conn); // ID ����������
	
	$signerName =  trim($xml->Signer); // ���������
	$signerName =iconv("UTF-8","windows-1251", $signerName);
	if ($signerName) $signerID = signer($signerName, $conn); // ������� ID ���������� � ����
	if ($signerID) { // ���� ��������� ����
		$pdf = file_get_contents($file_pdf);
		$pdf = pdfstr($pdf); // ����������� ���� pdf � �������� ������ ��� ������ � ��
		
		$query =  "INSERT into Acts (PubID, DatePub, DateSign, Number, Name, DocTypeID, AuthorityID, PdfFile, SignerID, IsPublished, NameForSearch) values ('".$pubID."', '".$datePub."', '".$dateSign."', '".$number."', '".$name."', ".$docType.", '".$authority."', 0x".$pdf.", ".$signerID.", 1, '".$keys."');";//
		sqlsrv_query( $conn, $query);
	}
	
// Close the connection.
	sqlsrv_close( $conn);
?>
 </body>
</html>
