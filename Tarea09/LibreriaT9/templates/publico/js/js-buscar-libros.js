function soloCaracteres(textoAValidar) {
    var nuevaExp = new RegExp(/^[A-ZÑa-zñáéíóú\d\w?¿, ]+$/);
    if (nuevaExp.test(textoAValidar)) {
        return true;
    }

    return false;
}


var buscar = document.getElementById("buscar");
document.getElementById("formulario").addEventListener("keypress", (evento) => {
    if (evento.key === "Enter") {

        evento.preventDefault();


    }

}, false);
var textoBuscar = document.getElementById("textoBuscar");
var buscar = document.getElementById("buscar");
textoBuscar.addEventListener("keyup", comprobarTexto, false);

buscar.addEventListener("click", comprobarTexto, false);

function comprobarTexto() {
    mostrarMensaje.innerHTML = "";

    var mensaje = "✔";

    var valor = textoBuscar.value;


    if (!soloCaracteres(valor)) {
        mensaje = "Solo caracteres.";
    }

    if (estaVacio(valor)) {
        mensaje = "busca algo....";
    }


    if (mensaje == "✔") {

        var url = "http://localhost/API/apiT9/api.php?action=get_titulos_libro&cadena=" + valor;
        url = url.replace(' ', '%20');

        $.get(url, function(data) {
            mostrarTitulos(data);
        });
    }
    mostrarMensaje.innerHTML = mensaje;

}


function estaVacio(texto) {
    if (texto.length == 0) {
        return true;
    }
    return false;
}

function mostrarTitulos(json) {
    var resultado = document.getElementById("resultado");
    resultado.innerHTML = "";
    var objeto = JSON.parse(json);
    for (dato of objeto) {
        resultado.innerHTML += dato.titulo + "<br>";

    }

}