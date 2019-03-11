<?php

ob_start();
ob_implicit_flush (TRUE);
ignore_user_abort (0);
require_once('config.php');
require_once("checkAuth.php");
$NomeServico = 'Checkcondutor';

$servico = new Sistema_Servicos();
$servico->setServico($NomeServico);

$control = new Sistema_ControlGeral();
$control->setServico($NomeServico);
$control->setIdUser($_SESSION['getId']);

if($control->Permissao() != true){
	$msg = 'Você nao tem permissão para acessar esse serviço!';
}else {
    $a = $control->getLimites();
    if($a['status'] != '1') {
    	$msg = 'Você não tem permissão para usar esse serviço.';
    }
    if($a['usado'] >= $a['limite']) {
    	$msg = 'Seu limite acabou, para adiquirir mais entre em contato!';
    }
}

if($dads['status'] == '0') {
	$msg = 'Indisponivel no momento!';
}

if(!isset($msg)) {
	if(date('H') >= 23) {
		if(date('H') == 23) {
			if(date('i') >= 59) {
				$msg = 'Horario de funcionamento, 08:00 até 23:59!';
			}
		}else{
			$msg = 'Horario de funcionamento, 08:00 até 23:59!';
		}
	}elseif(date('H') < 8) {
			$msg = 'Horario de funcionamento, 08:00 até 23:59!';
	}
}

if(isset($msg)){
	$tpl = file_get_contents('./tpls/checkCondutor/msg.html');	$tpl = str_replace('{{msg}}', $msg, $tpl);
	echo $tpl;
	die;
}


function xss($data, $problem='') {
	if(!is_string($data)) {
		return $data;
	}

	$data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strip_tags($data);
	if ($problem && strlen($data) == 0) {
		return ($problem);
	}
    return $data;
}

function curl($url,$cookies,$post,$referer=null,$header=1,$follow=false) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, $header);
	if ($cookies) curl_setopt($ch, CURLOPT_COOKIE, $cookies);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
	if(isset($referer)){ curl_setopt($ch, CURLOPT_REFERER,$referer); }
	else{ curl_setopt($ch, CURLOPT_REFERER,$url); }
	if(strlen($post) > 5)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
	$res = curl_exec( $ch);

	curl_close($ch); 
	return $res;
}

$url = 'https://checkbusca.com/sixapi/con.php?token=beb99570f3a3f81cf289a586e5abd89b&dados=';

if(isset($_POST['dados'])) {
    header('Content-type: application/json');
    $doc = xss($_POST['dados']);

    if(strlen($doc) > 3 && strlen($doc) < 15) {
    	$url = $url.$doc;
    	$dados =  curl($url, null, null, null, false);
    	if(!stristr($dados, '{')) {
	    	echo json_encode(array('msg' => 'nada_encontrado'));
    	}else{
    		if(stristr($dados, ':"nada encontrado"}')) {
		    	echo json_encode(array('msg' => 'nada_encontrado'));
    		}else{
    			if(stristr($dados, 'n_cnh')) {
			        $control->saveConsulta();
			        $comp = new Sistema_Verificacao();
			        $comp->setServico($NomeServico);
			        $comp->Computa();
	    			echo $dados;
    			}
    		}
    	}
    }else{
    	echo json_encode(array('msg' => 'nada_encontrado'));
    }
    die;
}


$tpl = file_get_contents('./tpls/checkCondutor/index.html');
$tpl = str_replace('Limite: <strong>xx</strong> - Usado: <strong>xx</strong>', "Limite: <strong>{$a['limite']}</strong> - Usado: <strong>{$a['usado']}</strong>", $tpl);
$tpl = str_replace(array("\n", "\r", "\t", "  ", "	"), '', $tpl);
echo $tpl;
die;
