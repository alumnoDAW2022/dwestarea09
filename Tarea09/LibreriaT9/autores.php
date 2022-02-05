<?php



require __DIR__ . '/vendor/autoload.php';



use Twig\Environment;

use Twig\Loader\FilesystemLoader;



$loader = new FilesystemLoader(__DIR__ . '/templates');

$twig = new Environment($loader);


/**
 * Tenemos 3 casos en esta página.
 * Caso 1: se recibe los parámetros GET action=get_datos_autor y id=número entero
 * Caso 2: se recibe el parámetro GET action=get_lista_autores
 * Caso 3: en cualquier otro caso no se renderiza ninguna página
 */
$lista = false;

if (isset($_GET["action"]) && isset($_GET["id"]) && $_GET["action"] == "get_datos_autor") {

    //Se realiza la peticion a la api que nos devuelve el JSON con la información de los autores

    if (is_numeric($_GET["id"])) {

        $info_autor = file_get_contents('https://dwestarea06.000webhostapp.com/apitarea9/api.php?action=get_datos_autor&id=' . $_GET["id"]);
        //$info_autor = file_get_contents('http://localhost/API/apiT9/api.php?action=get_datos_autor&id=' . $_GET["id"]);

        // Se decodifica el fichero JSON y se convierte a array

        $info_autor = json_decode($info_autor, true);
    }



    if (!empty($info_autor["datos"][0])) {



        echo $twig->render('infoAutor.html.twig', [

            'autor' => $info_autor["datos"][0],

            'libros' => $info_autor["libros"]

        ]);
    }
} else if (isset($_GET["action"]) && $_GET["action"] == "get_lista_autores") {

    $lista_actores = file_get_contents('https://dwestarea06.000webhostapp.com/apitarea9/api.php?action=get_lista_autores');
    //$lista_actores = file_get_contents('http://localhost/API/apiT9/api.php?action=get_lista_autores');



    $lista = json_decode($lista_actores, true);

    echo $twig->render('listadoAutores.html.twig', [

        'lista' => $lista

    ]);
} else if (isset($_GET["action"]) && $_GET["action"] == "get_autores_por") {

    echo $twig->render('buscarPor.html.twig', [
        'tituloH1' => 'autores',
        'botonBuscar' => 'btnAutores',
        'placeholder' => 'Autor'
    ]);
}
