<?php
require_once("../../cfg.php");
require_once("../../bd.php");
require_once("../../file.class.php");

$funcionalidade_id = $_GET['funcionalidade_id'];
$funcionalidade_tipo = $_GET['funcionalidade_tipo'];

if (is_numeric($funcionalidade_id) == false || is_numeric($funcionalidade_tipo) == false){
		die('RAAAAAAAAAA, pegadinha do Mallandro!'); // Sabe SQL injection?
	}

if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0){
	$fileName = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	
	
	echo("funcTipo=".$funcionalidade_tipo.NL);
	echo("funcID=".$funcionalidade_id.NL);
	echo("name=".$fileName.NL);
	echo("tipe=".$fileType.NL);
	echo("size=".$fileSize.NL);
	echo("tmp=".$tmpName.NL);
	
	
	$file = new File($funcionalidade_tipo, $funcionalidade_id,$fileName, $fileType, $fileSize, $tmpName);
	$file->upload();
	if ($file->temErro()){
		echo($file->getErrosString());
		$falha = $file->getErrosString();
		
		$location = '#';
	}else{
		echo("upload com sucesso".NL);
		
		global $tabela_arquivos;
		
		$consulta = new conexao();
		$consulta->solicitar("SELECT arquivo_id FROM $tabela_arquivos WHERE nome = '$fileName'");
		$falha = 0;

		//print_r($consulta);
	}
}

if (isset($_POST['gambiarra']) and $_POST['gambiarra'] == 3337333) { // Isso é um número primo.
echo "<script type='text/javascript'>
	window.top.window.previewArquivo(\"$falha\", \"$fileName\", \"".$consulta->resultado['arquivo_id']."\");
</script>";
} else {

$location='image_output.php';
$location.="?file=";
$location.=$consulta->resultado['arquivo_id'];

echo "<script type='text/javascript'>
	window.top.window.mostraPreviewImagem(\"$falha\", \"$fileName\", \"$location\");
</script>";}?>
