<?php
// !SQLINJECTION
session_start();

require_once("../../cfg.php");
require_once("../../bd.php");
require_once("../../funcoes_aux.php");
require_once("../../usuarios.class.php");
require_once("../../login.class.php");
require_once("../../file.class.php");	
require_once("../../link.class.php");
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Ovo de páscoa</title>
	<script language="javascript">
		function add_remove_owner (form){
			if (form.checked){
				window.top.window.add_owner(form.value);
				//alert ('got '+form.value);
			}
			else{
				window.top.window.remove_owner(form.value);
				//alert ('ungot '+form.value);
			}
		}
	</script>
</head>
<body>
<form method="get" action="edicao_blog_iframe.php">
	Pesquisar usuário via nome (Acentuação é importante): <input type="text" name="t" value="<?=$_GET['t']?>"/>
	<input type="submit" value="Pesquisar"/> <br />
</form>
<ul>
<?php
$donos = new conexao();
if (isset($_GET['t']) && $_GET['t'] != ''){
	$termo = $_GET['t'];
	$donos->solicitar("SELECT usuario_id, usuario_nome FROM $tabela_usuarios WHERE usuario_nome LIKE '%$termo%'");
} else {
	$donos->solicitar("SELECT usuario_id, usuario_nome FROM $tabela_usuarios");
}

for ($i=0; $i<count($donos->itens); $i++){
	if ($i%2){ // Se for impar
		echo '			<li class="user2">';}
	else { // It's par. Derp.
		echo '			<li class="user1">';}
		
		// id=\"".$donos->resultado['usuario_id']."\"
		// A linha acima pode vir a ser necessária.
	echo "<input type=\"checkbox\" id=\"".$donos->resultado['usuario_id']."\" value=\"".$donos->resultado['usuario_id'].';'.$donos->resultado['usuario_nome']."\" onclick=\"add_remove_owner(this)\" />".$donos->resultado['usuario_nome']."</li>\n";
	
	$donos->proximo();
}
?>
</ul>
</body>
</html>