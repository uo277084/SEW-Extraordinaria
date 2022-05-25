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
                var teamData = $(xml).find("datosEquipo")[0];
                var teamName = $(teamData).find("nombre").text();
                var antiguedad = $(teamData).find("antiguedad").text();
                var city = $(teamData).find("ciudad").text();
                datos += "<h1>" + teamName + "</h1>";
                datos += "<h2>Datos del equipo</h2>";
                datos += "<ul>";
                datos += "<li>Antiguedad: " + antiguedad + "</li>";
                datos += "<li>Ciudad: " + city + "</li>";
                datos += "</ul>";

                datos += "<h2>Nacionalidades de jugadores</h2>";
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
                        datos += "<figure>";
                        //Cogemos los datos del jugador
                        var nombre = $(this).attr("nombre").valueOf() + " " + $(this).attr("apellidos").valueOf();
                        var fechaNac = $(this).find("datosJugador").find("fechaNac");
                        var fechaDia = fechaNac.attr("dia").valueOf();
                        var fechaMes = fechaNac.attr("mes").valueOf();
                        var fechaYear = fechaNac.attr("year").valueOf();
                        var altura = $(this).find("datosJugador").find("altura").text();
                        var ciudad = $(this).find("datosJugador").find("ciudad").text();

                        //Indicamos como se mostrarán los datos del jugador
                        datos += "<h5>" + nombre + "</h5>";
                        datos += "<ul>";
                        datos += "<li>Fecha de nacimiento: " + fechaDia + "/" + fechaMes + "/" + fechaYear + "</li>";
                        datos += "<li>Altura: " + altura + "</li>";
                        datos += "<li>Ciudad: " + ciudad + "</li>";
                        //Poner una href para ver la foto (?)
                        datos += "</ul>";

                        datos += "<figure>";
                        //Cogemos los equipos antiguos
                        var equiposAntiguos = $(this).find("equipo");
                        if (equiposAntiguos.length > 1) {
                            datos += "<h6>Sus últimos equipos han sido:</h6>";
                        } else if (equiposAntiguos.length == 1) {
                            datos += "<h6>Su último equipo ha sido:</h6>";
                        } else {
                            datos += "<h6>Solo ha jugado en este equipo</h6>";
                        }

                        equiposAntiguos.each(function () {

                            var oldTeamData = $(this).find("datosEquipo");
                            var oldTeamName = $(oldTeamData).find("nombre").text();
                            var oldTeamFechaIni = $(oldTeamData).find("fechaIni");
                            var oldTeamFechaFin = $(oldTeamData).find("fechaFin");
                            var oldTeamAntiguedad = $(oldTeamData).find("antiguedad").text();
                            var oldTeamCity = $(oldTeamData).find("ciudad").text();

                            //Indicamos como se mostrarán los datos del equipo antiguo
                            datos += "<h4>" + oldTeamName + "</h4>";
                            datos += "<ul>";
                            datos += "<li>Fecha inicio contrato: " + oldTeamFechaIni.attr("dia").valueOf() +
                                "/" + oldTeamFechaIni.attr("mes").valueOf() + "/" +
                                oldTeamFechaIni.attr("year").valueOf() + "</li>";
                            datos += "<li>Fecha fin contrato: " + oldTeamFechaFin.attr("dia").valueOf() +
                                "/" + oldTeamFechaFin.attr("mes").valueOf() + "/" +
                                oldTeamFechaFin.attr("year").valueOf() + "</li>";
                            datos += "<li>Antiguedad: " + oldTeamAntiguedad + "</li>";
                            datos += "<li>Ciudad: " + oldTeamCity + "</li>";
                            datos += "</ul>";
                        });
                        datos += "</figure>";
                        datos += "</figure>";
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