<?php

function curlNew($url, $post=null, $header=false, $headers=null, $proxy=null){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($post){
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    if($headers){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_HEADER, $header);
	curl_setopt($ch, CURLOPT_PROXY, $proxy);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);


    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function getCookies($get){
	preg_match_all('/Set-Cookie: (.*);/U',$get,$temp);
	$cookie = $temp[1];
	$cookies = implode('; ',$cookie);
	return $cookies;
}

function save($string, $usuario=null) {

	if(!is_array($string)) {
		return false;
	}

	if(!$usuario) {
		$tudo = ler();
		if(!is_array($tudo)) {
			$tudo = $string;
		}else{
			$tudo = array_merge($tudo, $string);
		}
	}else{
		$string = array(
			"$usuario" => $string
		);

		$tudo = ler();
		$tudo = array_merge($tudo, $string);
	}

	$string  = json_encode($tudo);
    $fp      = fopen(__DIR__."/../config/config.json", "w+"); 
    $escreve = fwrite($fp, $string);
    fclose($fp);
}

function ler($usuario=null) {
    $arquivo = fopen(__DIR__.'/../config/config.json','r');
    if ($arquivo == false) die('Nao foi possivel abrir o arquivo.');

    $linha = fgets($arquivo);
    fclose($arquivo);

    $dados = json_decode($linha, true);

    if(isset($usuario)) {

		if(!is_array($dados[$usuario])) {
			return false;
		}

	    if(isset($dados[$usuario])) {
	    	$res = $dados[$usuario];
	    	return $res;
	    }
	}
	if(!is_array($dados)) {
		return false;
	}
    return $dados;
}

function savePlaca($string, $placa) {
	if(!is_array($string)){
		die('string invalida');
	}
	$string = json_encode($string);
    $fp = fopen(__DIR__."/../.cache/{$placa}.json", "w+"); 
    $escreve = fwrite($fp, $string);
    fclose($fp);
}

function lerPlaca($placa) {
	$dir = __DIR__."/../.cache/{$placa}.json";

	if(file_exists($dir)) {
		return json_decode(file_get_contents($dir), true);
	}else{
		return false;
	}
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

function corta($str, $left, $right) {
    $str = substr ( stristr ( $str, $left ), strlen ( $left ) );
	@$leftLen = strlen ( stristr ( $str, $right ) );
	$leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
	$str = substr ( $str, 0, $leftLen );
	return $str;
}

function clearStr($str) {
	if(!is_string($str)) {
		return $str;
	}
	if(!isset($str)) {
		return null;
	}elseif(is_array($str)){
		return $str;
	}elseif($str == false) {
		return null;
	}

	$str = xss($str);
	$str = rtrim($str);
	$str = ltrim($str);
	$str = str_replace(array("\n", "\t", "  ", "	", "\r", "(", ")", ";", ">", "<", "$"), '', $str);
	$str = utf8_decode($str);
	return $str;
}

function contarUsado($usuario, $tipo) {
	$dados = ler($usuario);
	if($tipo == 'vec') {
		$dados['usado_vec'] = $dados['usado_vec'] + 1;
	}elseif($tipo == 'con') {
		$dados['usado_con'] = $dados['usado_con'] + 1;
	}else{
		$dados['usado'] = $dados['usado'] + 1;
	}
	$dados['update'] = date("Y-m-d H:i:s");

	save($dados, $usuario);
}

function zerarUsado($usuario, $tipo) {
	$dados = ler($usuario);

	if($tipo == 'vec') {
		$dados['usado_vec'] = 0;
	}elseif($tipo == 'con') {
		$dados['usado_con'] = 0;
	}else{
		$dados['usado'] = 0;
	}

	$dados['update'] = date("Y-m-d H:i:s");
	save($dados, $usuario);
}

function saveCookie($cookie, $usuario) {
	$dados = ler($usuario);
	$dados['cookie'] = $cookie;
	$dados['update'] = date("Y-m-d H:i:s");
	save($dados, $usuario);
}

function lerCookie($usuario) {
	$dados = ler($usuario);
	return $dados['cookie'];
}

function saveStatus($status, $usuario) {
	$dados = ler($usuario);
	$dados['status'] = $status;
	$dados['update'] = date("Y-m-d H:i:s");
	save($dados, $usuario);
}

function saveStatusServicos($status, $usuario, $tipo=null) {
	$dados = ler($usuario);
	if(isset($tipo)) {
		if($tipo == 'con') {
			$dados['status_con'] = $status;
		}else{
			$dados['status_vec'] = $status;
		}
	}else{
		$dados['status'] = $status;
	}
	$dados['update'] = date("Y-m-d H:i:s");
	save($dados, $usuario);
}

function lerStatus($usuario) {
	$dados = ler($usuario);
	return $dados['status'];
}

function saveProxy($proxy, $usuario) {
	$dados = ler($usuario);
	$dados['proxy'] = $proxy;
	$dados['update'] = date("Y-m-d H:i:s");
	save($dados, $usuario);
}

function lerProxy($usuario) {
	$dados = ler($usuario);
	return $dados['proxy'];
}

function saveLimites($li_con, $us_con, $li_vec, $us_vec, $usuario) {
	$dados = ler($usuario);
	$dados['limite_vec'] = $li_vec;
	$dados['usado_vec']  = $us_vec;
	$dados['limite_con'] = $li_con;
	$dados['usado_con']  = $us_con;
	$dados['update'] = date("Y-m-d H:i:s");
	//saveLimites($limite_con, $usado_con, $limite_vec, $usado_vec, $usuario);

	save($dados, $usuario);
}


