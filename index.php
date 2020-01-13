<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
$accion = $_REQUEST["accion"];
$token  = $_REQUEST["token"];
//	REGISTRAR TOKEN.
if( $accion=="registrar" ){
	echo Auth::registrar_token();
}else if( $accion=="validar_token" ){
	echo Auth::validar_token($token);
}else if( $accion=="cerrar_sesion" ){
	echo "<script>localStorage.removeItem('token');</script>";
}else{
	//	ACCIONES.
	if( $accion!="" ){
		if( Auth::validar_token($token)==1 ){
			switch ($accion) {
			    case "prueba":
					$datos = Auth::Check($token);
					echo json_encode($datos);
			        break;
			}
		}
	}
}
class Auth
{
	private static $aud = null;
	private static $encrypt = ['HS256'];
	private static $secret_key = 'Sdw1s9x8@';
	public static function registrar_token()
	{
		$usuario_id = 1;
		$usuario    = "afperdomo";
		$token = Auth::SignIn([
	        'id' => $usuario_id,
	        'usuario' => $usuario
	    ]);
	    if( $token!="" ){
	    	$respuesta = "Token registrado correctamente<br><br>
	    	$token
	    	<script>localStorage.setItem('token','$token');</script>";
	    }
	    return $respuesta;
	}
    public static function validar_token($token)
    {
		$usuario_id = 1;
		$usuario = "afperdomo";
    	$datos = Auth::Check($token);
    	if( $usuario_id==$datos->id && $usuario==$datos->usuario ){
    		return 1;
    	}else{
    		return 0;
    	}
    }
    public static function SignIn($data)
    {
        $time = time();
        $token = array(
            'aud' => self::Aud(),
            'data' => $data
        );
        return JWT::encode($token, self::$secret_key);
    }
    public static function Check($token)
    {
    	$respuesta = "";
    	try{
    		return JWT::decode(
	            $token,
	            self::$secret_key,
	            self::$encrypt
	        )->data;
    	}catch(Exception $e){
			return $respuesta[0] = 'Error';
		}
    }
    private static function Aud()
    {
        $aud = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        return sha1($aud);
    }
}
?>