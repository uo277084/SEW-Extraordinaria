<?php 

session_start();

class OjeadoresDB {

    private $db;
    private $html;
    private $message;

    private $localhost = "localhost";
    private $username = "DBUSER2021";
    private $password = "DBPSWD2021";
    private $dbname = "ojeadores2122";

    /**
     * Constructor. Crea una conexión a la base de datos, crea las tablas si no existen y carga los datos del archivo CSV a la base de datos.
     */
    public function __construct() {
        $this->html = "";
        $this->createDB();
        $this->createTables();
        $this->loadData();
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
                            id INT NOT NULL,
                            
                            PRIMARY KEY (id)
        );");

        //Tabla Equipo
        $this->db->query("CREATE TABLE IF NOT EXISTS Equipo (
                            nombre VARCHAR(255) NOT NULL,
                            ciudad VARCHAR(255) NOT NULL,
                            estadio VARCHAR(255) NOT NULL,
                            liga VARCHAR(255) NOT NULL,
                            posicion_liga INT NOT NULL,
                            id INT NOT NULL,
                            
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
                            id_equipo INT NOT NULL,
                            id INT NOT NULL,
                            
                            PRIMARY KEY (id),
                            CONSTRAINT FK_Equipo FOREIGN KEY (id_equipo) REFERENCES Equipo(id)
        );");

        //Tabla Ojea
        $this->db->query("CREATE TABLE IF NOT EXISTS Ojea (
                            negociacion BOOLEAN NOT NULL,
                            tiempo_meses INT NOT NULL,

                            /* Ids objetos relacionados */
                            id_ojeador INT NOT NULL,
                            id_jugador INT NOT NULL,
                            
                            PRIMARY KEY (id_ojeador, id_jugador),
                            CONSTRAINT FK_Ojeador FOREIGN KEY (id_ojeador) REFERENCES Ojeador(id),
                            CONSTRAINT FK_Jugador FOREIGN KEY (id_jugador) REFERENCES Jugador(id)
        );");
    }

    /**
     * Función que carga los datos del archivo csv en la base de datos
     */
    public function loadData(){
        $file = fopen("dataBase.csv", "r");
        while(($line = fgetcsv($file)) !== false){
            if($line[0] == "O"){
                $query = "SELECT count(*) FROM Ojeador WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("s", $line[5]);
                $stmt->execute();
                $result = $stmt->get_result();
                $num=0;
                while($row = $result->fetch_array()) {
                    $num = $row[0];
                }
                $stmt->close();
                if($num==0){
                    $this->insertOjeador($line[1], $line[2], $line[3], $line[4], $line[5]);
                }
            }
            else if($line[0] == "E"){
                $query = "SELECT count(*) FROM Equipo WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("s", $line[6]);
                $stmt->execute();
                $result = $stmt->get_result();
                $num=0;
                while($row = $result->fetch_array()) {
                    $num = $row[0];
                }
                $stmt->close();
                if($num==0){
                    $this->insertEquipo($line[1], $line[2], $line[3], $line[4], $line[5], $line[6]);
                }
                
            }
            else if($line[0] == "J"){
                $query = "SELECT count(*) FROM Jugador WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("s", $line[9]);
                $stmt->execute();
                $result = $stmt->get_result();
                $num=0;
                while($row = $result->fetch_array()) {
                    $num = $row[0];
                }
                $stmt->close();
                if($num==0){
                    $this->insertJugador($line[1], $line[2], $line[3], $line[4], $line[5], $line[6], $line[7], $line[8], $line[9]);
                }
            }
            else if($line[0] == "A"){
                $query = "SELECT count(*) FROM Ojea WHERE id_ojeador = ? AND id_jugador = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ss", $line[8], $line[9]);
                $stmt->execute();
                $result = $stmt->get_result();
                $num=0;
                while($row = $result->fetch_array()) {
                    $num = $row[0];
                }
                $stmt->close();
                if($num==0){
                    $this->insertOjea($line[1], $line[2], $line[3], $line[4]);
                }
            }
        }
        fclose($file);
    }

    /**
     * Función que inserta un ojeador en la base de datos
     */
    public function insertOjeador($nombre, $apellidos, $edad, $ciudad_nacimiento, $id){
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "INSERT INTO Ojeador(nombre, apellidos, edad, ciudad_nacimiento, id) VALUES (?,?,?,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssisi", $nombre, $apellidos, $edad, $ciudad_nacimiento, $id);
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
        $stmt->bind_param("ssssii", $nombre, $ciudad, $estadio, $liga, $posicion_liga, $id);
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
        $stmt->bind_param("ssiissiii", $nombre, $apellidos, $edad, $internacional, $posicion, $pais, $goles, $id_equipo, $id);
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
        $stmt->bind_param("iiii", $negociacion, $tiempo_meses, $id_ojeador, $id_jugador);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Función que devuelve el atributo html
     */
    public function getHTML() {
        return $this->html;
    }

    /**
     * Función que le da valor vacío al atributo html
     */
    public function clearHTML() {
        $this->html = "";
    }

    /**
     * Función que recoge los nombres y apellidos de todos los ojeadores
     */
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
            $list .= "<tr><th>Nombre</th><th>Apellidos</th></tr>";
            while($row = $result->fetch_assoc()) {
                $list .= "<tr>";
                $list .= "<td>".$row["nombre"]." "."</td>";
                $list .= "<td>".$row["apellidos"]."</td>";
                $list .= "</tr>";
            }
            $list .= "</table>";
        }else{
            $list .= "<p>No hay Ojeadores</p>";
        }
        $stmt->close();
        return $list;
    }

    /**
     * Función que establece el valor de html como un formulario para insertar el nombre de un equipo
     */
    public function searchEquipoHTML() {
        $this->html = 
            "<h2>Área de búsqueda</h2>
            <form action='#' method='post'>
                <label for='ne'>Inserte nombre del equipo que desea buscar (la primera letra del nombre debe ir en mayúsculas)</label>
                <input id='ne' type='text' name='nameE' placeholder='Nombre' />
                <input type='submit' name='eqNa' value='Buscar' />
            </form>";
    }

    /**
     * Función que busca el equipo cuyo nombre contenga la cadena pasada como parámetro	
     */
    public function searchEquipo($text){
        $textQuery = "%".$text."%";
        $this->html = "<h2>Resultado de la búsqueda</h2>";
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Equipo WHERE nombre LIKE ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $textQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows >0){
            while($row = $result->fetch_array()) {
                $query2 = "SELECT * FROM Jugador WHERE id_equipo = ?";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param("s", $row['id']);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                while($row2 = $result2->fetch_array()) {
                    $this->html .= "<p>
                                        <h3>".$row2['nombre']." ".$row2['apellidos']."</h3>
                                        <p><b>Edad:</b> ".$row2['edad']."</p>
                                        <p><b>Internacional:</b> ".($row2['internacional'] ? "Si" : "No")."</p>
                                        <p><b>Posición:</b> ".$row2['posicion']."</p>
                                        <p><b>País:</b> ".$row2['pais']."</p>
                                        <p><b>Goles:</b> ".$row2['goles']."</p>
                                        <p><b>Equipo:</b> ".$row['nombre']."</p>
                                    </p>";
                }
                
                $stmt2->close();
            }
        }else{
            $this->html .= "<p>No hay resultados para tu búsqueda</p>";
        }
        $stmt->close();
    }

    /**
     * Función que establece el valor de html como un formulario para insertar el nombre o apellidos de un jugador
     */
    public function searchJugadorHTML() {
        $this->html = 
            "<h2>Área de búsqueda</h2>
            <form action='#' method='post'>
                <label for='nsj'>Inserte nombre o apellido del jugador que desea buscar (la primera letra del nombre y de los apellidos debe ir en mayúsculas)</label>
                <input id='nsj' type='text' name='nameSurnameJ' placeholder='Nombre o apellido' />
                <input type='submit' name='juNaSu' value='Buscar' />
            </form>";
    }

    /**
     * Función que busca el jugador cuyo nombre o apellido contenga la cadena pasada como parámetro	
     */
    public function searchJugador($text){
        $textQuery = "%".$text."%";
        $this->html = "<h2>Resultado de la búsqueda</h2>";
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Jugador WHERE nombre LIKE ? OR apellidos LIKE ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $textQuery, $textQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows >0){
            while($row = $result->fetch_array()) {
                $query2 = "SELECT * FROM Equipo WHERE id = ?";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param("s", $row['id_equipo']);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $teamName="";
                while($row2 = $result2->fetch_array()) {
                    $teamName = $row2['nombre'];
                }

                $this->html .= "<p>
                                    <h3>".$row['nombre']." ".$row['apellidos']."</h3>
                                    <p><b>Edad:</b> ".$row['edad']."</p>
                                    <p><b>Internacional:</b> ".($row['internacional'] ? "Si" : "No")."</p>
                                    <p><b>Posición:</b> ".$row['posicion']."</p>
                                    <p><b>País:</b> ".$row['pais']."</p>
                                    <p><b>Goles:</b> ".$row['goles']."</p>
                                    <p><b>Equipo:</b> ".$teamName."</p>
                                </p>";
                
                $stmt2->close();
            }
        }else{
            $this->html .= "<p>No hay resultados para tu búsqueda</p>";
        }
        $stmt->close();
    }

    /**
     * Función que establece el valor de html como un formulario para insertar el nombre o apellidos de un ojeador
     */
    public function searchOjeadorHTML() {
        $this->html = 
            "<h2>Área de búsqueda</h2>
            <form action='#' method='post'>
                <label for='nso'>Inserte nombre o apellido del ojeador que desea buscar (la primera letra del nombre y de los apellidos debe ir en mayúsculas)</label>
                <input id='nso' type='text' name='nameSurnameO' placeholder='Nombre o apellido' />
                <input type='submit' name='ojNaSu' value='Buscar' />
            </form>";
    }

    /**
     * Función que busca el ojeador cuyo nombre o apellido contenga la cadena pasada como parámetro	
     */
    public function searchOjeador($text){
        $textQuery = "%".$text."%";
        $this->html = "<h2>Resultado de la búsqueda</h2>";
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Ojeador WHERE nombre LIKE ? OR apellidos LIKE ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $textQuery, $textQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows >0){
            while($row = $result->fetch_array()) {
                $idOj = $row['id'];
                $this->html .= "<p>
                                    <h3>".$row['nombre']." ".$row['apellidos']."</h3>
                                    <p><b>Edad:</b> ".$row['edad']."</p>
                                    <p><b>Ciudad de nacimiento:</b> ".$row['ciudad_nacimiento']."</p>";

                $query2 = "SELECT * FROM Ojea WHERE id_ojeador = ?";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param("s", $idOj);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                $this->html .="<p><b>Jugadores que ojea:</b> <p><ol>";
                while($row2 = $result2->fetch_array()) {
                    $idJu = $row2['id_jugador'];

                    $query3 = "SELECT * FROM Jugador WHERE id = ?";
                    $stmt3 = $this->db->prepare($query3);
                    $stmt3->bind_param("s", $idJu);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();

                    while($row3 = $result3->fetch_array()) {
                        $query4 = "SELECT * FROM Equipo WHERE id = ?";
                        $stmt4 = $this->db->prepare($query4);
                        $stmt4->bind_param("s", $row3['id_equipo']);
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                        $teamName = "";
                        while($row4 = $result4->fetch_array()) {
                            $teamName = $row4['nombre'];
                        }

                        $this->html .= "<li>".$row3['nombre']." ".$row3['apellidos']." (".$teamName.")</li>";
                        
                        $stmt4->close();
                    }
                    $stmt3->close();
                }
                $this->html .= "</ol></p></p></p>";
                $stmt2->close();
            }
        }else{
            $this->html .= "<p>No hay resultados para tu búsqueda</p>";
        }
        $stmt->close();
    }

    /**
     * Función que establece el valor de html como un formulario para insertar el nombre o apellidos de un ojeador
     */
    public function negoOjeadorHTML() {
        $this->html = 
            "<h2>Área de búsqueda</h2>
            <form action='#' method='post'>
                <label for='nson'>Inserte nombre o apellido del ojeador que desea buscar (la primera letra del nombre y de los apellidos debe ir en mayúsculas)</label>
                <input id='nson' type='text' name='jugadorNegoOjeadorNombre' placeholder='Nombre o apellido' />
                <input type='submit' name='juNeOjNo' value='Buscar' />
            </form>";
    }

    /**
     * Función que busca las negociaciones activas del ojeador cuyo nombre o apellidos contienen la cadena pasada como parámetro	
     */
    public function negoOjeador($text){
        $textQuery = "%".$text."%";
        $this->html = "<h2>Resultado de la búsqueda</h2>";
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Ojeador WHERE nombre LIKE ? OR apellidos LIKE ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $textQuery, $textQuery);
        $stmt->execute();
        $result = $stmt->get_result();

        $idOj = "";
        $idJu = "";
        
        if($result->num_rows >0){
            while($row = $result->fetch_array()) {
                $idOj = $row['id'];
                $this->html = "<h3>Negociaciones activas de ".$row['nombre']."</h3>";

                $query2 = "SELECT * FROM Ojea WHERE id_ojeador = ? AND negociacion = 1";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param("s", $idOj);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                while($row2 = $result2->fetch_array()) {
                    $idJu = $row2['id_jugador'];

                    $query3 = "SELECT * FROM Jugador WHERE id = ?";
                    $stmt3 = $this->db->prepare($query3);
                    $stmt3->bind_param("s", $idJu);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();

                    while($row3 = $result3->fetch_array()) {
                        $query4 = "SELECT * FROM Equipo WHERE id = ?";
                        $stmt4 = $this->db->prepare($query4);
                        $stmt4->bind_param("s", $row3['id_equipo']);
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                        $teamName = "";
                        while($row4 = $result4->fetch_array()) {
                            $teamName = $row4['nombre'];
                        }

                        $this->html .= "<p>
                                            <h4>".$row3['nombre']." ".$row3['apellidos']."</h4>
                                            <p><b>Edad:</b> ".$row3['edad']."</p>
                                            <p><b>Internacional:</b> ".($row3['internacional'] ? "Si" : "No")."</p>
                                            <p><b>Posición:</b> ".$row3['posicion']."</p>
                                            <p><b>País:</b> ".$row3['pais']."</p>
                                            <p><b>Goles:</b> ".$row3['goles']."</p>
                                            <p><b>Equipo:</b> ".$teamName."</p>
                                        </p>";
                        
                        $stmt4->close();
                    }
                    $stmt3->close();
                }
                $stmt2->close();
            }
        }else{
            $this->html .= "<p>No hay resultados para tu búsqueda</p>";
        }
        $stmt->close();
    }

    /**
     * Función que establece el valor de html como un formulario para elegir el radio button del equipo que queremos buscar
     */
    public function golesEquipoHTML() {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Equipo";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $index=1;

        $this->html = "";
        if($result->num_rows > 0) {
            $this->html .= 
            "<h2>Área de búsqueda</h2>
            <p>Elija el equipo que desea buscar:</p>
            <form action='#' method='post'>";

            while($row = $result->fetch_assoc()) {
                $id_equipo = $row['id'];
                $nombre = $row['nombre'];

                $this->html .= "<label for='".$id_equipo."'>".$nombre."</label>";
                if($index!=1){
                    $this->html .= "<input id='".$id_equipo."' type='radio' value='".$id_equipo."' name='equipos'>";
                }else{
                    $this->html .= "<input id='".$id_equipo."' type='radio' value='".$id_equipo."' name='equipos' checked>";
                }
                
                $index += 1;
            }
            $this->html .= "<input type='submit' name='goEq' value='Buscar' />
                        </form>";
        }else{
            $this->html .= "<p>No hay Equipos</p>";
        }
        $stmt->close();
    }

    /**
     * Función que cuenta los goles que han marcado los jugadores del equipo cuyo nombre contiene la cadena pasada como parámetro
     */
    public function golesEquipo($idEquipo) {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Jugador WHERE id_equipo = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $idEquipo);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->html = "<h2>Resultado de la búsqueda</h2>";

        $goles = 0;

        while($row = $result->fetch_array()) {
            $goles += $row['goles'];
        }
    
        $this->html .= "<p>Los jugadores del equipo han marcado ".$goles." goles</p>";

        $stmt->close();
    }

    /**
     * Función que establece el valor de html como un formulario para elegir el radio button de la liga que queremos buscar
     */
    public function numLigaHTML() {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT DISTINCT liga FROM Equipo";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $index=1;

        $this->html = "";
        if($result->num_rows > 0) {
            $this->html .= 
            "<h2>Área de búsqueda</h2>
            <p>Elija una de las siguientes ligas disponibles para buscar:</p>
            <form action='#' method='post'>";

            while($row = $result->fetch_assoc()) {
                $liga = $row['liga'];

                $this->html .= "<label for='".$liga."'>".$liga."</label>";
                if($index!=1){
                    $this->html .= "<input id='".$liga."' type='radio' value='".$liga."' name='ligas'>";
                }else{
                    $this->html .= "<input id='".$liga."' type='radio' value='".$liga."' name='ligas' checked>";
                }
                
                $index += 1;
            }
            $this->html .= "<input type='submit' name='nmLg' value='Buscar' />
                        </form>";
        }else{
            $this->html .= "<p>No hay ligas disponibles</p>";
        }
        $stmt->close();
    }

    /**
     * Función que cuenta el número de jugadores ojeados que hay de la liga cuyo nombre contiene la cadena pasada como parámetro
     */
    public function numLiga($liga) {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Equipo WHERE liga = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $liga);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->html = "<h2>Resultado de la búsqueda</h2>";

        $num = 0;

        while($row = $result->fetch_array()) {
            //Por cada equipo de la liga, se cuenta el numero de jugadores
            $query2 = "SELECT count(*) FROM Jugador WHERE id_equipo = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("s", $row['id']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            while($row2 = $result2->fetch_array()) {
                $num += $row2[0];
            }
            $stmt2->close();
        }

        $this->html .= "<p>Hay ".$num." jugadores que jueguen en ".$liga."</p>";

        $stmt->close();
    }

    /**
     * Función que busca los jugadores internacionales que llevan siendo ojeados más de 6 meses
     */
    public function jugadorInter() {
        $this->db = new mysqli($this->localhost, $this->username, $this->password, $this->dbname);
        $query = "SELECT * FROM Ojea WHERE tiempo_meses > 6";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->html = "<h2>Resultado de la búsqueda</h2>";

        if($result->num_rows > 0) {
            while($row = $result->fetch_array()) {
                //Por cada relacion ojea, coger la info del jugador
                $query2 = "SELECT * FROM Jugador WHERE id = ?";
                $stmt2 = $this->db->prepare($query2);
                $stmt2->bind_param("s", $row['id_jugador']);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                while($row2 = $result2->fetch_array()) {
                    $query3 = "SELECT * FROM Equipo WHERE id = ?";
                    $stmt3 = $this->db->prepare($query3);
                    $stmt3->bind_param("s", $row2['id_equipo']);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();

                    $this->html .= "<p>
                                        <h3>".$row2['nombre']." ".$row2['apellidos']."</h3>
                                        <p><b>Edad:</b> ".$row2['edad']."</p>
                                        <p><b>Internacional:</b> ".($row2['internacional'] ? "Si" : "No")."</p>
                                        <p><b>Posición:</b> ".$row2['posicion']."</p>
                                        <p><b>País:</b> ".$row2['pais']."</p>
                                        <p><b>Goles:</b> ".$row2['goles']."</p>
                                        <p><b>Equipo:</b> ".$result3->fetch_array()['nombre']."</p>
                                    </p>";
                    
                    $stmt3->close();
                }
                $stmt2->close();
            }
        }else{
            $this->html .= "<p>No hay resultados para tu búsqueda</p>";
        }

        $stmt->close();
    }
}

if(isset($_SESSION['db'])){
    //Si no se crea
    //$_SESSION['db'] = new OjeadoresDB();
} else {
    $_SESSION['db'] = new OjeadoresDB();
}

$_SESSION['db']->clearHTML();

if (count($_POST) > 0){
    if(isset($_POST['equipoName'])){
        //quiere buscar por nombre de equipo
        $_SESSION['db']->searchEquipoHTML();
    }
    if(isset($_POST['eqNa'])){
        //ha interactuado con el input de buscar equipo
        $_SESSION['db']->searchEquipo($_POST['nameE']);
    }
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
        //quiere num de jugadores de una liga
        $_SESSION['db']->numLigaHTML();
    }
    if(isset($_POST['nmLg'])){
        //ha interactuado con el input num liga
        $_SESSION['db']->numLiga($_POST['ligas']);
    }
    if(isset($_POST['jugadorNegoOjeadorNombre'])){
        //quiere negociaciones del ojeador
        $_SESSION['db']->negoOjeadorHTML();
    }
    if(isset($_POST['juNeOjNo'])){
        //ha interactuado con el input negociacion
        $_SESSION['db']->negoOjeador($_POST['jugadorNegoOjeadorNombre']);
    }
    if(isset($_POST['golesEquipo'])){
        //quiere goles de un equipo
        $_SESSION['db']->golesEquipoHTML();
    }
    if(isset($_POST['goEq'])){
        //ha interactuado con el input goles de un equipo
        $_SESSION['db']->golesEquipo($_POST['equipos']);
    }
    if(isset($_POST['jugadorInterMedio'])){
        //quiere jugadores internacionales ojeados por más de 6 meses
        $_SESSION['db']->jugadorInter();
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
        <link rel='stylesheet' type='text/css' href='estilo/estiloOjeadoresPhp.css' />
        <link rel='stylesheet' type='text/css' href='estilo/layout.css' />
        <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
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
                        <label for='en'>Buscar por nombre de equipo</label>
                        <input id='en' type='submit' name='equipoName' value='Buscar' />
                    </fieldset>
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
            </section>
            <section>
                <h2>Nuevos ojeadores</h2>
                <p>
                    ¿Quieres trabajar con nosotros? Pulsa <a href='addOjeadores.php'>aquí</a> para obtener más información.
                </p>	
            </section>
        </main>

        <footer>
            <p>Página Real Sporting de Gijón | Web | Copyright @2022 Tania Bajo García</p>
        </footer>
    </body>

    </html>"
?>