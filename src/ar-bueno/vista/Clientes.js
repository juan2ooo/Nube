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
      { data: "horario", title: "Teléfono" },
      { data: "observacion", title: "Observación" },

      {
        data: null,
        title: "Acciones",
        render: function (data, type, row) {
          return (
            '<button class="btn-editar" data-id="' +
            row.id +
            '">Editar</button>' +
            '<input type="checkbox" id="subscribeNews" name="subscribe" value="newsletter" />' 
          );
        },
      },
    ],
    language: {
      url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json", // Español
    },
  });

  // Ejemplo de eventos para los botones (puedes personalizar)
  $("#resultsTable").on("click", ".btn-editar", function () {
    var id = $(this).data("id");
    alert("Editar ID: " + id);
    // Aquí puedes agregar tu lógica para editar
  });

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

$(document).on("click", "#searchButton", function () {
  const datosEnviar = { accion: "buscarClientes" };
  datosEnviar["telefono"] = $("#searchTerm1").val();
  datosEnviar["nombre"] = $("#searchTerm").val();

  if (datosEnviar["nombre"] === "" && datosEnviar["telefono"] === "") return;

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

      response["data"].forEach((element,i) => {
        const row = table.row.add(element).draw().node();
        $(row).attr("id", "row-" + i);
      });
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
