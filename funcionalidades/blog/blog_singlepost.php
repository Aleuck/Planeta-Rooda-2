<?php
session_start();

require_once("blog.class.php");
require_once("../../cfg.php");
require_once("../../bd.php");
require_once("../../usuarios.class.php");
require_once("../../reguaNavegacao.class.php");
require_once("ArquivosPost.class.php");

global $tabela_posts;
global $tabela_blogs;

$idTurma	= (isset($_GET['turma']) and is_numeric($_GET['turma'])) ? $_GET['turma'] : die("Erro desconhecido, por favor recarregue a pagina tente novamente2.");

$permissoes = checa_permissoes(TIPOBLOG, $idTurma);
if($permissoes === false){
	die("Funcionalidade desabilitada para a sua turma.");
}

$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : die ("Por favor acesse essa pagina com uma id de post setada.");
$blog_id = isset($_GET['blog_id']) ? $_GET['blog_id'] : die ("Por favor acesse essa pagina com uma id de blog setada.");

$post = new Post();
$post->open($post_id);

$consulta = new conexao();
$consulta->solicitar("SELECT Title FROM $tabela_blogs WHERE Id = $blog_id");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="utf-8">
<title>Planeta ROODA 2.0</title>
<link type="text/css" rel="stylesheet" href="planeta.css" />
<link type="text/css" rel="stylesheet" href="blog.css" />
<script src="../../jquery.js"></script>
<script type="text/javascript" src="planeta.js"></script>
<script type="text/javascript" src="blog.js"></script>
<script type="text/javascript" src="blog_ajax.js"></script>
<script type="text/javascript" src="../lightbox.js"></script>

<!--[if IE 6]>
<script type="text/javascript" src="planeta_ie6.js"></script>
<![endif]-->

</head>

<body onload="atualiza('ajusta()');inicia();">

	<div id="descricao"></div>

<div id="fundo_lbox"></div>
	<div id="light_box" class="bloco">
	</div>

<div id="topo">
<div id="centraliza_topo">
			<?php 
				$regua = new reguaNavegacao();
				$regua->adicionarNivel("Blog", "blog_inicio.php", false);
				$regua->adicionarNivel("Post Único");
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
				<div id="rel"><p id="balao">São pequenos textos escritos pelo autor do blog que podem conter imagens, vídeos, arquivos anexados e <i>links</i>. Podem ser comentados por outras pessoas, desde que estas façam login.</p></div>
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
			<a href="blog.php?id=<?=$blog_id?>&amp;turma=<?=$idTurma?>"><img src="images/botoes/bt_voltar.png" align="left"/></a>
		</div>
		<div id="esq" class="margem_paginas">
			<div class="bloco" id="identsingle">
				<a style="text-decoration:none" href="blog.php?blog_id=<?=$post->blogId?>&amp;turma=<?=$idTurma?>"><h1><?=$consulta->resultado['Title']?></h1></a>

				<div class="cor1">
				<ul class="sem_estilo">
					<li class="tabela_blog">
						<span class="titulo">
							<?=$post->getTitle()?>
						</span>
						<span class="data">
							<?=$post->getDate()?>
						</span>
					</li>
					<li class="tabela_blog">
							<?=$post->getText()?>
					</li>
					<li class="tabela_blog">
						<?php
						$arquivo = new ArquivosPost();
						$arquivo->abrirPost($post->getId());
						?>
						<ul>
							<?php
							while ($arquivo->existe()) {
								echo '<li><a href="abreArquivo.php?a='.$arquivo->getId().'&amp;p='.$post->getId().'" target="_blank">'.$arquivo->getNome().'</a></li>';
								$arquivo->proximo();
							}
							?>
						</ul>
					</li>
					<li class="tabela_blog">
						Por <?=$post->getAuthor()->getName()?><br />
						Tags: <?=$post->printPostTags(); ?>
					</li>
					<li class="tabela_blog">
						<input type="hidden" name="comentarios" value="<?=$post->getId();?>">
					</li>
				</ul>
				</div>
			</div>
		</div>
	</div><!-- Fecha Div conteudo -->
	</div><!-- Fecha Div conteudo_meio -->
	<div id="conteudo_base">
	</div><!-- para a imagem de fundo da base -->
</div><!-- fim da geral -->
<script src="../../js/rooda.js"></script>
<script src="../../js/ajax.js"></script>
<script src="../../comentarios.js"></script>
</body>
</html>
