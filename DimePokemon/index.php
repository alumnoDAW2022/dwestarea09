<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dime Pokemon</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 1.2em;
        }

        main {
            display: flex;
            background-color: #f1f1f1;
            padding: 15px;
            flex-wrap: wrap;
        }

        section {
            align-items: center;
            padding: 15px;
        }

        #numero {
            width: 55px;
        }
    </style>
</head>

<?php

$infoPokemon = array(
    'id' => "",
    'nombre' => "",
    'imagen' => "",
    'tipo' => ""
);
if (isset($_GET["numero"]) && isset($_GET["consultar"])) {

    if (!empty($_GET["numero"])) {
        $url = "https://pokeapi.co/api/v2/pokemon/" . $_GET["numero"];
        $result = @file_get_contents($url);

        if ($result) {
            $array = json_decode($result, true);
            $infoPokemon = array(
                'id' => $array["id"],
                'nombre' => ucfirst($array["forms"][0]["name"]),
                'imagen' => $array["sprites"]["other"]["dream_world"]["front_default"],
                'tipo' => ucfirst($array["types"][0]["type"]["name"])
            );
        }
    }
}

?>

<body>
    <h1>Dime Pokemon</h1>
    <main>
        <section>
            <form action="?" method="get">
                <p><label for="numero">Número</label></p>
                <p><input type="number" id="numero" name="numero"></p>
                <p><input type="submit" value="consultar" name="consultar"></p>
            </form>
        </section>
        <section>
            <article>
                <img src="<?php echo $infoPokemon["imagen"]; ?>">
            </article>
            <article>
                <p>id: <?php echo $infoPokemon["id"]; ?></p>
                <p>Nombre: <?php echo $infoPokemon["nombre"]; ?></p>
                <p>Tipo: <?php echo $infoPokemon["tipo"]; ?></p>
            </article>
        </section>
    </main>
</body>

</html>