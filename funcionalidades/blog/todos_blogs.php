<?php
require_once("../../cfg.php");
require_once("../../bd.php");
require_once("../../funcoes_aux.php");
require_once("../../usuarios.class.php");
require_once("blog.class.php");
require_once("../../file.class.php");
require_once("../../link.class.php");
require_once("../../reguaNavegacao.class.php");

$usuario = usuario_sessao();
if (!$usuario) { die("voce nao esta logado"); }

$usuario_id = $_SESSION['SS_usuario_id'];

$turma = (int) isset($_GET['turma']) ? $_GET['turma'] : 0;
$permissoes = checa_permissoes(TIPOBLOG, $turma);
if($permissoes === false){die("Funcionalidade desabilitada para a sua turma.");}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="utf-8" />
<title>Planeta ROODA 2.0</title>
<link type="text/css" rel="stylesheet" href="planeta.css" />
<link type="text/css" rel="stylesheet" href="blog.css" />
<script type="text/javascript" src="planeta.js"></script>
<script type="text/javascript" src="blog.js"></script>
<script type="text/javascript" src="../lightbox.js"></script>
<script src="../../js/thumbnailImages.js"></script>

<script>
function coment(){
	if (navigator.appVersion.substr(0,3) == "4.0"){ //versao do ie 7
		document.getElementById('ie_coments').style.width = 85 + '%';
		$('.bloqueia ul').css('margin-right','17px');
	}
}
</script>

<!--[if IE 6]>
<script type="text/javascript" src="planeta_ie6.js"></script>
<![endif]-->

</head>

<body onload="thumbnailImgsFromClass('lista_dir',250,300);atualiza('ajusta()');inicia();">
	<div id="topo">
		<div id="centraliza_topo">
			<?php 
				$regua = new reguaNavegacao();
				$regua->adicionarNivel("Webfólio", "blog_inicio.php", false);
				$regua->adicionarNivel("Todos Webfólios");
				$regua->imprimir();
			?>
			<p id="bt_ajuda"><span class="troca">OCULTAR AJUDANTE</span><span style="display:none" class="troca">CHAMAR AJUDANTE</span></p>
		</div>
	</div>
	
	<div id="geral">
	
	<!-- **************************
				cabecalho
	***************************** -->
	<div id="cabecalho">
		<div id="ajuda">
			<div id="ajuda_meio">
				<div id="ajudante">
					<div id="personagem"><img src="../../images/desenhos/ajudante.png" height=145 align="left" alt="Ajudante" /></div>
					<div id="rel"><p id="balao">O webfólio é um espaço pessoal para escrita, onde é possível anexar arquivos e links interessantes. Nele, você pode compartilhar diversos assuntos com seus colegas e permitir que eles, além de visualizar, publiquem comentários em seus posts e marquem suas reações ao lê-los.</p></div>
				</div>
			</div>
			<div id="ajuda_base"></div>
		</div>
	</div><!-- fim do cabecalho -->
	<div id="conteudo_topo"></div><!-- para a imagem de fundo do topo -->
	<div id="conteudo_meio"><!-- para a imagem de fundo do meio -->
	
	<!-- **************************
				conteudo
	***************************** -->
		
		<div id="conteudo"><!-- tem que estar dentro da div 'conteudo_meio' -->
			<div class="bts_cima">
				<a href="blog_inicio.php?turma=<?=$turma?>"><img src="../../images/botoes/bt_voltar.png" align="left"/></a>
			</div>
			<div id="meus_coletivos" class="bloco">
				<h1>TODOS OS WEBFÓLIOS</h1>
<?php
$bd = new conexao();
$bd->solicitar("SELECT * FROM blogblogs WHERE Turma = $turma");

$i = 0; // Para a classe da cor.

foreach($bd->itens as $b) {
	$b = new Blog($b['Id'], $turma);
	$i = ($i%2)+1;
?>
				<div class="cor<?=$i?>">
					<div class="lista_esq">
						<div class="imagem"><img src="../../image_output.php?blogpic=1&amp;file=<?=$b->getId()?>&amp;forum=0" /></div> <!--IMAGEM DO CRIADOR DO BROGUI VAI AQUI GENTE BOA-->
						<ul>
							<li><a href="blog.php?id=<?=$b->getId()?>&amp;turma=<?=$turma?>"><?=$b->getTitle()?></a></li>
							<li class="mensagens"><?=numeroMensagens($b->getSize())?></li>
						</ul>
					</div>
					<div class="lista_dir">
						<ul>
							<li><a href="blog.php?id=<?=$b->getId()?>&amp;turma=<?=$turma?>"><?=getTextSample($b->getId())?></a></li>
							<li class="criado_por">Criado Por: <?=getPrintableOwners($b->getId())?></li>
							<li>
								<div align="right">
<?php
/*if ($usuario->podeAcessar('blog_editarPosts')){
	echo"									<img class=\"clicavel\" alt=\"Editar\" src=\"images/botoes/bt_editar.png\" onclick=\"editaBlog(".$b->getId().")\"/>"
}
if ($usuario->podeAcessar('blog_editarPosts')){
	echo "									<img class=\"clicavel\" alt=\"Excluir\" src=\"images/botoes/bt_excluir.png\" onclick=\"deletaBlog(".$b->getId().")\" />";
}*/
?>
								</div>
							</li>
						</ul>
					</div>
				</div>
<?php
}
?>
			</div>
			<div class="bts_baixo">
				<a href="blog_inicio.php?turma=<?=$turma?>"><img src="../../images/botoes/bt_voltar.png" align="left"/></a>
			</div>
		</div><!-- Fecha Div conteudo -->
	</div><!-- Fecha Div conteudo_meio -->   
	<div id="conteudo_base">
	</div><!-- para a imagem de fundo da base -->
	</div><!-- fim da geral -->

</body>
</html>
