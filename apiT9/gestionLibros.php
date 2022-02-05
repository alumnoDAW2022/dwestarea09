<?php
// gestionLibros.php


/**
 * Esta clase gestiona la base de datos Libros
 * 
 * @author Enrique Castro Molina
 * @version 0.0.3 Estable - Para Tarea 09 DWES
 */

class gestionLibros
{
    /**
     * Este método establece un conexión con el sistema gestor de BBDD
     * @param string servidor de base de datos
     * @param string usuario de base de datos
     * @param string contraseña del usuario 
     * @param string nombre de la base de datos
     * @return mysqli la conexión establecida o null en caso de error.
     */
    public function conexion($servidor, $usuario, $pass, $baseDeDatos)
    {
        @$conex = new mysqli($servidor, $usuario, $pass, $baseDeDatos);

        if ($conex->connect_error) {
            return null;
        }
        return $conex;
    }

    /**
     * Este método realiza la consulta a la base de datos 
     * Consulta los autores de la tabla autor
     * @param mysqli la conexion establecida
     * @param int el id de autor
     * @return array asociativo con los datos, null en caso de sin datos o error
     */
    public function consultarAutores($conex, $id)
    {
        $consulta = creaConsultaAutor($id);
        $resultset = $conex->query($consulta);
        if ($resultset->num_rows > 0 && !$conex->error) {
            $resultado = $resultset->fetch_all(MYSQLI_ASSOC);
            return $resultado;
        } else {
            return null;
        }
    }

    /**
     * Este método realiza la consulta a la base de datos 
     * Consulta los libros de la tabla libros por id de autor
     * @param mysqli la conexion establecida
     * @param int el id de autor
     * @return array asociativo con los datos, null en caso de sin datos o error
     */
    public function consultarLibros($conex, $idAutor)
    {

        $consulta = creaConsultaLibro($idAutor);
        $resulset = $conex->query($consulta);
        if ($resulset->num_rows > 0 && !$conex->error) {
            $resultado = $resulset->fetch_all(MYSQLI_ASSOC);
            return $resultado;
        } else {
            return null;
        }
    }

    /**
     * Este método realiza la consulta a la base de datos 
     * Consulta los datos del libro de la tabla libros por id de libro
     * @param mysqli la conexion establecida
     * @param int el id de libro
     * @return array asociativo con los datos, null en caso de sin datos o error
     */
    public function consultarDatosLibro($conex, $idLibro)
    {
        if (!is_numeric($idLibro)) {
            return null;
        }
        $consulta = "SELECT * FROM libro where id=$idLibro";
        $resultset = $conex->query($consulta);
        if ($resultset->num_rows > 0 && !$conex->error) {
            $resultado = $resultset->fetch_array(MYSQLI_ASSOC);
            return $resultado;
        } else {
            return null;
        }
    }

    /**
     * Este método borra un libro con el id indicado
     * Elimina los datos del libro de la tabla libro
     * @param mysqli la conexion establecida
     * @param int el id de libro
     * @return boolean  true en caso de producirse y false en caso de no 
     * producirse o error.
     */
    public function borrarLibro($conex, $idLibro)
    {
        $consulta = "DELETE FROM libro where id=$idLibro";
        if ($conex->query($consulta) === TRUE && $conex->affected_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Este método borra el autor con el id indicado
     * Además, elimina los libros asociados a su id
     * de la tabla libro
     * @param mysqli la conexion establecida
     * @param int el id de autor
     * @return boolean  true en caso de producirse y false en caso de no
     */
    public function borrarAutor($conex, $idAutor)
    {

        $conex->autocommit(FALSE);
        $conex->begin_transaction();
        $conex->query("SET foreign_key_checks = 0");
        $consulta = "DELETE FROM autor where id=$idAutor";
        $all_query_ok = true;

        $conex->query($consulta) ? null
            : $all_query_ok = false;

        $consulta = "DELETE FROM libro where id_autor=$idAutor";
        $conex->query($consulta) ? null
            : $all_query_ok = false;

        $conex->autocommit(TRUE);
        $conex->query("SET foreign_key_checks = 1");

        if ($all_query_ok) {
            $conex->commit();
            return true;
        } else {
            $conex->rollback();
            return false;
        }
    }

    /**
     * Esta método consulta los títulos de libros que coinciden con un
     * patrón de texto recibido
     * @param mysqli la conexion establecida
     * @param string patrón que debe coincidir
     * @return array asociativo con los titulos, null en caso de sin datos o error
     */
    public function consultaTitulosLibros($conex, $patron)
    {
        /* Se usa una sentencia preparada */

        $patron = '%' . $patron . '%';

        if ($stmt = $conex->prepare("SELECT titulo FROM libro where titulo LIKE ?")) {
            /* Ligar parámetros para marcadores */
            $stmt->bind_param('s', $patron);
            /* Ejecutar la consulta */
            $stmt->execute();
            /* Ligar variables de resultado */
            $result = $stmt->get_result();

            $resultado = $result->fetch_all(MYSQLI_ASSOC);
            /* cerrar sentencia */

            return $resultado;
        }
        return null;
    }
	
	/**
     * Esta método consulta los autores que coinciden con un
     * patrón de texto recibido
     * @param mysqli la conexion establecida
     * @param string patrón que debe coincidir
     * @return array asociativo con los id, nombre, apellidos, null en caso de sin datos o error
     */
    public function consultaAutoresPorPatron($conex, $patron)
    {
        /* Se usa una sentencia preparada */	
		//$patron = "";
        $patron = '%' . $patron . '%';

        if ($stmt = $conex->prepare("SELECT id, nombre, apellidos FROM autor where nombre LIKE ? OR apellidos LIKE ?")) {
            /* Ligar parámetros para marcadores */
            $stmt->bind_param('ss', $patron, $patron);
            /* Ejecutar la consulta */
            $stmt->execute();
            /* Ligar variables de resultado */
            $result = $stmt->get_result();

            //$resultado = $result->fetch_all(MYSQLI_ASSOC);
			$resultado = $result->fetch_all(MYSQLI_ASSOC);
            /* cerrar sentencia */

            return $resultado;
        }
        return null;
    }
	
	
	
}






#
# Funciones a mayores:
#
#

/**
 * Esta función construye una consulta SQL dependiendo si tiene un id
 * que buscar o no
 * @param int id de autor
 * @return string la consulta en la tabla autor
 */
function creaConsultaAutor($id)
{
    if (is_numeric($id)) {
        $consulta = "SELECT * FROM autor where id=$id";
    } else {
        $consulta = "SELECT * FROM autor";
    }

    return $consulta;
}

/**
 * Esta función construye una consulta SQL dependiendo si tiene un id
 * que buscar o no
 * @param int id de autor
 * @return string la consulta en la tabla Libro
 */
function creaConsultaLibro($idAutor)
{
    if (is_numeric($idAutor)) {
        $consulta = "SELECT * FROM libro where id_autor=$idAutor";
    } else {
        $consulta = "SELECT * FROM libro";
    }

    return $consulta;
}
