<?php



require __DIR__ . '/vendor/autoload.php';



use Twig\Environment;

use Twig\Loader\FilesystemLoader;



$loader = new FilesystemLoader(__DIR__ . '/templates');

$twig = new Environment($loader);


/**
 * Tenemos 4 casos en esta página.
 * Caso 1: se recibe los parámetros GET action=get_datos_libro y id=número entero
 * Caso 2: se recibe el parámetro GET action=get_lista_libros
 * Caso 3: se recibe el parámetro GET action=get_titulos_libros
 * Caso 4: en cualquier otro caso no se renderiza ninguna página
 */


$lista = false;

if (isset($_GET["action"]) && isset($_GET["id"]) && $_GET["action"] == "get_datos_libro") {

    //Se realiza la peticion a la api que nos devuelve el JSON con la información de los autores

    if (is_numeric($_GET["id"])) {

        $info_libro = file_get_contents('https://dwestarea06.000webhostapp.com/apitarea9/api.php?action=get_datos_libro&id=' . $_GET["id"]);
        //$info_libro = file_get_contents('http://localhost/API/apiT9/api.php?action=get_datos_libro&id=' . $_GET["id"]);
        // Se decodifica el fichero JSON y se convierte a array

        $info_libro = json_decode($info_libro, true);
    }



    if (!empty($info_libro["datos"])) {



        echo $twig->render('infoLibro.html.twig', [

            'libro' => $info_libro["datos"],



        ]);
    }
} else if (isset($_GET["action"]) && $_GET["action"] == "get_lista_libros") {

    $lista_libros = file_get_contents('https://dwestarea06.000webhostapp.com/apitarea9/api.php?action=get_lista_libros');
    //$lista_libros = file_get_contents('http://localhost/API/apiT9/api.php?action=get_lista_libros');



    $lista = json_decode($lista_libros, true);

    echo $twig->render('listadoLibros.html.twig', [

        'lista' => $lista

    ]);
} else if (isset($_GET["action"]) && $_GET["action"] == "get_titulos_libros") {

    //echo $twig->render('buscarPorTitulo.html.twig', []);

    echo $twig->render('buscarPor.html.twig', [
        'tituloH1' => 'libros',
        'botonBuscar' => 'btnLibros',
        'placeholder' => 'Titulo'
    ]);
}
