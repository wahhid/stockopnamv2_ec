<?php
ob_start();
session_start();

$_SESSION['message'] = '';
$_SESSION['ean'] = '';
$_SESSION['productname'] = '';

include '../config/dbconnect.php';

if(!isset($_SESSION['user'])){
    header('Location: index.php');
}

function product($ean){
    $query = "SELECT * FROM product WHERE ean='" . $ean . "'";
    $rs = mysql_query($query);
    if($rs){
        if(mysql_num_rows($rs) == 1){
            $arr = mysql_fetch_array($rs);
            $result = array(
                 "success" => TRUE,
                "data" => $arr
            );
            return $result;
        }else{
            $result = array(
                "success" => FALSE,
                "error" => "Product not found"
            );
            return $result;
        }
    }else{
        $result = array(
            "success" => FALSE,
            "error" => mysql_error()
        );
        return $result;
    }    
}

if(isset($_POST['submit'])){
    if($_POST['ean'] != ''){
		$sql1 = mysql_query("select * from product where ean = '" . $_POST['ean'] . "'");
		$row1 = mysql_fetch_array($sql1);
		$arc = $row1['article'];
		$sql2 = mysql_query("select * from stockbin where storagebin = '" . $_SESSION['binname'] . "' and articleno = '" . $arc . "'");
		$cn = mysql_num_rows($sql2);
		if($cn == ''){
			echo 'Data Not Found';
		}else{
	        $result = product($_POST['ean']);
	        if($result['success']){            
	            $_SESSION['ean'] = $_POST['ean'];
	            $_SESSION['uom'] = $result['data']['uom'];            
	            $_SESSION['productname'] = $result['data']['productname'];
	            $_SESSION['article'] = $result['data']['article'];
	            $_SESSION['nopid'] = 0;
	            header('Location: inputbatch.php');
	        }else{
	            $_SESSION['ean'] = $_POST['ean'];
	            $_SESSION['uom'] = $product['uom'];
	            $_SESSION['productname'] = $product['productname'];
	            $_SESSION['article'] = $product['article'];
	            $_SESSION['nopid'] = 1;
	            header('Location: inputbatch.php');
	        }
		}
    }else{
        $_SESSION['message'] = "Product not found!";
    }           
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Stock System - Ranch Market</title>
    </head>
    <body>                
        <form method="POST">
            <table border="1">
                <tr>
                    <td>DC</td>
                    <td><?php echo $_SESSION['dc'];?></td>
                </tr>
                <tr>
                    <td>Periode</td>
                    <td><?php echo $_SESSION['periodename']; ?></td>
                </tr>
				<tr>
                    <td>Bin</td>
                    <td><?php echo $_SESSION['binname']; ?></td>
                </tr>
                <tr>
                    <td>Ean</td>
                    <td><input type="text" name="ean" value=""/></td>
                </tr>         
                <tr>
                    <td></td>
                    <td><input type="submit" name="submit" value="submit" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="closetrans.php">Close Transaction</a></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Session <?php echo $_SESSION['periodesession']; ?></td>
                </tr>                   
            </table>                             
        </form>        
        <?php
            echo $_SESSION['message'];
        ?>
    </body>
</html>
