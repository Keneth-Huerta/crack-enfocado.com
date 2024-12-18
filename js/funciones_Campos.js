$(document).ready(function () {
    console.log("Script cargado correctamente.");
    $("#formulario").validate({
        rules: {
            nombre: {
                required: true,
                minlength: 4
            },
            apellido: {
                required: true,
                minlength: 4
            },
            boleta: {
                required: true,
                number: true,
                minlength: 8,
                maxlength: 10
            },
            correo: {
                required: true,
                email: true
            },
            contraseña: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            nombre: {
                required: "Por favor ingresa tu nombre.",
                minlength: "4 caracteres min."
            },
            apellido: {
                required: "Por favor ingresa tu apellido.",
                minlength: "4 caracteres min."
            },
            boleta: {
                required: "Por favor ingresa tu número de boleta.",
                number: "La boleta debe contener solo números.",
                minlength: "El número de boleta debe tener al menos 10 caracteres.",
                maxlength: "El número de boleta no debe exceder los 10 caracteres."
            },
            correo: {
                required: "Por favor ingresa tu correo electrónico.",
                email: "Ingresa un correo electrónico válido."
            },
            contraseña: {
                required: "Por favor ingresa una contraseña.",
                minlength: "La contraseña debe tener al menos 6 caracteres."
            }
         
        }
    });
});


