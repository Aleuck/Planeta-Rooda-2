<?php
require_once("cfg.php");
require_once("bd.php");




class File {
private $id;
private $link;				 //string com o endereco para o arquivo no servidor
private $nome;				 //nome do arquivo. Deve conter a extensao tamb�m 
private $tipo = "";
private $tamanho;
private $fileContent = "";	 //conteudo do arquivo
private $funcionalidade_tipo;	//tipo da funcionalidade a qual o arquivo pertence. 
								 //	Possibilidades: 1-blog, 2-portfolio, 3-biblioteca
private $funcionalidade_id;	//id da funcionalidade a qual o arquivo pertence 
private $erros = array();		//array de strings que guarda os erros, caso aja algum
private $download = false;	 //variavel que diz se se tem os dados necessarios para se fazer o download
private $upload = false;		 //variavel que diz se se tem os dados necessarios para se fazer o upload

 //aceita como parametros funcionalidade_tipo(integer > 0), funcionalidade_id(integer > 0) e 
 //nome(string), o que eh suficiente para efetuar download do BD, ou entao
 //aceita funcionalidade_tipo, funcionalidade_id, nome, tipo, tamanho e nome temporario do arquivo no servidor,
 //que eh o necessario para efetuar o upload
public function File($funcionalidade_tipo=-2, $funcionalidade_id=-2, $nome="", $tipo="", $tamanho=0, $fileNameServ=""){
	global $tabela_arquivos;
	if(($funcionalidade_tipo!==-2) and ($funcionalidade_id!==-2) and ($nome!=="") and ($tipo==="") and ($tamanho===0) and ($fileNameServ==="")){		
		$this->nome = $nome;
		$this->funcionalidade_tipo = $funcionalidade_tipo;
		$this->funcionalidade_id = $funcionalidade_id;
		$this->download = true;
	
	}
	else if (($funcionalidade_tipo>0) and ($funcionalidade_id>0) and ($nome!=="") and ($tipo!=="") and ($tamanho>0) and ($fileNameServ!=="")){
		$this->nome = $nome;
		$this->tipo = $tipo;
		$this->tamanho = $tamanho; 
		$this->funcionalidade_tipo = $funcionalidade_tipo;
		$this->funcionalidade_id = $funcionalidade_id;

		$file	= fopen($fileNameServ, 'r');
		$fileContent = fread($file, filesize($fileNameServ));
		$fileContent = addslashes($fileContent);
		$this->fileContent = $fileContent;
		fclose($file);
		$this->upload = true;
	}
	else{
		$this->erros[] = "ERRO - Parametros errados em File Constructor";
	}	
}	

public function download(){
	if ($this->download === true){
		global $tabela_arquivos;
		$nome = $this->nome;
		$funcionalidade_tipo = $this->funcionalidade_tipo;
		$funcionalidade_id = $this->funcionalidade_id;
		
		$consulta = new conexao($host=0,$base=0,$usuario=0,$senha=0);
		//$consulta->connect();
		$consulta->solicitar("SELECT * FROM $tabela_arquivos WHERE nome = '$nome' 
															 AND funcionalidade_tipo = '$funcionalidade_tipo' 
															 AND funcionalidade_id = '$funcionalidade_id';");
		
		$this->id = $consulta->resultado["arquivo_id"];
		$this->nome = $consulta->resultado["nome"];
		$this->tipo = $consulta->resultado["tipo"];
		$this->tamanho = $consulta->resultado["tamanho"];
		$this->fileContent = $consulta->resultado["arquivo"];
		$this->funcionalidade_tipo = $consulta->resultado["funcionalidade_tipo"];
		$this->funcionalidade_id = $consulta->resultado["funcionalidade_id"];
		$nome = $consulta->resultado["nome"];
		$tipo = $consulta->resultado["tipo"];
		$tamanho = $consulta->resultado["tamanho"];
		
		if ($consulta->erro !== ""){
			$this->erros[] = "ERRO - \"".$consulta->erro."\"";
		}
		else {
			header("Content-length: $tamanho");
			header("Content-type: $tipo");
			header("Content-Disposition: attachment; filename=$nome");
			echo $this->fileContent;
		}
		
	}
	else {
		$this->erros[] = "ERRO - dados incorretos ou insuficientes para efetuar download";
	}
	
}

/*
	public function getFileListArray($funcionalidade_tipo, $funcionalidade_id){
		$consulta = new conexao();
		$consulta->connect();
		
		
		$consulta->solicitar("SELECT nome
							FROM $tabela_arquivos 
							WHERE funcionalidade_tipo='$funcionalidade_tipo' 
								AND funcionalidade_id='$funcionalidade_id'");
		$retorno = new Array();
		for($i=0 ; $i<count($consulta->itens);$i++) {
			$retorno[] = $consulta->resultado['nome'];
			$consulta->proximo();
		}
		return $retorno;
	
	}*/
	//funcao que retorna true se aconteceu algum erro
	//retorna false caso contrario
	public function temErro(){
		if (count($this->erros) == 0){
			return false;
		}
		else return true;	
	}
	
	//funcao que retorna os erros que aconteceram
	public function getErrosArray(){
		return $this->erros;
	}
	
	public function getErrosString(){
		$erros = "";		
		for ($i = 0 ; $i < count($this->erros) ; $i++){		 
			if ($i < (count($this->erros) - 1) ){
				$erros .= $this->erros[$i]."<BR />";
			}
			else {
				$erros .= $this->erros[$i];
			}			
		}	
		return $erros;
	}
	

public function getNome() {
	return $this->nome;
}

public function getConteudoArquivo() {
	return $this->fileContent;
}	

public function getTamanho() {
	return $this->tamanho;	
}

public function getTipo(){
	return $this->tipo;
}
	
public function getFuncionalidadeTipo(){
	return $this->funcionalidade_tipo;
}
	
public function getFuncionalidadeId(){
	return $this->funcionalidade_id;
}

public function getId(){
	return $this->id;
}

//manda os meta-dados do arquivo pro Bd
//obs: retorna erro caso jah tenha no bd um arquivo de mesmo nome, funcionalidade_tipo e funcionalidade_id
public function upload(){	
	if ($this->upload === true){
		global $tabela_arquivos;
		$nome 					= $this->getNome();	
		$tipo					= $this->getTipo();
		$tamanho				= $this->getTamanho();
		$ConteudoArquivo		= $this->getConteudoArquivo();
		$funcionalidade_tipo 	= $this->getFuncionalidadeTipo();
		$funcionalidade_id 		= $this->getFuncionalidadeId();				
		
		$consulta = new conexao();
		$consulta->connect();
		$consulta->solicitar("SELECT * FROM $tabela_arquivos WHERE nome = '$nome' 
															 AND funcionalidade_tipo = '$funcionalidade_tipo' 
															 AND funcionalidade_id = '$funcionalidade_id';");
		if (count($consulta->itens) === 0){
			$consulta->solicitar("INSERT INTO $tabela_arquivos
								(nome , tipo, tamanho, arquivo, funcionalidade_tipo, funcionalidade_id)
						VALUES ('$nome', '$tipo', '$tamanho', '$ConteudoArquivo', '$funcionalidade_tipo','$funcionalidade_id');");	
			if ($consulta->erro !== ""){
			$this->erros[] = "ERRO - \"".$consulta->erro."\"";
			}
		
		}
		else {
			$this->erros[] = "ERRO - Arquivo ja existe no banco de dados";
		}
		
	}
	else {
		$this->erros[] = "ERRO - dados incorretos ou insuficientes para efetuar upload";
	}
}
//comentario_id,post_id,comentario_msg,comentario_data,owner_id
//funcao que retorna true se alguma propriedade (que nao seja o id) do File esta vazia
public function isAnyPropEmpty(){	
	if (($this->getNome() === "") or ($this->getTamanho()===0) or ($this->getArquivo()==="") or ($this->getFuncionalidadeTipo()===-2) or ($this->getFuncionalidadeId()===-2)){
	return true;
	}
	else return false;
}

private function limparFile(){
	$this->id = -1;	
	$this->nome = "";	
	$this->tamanho = 0;
	$this->tipo = "";
	$this->funcionalidade_tipo = 0;
	$this->funcionalidade_id = 0; 
}

public function toString(){	
	$saida= "id=".$this->getId();
	$saida .= ", nome=".$this->getNome();	
	$saida .= ", tamanho=".$this->getTamanho();	
	$saida .= ", tipo=".$this->getTipo();
	$saida .= ", funcionalidade_tipo=".$this->getFuncionalidadeTipo();
	$saida .= ", funcionalidade_id=".$this->getFuncionalidadeId();	
	return $saida;
}

}

?>
