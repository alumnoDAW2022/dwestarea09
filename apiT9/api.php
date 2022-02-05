<?php

//datos conexion en hosting
/*
define("SERVIDOR_DB", "localhost");
define("USUARIO_DB", "id18218086_userlibros");
define("PASS_DB", "df-)1=J{(URJ[_qI");
define("NOMBRE_DB", "id18218086_tarea06libros");
*/
//datos conexion en local
define("SERVIDOR_DB", "localhost");
define("USUARIO_DB", "root");
define("PASS_DB", "");
define("NOMBRE_DB", "libros");

require_once('gestionLibros.php');

/**
 * Esta función hace una conexión a la base de datos para recoger una array 
 * con los datos de todos los autores
 * @return array lista_autores
 */
function get_lista_autores()
{

	$miObjeto = new gestionLibros();
	$conexion = $miObjeto->conexion(SERVIDOR_DB, USUARIO_DB, PASS_DB, NOMBRE_DB);
	$lista_autores = $miObjeto->consultarAutores($conexion, null);
	$conexion->close();

	return $lista_autores;
}

/**
 * Esta función hace una conexión a la base de datos y recoge en un array
 * los datos de un autor específico y sus libros.
 * @param int id de autor
 * @return array info_autor
 */
function get_datos_autor($id)
{
	$info_autor = array();

	$miObjeto = new gestionLibros();
	$conexion = $miObjeto->conexion(SERVIDOR_DB, USUARIO_DB, PASS_DB, NOMBRE_DB);
	$info_autor["datos"] = $miObjeto->consultarAutores($conexion, $id);
	if (!empty($info_autor["datos"])) {
		$info_autor["libros"] = $miObjeto->consultarLibros($conexion, $id);
	}

	$conexion->close();

	return $info_autor;
}

/**
 * Esta función hace una conexión a la base de datos y recoge en un array
 * los datos de todos los libros
 * @return array info_libros
 */
function get_lista_libros()
{

	$miObjeto = new gestionLibros();
	$conexion = $miObjeto->conexion(SERVIDOR_DB, USUARIO_DB, PASS_DB, NOMBRE_DB);
	$info_libros = $miObjeto->consultarLibros($conexion, null);
	$conexion->close();

	return $info_libros;
}

/**
 * Esta función hace una conexión a la base de datos y recoge en un array
 * los datos de un libro específico y los datos de su autor
 * @param int id de libro
 * @return array asociativo con los datos del libro y su autor.
 */
function get_datos_libro($id)
{
	$miObjeto = new gestionLibros();
	$conexion = $miObjeto->conexion(SERVIDOR_DB, USUARIO_DB, PASS_DB, NOMBRE_DB);
	$info_libro["datos"] = $miObjeto->consultarDatosLibro($conexion, $id);
	if (!empty($info_libro["datos"])) {
		$info_autor["autor"] = $miObjeto->consultarAutores($conexion, $info_libro["datos"]["id_autor"]);
		$info_libro["datos"]["autor"] = $info_autor["autor"][0]["nombre"] . " " . $info_autor["autor"][0]["apellidos"];
	}

	$conexion->close();

	return $info_libro;
}

/**
 * Esta función hace una conexión a la base de datos y recoge en un array
 * una lista de titulos que coinciden con un texto recibido
 * @param string texto a buscar en titulos
 * @return array con los titulos que coinciden
 */
function get_titulos_libro($cadena)
{
	$miObjeto = new gestionLibros();
	$conexion = $miObjeto->conexion(SERVIDOR_DB, USUARIO_DB, PASS_DB, NOMBRE_DB);

	$info_titulos = $miObjeto->consultaTitulosLibros($conexion, $cadena);
	
	if(empty($info_titulos)){
		return null;
	}

	$conexion->close();
	return $info_titulos;
}

/**
 * Esta función hace una conexión a la base de datos y recoge en un array
 * una lista de autor que coinciden con un texto recibido y sus libros
 * @param string texto a buscar en autores
 * @return array con los autores y sus libros
 */
function get_autor_con_libros($cadena)
{
	$info_autores = array();
	$miObjeto = new gestionLibros();
	$conexion = $miObjeto->conexion(SERVIDOR_DB, USUARIO_DB, PASS_DB, NOMBRE_DB);

	$info_autor["autor"] = $miObjeto->consultaAutoresPorPatron($conexion, $cadena);
	if(empty($info_autor["autor"])){
		// si no hay ids autores con coincidencia
		//$libros_autores = $miObjeto->consultaLibros($conexion, $info_autor);
		return null;
	}
	
	//gestionamos el array info_autores
	//var_dump($info_autor["autor"]);
	
	for($i = 0; $i<count($info_autor["autor"]);$i++){
		//echo $ids_autores[$i][0];
		/* $info_autores["id"] = $ids_autores[$i][0];
		$info_autores[$ids_autores[$i][0]]["datos"] = $miObjeto->consultarLibros($conexion, $ids_autores[$i][0]);
		
		$info_autor["autor"] = $miObjeto->consultarAutores($conexion, $info_libro["datos"]["id_autor"]);
		$info_libro["datos"]["autor"] = $info_autor["autor"][0]["nombre"] . " " . $info_autor["autor"][0]["apellidos"]; */
		$info_autores[$i]["autor"] = $info_autor["autor"][$i]["nombre"] . " " . $info_autor["autor"][$i]["apellidos"];
		$info_autores[$i]["libros"] = $miObjeto->consultarLibros($conexion, $info_autor["autor"][$i]["id"]);
	
	}
	
	
	$conexion->close();
	return $info_autores;
}


$posibles_URL = array(
	"get_lista_autores",
	"get_datos_autor",
	"get_lista_libros",
	"get_datos_libro",
	"get_titulos_libro",
	"get_autor_con_libros"
);

$valor = "Ha ocurrido un error";

if (isset($_GET["action"]) && in_array($_GET["action"], $posibles_URL)) {
	switch ($_GET["action"]) {
		case "get_lista_autores":
			$valor = get_lista_autores();
			break;
		case "get_datos_autor":
			if (isset($_GET["id"]))
				$valor = get_datos_autor($_GET["id"]);
			else
				$valor = "Argumento no encontrado";
			break;
		case "get_lista_libros":
			$valor = get_lista_libros();
			break;
		case "get_datos_libro":
			if (isset($_GET["id"]))
				$valor = get_datos_libro($_GET["id"]);
			else
				$valor = "Argumento de libro no encontrado";
			break;
		case "get_titulos_libro":
			if (isset($_GET["cadena"]))
				$valor = get_titulos_libro(htmlspecialchars_decode($_GET["cadena"]));
			else
				$valor = "Argumento de no encontrado";
			break;
		case "get_autor_con_libros":
			if (isset($_GET["cadena"]))
				$valor = get_autor_con_libros(htmlspecialchars_decode($_GET["cadena"]));
			else
				$valor = "Argumento de no encontrado";
			break;
	}
}

//devolvemos los datos serializados en JSON
exit(json_encode($valor));
