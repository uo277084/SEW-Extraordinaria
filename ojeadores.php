<?php 

session_start();

class OjeadoresDB {

    private $db;
    private $html;

    private $localhost = "localhost";
    private $username = "DBUSER2021";
    private $password = "DBPSWD2021";
    private $dbname = "ojeadores2122";

    public function __construct() {
        $this->html = "";
        $this->createDB();
        $this->createTables();
    }

    /**
     * Función que crea la base de datos
     */
    public function createDB() {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, "");
        if($this->db->connect_error)    {
            exit ("<h2>ERROR de conexión:".$db->connect_error."</h2>");
        }
        $this->db->query("CREATE DATABASE IF NOT EXISTS $this->dbname COLLATE utf8_spanish_ci;");
    }

    /**
     * Función que crea las tablas
     */
    public function createTables() {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);

        //Tabla Ojeador
        $this->db->query("CREATE TABLE IF NOT EXISTS Ojeador (
                            nombre VARCHAR(255) NOT NULL,
                            apellidos VARCHAR(255) NOT NULL,
                            edad INT NOT NULL,
                            ciudad_nacimiento VARCHAR(255) NOT NULL,
                            id VARCHAR(255) NOT NULL,
                            
                            PRIMARY KEY (id)
        );");

        //Tabla Equipo
        $this->db->query("CREATE TABLE IF NOT EXISTS Equipo (
                            nombre VARCHAR(255) NOT NULL,
                            ciudad VARCHAR(255) NOT NULL,
                            estadio VARCHAR(255) NOT NULL,
                            liga VARCHAR(255) NOT NULL,
                            posicion_liga INT NOT NULL,
                            id VARCHAR(255) NOT NULL,
                            
                            PRIMARY KEY (id)
        );");

        //Tabla Jugador
        $this->db->query("CREATE TABLE IF NOT EXISTS Jugador (
                            nombre VARCHAR(255) NOT NULL,
                            apellidos VARCHAR(255) NOT NULL,
                            edad INT NOT NULL,
                            internacional BOOLEAN NOT NULL,
                            posicion VARCHAR(255) NOT NULL,
                            pais VARCHAR(255) NOT NULL,
                            goles INT NOT NULL,
                            id_equipo VARCHAR(255) NOT NULL,
                            id VARCHAR(255) NOT NULL,
                            
                            PRIMARY KEY (id),
                            CONSTRAINT FK_Equipo FOREIGN KEY (id_equipo) REFERENCES Equipo(id)
        );");

        //Tabla Ojea
        $this->db->query("CREATE TABLE IF NOT EXISTS Ojea (
                            negociacion BOOLEAN NOT NULL,
                            tiempo_meses INT NOT NULL,

                            /* Ids objetos relacionados */
                            id_ojeador VARCHAR(255) NOT NULL,
                            id_jugador VARCHAR(255) NOT NULL,
                            
                            PRIMARY KEY (id_ojeador, id_jugador),
                            CONSTRAINT FK_Ojeador FOREIGN KEY (id_ojeador) REFERENCES Ojeador(id),
                            CONSTRAINT FK_Jugador FOREIGN KEY (id_jugador) REFERENCES Jugador(id)
        );");
    }

    public function getHTML() {
        return $this->html;
    }

    public function getOjeadores() {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Ojeador";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $list = "";
        if($result->num_rows > 0) {
            $list .= "<p>Los ojeadores que actualmente trabajan para el Real Sporting de Gijón son:</p>";
            $list .= "<table>";
            $list .= "<caption>Ojeadores</caption>";
            $list .= "<tr><th>Nombre</th><th>Apellidos</th><th>Edad</th><th>Ciudad</th><th>Id</th></tr>";
            while($row = $result->fetch_assoc()) {
                $list .= "<tr>";
                $list .= "<td>".$row["nombre"]." "."</td>";
                $list .= "<td>".$row["apellidos"]."</td>";
                $list .= "<td>".$row["edad"]."</td>";
                $list .= "<td>".$row["ciudad_nacimiento"]."</td>";
                $list .= "</tr>";
            }
            $list .= "</table>";
        }else{
            $list .= "<p>No hay Ojeadores</p>";
        }
        return $list;
    }

    public function searchJugadorHTML() {
        $this->html = 
            "<h2>Área de búsqueda</h2>
            <form action='#' method='post'>
                <label for='nsj'>Inserte nombre o apellido del jugador que desea buscar</label>
                <input id='nsj' type='text' name='nameSurnameJ' placeholder='Nombre o apellido' />
                <input type='submit' name='juNaSu' value='Buscar' />
            </form>";
    }

    public function searchJugador($text){
        $textQuery = "%"+$text+"%";
        $this->html = "";
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Jugador WHERE nombre LIKE ? OR apellidos LIKE ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $textQuery, $textQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_array()) {
            $query2 = "SELECT * FROM Equipo WHERE id = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("s", $row['id_equipo']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            $this->html .= "<p>
                                <h3>".$row['nombre']." ".$row['apellidos']."</h3>
                                <p><b>Edad:</b> ".$row['edad']."</p>
                                <p><b>Internacional:</b> ".($row['internacional'] ? "Si" : "No")."</p>
                                <p><b>Posición:</b> ".$row['posicion']."</p>
                                <p><b>País:</b> ".$row['pais']."</p>
                                <p><b>Goles:</b> ".$row['goles']."</p>
                                <p><b>Equipo:</b> ".$result2->fetch_array()['nombre']."</p>
                            </p>";
        }
    }

    public function searchOjeadorHTML() {
        $this->html = 
            "<h2>Área de búsqueda</h2>
            <form action='#' method='post'>
                <label for='nso'>Inserte nombre o apellido del ojeador que desea buscar</label>
                <input id='nso' type='text' name='nameSurnameO' placeholder='Nombre o apellido' />
                <input type='submit' name='ojNaSu' value='Buscar' />
            </form>";
    }

    public function searchOjeador($text){
        $textQuery = "%"+$text+"%";
        $this->html = "";
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Ojeador WHERE nombre LIKE ? OR apellidos LIKE ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $textQuery, $textQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_array()) {
            $this->html .= "<p>
                                <h3>".$row['nombre']." ".$row['apellidos']."</h3>
                                <p><b>Edad:</b> ".$row['edad']."</p>
                                <p><b>Ciudad de nacimiento:</b> ".$row['ciudad_nacimiento']."</p>
                            </p>";
        }
    }
}

if(isset($_SESSION['db'])){
    //Si no se crea
    //$_SESSION['db'] = new OjeadoresDB();
} else {
    $_SESSION['db'] = new OjeadoresDB();
}

if (count($_POST) > 0){
    if(isset($_POST['jugadorNameSurname'])){
        //quiere buscar por nombre/apellido de jugador
        $_SESSION['db']->searchJugadorHTML();
    }
    if(isset($_POST['juNaSu'])){
        //ha interactuado con el input de buscar jugador
        $_SESSION['db']->searchJugador($_POST['nameSurnameJ']);
    }
    if(isset($_POST['ojeadorNameSurname'])){
        //quiere buscar por nombre/apellido de ojeador
        $_SESSION['db']->searchOjeadorHTML();
    }
    if(isset($_POST['ojNaSu'])){
        //ha interactuado con el input de buscar ojeador
        $_SESSION['db']->searchOjeador($_POST['nameSurnameO']);
    }
    if(isset($_POST['numLiga'])){
        //quiere num de equipos de una liga
    }
    if(isset($_POST['nmLg'])){
        //ha interactuado con el input num liga
    }
    if(isset($_POST['jugadorNegoOjeadorNombre'])){
        //quiere num de equipos de una liga
    }
    if(isset($_POST['juNeOjNo'])){
        //ha interactuado con el input num liga
    }
    if(isset($_POST['golesEquipo'])){
        //quiere num de equipos de una liga
    }
    if(isset($_POST['goEq'])){
        //ha interactuado con el input num liga
    }
    if(isset($_POST['jugadorInterMedio'])){
        //quiere num de equipos de una liga
    }
    if(isset($_POST['juInMe'])){
        //ha interactuado con el input num liga
    }
}

$listaOjeadores = $_SESSION['db']->getOjeadores();
$htmlShow = $_SESSION['db']->getHTML();

//Mostrar html
echo 
    "<!DOCTYPE HTML>

    <html lang='es'>

    <head>
        <!-- Datos que describen el documento -->
        <meta charset='UTF-8' />
        <meta name='author' content='Tania Bajo García' />
        <meta name='description' content='Información sobre el Real Sporting de Gijón'/>
        <meta name='keywords' content='sporting,gijon,real,informacion,localizacion,estadio,equipacion' />
        <title>Información del Real Sporting de Gijón</title>
        <link rel='stylesheet' type='text/css' href='estilo/estilo.css' />
        <link rel='stylesheet' type='text/css' href='estilo/layout.css' />
    </head>
    
    <body>
        <header>
            <nav>
                <ul>
                    <li>
                        <a href='informacion.html'>
                            <img src='multimedia/html/escudo.png' alt='Escudo del Real Sporting de Gijón'>
                        </a>
                    </li>
                    <li>
                        <a title='Información del Real Sporting de Gijón' tabindex='1' href='informacion.html'
                            accesskey='I'>Información</a>
                    </li>
                    <li>
                        <a title='Logros del Real Sporting de Gijón' tabindex='2' href='listaLogros.html'
                            accesskey='L'>Logros</a>
                    </li>
                    <li>
                        <a title='Quini - Jugador histórico' tabindex='3' href='jugadorHistorico.html'
                            accesskey='Q'>Quini</a>
                    </li>
                    <li>
                        <a title='Equipo' tabindex='4' href='jugadoresEquipo.html' accesskey='E'>Equipo</a>
                    </li>
                    <li>
                        <a title='Como llegar a El Molinón' tabindex='5' href='comoLlegar.html' accesskey='C'>Como
                            llegar</a>
                    </li>
                    <li>
                        <a title='Plantilla 21/22' tabindex='6' href='plantillaTemporada.html' accesskey='P'>Plantilla
                            21/22</a>
                    </li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>Ojeadores temporada 21/22</h1> 
            <section>
                <h2>Ojeadores actuales</h2>
                $listaOjeadores
            </section>
            <section>
                <h2>Consulta de información</h2>
                <p>
                    Actualmente, el Real Sporting cuenta con los servicios de varios ojeadores. Tienes la posibilidad
                     de consultar información relativa a ellos, a los jugadores que ojean e, incluso de los equipos 
                     en los que juegan los jugadores ojeados.
                </p>
                <!--Opciones de manejo-->
                <form action='#' method='post'>
                    <fieldset>
                        <label for='jns'>Buscar por nombre o apellido de jugador</label>
                        <input id='jns' type='submit' name='jugadorNameSurname' value='Buscar' />
                    </fieldset>
                    <fieldset>
                        <label for='ons'>Buscar por nombre o apellido de ojeador</label>
                        <input id='ons' type='submit' name='ojeadorNameSurname' value='Buscar' />
                    </fieldset>
                    <fieldset>
                        <label for='nl'>Número de jugadores ojeados de la liga que busques</label>
                        <input id='nl'type='submit' name='numLiga' value='Buscar' />
                    </fieldset>
                    <fieldset>
                        <label for='jnon'>Jugadores con negociaciones activas con el ojeador que 
                            tenga el nombre que busques</label>
                        <input id='jnon' type='submit' name='jugadorNegoOjeadorNombre' value='Buscar' />
                    </fieldset>
                    <fieldset>
                        <label for='ge'>Número de goles marcados por los jugadores del equipo que busques</label>
                        <input id='ge' type='submit' name='golesEquipo' value='Buscar' />
                    </fieldset>
                    <fieldset>
                        <label for='jim'>Jugadores internacionales ojeados por más de medio año</label>
                        <input id='jim' type='submit' name='jugadorInterMedio' value='Buscar' />
                    </fieldset>
                </form>
            </section>
            <section>
                $htmlShow
            <section>
                <h2>Nuevos ojeadores</h2>
                <p>
                    ¿Quieres trabajar con nosotros? ¿Tus compañeros también? ¡Uníos a nosotros!
                </p>
                <p>
                    Sólo tendréis que adjuntar un archivo JSON en el que aparezcan vuestros datos, pulsar 
                    el botón 'Subir' y listo.
                </p>
                <p>
                    Los datos que debéis especificar son:
                        <ul>
                            <li>Nombre</li>
                            <li>Apellidos</li>
                            <li>Edad</li>
                            <li>Ciudad de nacimiento</li>
                        </ul>
                </p>
                <p>
                    Si actualmente estaís detrás de un jugador, deberas proporcionar los siguientes datos sobre él:
                        <ul>
                            <li>Nombre</li>
                            <li>Apellidos</li>
                            <li>Edad</li>
                            <li>Internacionalidad del jugador (sí o no)</li>
                            <li>Posición de juego</li>
                            <li>País de procedencia</li>
                            <li>Goles marcados</li>
                            <li>Si hay negociaciones actualmente o no</li>
                            <li>Tiempo (en meses) que llevas ojeándolo</li>
                            <li>Datos del equipo en el que juega (especificados a continuación)</li>
                        </ul>
                </p>
                <p>
                    Los datos del equipo que deberán figurar en el archivo son los siguientes:
                        <ul>
                            <li>Nombre</li>
                            <li>Ciudad</li>
                            <li>Estadio</li>
                            <li>Liga</li>
                            <li>Posición en la liga</li>
                        </ul>
                </p>
                <!--Introducir formulario para subir archivo-->
                <fieldset>
                    <label for='jsonFile'>Sube el archivo JSON que desea procesar:</label>
                    <input id='jsonFile' type='file' />
                </fieldset>
                <p>
                    <input type='button' value='Subir' />
                </p>
            </section>
            <!--Poner aquí el manejo de búsqueda en las tablas--> 
        </main>

        <footer>
            <p>Página Real Sporting de Gijón | Web | Copyright @2022 Tania Bajo García</p>
        </footer>
    </body>

    </html>"
?>