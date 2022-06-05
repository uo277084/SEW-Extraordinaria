"use strict";
class JSONManager {
    constructor() {
        this.apikey = "ae42ccc41b12de5fbb872b9964dec7746a28f241613a34e65587e4cb39ef2be8";

        //Formato de llamada a la api
        //https://www.apiclient.resultados-futbol.com/scripts/api/api.php?key=YOUR_KEY&format=json&req=categories&filter=all
        //Para coger la id de un equipo
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=YOUR_KEY & tz=Europe/Madrid & format=json & req=teams & league=1 & year=2017
        //Para coger los jugadores de un equipo
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=YOUR_KEY&tz=Europe/Madrid&format=json&req=team_players&team=6313715&year=2020

    }

    getIdCountry(apikey, countryName, getIdCompetition, getIdTeam, getIdPlayers) {
        $.ajax({
            dataType: "json",
            url: "https://apiv2.allsportsapi.com/football/?met=Countries&APIkey=" + apikey,
            method: 'GET',
            success: function (data) {
                var countries = data.result;
                for (var i = countries.length - 1; i >= 0; i--) {
                    if (countries[i].country_name == countryName) {
                        getIdCompetition(apikey, countries[i].country_key, "Segunda División", getIdTeam, getIdPlayers);
                        break;
                    }
                }
            },
            error: function () {
                $("figure").html("Hay problemas para obtener el identificador del país, ¡lo sentimos!");
            }
        });
    }

    getIdCompetition(apikey, countryId, leagueName, getIdTeam, getIdPlayers) {
        var url = "https://apiv2.allsportsapi.com/football/?met=Leagues&APIkey=" + apikey + "&countryId=" + countryId;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var competitions = data.result;
                for (var i = 0; i < competitions.length; i++) {
                    if (competitions[i].league_name == leagueName) {
                        getIdTeam(apikey, competitions[i].league_key, "Sporting Gijon", getIdPlayers);
                        break;
                    }
                }
            },
            error: function () {
                $("figure").html("Hay problemas para obtener el identificador de la competición, ¡lo sentimos!");
            }
        });
    }

    getIdTeam(apikey, leagueId, teamName, getIdPlayers) {
        var url = "https://apiv2.allsportsapi.com/football/?&met=Teams&APIkey=" + apikey + "&leagueId=" + leagueId;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var teams = data.result;
                for (var i = teams.length - 1; i >= 0; i--) {
                    if (teams[i].team_name == teamName) {
                        getIdPlayers(apikey, teams[i].team_key);
                        break;
                    }
                }
            },
            error: function () {
                $("figure").html("Hay problemas para obtener el identificador del Sporting, ¡lo sentimos!");
            }
        });
    }

    getIdPlayers(apikey, teamId) {
        var url = "https://apiv2.allsportsapi.com/football/?&met=Teams&APIkey=" + apikey + "&teamId=" + teamId;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var players = data.result[0].players;
                var position;
                var dataToShow = "";
                for (var i = 0; i < players.length; i++) {
                    console.log(i);
                    position = players[i].player_type;
                    dataToShow += "<h3>" + players[i].player_name + "</h3>";
                    if (position == "Goalkeepers") {
                        dataToShow += "<p>" + "<b>Posición</b>: Portero</p>";
                    } else if (position == "Defenders") {
                        dataToShow += "<p>" + "<b>Posición</b>: Defensa</p>";
                    } else if (position == "Midfielders") {
                        dataToShow += "<p>" + "<b>Posición</b>: Centrocampista</p>";
                    } else {
                        //Delanteros
                        dataToShow += "<p>" + "<b>Posición</b>: Delantero</p>";
                    }
                    dataToShow += "<p>" + "<b>Número</b>: " + players[i].player_number + "</p>";
                    dataToShow += "<p>" + "<b>Partidos jugados</b>: " + players[i].player_match_played + "</p>";
                    dataToShow += "<p>" + "<b>Goles marcados</b>: " + players[i].player_goals + "</p>";
                    dataToShow += "<p>" + "<b>Edad</b>: " + players[i].player_age + "</p>";
                }
                $("figure").html(dataToShow);
            },
            error: function () {
                $("figure").html("Hay problemas para obtener el identificador del sporting, ¡lo sentimos!");
            }
        });
    }

    loadInfoPlayersJSON() {
        //Crear elemento donde se va a mostrar la información
        var p = document.createElement("figure");
        p.innerHTML = "";
        $("section").after(p);

        this.getIdCountry(this.apikey, "Spain", this.getIdCompetition, this.getIdTeam, this.getIdPlayers);
        /*
         "team_key": "7270",
            "team_name": "Sporting Gijon",
            "team_logo": "https://apiv2.allsportsapi.com/logo/7270_sporting-gijon.jpg",
            "players": [
        */
    }
}
var jsonManager = new JSONManager();