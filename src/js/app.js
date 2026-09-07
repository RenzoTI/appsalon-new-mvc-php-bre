let paso = 1;

// * Si se quiere paginar se crea 2 varias inicial y una final
let pasoInicial =1 ;
let pasoFinal = 3 ;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});


function iniciarApp(){
    // * alert('App Lista');

    mostrarSeccion(); // ? Muestra y ocultas las secciones
    tabs(); // ? Cambia la seccion cuando se presionen los tabs
    botonesPaginador(); // ? Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();
    
    consultarAPI(); // ? Consultar la API en el backend de PHP

    idCliente();
    nombreCliente(); // ? Añade el nombre del cliente al objeto de cita
    selecionarFecha(); // ? Añade de la fecha de la cita en el objeto
    seleccionarHora(); // ? Añade de la hora de la cita en el objeto

    mostrarResumen(); // ? Muestra el resumen de la cita

}

function mostrarSeccion() {
    //console.log('Mostrando seccion..');

    // ? Ocultar la seccion que tenga la clase mostrar

    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    // ? Seleccionar la seccion con el paso....
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

     // ? Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    } 

    // ? Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');
   // console.log(botones); // ? Me arroja un node list que es pareceido a un arreglo

    botones.forEach( boton => {
        boton.addEventListener('click', function(e){ //ahora le pasamos el evento e
            //console.log('Diste Click');
            //console.log(e); // * aca vemos todos los eventos que sucede cuando hacemos un click
            //console.log(e.target.dataset.paso); // * Esto me vota un string (sale de color negro letras) hay que convertirlo a entero
            //console.log(parseInt(e.target.dataset.paso)); // * Van a ser entero (salen de color azul ya es entero)
            paso = parseInt(e.target.dataset.paso); // * Le asignamos a la variable paso que t enemos arriba como global

            mostrarSeccion();
           botonesPaginador();


        });
    });
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {

        if(paso <= pasoInicial) return;
        paso--;
        
        botonesPaginador();
    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {

        if(paso >= pasoFinal) return;
        paso++;
        
        botonesPaginador();
    })
}


async function consultarAPI(){

    try {
        const url = '/api/servicios'; // * Esto lo valida en la url de cita.
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

       
        
    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios)
{   
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;
        //console.log(nombre);

        const nombreServicio = document.createElement('P'); // ? Se rrecomienda ponerlo en mayuscula
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;
        //console.log(nombreServicio);

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;
        //console.log(precioServicio);

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        //servicioDiv.onclick = seleccionarServicio;
        servicioDiv.onclick = function(){ // ? Para pasarle un dato de una funcion a otra cuando lo creamos con scripting
            seleccionarServicio(servicio); // ? lo hacemos por medio de un callback
        }
        //console.log(servicioDiv);

        servicioDiv.appendChild(nombreServicio); // ? Lo agrego a mi DIV creado
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv); // ? Lo agrego a mi vista de view citas index


    });

}

function seleccionarServicio(servicio){
    //console.log('desde selecionarServicio');
    //console.log(servicio);
    const { id } = servicio; // Aca se extrae el id de arreglo servicio

    const { servicios } = cita; //Extraer el arreglo de servicios del objeto de citas

    // Identificar el elemento al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    //Comprobar si uun servicio ya fue agregado
    if( servicios.some( agregado => agregado.id === id) ){// Va iterar y valida si esta agregado o no arroja true o false
        //el some es util para revisar si en un arreglo ya esta  un elemento
        // console.log('Ya esta agregado');
        //Eliminarlo
        cita.servicios = servicios.filter( agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');

    } else {
        // console.log('Articulo NUevo, no estaba agregado');
        //Agregarlo
        cita.servicios = [...servicios, servicio]; // Tomo una copia de lo que hay en el arrreglo de servicios y le
    //agrego un nuevo objeto(servicio) y me crea un solo arreglo con la informacion nueva y reescribe en servicios
        divServicio.classList.add('seleccionado');
    }
 
   // console.log(cita);
    //console.log(servicio);
}

function idCliente(){
    cita.id = document.querySelector('#id').value;
}

function nombreCliente(){
    //console.log(cita);
    const nombre = document.querySelector('#nombre').value;
    cita.nombre = nombre;
    //Tambien se puede hacer en una sola linea cita.nombre = document.querySelector('#nombre').value;
    //console.log(nombre);
}

function selecionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        //console.log('seleccionaste una fecha');
        //console.log(inputFecha.value);
        //console.log(e.target.value);
        //cita.fecha = inputFecha.value;
        const dia =new Date(e.target.value).getUTCDay(); // ? COn la funcion getUTCDay 0= domingo 1= Lunes ...
        //console.log(dia);

        if( [6,0].includes(dia) ) { 
            e.target.value = '';
            //console.log('Sabados y Domingos no abrimos');
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            //console.log('correcto');
            cita.fecha = e.target.value;
        }
    });

}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
       // console.log(e.target.value);

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0]; //Permite separar un string en este caso de la hora seria los :
            //console.log(hora);
        if(hora < 10 || hora > 18){
            //console.log('Horas no validas');
            e.target.value = '';
            mostrarAlerta('Hora No Valida', 'error', '.formulario');
        } else {
            //console.log('Hora Valida');
            cita.hora = e.target.value;
            //console.log(cita);
        }
        
    });
    
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {

    //Previene que se genere mas de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    //Scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

   // console.log(alerta);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
         // Eliminar la alerta
        setTimeout( () => {
        alerta.remove();
    }, 3000);

    }
   
}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el Contenido de Resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);

    }

    console.log( Object.values(cita) ); //Va iterar en todos los objetos y se puede validar cual de ellos esta vacio
   // console.log(cita.servicios.length); // Nos permite verificar si un arreglo esta vacion 
   
    if(Object.values(cita).includes('') || cita.servicios.length === 0 ){
        //console.log('Hace falta datos o Servicios');
        mostrarAlerta('Faltandatos de Servicios, Fecha u Hora', 'error', '.contenido-resumen', false);

        return;

    } //else {console.log('Todo bien');}

    //Formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;

    // Heading para Servicios en Resumen
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    servicios.forEach(servicio => {
        const { id, precio, nombre } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    // Heading para Cita en Resumen
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    // Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date( Date.UTC(year, mes, dia));
    
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    // Boton para Crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
   botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);

   resumen.appendChild(botonReservar);
}

async function reservarCita(){

    const { nombre, fecha, hora, servicios, id } = cita;
    //console.log('Reservando cita');

    const idServicios = servicios.map( servicio => servicio.id );
    // Map busca las coincidencias y lo va colocando en esta variable idServicios
    //  console.log(idServicios);

    
    const datos = new FormData(); // Este FormData va actuar como un submit pero en java script
    
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    //console.log([...datos]);

    try {
         // Peticion hacia la api
    const url = '/api/citas'

    const respuesta = await fetch(url, {
        method: 'POST',
        body: datos
    });
       // console.log(respuesta); // Se valida que la coenxcion fue correcta

       const resultado = await respuesta.json();
      // console.log(resultado.resultado);// Nos vota true o false

       if(resultado.resultado){
        Swal.fire({
            icon: 'success',
            title: 'Cita Creada',
            text: 'Tu cita fue creada correctamente',
            //footer: '<a href="">Why do I have this issue?</a>'
            button: 'OK'
          }).then( () => {
            setTimeout( () => {
                window.location.reload();
            }, 3000);
            
          })
       }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error...',
            text: 'Hubo un error alguardar la cita',
            //footer: '<a href="">Why do I have this issue?</a>'
          })
    }

   // console.log([...datos]); // Nos permite ver los elementos que estan en el FormData

}