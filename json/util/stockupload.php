<?php
ob_start();
session_start();
if(!isset($_SESSION['login'])){
    echo '{success: false,message:"Access Denied"}';    
    return;
}
include '../../config/dbconnect.php';

function deleteall($periode){
    $query = "DELETE FROM stockbin WHERE periode=" . $periode;
    return delete($query);
}

function isempty($var){
    if(empty($var)){
        return 0;
    }else{
        return $var;
    }
}


function insertstock($row,$periode){
	if(strlen($row[10])==7){
		$bc = '0'.$row[10];
	}else{$bc = $row[10];}
	$st = '00'.$row[2];
	$sl = '000'.$row[7];
    $query = "INSERT INTO stockbin (dc,periode,warehouse,pid,storagetype,itemno,storagebin,quantno,storageloc,articleno,description,batchno,stkcat,specialstock,countedqty,counteduom,stocksap,deff) VALUES (" .
            "'" . $_SESSION['dc'] . "'," .
            $periode . "," .
            "'" . $row[0] . "'," .
            isempty($row[1]) . "," .
            "'" . $row[2] . "'," .
            isempty($row[3]) . "," .
            "'" . $row[4] . "'," .
            isempty($row[5]) . "," .
            "'" . $row[7] . "'," .
            "'" . $row[8] . "'," .
            "'" . $row[9] . "'," .
            "'" . $bc . "'," .
            "'" . $row[11] . "'," .
            "'" . $row[12] . "'," .
            isempty($row[13]) . "," .                       
            "'" . $row[14] . "'," .
			"'" . $row[15] . "'," .
			"'" . $row[16]. "'" .
			
            ")";    
    $rs = mysql_query($query);
    if($rs){
       $result = array(
           "success" => TRUE,
           "data" => ''
       );
       return $result;
    }else{
       $result = array(
           "success" => FALSE,
           "error" => $query
       );
       return $result;        
    }
}

$returnResponse = $_REQUEST['returnResponse'];
sleep(1);

if ($returnResponse != "") {
    header('HTTP/1.0 '.$returnResponse.' Server status', true, $returnResponse);
    echo '{success:false, message:"Faked error from server", errors:{"photo-path":"The server returned this"}}';
} else {
    $periode = $_POST['periode'];
    $temp_file_name = $_FILES['stockfile']['tmp_name'];
    $original_file_name = $_FILES['stockfile']['name'];
    
    $ext = explode ('.', $original_file_name);
    $ext = $ext [count ($ext) - 1];
    $file_name = str_replace ($ext, '', $original_file_name);
    $new_name = '_'.$file_name . $ext;              
    
    if (move_uploaded_file($temp_file_name, '../../upload/' . $new_name)) {
        deleteall($periode);
        $file = fopen('../../upload/' . $new_name,"r");
        $totalrow = 0;
        $success = 0;
        $failed = 0;
        $message='';
        while(! feof($file)){
            $totalrow++;
            $row = fgetcsv($file,null,",","\"");
            $resultinsertstock = insertstock($row,$periode); 
            if($resultinsertstock['success']){
                $success++;            
            }else{
                $failed++;             
                $message .= $resultinsertstock['error'];
            }
        }
        fclose($file);           
        echo json_encode(array(
            "success" => true,
            "Message" => "Success : " . $success . ", Failed : " . $failed . ", Total : " . $totalrow             
        ));          
    } else {
        echo json_encode(array(
            "success" => false,
            "errorMessage" => "Error Process File"
        ));
    }                
}
mysql_close(); 
?>