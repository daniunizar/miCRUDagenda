<?php
require_once 'bd.php';
    $funcionSolicitada = filter_input(INPUT_GET, "funcion_solicitada");
    switch ($funcionSolicitada) {
        case 0: //insertar
            db::insertarContacto();
            db::muestraContactos();
            break;
        case 1: //editar
            db::editarContacto();
            db::muestraContactos();
            break;
        case 2: //eliminar
            db::eliminarContacto();
            db::muestraContactos();
            break;
        case 3: //mostrar
            db::muestraContactos();
            break;
    }
?>