<?php
	require_once("../../cfg.php");
	require_once("../../bd.php");

	global $tabela_arquivos;
	
	if (isset($_GET['id']) and is_numeric($_GET['id']))
		$id = $_GET['id'];
	else
		die("N&atilde;o sei o que deu errado, mas n&atilde;o se preocupe. Nossa equipe de macacos altamente treinados est&aacute; tentando resolver o problema.");

	$consulta = new conexao();
	$consulta->connect();
	$consulta->solicitar("SELECT * FROM $tabela_arquivos WHERE arquivo_id = $id");
	
	if($consulta->registros != 0){
		$fileContent = $consulta->resultado["arquivo"];
		$nome = $consulta->resultado["nome"];
		$tipo = $consulta->resultado["tipo"];
		$tamanho = $consulta->resultado["tamanho"];
	
		if ($consulta->erro != "" and $fileContent != ""){
			echo "ERRO - \"".$consulta->erro."\"";
		} else {
			header("Content-length: $tamanho");
			header("Content-type: $tipo");
			header("Content-Disposition: attachment; filename=$nome");
			echo $fileContent;
		}
	} else{
		die("Arquivo n&atilde;o encontrado.");
	}
	
?>
