"use strict";
class JSONManager {
    constructor() {
        this.apikey = "09814bfb9805145a110799740887e318";
        this.urlBase = "https://apiclient.besoccerapps.com/scripts/api/api.php?key=" + this.apikey + "&tz=Europe/Madrid&format=json&req=";

        this.countryName = "es";
        this.leagueName = "Segunda División";
        this.teamName = "Real Sporting";
        this.year = "2022";

        //TODO limpiar esto
        //Formato de llamada a la api
        //https://www.apiclient.resultados-futbol.com/scripts/api/api.php?key=YOUR_KEY&format=json&req=categories&filter=all
        //Para coger la competición
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=09814bfb9805145a110799740887e318&tz=Europe/Madrid&format=json&req=categories&country=es
        //Para coger la id de un equipo
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=09814bfb9805145a110799740887e318&tz=Europe/Madrid&format=json&req=teams&league=2&year=2022
        //Para coger los jugadores de un equipo
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=09814bfb9805145a110799740887e318&tz=Europe/Madrid&format=json&req=team_players&team=6443478&year=2022
    }

    getIdTeamCompetition(leagueName, teamName, getIdTeam, getPlayers) {
        var url = this.urlBase + "categories&country=" + this.countryName;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var competitions = data.category.spain.ligas;
                for (var i = 0; i < competitions.length; i++) {
                    if (competitions[i].name == leagueName) {
                        getIdTeam(competitions[i].id, teamName, getPlayers);
                        break;
                    }
                }
            },
            error: function () {
                $("section").html("Hay problemas para obtener los datos de la competición, ¡lo sentimos!");
            }
        });
    }

    getIdTeam(leagueId, teamName, getPlayers) {
        var url = this.urlBase + "teams&league=" + leagueId + "&year=" + this.year;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var teams = data.team;
                for (var i = teams.length - 1; i >= 0; i--) {
                    if (teams[i].nameShow == teamName) {
                        getPlayers(teams[i].id_comp);
                        break;
                    }
                }
            },
            error: function () {
                $("section").html("Hay problemas para obtener los datos del equipo, ¡lo sentimos!");
            }
        });
    }

    getPlayers(teamId) {
        var url = this.urlBase + "team_players&team=" + teamId + "&year=" + this.year;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var players = data.player;
                var player, position;
                var dataToShow = "";
                for (var i = 0; i < players.length; i++) {
                    player = players[i];
                    position = player.role;
                    dataToShow += "<h3>" + player.name + " " + player.last_name + "</h3>";
                    dataToShow += "<p>" + "<b>Número</b>: " + player.squadNumber + "</p>";
                    if (position == 1) {
                        dataToShow += "<p>" + "<b>Posición</b>: Portero</p>";
                    } else {
                        if (position == 2) {
                            dataToShow += "<p>" + "<b>Posición</b>: Defensa</p>";
                        } else if (position == 3) {
                            dataToShow += "<p>" + "<b>Posición</b>: Centrocampista</p>";
                        } else {
                            dataToShow += "<p>" + "<b>Posición</b>: Delantero</p>";
                        }
                        dataToShow += "<p>" + "<b>Goles marcados</b>: " + player.goals + "</p>";
                    }
                    dataToShow += "<p>" + "<b>Tarjetas rojas</b>: " + player.reds + "</p>";
                    dataToShow += "<p>" + "<b>Tarjetas amarillas</b>: " + player.yellows + "</p>";
                }
                $("figure").html(dataToShow);
            },
            error: function () {
                $("section").html("Hay problemas para obtener los datos de los jugadores, ¡lo sentimos!");
            }
        });
    }

    loadInfoPlayersJSON() {
        //Crear elemento donde se va a mostrar la información
        var p = document.createElement("figure");
        p.innerHTML = "";
        $("section").after(p);

        //Primero hacer la llamada a la api para coger la id de la competicion del equipo
        this.getIdTeamCompetition(this.leagueName, this.teamName, this.getIdTeam, this.getPlayers);
    }
}
var jsonManager = new JSONManager();