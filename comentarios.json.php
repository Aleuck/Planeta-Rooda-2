<?php
// ARQUIVO GERAL PARA TODAS AS FUNCIONALIDADES, VERIFIQUE O ARQUIVO DE MESMO NOME NA FUNCIONALIDADE DESEJADA
if (   !class_exists("Comentario")
	|| !function_exists("tituloDaRef")
	|| !function_exists("turmaDaRef")
	|| !function_exists("usuarioDaRef")) {
	exit("Uso inadequado.");
}
/**
* permissoesComentarios(@param idUsuario, @param idTurma)
* -- retorna uma array associativa com as permissoes do usuario
* @return array(
*         	'visualizar' => bool,
*         	'comentar' => bool,
*         	'excluir' => bool
*         )
*/
function permissoesComentarios($idRef, $usuario) {
	if (is_numeric($usuario)) {
		$usuario_id = (int) $usuario;
		$usuario = new Usuario();
		$usuario->openUsuario($usuario_id);
	}
	if (!is_object($usuario))
		throw new Exception("Error Processing Request", 1);

	if (get_class($usuario) !== 'Usuario')
		throw new Exception("Error Processing Request", 1);
	$return  = array('ver' => false, 'comentar' => false, 'excluir' => false);;

	$turma = turmaDaRef($idRef);
	if (!$usuario->pertenceTurma($turma)) {
		// usuario nao pertence à turma, portanto nao possui nenhuma permissao.
		$return['aaa'] = 3;
		return $return;
	}
	$perm = checa_permissoes(FUNCIONALIDADE, $turma);
	if ($perm) {
		if ($usuario->podeAcessar($perm[PERM_COMENT_VER], $turma)) {
			$return['ver'] = true;
		}
		if ($usuario->podeAcessar($perm[PERM_COMENT_INSERIR], $turma)) {
			$return['comentar'] = true;
		}
		if ($usuario->podeAcessar($perm[PERM_COMENT_EXCLUIR], $turma)
			|| $usuario->getId() === usuarioDaRef($idRef)) {
			// usuario deve poder excluir se tiver permissao
			// ou for quem postou o objeto comentado (post, material)
			$return['excluir'] = true;
		}
	}

	return $return;
}

// ============================================================================

header("Content-Type: application/json; charset=UTF-8");
$json = array();

$usuario = usuario_sessao();
if ($usuario === false) {
	exit('{"erro":"você não está logado","usuario":false}');
}
$json['usuario'] = $usuario->getSimpleAssoc();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$idRef = isset($_GET['idRef']) ? (int) $_GET['idRef'] : 0;
$ultimo = isset($_GET['ultimo']) ? (int) $_GET['ultimo'] : 0;
$acao = isset($_GET['acao']) ? trim($_GET['acao']) : "";
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : "";

// keep it simple, stupid!
switch ($acao) {
	// retorna usuario com permissoes de comentario na turma/funcionalidade
	case 'permissoes':
		$turma = isset($_GET['turma']) ? (int) $_GET['turma'] : 0;
		$json['usuario']['permissoes'] = permissoesComentarios($usuario, $turma);
		break;
	case 'stats':
		$json['turma'] = turmaDaRef($idRef);
		$permissoes = permissoesComentarios($idRef, $usuario);
		$json['permissoes'] = $permissoes;
		if (!$permissoes['ver']) {
			$json['erro'] = 'Você não tem permissão para ver estes comentários.';
			break;
		}
		$json['idRef'] = $idRef;
		$json['idUsuario'] = usuarioDaRef($idRef);
		$json['titulo'] = tituloDaRef($idRef);
		$json['numComentarios'] = Comentario::numeroComentarios($idRef);
		$json['ultimoId'] = Comentario::ultimoId($idRef);
		$json['novosComentarios'] = Comentario::numeroComentarios($idRef,$ultimo);
		break;
	// retorna lista de comentarios do recurso
	case 'listar':
		$permissoes = permissoesComentarios($idRef, $usuario);
		$json['permissoes'] = $permissoes;
		if (!$permissoes['ver']) {
			$json['erro'] = 'Você não tem permissão para ver estes comentários.';
			break;
		}
		$json['idRef'] = $idRef;
		$json['turma'] = turmaDaRef($idRef);
		$json['comentarios'] = array();
		$comentario = new Comentario();
		$comentario->abrirComentarios($idRef, $ultimo);
		while ($comentario->existe()) {
			$json['comentarios'][] = $comentario->getAssoc();
			$comentario->proximo();
		}
		break;

	case 'listarIds' :
		$permissoes = permissoesComentarios($idRef, $usuario);
		if (!$permissoes['ver']) {
			$json['erro'] = 'Você não tem permissão para ver estes comentários.';
			break;
		}
		$json['idRef'] = $idRef;
		$json['turma'] = turmaDaRef($idRef);
		$json['ids'] = Comentario::listaIds($idRef);
		break;

	case 'enviar':
		$permissoes = permissoesComentarios($idRef, $usuario);
		if (!$permissoes['comentar']) {
			$json['erro'] = 'Você não tem permissão para comentar aqui.';
			break;
		}
		$json['turma'] = turmaDaRef($idRef);
		$comentario = new Comentario();
		try {
			$comentario->setIdRef($idRef);
			$comentario->setUsuario($usuario);
			$comentario->setMensagem($mensagem);
			$comentario->salvar();
		} catch (Exception $e) {
			$json['erro'] = $e->getMessage();
			break;
		}
		$json['comentario'] = $comentario->getAssoc();
		break;
	
	case 'excluir':
		try {
			$comentario = new Comentario($id);
		} catch (Exception $e) {
			$json['erro'] = $e->getMessage();
			break;
		}
		$idRef = $comentario->getIdRef();
		$permissoes = permissoesComentarios($idRef, $usuario);
		if (!$permissoes['excluir']
			&& $usuario->getId() !== usuarioDaRef($idRef)
			&& $usuario->getId() !== $comentario->getIdUsuario()) {
			$json['erro'] = 'Você não tem permissão para excluir esse comentário.';
			break;
		}
		try {
			$comentario->excluir();
		} catch (Exception $e) {
			$json['erro'] = $e->getMessage();
			break;
		}
		$json['comentarioExcluido'] = $id;
		break;
	
	default:
		$json['erro'] = 'Nenhuma ação foi solicitada.';
		break;
}
echo json_encode($json);
?>