function soloCaracteres(textoAValidar) {
    var nuevaExp = new RegExp(/^[A-ZÑa-zñáéíóú\d\w?¿, ]+$/);
    if (nuevaExp.test(textoAValidar)) {
        return true;
    }

    return false;
}


var buscar;
var estoyConAutores = false;
var estoyConLibros = false;



document.getElementById("formulario").addEventListener("keypress", (evento) => {
    if (evento.key === "Enter") {
        evento.preventDefault();
    }

}, false);

var textoBuscar = document.getElementById("textoBuscar");

textoBuscar.addEventListener("keyup", comprobarTexto, false);


function comprobarTexto() {
    mostrarMensaje.innerHTML = "";
    resultado.innerHTML = "";

    var mensaje = "✔";

    var valor = textoBuscar.value;


    if (!soloCaracteres(valor)) {
        mensaje = "Solo caracteres.";
    }

    if (estaVacio(valor)) {
        mensaje = "busca algo....";
    }


    if (mensaje == "✔") {

        var url = generarUrl(valor);
        url = url.replace(' ', '%20');

        $.get(url, function(data) {
            mostrarInformacion(data);
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

function mostrarInformacion(json) {
    var resultado = document.getElementById("resultado");
    resultado.innerHTML = "";
    var objeto = JSON.parse(json);
    if (objeto) {
        if (estoyConLibros) {
            for (dato of objeto) {
                resultado.innerHTML += dato.titulo + "<br>";
            }
        }
        if (estoyConAutores) {
            for (let i = 0; i < objeto.length; i++) {
                resultado.appendChild(montarTabla(objeto[i]));
            }
        }
    } else {
        resultado.innerHTML = "Sin datos";
    }

}

function generarUrl(valor) {
    if (estoyConAutores) {
        return "https://dwestarea06.000webhostapp.com/apitarea9/api.php?action=get_autor_con_libros&cadena=" + valor;
    } else {
        return "https://dwestarea06.000webhostapp.com/apitarea9/api.php?action=get_titulos_libro&cadena=" + valor;
    }

}

function detectaBoton() {
    var boton = document.getElementsByTagName("input")[1];
    if (boton.getAttribute("id") == "btnAutores") {
        estoyConAutores = true;
        buscar = document.getElementById("btnAutores");
    } else {
        buscar = document.getElementById("btnLibros");
        estoyConLibros = true;
    }


}

function montarTabla(datoAutor) {

    let table = document.createElement('table');
    let thead = document.createElement('thead');
    let tbody = document.createElement('tbody');



    let filaNombre = document.createElement("tr");
    let encabezado = document.createElement("th");
    encabezado.innerHTML = datoAutor.autor;
    filaNombre.appendChild(encabezado);
    for (let i = 0; i < datoAutor.libros.length; i++) {
        filaTitulo = document.createElement("tr");
        datoTitulo = document.createElement("td");
        datoTitulo.innerHTML = datoAutor.libros[i].titulo;
        filaTitulo.appendChild(datoTitulo);
        tbody.appendChild(filaTitulo);
    }
    thead.appendChild(filaNombre);
    table.appendChild(thead);


    table.appendChild(tbody);
    return table;
}

detectaBoton();