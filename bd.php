<?php
    class db{
        static private $con;
        static private $error;
        static private $estado;
   
    /**
     * CONECTAR
     * Función estática que realiza la conexión a la base de datos.
     * Si tiene éxito o no se materializa en la variable $estado que retorna el error y número de error en caso de fallar, y 'Conectado correctamente' en caso de tener éxito.
     * Al ser estática se la llama con un bd::conectar(); desde otras páginas php que la tengan integrada con un requiered/included.
     */
    static public function conectar(){
        $host = "localhost";
        $user = "root";
        $pass = "";
        $bbdd = "agenda";
        $con = new mysqli($host, $user, $pass, $bbdd);
        if($con->connect_errno!==0){
            self::$estado .= $con->connect_error;
            self::$error = $con->connect_errno;
        }else{
            self::$estado = "Conectado correctamente";
            $con->set_charset("UTF-8");
            self::$con = $con;
        }
        return self::$estado;
    }

    /**
     *  SELECT
     *  Esta función recupera los registros de la tabla contactos y los devuelve listados en formato tabla HTML.
     */
    static public function muestraContactos(){
        //Abrimos la conexión
        if(!self::$con){
            self::conectar();
        }
        //Preparamos la consulta
        $query = "SELECT * FROM contactos ORDER BY apellido, nombre";
        //Ejecutamos la consulta y guardamos el conjunto de registros que devuelve en $result
        $result = mysqli_query(self::$con, $query);
        $txt = ""; //Generamos la variable con el texto que construirá una tabla html con los registros recuperados
        // $txt .= "<table id='tabla_contactos'>";
        //Recuperamos los resultados uno a uno y los insertamos en mediante html en una tabla que retornaremos
        $txt.="<tr  class='table-info'><th>#Ref.</th><th>Apellido</th><th>Nombre</th><th>Teléfono</th></tr>";
        while($registro = mysqli_fetch_row($result)){
            $txt.="<tr>";
            $txt.="<td>".$registro[0]."</td>";
            $txt.="<td>".$registro[2]."</td>";
            $txt.="<td>".$registro[1]."</td>";
            $txt.="<td>".$registro[3]."</td>";
        }
        // $txt .= "</table>";
        echo $txt;//Devolvemos el listado de registros de la consulta en forma de tabla html
    }
    
    /**
     * INSERT
     * Esta función inserta un nuevo registro en contactos.
     * Extrae la información de los campos del 'formulario' HTML correspondiente al nuevo registro.
     */
    static public function insertarContacto(){
        //Abrimos la conexión
        if(!self::$con){
            self::conectar();
        }
        $nombre = filter_input(INPUT_GET, "nombre")?filter_input(INPUT_GET, "nombre"):"generico";
        $apellido = filter_input(INPUT_GET, "apellido")?filter_input(INPUT_GET, "apellido"):"generico";
        $tlf = filter_input(INPUT_GET, "tlf")?filter_input(INPUT_GET, "tlf"):"123456789";
        //echo("recibido: $nombre - $apellido - $tlf .");
        //Preparamos la consulta
        $query = "INSERT INTO contactos VALUES (null, '$nombre', '$apellido', '$tlf')";
        
        //Dos formas de ejecutar la consulta, comentamos una de ellas:
        $result = mysqli_query(self::$con, $query); //Con mysqli_query pasándole 2 parámetros: conexión y consulta
        //$result = self::$con -> query($query); //Se coge la conexión y se llama al método query pasándole por parámetro la consulta
        
        //No vamos a devolver nada, pero si quisiéramos debuguear podemos habilitar las líneas de abajo para mostrar las filas afectadas
        if($result){
            // echo "Se han INSERTADO $con->affected_rows"."<br/>";
            // echo "El id ha sido: ".$con->insert_id . "<br/>";
        }else{
            // echo "No se pudo";
        }
    }
    /**
     * UPDATE
     * Esta función ejecuta la actualización de un registro de la base de la tabla contactos.
     * La id del registro la extrae el HTML original de un campo oculto en el que se guarda el registro seleccionado mediante un evento de clik de ratón en la tabla de contactos.
     * El resto de datos se optienen de la misma forma, pero se podrían obtener de la propia bbdd a partir de una consulta sobre esa ID.
     * Todos los datos se envían por ajax mediante el método GET.
     */
    static public function editarContacto(){
        //Abrimos la conexión
        if(!self::$con){
            self::conectar();
        }
        $id=filter_input(INPUT_GET, "id");
        $nombre = filter_input(INPUT_GET, "nombre")?filter_input(INPUT_GET, "nombre"):"generico";
        $apellido = filter_input(INPUT_GET, "apellido")?filter_input(INPUT_GET, "apellido"):"generico";
        $tlf = filter_input(INPUT_GET, "tlf")?filter_input(INPUT_GET, "tlf"):"123456789";
        //echo("recibido: $nombre - $apellido - $tlf .");
        //Preparamos la consulta
        $query = "UPDATE contactos SET nombre='$nombre', apellido='$apellido', tlf='$tlf' WHERE id='$id'";
        
        //2 Formas de ejecutar la consulta, comentamos una de ellas:
        //$result = mysqli_query(self::$con, $query); //Con mysqli_query pasándole 2 parámetros: conexión y consulta
        $result = self::$con ->query($query); //Se coge la conexión y se llama al método query pasándole por parámetro la consulta

        //La función no devuelve nada, sólo ejecuta la actualización. Pero si por debugeo quisiéramos mostrar las filas afectadas podemos habilitar las líneas inferiores
        if($result){
            //echo "Se han ACTUALIZADO $con->affected_rows"."<br/>";
            //echo "El id ha sido: ".$con->insert_id . "<br/>";
        }else{
            //echo "No se pudo";
        }
    }

    /**
     * DELETE
     * Esta función elimina un registro de la tabla contactos.
     * Recibe el ID en la url de un ajax mediante método GET.
     * El HTML extrae esa id de un campo oculto en que se guarda el contenido de la primera celda de cada elemento que se clickea, gestionado por un evento click.
     */
    static public function eliminarContacto(){
        //Abrimos la conexión
        if(!self::$con){
            self::conectar();
        }
        $id=filter_input(INPUT_GET, "id");
        //echo "Id recibida: ".$id;
        //Preparamos la consulta
        $query = "DELETE FROM contactos WHERE id='$id'";

        //2 Formas de ejecutar la consulta, comentamos una de ellas:
        //$result = mysqli_query($con, $query); //Con mysqli_query pasándole 2 parámetros: conexión y consulta
        $result = self::$con ->query($query); //Se coge la conexión y se llama al método query pasándole por parámetro la consulta

        //La función no devuelve nada, sólo ejecuta la la eliminación del registro. Pero si por debugeo quisiéramos mostrar las filas afectadas podemos habilitar las líneas inferiores
        if($result){
            //echo "Se han ELIMINADO $con->affected_rows"."<br/>";
            //echo "El id ha sido: ".$con->insert_id . "<br/>";
        }else{
            //echo "No se pudo";
        }
        
    }

    /**
     * CERRAR CONEXIÓN
     * Esta función comprueba si hay una conexión abierta, y si la hay la cierra
     */
    static public function cerrarConexion(){
        if(self::$con){//Si existe una conexión abierta...
            self::$con->close();//Cerrar conexión
        }
    }
 








}
?>