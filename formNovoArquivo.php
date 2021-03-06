<?php

require_once("cfg.php");
require_once("bd.php");
require_once("file.class.php");
?>
<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Planeta ROODA 2.0</title>
<!--
<link type="text/css" rel="stylesheet" href="planeta.css" />
<link type="text/css" rel="stylesheet" href="portfolio.css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="../../planeta.js"></script>
<script type="text/javascript" src="portfolio.js"></script>
-->
</head>

<body>

<div id="addFileDiv">
	<form name="addFileForm" method="post" enctype="multipart/form-data">
		<ul>
			<li><div>titulo do Arquivo</div><input type="text" name="tituloArquivo" align="left"/></li>
			<li><div>autor</div><input type="text" name="autorArquivo" align="left"/></li>
			<li><div>tags</div><input type="text" name="tagsArquivo" align="left"/></li>
			<li><div>Arquivo</div><input type="file" name="arquivo" align="left"/></li>
			<div>
				<input type="image" onClick="" src="images/botoes/bt_cancelar.png" align="left"/>
				<input type="image" onClick="addFileForm.submit()" src="images/botoes/bt_confirm.png" align="right"/>
			</div>
		</ul>
	</form>
	
</div>

</body>

<?php	
    if($_FILES['arquivo']['size'] > 0){
		$consulta = new conexao();
		$tipoPortfolio = TIPOPORTFOLIO;
		$projeto_id = (int) $_GET['projeto_id'];
		$titulo = $consulta->sanitizaString($_POST['tituloArquivo']);
		$tags = $consulta->sanitizaString($_POST['tagsArquivo']);
		$autor = $consulta->sanitizaString($_POST['autorArquivo']);
		$fileName = $consulta->sanitizaString($_FILES['arquivo']['name']);
		$tmpName  = $_FILES['arquivo']['tmp_name'];
		$fileSize = (int) $_FILES['arquivo']['size'];
		$fileType = $consulta->sanitizaString($_FILES['arquivo']['type']);

		//echo $_POST['tituloArquivo']."    ".$_POST['tagsArquivo']."    ".$_POST['autorArquivo']."    ".$fileName."    ".$fileSize."   ".$fileType;
	
		$fp      = fopen($tmpName, 'r');
		$content = fread($fp, filesize($tmpName));
		$content = $consulta->sanitizaString($content);
		fclose($fp);

		global $tabela_arquivos;
		$consulta->solicitar("INSERT INTO $tabela_arquivos
							  (  titulo ,  nome     ,  autor ,  tipo     ,  tamanho  ,  arquivo ,  tags , dataUpload, funcionalidade_tipo, funcionalidade_id) VALUES
							  ('$titulo','$fileName','$autor','$fileType','$fileSize','$content','$tags', NOW()     ,'$tipoPortfolio', '$projeto_id'  )");
		

		
		echo "<br>File ".$_FILES['arquivo']['name']." uploaded<br>";	
	}
?>
