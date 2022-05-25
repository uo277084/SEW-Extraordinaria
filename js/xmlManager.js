"use strict";
class XMLManager {
    constructor() {
        this.team = null;
        this.paises = [];
        this.jugadoresPais = [];
    }

    uploadTeamXML() {
        var reader = new FileReader();
        //El archivo subido
        var file = document.getElementById("uploadTeamXML").files[0];
        if (file == null) {
            alert("No hay archivo seleccionado");
        }
        //Area donde mostraremos el contenido del archivo
        var dataArea = document.getElementById("teamData");

        var typeMatch = /text.xml/;
        //Comprobamos que el archivo es un XML
        if (file.type.match(typeMatch)) {
            var datos = "";

            reader.readAsText(file);
            reader.onloadend = function () {
                var xml = $(reader.result);

                //Obtenemos los datos del equipo
                var teamData = $(xml).find("datosEquipo");
                var teamName = $(teamData).find("nombre").text();
                var antiguedad = $(teamData).find("antiguedad").text();
                var city = $(teamData).find("ciudad").text();
                datos += "<h2>" + teamName + "</h2>";
                datos += "<h3>Datos del equipo</h3>";
                datos += "<ul>";
                datos += "<li>Antiguedad: " + antiguedad + "</li>";
                datos += "<li>Ciudad: " + city + "</li>";
                datos += "</ul>";

                //Cogemos los países
                var countries = $(xml).find("pais")
                countries.each(function () {
                    //Cogemos los datos del país
                    var nombrePais = $(this).attr("nombre").valueOf();
                    var coordLongitud = $(this).find("datosPais").find("coord").find("longitud").text();
                    var coordLatitud = $(this).find("datosPais").find("coord").find("latitud").text();
                    var poblacion = $(this).find("datosPais").find("poblacion").text();
                    var continente = $(this).find("datosPais").find("continente").text();

                    //Indicamos como se mostrarán los datos del país
                    datos += "<h3>" + nombrePais + "</h3>";
                    datos += "<h4>Datos del país</h4>";
                    datos += "<ul>";
                    datos += "<li>Coordenadas: latitud->" + coordLatitud + " longitud->" + coordLongitud + "</li>";
                    datos += "<li>Poblacion: " + poblacion + "</li>";
                    datos += "<li>Continente: " + continente + "</li>";
                    datos += "</ul>";

                    datos += "<h4>Jugadores</h4>";

                    //Cogemos los jugadores del país
                    var jugadores = $(this).find("jugador");
                    jugadores.each(function () {
                        //Cogemos los datos del jugador
                        var nombre = $(this).attr("nombre").valueOf() + " " + $(this).attr("apellidos").valueOf();
                        var fechaNac = $(this).find("fechaNac");
                        var fechaDia = fechaNac.attr("dia").valueOf();
                        var fechaMes = fechaNac.attr("mes").valueOf();
                        var fechaYear = fechaNac.attr("year").valueOf();
                        var altura = $(this).find("altura").text();
                        var ciudad = $(this).find("ciudad").text();

                        //Indicamos como se mostrarán los datos del jugador
                        datos += "<h5>Datos de " + nombre + "</h5>";
                        datos += "<ul>";
                        datos += "<li>Fecha de nacimiento: " + fechaDia + "/" + fechaMes + "/" + fechaYear + "</li>";
                        datos += "<li>Altura: " + altura + "</li>";
                        datos += "<li>Ciudad: " + ciudad + "</li>";
                        datos += "</ul>";
                        //Mostrar los datos de los equipos antiguos
                    });
                });
                //Mostramos los datos del equipo
                dataArea.innerHTML = datos;
            };
        } else {
            //Lanza error
            alert("El archivo tiene que tener formato XML");
        }
    }
}
var xmlManager = new XMLManager();