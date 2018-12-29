<?php
session_start();
/**
 * Obtiene parámetros que hayan sido enviados por $_POST o $_GET
 * @param string $name Nombre del parámetro a obtener
 * @param string $default Opcional. Valor del parámetro si en caso se encuentra vacío.
 * @return string
 */
function getParam($name, $default = null) {
    if (array_key_exists($name, $_REQUEST)) {
        $param = $_REQUEST[$name];
        if ($default != null && isEmpty($param)) {
            return $default;
        }
        $db = DbManager::getMe();
        $param = $db->escape($param);
        return $param;
    }
    else
        return $default;
}
/**
 * Verifica si un valor esta vacio o es nulo. Retorna true o false
 * @param string $str
 * @return boolean 
 */
function isEmpty($str) {
    return (strlen(trim($str)) == 0) || ($str == "null" || $str == 'NULL');
}

function isNotSetOrEmpty($str){
    return !isset($str) || isEmpty($str);
}

function existsAndNotEmpty($key, $array){
    return isset($array[$key]) && !isEmpty($array[$key]);
}

/**
 * Setea los mensajes del inicio
 */
function messageMe(){
    if(!isEmpty(getParam("mess"))){
        $mess = getParam("mess");
        $type = " ";
        switch($mess){
            case "w":{
                $type = " alert-warning ";
                $mess = "Ha ocurrido un error. Intenta más tarde.";
                break;
            }
            case "s":{
                $type = " alert-success ";
                $mess = "El usuario ha sido creado exitosamente";
                break;
            }
            case "d":{
                $type = " alert-danger ";
                $mess = "Los datos ingresados son incorrectos";
                break;
            }
            case "p":{
                $type = " alert-danger ";
                $mess = "Las contraseñas no coinciden.";
                break;
            }
            case "nv":{
                $type = " alert-danger ";
                $mess = "¡Ups! Usuario no válido";
                break;
            }
            case "lo":{
                $type = " alert-info ";
                $mess = "¡Has salido de la aplicación exitosamente!";
                break;
            }
            case "gh":{
                $type = " alert-danger ";
                $mess = "Es una prueba de alerta";
                break;
            }
            case "conf":{
                $type = " alert-info ";
                $mess = "Gracias por confirmar que verificaras el perfil, un usuario temporal te ha sido enviado, para que verifiques la información de la persona que te puso como referencia. Seras contactado en 48horas.";
                break;
            }
            case "not_conf":{
                $type = " alert-info ";
                $mess = "La referencia ya fue confirmada o ha expirado, revisa tu correo para el usuario temporal";
                break;
            }
            case "a_u":{
                $type = " alert-info ";
                $mess = "El usuario que deseas crear ya existe";
                break;
            }
            case "sent":{
                $type = " alert-info ";
                $mess = "Gracias por tu mensaje. Nos pondremos en contacto a la brevedad.";
                break;
            }
            case "has_req": {
                $type = " alert-warning ";
                $mess = "Ya tienes una solicitud de reseteo de contraseña. Pronto nos pondremos en contacto";
                break;
            }
            case "req_sent": {
                $type = " alert-success ";
                $mess = "Tu solicitud de reseteo de contraseña ha sido enviada, nos comunicaremos contigo en las siguientes 24 horas";
                break;
            }
            case "pass_not_valid": {
                $type = " alert-danger ";
                $mess = "La solicitud de cambio de contraseña ha expirado o no es válida";
                break;
            }
            case "pass_done": {
                $type = " alert-success ";
                $mess = "Tu contraseña ha sido cambiada exitosamente. Ya puedes usarla para ingresar";
                break;
            }
        }
        ?>
        <div class="alert<?php echo $type ?>alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">x</span>
                <span class="sr-only">Cerrar</span>
            </button>
            <?php echo $mess ?>
        </div>
        <?php
    }
}

/**
 * Prepara valores para ser insertados en la base de datos dependiendo de su tipo.
 * @param string $theValue Valor a ser insertado
 * @param string $theType Tipo de valor a ingresar. Tipos válidos: text, int, float, date, defined
 * @param boolean $forLike Si va a ser usado para un query like
 * @param string $theDefinedValue Setea el valor si $theValue esta definido
 * @param type $theNotDefinedValue Setea el valor si $theValue no esta definido
 * @return mixed Retorna el valor listo para ser insertado
 */
function sqlValue($theValue, $theType, $forLike = false, $theDefinedValue = "", $theNotDefinedValue = "") {
    $db = DbManager::getMe();
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    switch ($theType) {
        case "text":
            $theValue = $forLike ? '%' . $theValue . '%' : $theValue;
            $theValue = ($theValue != "") ? "'" . utf8_decode($theValue) . "'" : "NULL";
            break;
        case "int":
            $theValue = ($theValue != "") ? intval($theValue) : "NULL";
            break;
        case "float":
            $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
            break;
        case "double":
            $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
            break;
        case "date":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
        case "defined":
            $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
            break;
    }
    return $theValue;
}
/**
 * Retorna la fecha y hora del momento en formato para datetime SQL
 * @param string $format
 * @return type
 */
function now($format = "Y-m-d H:i:s"){
    return date($format);
}

function encode_email_address($email) {
    $email_encoded = rtrim(strtr(base64_encode($email), '+/', '-_'), '=');
    return $email_encoded;
}

function decode_email_address($encoded){
    $decoded_email = base64_decode(strtr($encoded, '-_', '+/'));
    return $decoded_email;
}

function inputStreamToArray($stripslashes = true){
    $rawJson = file_get_contents("php://input");
    if(!$rawJson){
        return false;
    } else {
        $array = json_decode($stripslashes ? stripslashes($rawJson) : $rawJson, true);
        return $array;
    }
}

function array_utf8_encode($array){
    $output = array();
    foreach ($array as $k => $v){
        $output[$k] = utf8_encode($v);
    }
    return $output;
}

function parseSqlDT($time) {
    $phpTime = strtotime($time);
    return date('Y-m-d H:i:s', $phpTime);
}

function escape_filename($string){
     return str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9_%\[().\]\\/-]/s', '', $string));
}

function self_escape_string($str){
    $str = preg_replace("/&#?[a-z0-9]{2,8};/i","",$str);
    $str = (strlen($str) !== strlen(utf8_decode($str))) ? $str : utf8_encode($str);
    return $str;
}

function sanitize_array_by_keys($arrayToSanitize, $arrayKeysToSanitize){
    $i = 0;
    while(count($arrayToSanitize) > $i){
        foreach($arrayKeysToSanitize as $k){
            $arrayToSanitize[$i][$k] = self_escape_string($arrayToSanitize[$i][$k]);
        }
        $i++;
    }
    return $arrayToSanitize;
}

function sanitize_by_keys($arrayToSanitize, $arrayKeysToSanitize){
    foreach($arrayKeysToSanitize as $k){
        $arrayToSanitize[$k] = self_escape_string($arrayToSanitize[$k]);
    }
    return $arrayToSanitize;
}
?>
