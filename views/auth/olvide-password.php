<h1 class="nombre-pagina">Olvide Password</h1>
<p class="descripcion-pagina">Restablece tu password escribiendo tu email a continuacion</p>

<?php 
include_once __DIR__ . "/../templates/alertas.php";

?> 

<form action="/olvide" method="POST" class="formulario">
    <div class="campo">
        <label for="email"></label>
        <input 
            type="email"
            id="email"
            name="email"
            placeholder="Tu E-mail"
        >
    </div>

    <input type="submit" class="boton" value="Enviar Instruciones">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar sesion</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>