"use strict";
class JSONManager {
    constructor() {
        this.apikey = "09814bfb9805145a110799740887e318";
        this.league = 2;
        this.req1 = "teams";
        this.req2 = "team_players";
        this.team = "45678"; //TODO
        this.year = 2022;
        this.urlTeam = "https://apiclient.besoccerapps.com/scripts/api/api.php?key=" + this.apikey + "&tz=Europe/Madrid&format=json&req=" + this.req1 + "&league=" + this.league + "&year=" + this.year;

        //Formato de llamada a la api
        //https://www.apiclient.resultados-futbol.com/scripts/api/api.php?key=YOUR_KEY&format=json&req=categories&filter=all
        //Para coger la id de un equipo
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=YOUR_KEY & tz=Europe/Madrid & format=json & req=teams & league=1 & year=2017
        //Para coger los jugadores de un equipo
        //https://apiclient.besoccerapps.com/scripts/api/api.php?key=YOUR_KEY&tz=Europe/Madrid&format=json&req=team_players&team=6313715&year=2020

    }

    getIdTeamCompetition(url) {
        $.ajax({
            dataType: "json",
            url: url,
            method: 'GET',
            success: function (data) {
                var datos = data;
            },
            error: function () {
                $("section").html("Hay problemas para obtener el identificador del sporting, ¡lo sentimos!");
            }
        });
    }

    loadInfoPlayersJSON() {
        //Crear elemento donde se va a mostrar la información
        var p = document.createElement("figure");
        p.innerHTML = "";
        $("section").after(p);

        //Primero hacer la llamada a la api para coger la id de la competicion del equipo
        this.getIdTeamCompetition(this.urlTeam);
    }
}
var jsonManager = new JSONManager();