var table;
$(document).ready(function () {
  // Array de datos de ejemplo (puedes modificarlo según tus necesidades)
  /*     var datos = [
        {

            nombre: "Juan Pérez",
            telefono: "555-1234",
            horario: "juan@example.com",
            observacion: "juan@example.com"
        },
        {

            nombre: "Juan Pérez",
            telefono: "555-1234",
            horario: "juan@example.com",
            observacion: "juan@example.com"
        },
        {

            nombre: "Juan Pérez",
            telefono: "555-1234",
            horario: "juan@example.com",
            observacion: "juan@example.com"

        }
    ]; */

  const datos = [];

  // Inicializar DataTable
  table = $("#resultsTable").DataTable({
    data: datos,
    columns: [
      { data: "nombre", title: "Nombre" },
      { data: "telefono", title: "Teléfono" },
      { data: "horario", title: "Horario" },
      { data: "observacion", title: "Observación" },

      {
        data: null,
        title: "Acciones",

        render: function (data, type, row) {
          return (
            '<div class="acciones-container">' +
            '<button class="btn-editar">Editar</button>' +
            //'<button class="btn-editar" id="' + row.id + '">Editar</button>' +
            '<input type="checkbox" id="subscribeNews" name="subscribe" value="newsletter" />' +
            "</div>"
          );
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json", // Español
    },
  });

  // Ejemplo de eventos para los botones (puedes personalizar)
  /*   $("#resultsTable").on("click", ".btn-editar", function () {
    var id = $(this).data("id");
    alert("Editar ID: " + id);
    // Aquí puedes agregar tu lógica para editar
  }); */

  $("#resultsTable").on("click", ".btn-eliminar", function () {
    var id = $(this).data("id");
    if (confirm("¿Estás seguro de eliminar el registro con ID: " + id + "?")) {
      // Aquí puedes agregar tu lógica para eliminar
      table.row($(this).parents("tr")).remove().draw();
      alert("Registro eliminado");
    }
  });
});

/* // Cerrar modal al hacer clic fuera del contenido
window.addEventListener("click", function (event) {
  const modal = document.getElementById("myModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
}); */

let mouseDownInsideModal = false;

// Detecta si el clic INICIÓ dentro del modal-content
$(document).on("mousedown", "#myModal .modal-content", function () {
  mouseDownInsideModal = true;
});

// Cierra solo si:
// 1. El clic NO comenzó dentro del modal Y
// 2. Se hace clic en el fondo del modal (#myModal)
$(document).on("mouseup", "#myModal", function (e) {
  if (!mouseDownInsideModal && e.target === this) {
    $(this).hide();
  }
  mouseDownInsideModal = false; // Resetear
});

$(document).on("click", "#cerrar", function () {
  $("#myModal").hide();
});

// Cierra solo si:
// 1. El clic NO comenzó dentro del modal Y
// 2. Se hace clic en el fondo del modal (#myModal)
$(document).on("mouseup", "#miModal", function (e) {
  if (!mouseDownInsideModal && e.target === this) {
    $(this).hide();
  }
  mouseDownInsideModal = false; // Resetear
});

$(document).on("click", "#cerrar", function () {
  $("#miModal").hide();
});

$(document).on("click", "#searchButton", function () {
  const datosEnviar = { accion: "buscarClientes" };
  datosEnviar["telefono"] = $("#searchTerm1").val();
  datosEnviar["nombre"] = $("#searchTerm").val();
  datosEnviar["all"] = false;

  if (datosEnviar["nombre"] === "" && datosEnviar["telefono"] === "")
    datosEnviar["all"] = true;

  $.ajax({
    url: "../controlador/ControllerAdmin.php",
    type: "POST",
    dataType: "json", // <-- muy importante
    data: datosEnviar,
    success: function (response) {
      if (response["message"] === "N") {
        abrirErrorSweet("No se encontro resultado");
      }

      console.log("respuesta desde el server:", response["data"]);

      table.clear().draw();

      //const fila = {};
      response["data"].forEach((element, i) => {
        if(element["horario"]===1){
          element["horario"] = "Mañana"
        }else if(element["telefono"]===2){
          element["horario"] = "Tarde"
        }else{
          element["horario"] = "Noche"
        }
        
        table.row.add(element); // agrego fila y obtengo el <tr>
        //$(rowNode).attr("id", "row-" + i);             // le pongo un id único
      });

      // dibujar solo una vez al final
      table.draw();
    },
    error: function (xhr, status, error) {
      alert(error);
    },
  });
});

function abrirModal() {
  $(myModal).css("display", "block"); // Esto está mal, 'auto' no es valor válido para display
}

$(document).ready(function () {
  $("#saveClient").click(function () {
    // Obtener valores de los campos
    var nombre = $("#nombreCliente").val();
    var telefono = $("#telCliente").val();

    // Validar campos obligatorios
    if (!nombre || nombre.trim() === "") {
      alert("El campo Nombre es obligatorio");
      $("#nombreCliente").focus();
      return false;
    }

    if (!telefono || telefono.trim() === "") {
      alert("El campo Teléfono es obligatorio");
      $("#telCliente").focus();
      return false;
    }

    // Crear objeto con los datos si la validación pasa
    var datosEnviar = {
      accion: "saveClient",
      nombre: nombre,
      telefono: telefono,
      horario: $("#horarioCliente").val(),
      observacion: $("#obsCliente").val(),
    };

    $.ajax({
      url: "../controlador/ControllerAdmin.php",
      type: "POST",
      data: datosEnviar,
      success: function (response) {
        if (response["success"] == true) {
          todoOKSweet("Cliente creado");
          vaciarClienteModal();
        } else {
          console.log("??");
          if (response["message"].toLowerCase().startsWith("duplicate entry")) {
            abrirErrorSweet("Numero ya existe, ingrese otro");
          } else
            abrirErrorSweet("Error desconocido, contacte al administrador");
        }
      },
      error: function (xhr, status, error) {
        alert(error);
      },
    });
  });
});

function abrirErrorSweet(mensaje) {
  Swal.fire({
    icon: "error",
    title: mensaje,
  });
}

function todoOKSweet(mensaje) {
  Swal.fire({
    title: mensaje,
    icon: "success",
    draggable: true,
  });
}

function vaciarClienteModal() {
  $("#nombreCliente").val("");
  $("#telCliente").val("");
  $("#obsCliente").val("");
}

function eliminarElemento() {
  const telefono = [];

  $("#resultsTable tr").each(function () {
    const ultimoElemento = $(this)
      .find("th,td")
      .last()
      .find("input[type='checkbox']");

    if (ultimoElemento.prop("checked")) {
      console.log("El checkbox está marcado - acción de eliminar aquí");

      // Obtener el texto del segundo elemento de la fila
      telefono.push($(this).find("th,td").eq(1).text().trim());

      // Eliminar la fila visualmente
      $(this).remove();
    }
  });

  console.log("Teléfonos a enviar:", telefono);

  // === AJAX request ===
  $.ajax({
    url: "../controlador/ControllerAdmin.php", // aquí pones la ruta de tu archivo PHP
    type: "POST",
    data: {
      accion: "desabilitarCliente", // para que en PHP sepas qué función ejecutar
      telefonos: telefono, // array de teléfonos
    },
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      alert("Clientes deshabilitados correctamente");
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
    },
  });
}

$("#resultsTable tbody").on("click", ".btn-editar", function () {
  // Obtener los datos de la fila en la que se hizo clic
  let datosFila = table.row($(this).closest("tr")).data();
  console.log($(this))

  localStorage.setItem('fila', table.row($(this).closest("tr")).index());

  openModal(datosFila);
});

function openModal(datos) {
  console.log(datos)
  document.getElementById("miModal").style.display = "flex";
  $("#nombre").val(datos["nombre"]);

  localStorage.setItem('telefono', datos["telefono"]);
  $("#telefono").val(datos["telefono"]);
  console.log(datos["horario"])
  $("#horario").val(datos["horario"]);
  $("#observacion").val(datos["observacion"]);
}

function cerrarModal() {
  document.getElementById("miModal").style.display = "none";
  $("#nombre").val("");
  $("#telefono").val("");
  $("#horario").val("");
  $("#observacion").val("");
}

function editar() {
  // Crear un diccionario con pares clave-valor
  const datosEditar = {
    nombre: $("#nombre").val(),
    telefono: $("#telefono").val(),
    horario: $("#horario").val(),
    observacion: $("#observacion").val()
  };


  // === AJAX request ===
  $.ajax({
    url: "../controlador/ControllerAdmin.php",
    type: "POST",
    data: {
      accion: "editar",
      datos: datosEditar,
      clave: "telefono",
      valor: localStorage.getItem('telefono')
    },
    //console.log(data)
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      //console.log(data);
      alert("Clientes editados correctamente");
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
    },
  });




    const filaIndex = localStorage.getItem('fila');

    table.row(filaIndex).data(datosEditar).draw(false);

    // Limpiar localStorage si quieres
    localStorage.removeItem('fila');

}



/* function eliminarElemento() {
  $("#resultsTable tr").each(function() {
    const ultimoElemento = $(this).find("th, td").last().find("input[type='checkbox']");
    
    if (ultimoElemento.length > 0) {
      console.log("Checkbox encontrado. ¿Está marcado?", ultimoElemento.prop('checked'));
      console.log("ljkn"); // Este mensaje se mantiene como en tu versión original
      
      if (ultimoElemento.prop('checked')) {
        console.log("El checkbox está marcado - acción de eliminar aquí");
        // Tu lógica para eliminar el elemento cuando está marcado
      }
    }
  });
} */
