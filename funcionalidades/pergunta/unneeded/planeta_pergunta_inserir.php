<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Planeta ROODA 2.0</title>
<link type="text/css" rel="stylesheet" href="../../planeta.css" />
<link type="text/css" rel="stylesheet" href="../forum/forum.css" />
<link type="text/css" rel="stylesheet" href="planeta_pergunta.css" />
<script type="text/javascript" src="../../jquery.js"></script>
<script type="text/javascript" src="../../jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="../../planeta.js"></script>
<script type="text/javascript" src="../forum/forum.js"></script>
<!--[if IE 6]>
<script type="text/javascript" src="../../planeta_ie6.js"></script>
<![endif]-->

</head>

<body onload="atualiza('ajusta()');inicia();">
<img src="../../images/fundos/fundo4.png" width="100%" height="100%" style="position:fixed" />

<div id="topo">
	<div id="centraliza_topo">
        <p id="hist"><a href="#">Planeta ROODA</a> > <a href="#">Aluno Fulaninho de Tal</a> > <a href="#">Planeta Pergunta</a></p>
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
            	<div id="personagem"></div>
                <div id="rel"><p id="balao">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. 
				Etiam eget ligula eu lectus lobortis condimentum. Aliquam nonummy auctor massa. Pellentesque 
				habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p></div>
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
    <div id="conteudo"> <!-- tem que estar dentro da div 'conteudo_meio' -->
    <form>
    <div class="bts_cima">
    	<a href="planeta_pergunta_responder.html"><input type="image" src="../../images/botoes/bt_cancelar.png" align="left"/></a>
    	<input type="image" src="../../images/botoes/bt_confirm.png" align="right" />
    </div>
    
   
	
            
        <div id="criar_topico" class="bloco">
        <h1> INSERIR PERGUNTA</h1>
        
        <ul class="sem_estilo">
         <li class="espaco_linhas"></li>
                <pre style="font-family: Trebuchet MS, Tahoma, Verdana">Escolha o tipo de questão: </pre>
                <select>
                	<option>Múltipla Escolha</option>
                    <option>Subjetiva</option>
                    <option>Verdadeiro ou Falso</option>
                    </select>
                 </ul>
    </div>
        
	
    
    <div class="bts_baixo">
    	<a href="planeta_pergunta_responder.html"><input type="image" src="../../images/botoes/bt_cancelar.png" align="left"/></a>
    	<input type="image" src="../../images/botoes/bt_confirm.png" align="right" />
    </div>
    </form>
    </div>
    <!-- fim do conteudo -->
    
    
    

</div> <!-- fim do conteudo_meio -->  
<div id="conteudo_base"></div><!-- para a imagem de fundo da base -->


</div><!-- fim da geral -->

</body>
</html>
