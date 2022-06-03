"use strict";
class Geolocalization {
    constructor() {
        navigator.geolocation.getCurrentPosition(this.getPosicion.bind(this));
        this.longitudMolinon = -5.63747;
        this.latitudMolinon = 43.53618;
    }

    getPosicion(posicion) {
        this.longitud = posicion.coords.longitude;
        this.latitud = posicion.coords.latitude;
    }
    calculateDistance() {
        if (!navigator.geolocation) {
            alert("El navegador que estás usando no soporta API Geolocalización");
        }
        //Area donde mostraremos el contenido del archivo
        var dataArea = document.getElementById('ubicacion');

        //Calculos de radianes de las coordenadas
        var radLongMolinon = this.longitudMolinon * (Math.PI / 180);
        var radLatMolinon = this.latitudMolinon * (Math.PI / 180);
        var radLong = this.longitud * (Math.PI / 180);
        var radLat = this.latitud * (Math.PI / 180);

        //Diferencia de las longitudes y latitudes
        var difLong = radLongMolinon - radLong;
        var difLat = radLatMolinon - radLat;

        //Radio de la tierra
        var radHearth = 6371;

        var a = Math.sin(difLat / 2) * Math.sin(difLat / 2) + Math.cos(radLat) * Math.cos(radLatMolinon) * Math.sin(difLong / 2) * Math.sin(difLong / 2);
        var b = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var c = radHearth * b;
        var d = c.toFixed(3);

        //Para poner los datos con comas
        var datos = d.split(".");
        dataArea.innerHTML = "<p>Estas a una distancia de " + datos[0] + "," + datos[1] + " km del Molinón</p>" +
            "<figure>* Si no se carga correctamente la distancia, recargue la página y vuélvalo a intentar.</figure>";
    }
}
var geolocalization = new Geolocalization();