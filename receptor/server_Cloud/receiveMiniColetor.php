<?php

	//este arquivo serve somente para receber os dados do mini coletor e gravar no BD mosaico PG
	include("funcApp.php");
	include("connectionPg.php");
	
	if($_POST){
		if($_POST['code'] == md5('miniColetor')){		
			$k = log((float) $_POST['umidade'] / 100) + (17.62 * (float) $_POST['temp']) / (243.12 + (float) $_POST['temp']);
			$val = 243.12 * $k / (17.62 - $k);
			$dewPoint = (float) number_format($val, 2, '.', '');
			
			$query = "INSERT INTO clima VALUES (".$_POST['codEstacao'].", cast(to_char(to_date('".$_POST['data']."','YYYYMMDD'),'YYYYDDD')as numeric), 
			'".$_POST['time'].":00:00', ".$_POST['temp'].", ".$_POST['umidade'].", null, null, NULL, null, NULL, null, NULL, NULL, NULL, 0, NULL, 
			NULL, NULL, NULL, ".$dewPoint.")";
			
			pg_send_query($con, $query);
			$retorno = pg_get_result($con);
			if (pg_result_error_field($retorno, PGSQL_DIAG_SOURCE_FUNCTION) == '_bt_check_unique') {
				echo "Duplicidade não permitida...\n";
			} else {
				echo "registro inserido.\n";
			}
		}
	}
?>
