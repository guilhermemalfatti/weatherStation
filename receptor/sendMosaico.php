<?php
try {
    // PDO em ação!
    $pdo = new PDO ( "mysql:host=localhost;dbname=smsd", "root", "123456" );
 
    // Com o objeto PDO instanciado
    // preparo uma query a ser executada
    $stmt = $pdo->prepare(" SELECT id, substring(receivingdatetime, 12, 2) hora2gammu, SUBSTRING(textdecoded,5,4) ano,SUBSTRING(textdecoded,10,2) mes, SUBSTRING(textdecoded,13,2) dia,substring(textDecoded, 1, 3)codestacao, AVG(SUBSTRING(textdecoded,25,14)) temp ,avg(SUBSTRING(textdecoded,40,14)) umidade,SUBSTRING(textdecoded,16,2) hora FROM inbox where enviado = 0 group by SUBSTRING(textdecoded,16,2),substring(textDecoded, 1, 3), SUBSTRING(textdecoded,5,4), SUBSTRING(textdecoded,10,2),SUBSTRING(textdecoded,13,2);");//agrupa por hora, estacao e data
 
    // Executa query
    $stmt->execute();
 
    // lembra do mysql_fetch_array?
    //PDO:: FETCH_OBJ: retorna um objeto anônimo com nomes de propriedades que
    //correspondem aos nomes das colunas retornadas no seu conjunto de resultados
    //Ou seja o objeto "anônimo" possui os atributos resultantes de sua query
    $count = 0;
    foreach (  $stmt->fetchall ( PDO::FETCH_OBJ ) as $obj) {
		
		$count ++;
		//aqui envia para mosaico os dados
		$code = md5('miniColetor');
		$umidade = $obj->umidade;
		$codEstacao = $obj->codestacao;//codigo estação cadastrada no BD
		$temp = $obj->temp;
		if($obj->codestacao == 352)
			$time = $obj->hora;		
		else
			$time = $obj->hora2gammu;
		$data = $obj->ano.$obj->mes.$obj->dia;
		
		
		$ch = curl_init();
		$dados = array("data"=>$data, "code"=>$code, "umidade"=> number_format($umidade, 2, '.', ''), "codEstacao" => $codEstacao, "temp" => number_format($temp, 2, '.', ''), "time"=>$time);

		curl_setopt($ch, CURLOPT_URL, "http://mosaico.upf.br/~coletor/webStation/apps/receiveMiniColetor.php");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		$output = 	curl_exec($ch);
		curl_close($ch);
		
		if($output == "registro inserido.\n")
		if($obj->codestacao == 352)
			$stmt = $pdo->prepare("update inbox set enviado = 1 where enviado = 0 and SUBSTRING(textdecoded,16,2) = ". $obj->hora);
		else
			$stmt = $pdo->prepare("update inbox set enviado = 1 where enviado = 0 and SUBSTRING(receivingdatetime,12,2) = ". $obj->hora2gammu);
		
		// Executa query
		$stmt->execute();		
        
    }
    
    // fecho o banco
    $pdo = null;
    // tratamento da exeção
} catch ( PDOException $e ) {
    echo $e->getMessage ();
}
?>

