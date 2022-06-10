"use strict";
class JSONManager {
    constructor() {
        this.apikey = "3f8714347c0a5eed0fafaddf1b9865d9";
    }

    getIdTeam(apikey, teamName, season, getIdPlayers) {
        var url = "https://v3.football.api-sports.io/teams?name=" + teamName;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            headers: {
                "x-rapidapi-key": apikey
            },
            success: function (data) {
                var datos = data.response;
                var team = data.response[0].team;
                getIdPlayers(apikey, team.id, season);
            },
            error: function () {
                $("figure").html("Hay problemas para obtener el identificador del Sporting, ¡lo sentimos!");
            }
        });
    }

    getIdPlayers(apikey, teamId, season) {
        var url = "https://v3.football.api-sports.io/players?season=" + season + "&team=" + teamId;
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            headers: {
                "x-rapidapi-key": apikey
            },
            success: function (data) {
                var players = data.response;
                var playerData, playerStats, playerPosition, playerMatchs, playerGoals, playerSaves;
                var dataToShow = "";
                for (var i = 0; i < players.length; i++) {
                    playerData = players[i].player;
                    playerStats = players[i].statistics[0];
                    playerPosition = playerStats.games.position;
                    playerMatchs = playerStats.games.appearences;
                    playerGoals = playerStats.goals.total;
                    playerSaves = playerStats.goals.saves;

                    dataToShow += "<h3>" + playerData.name + "</h3>";
                    dataToShow += "<p>" + "<b>Edad</b>: " + playerData.age + "</p>";
                    dataToShow += "<p>" + "<b>Partidos jugados</b>: " + playerMatchs + "</p>";
                    if (playerPosition == "Goalkeeper") {
                        dataToShow += "<p>" + "<b>Posición</b>: Portero</p>";
                        if (playerSaves != null) {
                            dataToShow += "<p>" + "<b>Goles parados</b>: " + playerSaves + "</p>";
                        } else {
                            dataToShow += "<p>" + "<b>Goles parados</b>: " + 0 + "</p>";
                        }
                    } else {
                        if (playerPosition == "Defender") {
                            dataToShow += "<p>" + "<b>Posición</b>: Defensa</p>";
                        } else if (playerPosition == "Attacker") {
                            dataToShow += "<p>" + "<b>Posición</b>: Delantero</p>";
                        } else {
                            //Mediocentro
                            dataToShow += "<p>" + "<b>Posición</b>: Centrocampista</p>";
                        }
                        if (playerGoals != null) {
                            dataToShow += "<p>" + "<b>Goles marcados</b>: " + playerGoals + "</p>";
                        } else {
                            dataToShow += "<p>" + "<b>Goles marcados</b>: " + 0 + "</p>";
                        }
                    }
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

        this.getIdTeam(this.apikey, "sporting gijon", "2021", this.getIdPlayers);
    }
}
var jsonManager = new JSONManager();