$(document).ready(function(){
    
    $(".contenedor-formularios").find("input, textarea").on("keyup blur focus", function (e) {

        var $this = $(this),
          label = $this.prev("label");

        if (e.type === "keyup") {
            if ($this.val() === "") {
                label.removeClass("active highlight");
            } else {
                label.addClass("active highlight");
            }
        } else if (e.type === "blur") {
            if($this.val() === "") {
                label.removeClass("active highlight"); 
                } else {
                label.removeClass("highlight");   
                }   
        } else if (e.type === "focus") {
            if($this.val() === "") {
                label.removeClass("highlight"); 
            } 
            else if($this.val() !== "") {
                label.addClass("highlight");
            }
        }

    });

    $(".tab a").on("click", function (e) {

        e.preventDefault();

        $(this).parent().addClass("active");
        $(this).parent().siblings().removeClass("active");

        target = $(this).attr("href");

        $(".contenido-tab > div").not(target).hide();

        $(target).fadeIn(600);

    });
    
});









$(document).ready(function() {
    $('#registro').click(function() {
        // Crear objeto con los datos
        var datosEnviar = {
            accion: 'registrarAdmin',
            nombre: $('#nombreAdminRegistro').val(),
            usr: $('#usrRegistroAdmin').val(),
            cont: $('#contRegistro').val()
        };
        

        
        $.ajax({
            url: '../auth/ControllerAuth.php',
            type: 'POST',
            data: datosEnviar,
            success: function(response) {
                alert("Todo Ok");
            },
            error: function(xhr, status, error) {
                alert("ERROR!!");
            }
        });
    });
});











$(document).ready(function() {
    $('#inicioSesion').click(function() {
        // Crear objeto con los datos
        var datosEnviar = {
            accion: 'login',
            usr: $('#usrLogin').val(),
            cont: $('#passwordLogin').val()
        };

        console.log(datosEnviar)
        

        
        $.ajax({
            url: '../auth/ControllerAuth.php',
            type: 'POST',
            data: datosEnviar,
            success: function(response) {
                console.log('Respuesta del servidor:', response);

            //window.location.href = "PanelPrincipal.html";
            if(response == "Validado")
            window.location.replace("PanelPrincipal.php");

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});
