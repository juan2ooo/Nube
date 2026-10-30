$(document).ready(function () {
  $("#savePropiedad").click(function () {
    // Obtener valores de los campos
    console.log("empieza")
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

    // Crear objeto FormData (permite enviar texto + archivos)
    var formData = new FormData();
    formData.append("accion", "savePropieda");
    formData.append("nombre", nombre);
    formData.append("telefono", telefono);
    formData.append("horario", $("#horarioCliente").val());
    formData.append("observacion", $("#obsCliente").val());

    // 👉 aquí agregamos el archivo de video
    var videoFile = $("#videoCliente")[0].files[0]; // input type="file" con id="videoCliente"
    if (videoFile) {
      formData.append("video", videoFile);
    }

    console.log("ANTES DE AJAX")

    $.ajax({
      url: "../controlador/ControllerAdmin.php",
      type: "POST",
      data: formData,
      processData: false, // necesario para FormData
      contentType: false, // necesario para FormData
success: function (response) {
  console.log("Respuesta del servidor:", response);

  if (response.success === true) {
    //todoOKSweet("Cliente creado");
    console.log("Cliente creado");
    //vaciarClienteModal();
    console.log("Video URL:", response.url);
  } else {
    if (response.message && response.message.toLowerCase().startsWith("duplicate entry")) {
      //abrirErrorSweet("Número ya existe, ingrese otro");
      console.log("Número ya existe, ingrese otro");
    } else {
      //abrirErrorSweet("Error: " + response.message);
    }
  }
}
,
      error: function (xhr, status, error) {
        alert(error);
      },
    });
  });
});



function abrirModal() {
  $(myModal).css("display", "block"); // Esto está mal, 'auto' no es valor válido para display
}
