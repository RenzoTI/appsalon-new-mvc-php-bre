<?php 

    // ? La forma en la que vamos a iterar sobre ello para que nos muestre correctamente va a ser con dos for
    // ? each que vamos a poner.
    // ? El primer foreach va a iterar sobre el arreglo principal para acceder al key y el segundo y acceder
    // ? a los mensajes.

    foreach($alertas as $key => $mensajes):
        // debuguear($Key); Va imprimir la llave de Error  string(5) "error"
        // debuguear($mensajes); VA imprimir los mensajes
        foreach($mensajes as $mensaje): // Como los mensajes son otro arreglo lo recorremos 
?>      
        
        <div class="alerta <?php echo $key; ?>"> 
           <!--  Creamos una clase alerta y tambien creamos otra clase con el valor de $key -->
                    <?php echo $mensaje; ?>
        </div>

<?php        
        endforeach;     

    endforeach;
?>