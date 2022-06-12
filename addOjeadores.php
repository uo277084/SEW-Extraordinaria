<?php 

session_start();

class OjeadoresAdd {

    private $db;
    private $message;

    private $localhost = "localhost";
    private $username = "DBUSER2021";
    private $password = "DBPSWD2021";
    private $dbname = "ojeadores2122";

    /**
     * Constructor.
     */
    public function __construct() {
        $this->message = "";
        $this->createDB();
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
     * Función que añade los datos de un archivo JSON a la base de datos
     */
    public function loadJSON(){
        $json = $_FILES['jsonFile'];
        $info = file_get_contents($json['tmp_name']);
        $ojeadores = json_decode($info, true);
        $this->message = "";

        foreach ($ojeadores as $ojeador) {
            $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
            $queryMaxId ="SELECT MAX(id) FROM Ojeador";
            $stmt = $this->db->prepare($queryMaxId);
            $stmt->execute();
            $result=$stmt->get_result();
            $newIdO = 0;
            while($row = $result->fetch_array()){
                $newIdO = $row[0] + 1;
            }

            //datos de ojeador
            $nombre = $ojeador["nombre"];
            $apellidos = $ojeador["apellidos"];
            $edad = $ojeador["age"];
            $ciudad_nacimiento= $ojeador["ciudad_nacimiento"];
            $jugadores = $ojeador["jugadores"];

            //inserta ojeador
            $this->insertOjeador($nombre, $apellidos, $edad, $ciudad_nacimiento, $newIdO);

            //recorrer jugadores
            foreach ($jugadores as $jugador) {
                $nombreJ = $jugador["nombre"];
                $apellidosJ = $jugador["apellidos"];
                $edadJ = $jugador["age"];
                $internacional = $jugador["internacional"];
                $posicion = $jugador["posicion"];
                $pais = $jugador["pais"];
                $goles = $jugador["goles"];
                $negociacion = $jugador["negociacion"];
                $tiempo_meses = $jugador["tiempo_meses"];
                $equipo = $jugador["equipo"];

                //calcular id jugador
                $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
                $queryMaxId2 ="SELECT MAX(id) FROM Jugador";
                $stmt2 = $this->db->prepare($queryMaxId2);
                $stmt2->execute();
                $result2=$stmt2->get_result();
                $newIdJ = 0;
                while($row2 = $result2->fetch_array()){
                    $newIdJ = $row2[0] + 1;
                }

                //datos del equipo (se tiene que crear primero el equipo)
                $nombreE = $equipo["nombre"];
                $ciudad = $equipo["ciudad"];
                $estadio = $equipo["estadio"];
                $liga = $equipo["liga"];
                $posicion_liga = $equipo["posicion_liga"];

                //calcular id equipo
                $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
                $queryMaxId3 ="SELECT MAX(id) FROM Equipo";
                $stmt3 = $this->db->prepare($queryMaxId3);
                $stmt3->execute();
                $result3=$stmt3->get_result();
                $newIdE = 0;
                while($row3 = $result3->fetch_array()){
                    $newIdE = $row3[0] + 1;
                }

                //inserta equipo
                $this->insertEquipo($nombreE, $ciudad, $estadio, $liga, $posicion_liga, $newIdE);

                //inserta jugador
                $this->insertJugador($nombreJ, $apellidosJ, $edadJ, $internacional, $posicion, $pais, $goles, $newIdE, $newIdJ);

                //insertar relacion ojea entre jugador y ojeador
                $this->insertOjea($negociacion, $tiempo_meses, $newIdO, $newIdJ);

                //cerramos statements
                $stmt2->close();
                $stmt3->close();
            }
            //cerramos statement
            $stmt->close();
        }
        $this->message .="Datos cargados correctamente";
        
    }

    /**
     * Función que inserta un ojeador en la base de datos
     */
    public function insertOjeador($nombre, $apellidos, $edad, $ciudad_nacimiento, $id){
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "INSERT INTO Ojeador(nombre, apellidos, edad, ciudad_nacimiento, id) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssiss", $nombre, $apellidos, $edad, $ciudad_nacimiento, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Función que inserta un equipo en la base de datos
     */ 
    public function insertEquipo($nombre, $ciudad, $estadio, $liga, $posicion_liga, $id){
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "INSERT INTO Equipo(nombre, ciudad, estadio, liga, posicion_liga, id) VALUES (?,?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssssis", $nombre, $ciudad, $estadio, $liga, $posicion_liga, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Función que inserta un jugador en la base de datos
     */
    public function insertJugador($nombre, $apellidos, $edad, $internacional, $posicion, $pais, $goles, $id_equipo, $id){
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "INSERT INTO Jugador(nombre, apellidos, edad, internacional, posicion, pais, goles, id_equipo, id) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssiississ", $nombre, $apellidos, $edad, $internacional, $posicion, $pais, $goles, $id_equipo, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Función que inserta una relación ojea en la base de datos
     */
    public function insertOjea($negociacion, $tiempo_meses, $id_ojeador, $id_jugador){
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "INSERT INTO Ojea(negociacion, tiempo_meses, id_ojeador, id_jugador) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiss", $negociacion, $tiempo_meses, $id_ojeador, $id_jugador);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Función que devuelve el atributo message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Función que le da el valor pasado por parámetro al atributo message
     */
    public function setMessage($messagetext) {
        $this->message = $messagetext;
    }

    /**
     * Función que le da valor vacío al atributo message
     */
    public function clearMessage() {
        $this->message = "";
    }
}

if(isset($_SESSION['dba'])){
    //Si no se crea
    //$_SESSION['db'] = new OjeadoresDB();
} else {
    $_SESSION['dba'] = new OjeadoresAdd();
}

$_SESSION['dba']->clearMessage();

if (count($_POST) > 0){

    if(isset($_POST['subirFile'])){
        $_SESSION['dba']->loadJSON();
    }
}

$messageShow = $_SESSION['dba']->getMessage();

//Mostrar html
echo 
    "<!DOCTYPE HTML>

    <html lang='es'>

    <head>
        <!-- Datos que describen el documento -->
        <meta charset='UTF-8' />
        <meta name='author' content='Tania Bajo García' />
        <meta name='description' content='Añadir ojeadores'/>
        <meta name='keywords' content='sporting,gijon,real,ojeadores,trabajo,jugadores,equipo' />
        <title>Trabajo</title>
        <link rel='stylesheet' type='text/css' href='estilo/estilo.css' />
        <link rel='stylesheet' type='text/css' href='estilo/estiloOjeadoresPhp.css' />
        <link rel='stylesheet' type='text/css' href='estilo/layout.css' />
    </head>
    
    <body>
        <header>
            <nav>
                <ul>
                    <li>
                        <a href='index.html'>
                            <img src='multimedia/html/escudo.png' alt='Escudo del Real Sporting de Gijón'>
                        </a>
                    </li>
                    <li>
                        <a title='Información del Real Sporting de Gijón' tabindex='1' href='index.html'
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
                    <li>
                    <a title='Ojeadores' tabindex='7' href='ojeadores.php' accesskey='O'>Ojeadores</a>
                </li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>Trabaja con nosotros</h1> 
            <section>
                <h2>Nuevos ojeadores</h2>
                <p>
                    ¿Quieres trabajar con nosotros? ¿Tus compañeros también? ¡Uníos a nosotros!
                </p>
                <p>
                    Sólo tendréis que adjuntar un archivo JSON en el que aparezcan vuestros datos, pulsa el botón 'Subir' y listo.
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
                <p>
                        El archivo tendrá que tener la siguiente estructura para ser válido:
                </p>
                <pre>
                        [
                            {
                                \"nombre\": \"Nombre ojeador\",
                                \"apellidos\": \"Apellidos ojeador\",
                                \"edad\": \"Edad\",
                                \"ciudad_nacimiento\": \"Ciudad de nacimiento\",
                                \"jugadores\": [
                                    {
                                        \"nombre\": \"Nombre jugador\",
                                        \"apellidos\": \"Apellidos jugador\",
                                        \"edad\": \"Edad\",
                                        \"internacional\": \"Internacionalidad (true o false)\",
                                        \"posicion\": \"Posición\",
                                        \"pais\": \"País de procedencia\",
                                        \"goles\": \"Goles\",
                                        \"negociaciones\": \"Negociaciones (true o false)\",
                                        \"tiempo_meses\": \"Tiempo ojeado en meses\"
                                        \"equipo\": {
                                            \"nombre\": \"Nombre equipo\",
                                            \"ciudad\": \"Ciudad\",
                                            \"estadio\": \"Estadio\",
                                            \"liga\": \"Liga\",
                                            \"posicion_liga\": \"Posición\"
                                        }
                                    },
                                    {
                                        ...
                                    }
                                ]
                            },
                            {
                                ...
                            }
                        ]
                </pre>
                <!--Introducir formulario para subir archivo-->
                <form action='' method='post' enctype='multipart/form-data'>
                    <fieldset>
                        <label for='jsonFileId'>Sube el archivo JSON que desea procesar:</label>
                        <input id='jsonFileId' type='file' name='jsonFile' required/>
                        <button type='submit' name='subirFile' value='Subir'>Subir</button>
                    </fieldset>
                </form>
                <p>
                    $messageShow
                </p>	
            </section>
        </main>

        <footer>
            <p>Página Real Sporting de Gijón | Web | Copyright @2022 Tania Bajo García</p>
        </footer>
    </body>

    </html>"
?>