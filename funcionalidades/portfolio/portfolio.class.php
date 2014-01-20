<?php
require_once("../../cfg.php");
require_once("../../bd.php");

class post{
	private $id;
	private $projeto_id;
	private $user_id;
	private $titulo;
	private $texto;
	private $tags;
	private $dataCriacao;
	private $dataUltMod;

	function __construct($id, $dados = false){
		if($dados !== false){
			$this->id			= $dados['id'];
			$this->projeto_id	= $dados['projeto_id'];
			$this->user_id		= $dados['user_id'];
			$this->titulo		= $dados['titulo'];
			$this->texto		= $dados['texto'];
			$this->tags			= $dados['tags'];
			$this->dataCriacao	= $dados['dataCriacao'];
			$this->dataUltMod	= $dados['dataUltMod'];
		}else{
			$this->carrega($id);
		}
	}

	function carrega($id){
		global $tabela_portfolioPosts;
		$q = new conexao();
		$q->solicitar('SELECT * FROM $tabela_portfolioPosts WHERE id = $id');

		if ($q->erro != ""){
			$this->id			= "Esse post não existe!";
			$this->projeto_id	= "Esse post não existe!";
			$this->user_id		= "Esse post não existe!";
			$this->titulo		= "Esse post não existe!";
			$this->texto		= "Esse post não existe!";
			$this->tags			= "Esse post não existe!";
			$this->dataCriacao	= "Esse post não existe!";
			$this->dataUltMod	= "Esse post não existe!";
		}

		$this->id			= $dados['id'];
		$this->projeto_id	= $dados['projeto_id'];
		$this->user_id		= $dados['user_id'];
		$this->titulo		= $dados['titulo'];
		$this->texto		= $dados['texto'];
		$this->tags			= $dados['tags'];
		$this->dataCriacao	= $dados['dataCriacao'];
		$this->dataUltMod	= $dados['dataUltMod'];
	}

	function salvar(){
		$q = new conexao();

		$this->projeto_id = $q->sanitizaString($this->projeto_id);
		$this->user_id = $q->sanitizaString($this->user_id);
		$this->titulo = $q->sanitizaString($this->titulo);
		$this->texto = $q->sanitizaString($this->texto);
		$this->tags = $q->sanitizaString($this->tags);
		$this->dataCriacao = $q->sanitizaString($this->dataCriacao);
		$this->dataUltMod = $q->sanitizaString($this->dataUltMod);

		if($this->existe){
			$query = "UPDATE $tabela_portfolioPosts SET 
				projeto_id = '$this->projeto_id',
				user_id = '$this->user_id',
				titulo = '$this->titulo',
				texto = '$this->texto',
				tags = '$this->tags',
				dataCriacao = '$this->dataCriacao',
				dataUltMod = '$this->dataUltMod'
			WHERE id = '$this->id'";
		}else{
			$query = "INSERT INTO $tabela_portfolioPosts VALUES(
				'$this->projeto_id',
				'$this->user_id',
				'$this->titulo',
				'$this->texto',
				'$this->tags',
				'$this->dataCriacao',
				'$this->dataUltMod')";
		}
		
		$q->solicitar($query);
		if($q->erro == ""){
			die("N&atilde;o foi possivel SALVAR o post de id '$this->id'.");
		}
	}
	function getId(){return $this->id;}
	function getIdProjeto(){return $this->projeto_id;}
	function getIdUsuario(){return $this->user_id;}
	function getTitulo(){return $this->titulo;}
	function getTexto(){return $this->texto;}
	function getTags(){return $this->tags;}
	function getDataCriacao(){return $this->dataCriacao;}
	function getDataUltMod(){return $this->dataUltMod;}

	function setId($arg){$this->id = $arg;}
	function setIdProjeto($arg){$this->projeto_id = $arg;}
	function setIdUsuario($arg){$this->user_id = $arg;}
	function setTitulo($arg){$this->titulo = $arg;}
	function setTexto($arg){$this->texto = $arg;}
	function setTags($arg){$this->tags = $arg;}
	function setDataCriacao($arg){$this->dataCriacao = $arg;}
	function setDataUltMod($arg){$this->dataUltMod = $arg;}

	function geraHtmlPost(){
		$html = "
		<div class=\"cor".alterna()."\" id=\"postDiv".$this->id."\">
					<ul class=\"sem_estilo\">
						<li class=\"tabela_port\">
							<span class=\"titulo\">
								<div class=\"textitulo\">".$this->titulo."</div>
							</span>
							<span class=\"data\">
								".$this->dataCriacao."
								<button type=\"button\" class=\"bt_excluir\" onclick=\"ROODA.ui.confirm('Tem certeza que deseja apagar este post?',function () { deletePost(".$this->id."); });\">Excluir</button>
							</span>
						</li>
						<li class=\"tabela_port postagem\">
						<p>
							".$this->texto."
						</p>
						</li>
						<li class=\"tabela_port\">
							<a class=\"bt_abre_coment\" onclick=\"abreComentarios($this->id)\" id=\"abre_coment_$this->id\">Ver comentários</a>
						</li>
					</ul>
				</div>
		";

		return $html;
	}
}


class projeto{
	private $id = 0;
	private $titulo = "";
	private $dataCriacao;
	private $dataEncerramento;
	private $ownersIds = array();

	private $posts = array();
	private $tags = array();

	private $existe = 0;
	private $turma = 0;

	function __construct(	$id = 0,
							$titulo = "",
							$palavras = "",
							$dataCriacao = 0,
							$dataEncerramento = 0,
							$ownersIds = array()
						){
		if($id === 0){
			$this->id = 0;
			$this->titulo = $titulo;
			$this->palavras = explode(';', $palavras);
			$this->dataCriacao = $dataCriacao;
			$this->dataEncerramento = $dataEncerramento;
			$this->ownersIds = is_array($ownersIds) ? $ownersIds : explode(';', $ownersIds);
		}else{
			$this->carrega($id);
		}
	}

	function getTurma(){return $this->turma;}
	function getDataCriacao(){return $this->dataCriacao;}
	function getDataEncerramento(){return $this->dataEncerramento;}
	function getPalavras(){return $this->palavras;}
	function getPalavrasString(){return implode(', ', $this->palavras);}

	function carrega($idProjeto){
		global $tabela_portfolioProjetos;
		$q = new conexao();
		$idProjeto = $q->sanitizaString($idProjeto);
		$q->solicitar("SELECT * FROM $tabela_portfolioProjetos WHERE id = $idProjeto");

		if($q->registros > 0){
			$this->id = $idProjeto;
			$this->titulo = $q->resultado['titulo'];
			$this->palavras = explode(';', $q->resultado['tags']);
			$this->dataCriacao = $q->resultado['dataCriacao'];
			$this->dataEncerramento = $q->resultado['dataEncerramento'];
			$this->ownersIds = explode(";", $q->resultado['owner_ids']);
			$this->existe = 1;

			$this->carregaPosts();
		}else{
			die("Esse projeto não existe.");
		}
	}

	// Confere se o usuário é dono
	function ehDono($userId){
		if(in_array($userId, $this->ownersIds)){
			return true;
		}else{
			return false;
		}
	}

	function carregaPosts(){
		global $tabela_portfolioPosts;
		$q = new conexao();
		$q->solicitar("SELECT * FROM $tabela_portfolioPosts WHERE projeto_id = ".$this->id);

		for($i=0; $i < $q->registros; $i++){
			$newPost = new post(0, $q->resultado);

			array_push($this->posts, $newPost);
			$q->proximo();
		}
	}

	function salvar(){
		global $tabela_portfolioPosts; global $tabela_portfolioProjetos;

		$q = new conexao();

		$this->id = $q->sanitizaString($this->id);
		$this->titulo = $q->sanitizaString($this->titulo);
		$palavrasImplodido = $q->sanitizaString(implode(';', $this->palavras));
		$this->dataCriacao = $q->sanitizaString($this->dataCriacao);
		$this->dataEncerramento = $q->sanitizaString($this->dataEncerramento);
		$ownersIdsImplodido = $q->sanitizaString(implode(';', $this->ownersIds));
		$this->turma = $q->sanitizaString($this->turma);

		if($this->existe){
			$query = "UPDATE $tabela_portfolioProjetos SET 
				titulo = $this->titulo,
				tags = $palavrasImplodido,
				owner_id = $ownersIdsImplodido,
				dataCriacao = $this->dataCriacao,
				dataEncerramento = $this->dataEncerramento,
				turma = $this->turma
			WHERE
				id = $this->id";
		}else{
			$query = "INSERT INTO $tabela_portfolioPosts VALUES(
				'$this->id',
				'$this->titulo',
				'$palavrasImplodido',
				'1',
				'$this->dataCriacao',
				'$this->dataEncerramento',
				'$ownersIdsImplodido',
				'$this->turma')";
		}
		
		if($q->erro == ""){
			$numeroPosts = count($this->posts);

			for($i=0; $i < $numeroPosts; $i++){
				$this->posts[$i]->salvar();
			}
		}else{
			die("Erro ao salvar o projeto, por favor tente novamente em um momento.");
		}
	}
}