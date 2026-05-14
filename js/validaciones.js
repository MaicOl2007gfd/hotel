(function () {
    const form = document.getElementById('registerForm');
    if (!form) {
        return;
    }
    const nombre = document.getElementById('Nombre');
    const email = document.getElementById('Email');
    const contraseña1 = document.getElementById('Contraseña1');
    const contraseña2 = document.getElementById('Contraseña2');
    const boton = document.getElementById('btnEnviar');
    const radios = document.querySelectorAll('select[name="fav_language"]');
    const cedula = document.getElementById('Cedula');
    const emailRegex = /^\S+@\S+\.\S+$/;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.{8,}).*$/;

    function obtenerError(input) {
        return input.parentNode.querySelector('.message-error-js');
    }

    function numeroCedulaValido() {
        const valor = cedula.value.trim();
        const cedulaRegex = /^\d{7,10}$/;
        if (!cedulaRegex.test(valor)) {
            crearError(cedula, 'Ingrese un número de documento válido (7 a 10 dígitos).');
            return false;
        }
        limpiarError(cedula);
        return true;
    }

    function radioSeleccionado() {
        const select = document.querySelector('select[name="fav_language"]');
        return select && select.value !== '';
    }

    function crearError(input, mensaje) {
        limpiarError(input);
        const error = document.createElement('div');
        error.className = 'message-error message-error-js';
        error.textContent = mensaje;
        input.parentNode.appendChild(error);
    }

    function limpiarError(input) {
        const error = obtenerError(input);
        if (error) {
            error.remove();
        }
    }

    function validarNombre() {
        const valor = nombre.value.trim();
        if (valor.length < 3) {
            crearError(nombre, 'El nombre debe tener al menos 3 caracteres.');
            return false;
        }
        limpiarError(nombre);
        return true;
    }

    function validarEmail() {
        const valor = email.value.trim();
        if (!emailRegex.test(valor)) {
            crearError(email, 'Ingrese un correo electrónico válido.');
            return false;
        }
        limpiarError(email);
        return true;
    }

    function validarContraseña1() {
        const valor = contraseña1.value;
        if (!passwordRegex.test(valor)) {
            crearError(contraseña1, 'La contraseña debe tener al menos 8 caracteres, con mayúsculas, minúsculas y números.');
            return false;
        }
        limpiarError(contraseña1);
        return true;
    }

    function validarContraseña2() {
        const valor1 = contraseña1.value;
        const valor2 = contraseña2.value;
        if (valor2 !== valor1 || valor2 === "") {
            crearError(contraseña2, 'Las contraseñas no coinciden.');
            return false;
        }
        limpiarError(contraseña2);
        return true;
    }

    function validarFormulario() {
        return (
            radioSeleccionado() &&
            numeroCedulaValido() &&
            validarNombre() &&
            validarEmail() &&
            validarContraseña1() &&
            validarContraseña2()
        );
    }

    

    //  mantiene las validaciones visuales
    nombre.addEventListener('input', () => {
        validarNombre();
        validarFormulario();
    });

    email.addEventListener('input', () => {
        validarEmail();
        validarFormulario();
    });

    contraseña1.addEventListener('input', () => {
        validarContraseña1();
        if (contraseña2.value.length > 0) {
            validarContraseña2();
        }
        validarFormulario();
    });

    contraseña2.addEventListener('input', () => {
        validarContraseña2();
        validarFormulario();
    });

    form.addEventListener('submit', function (event) {
        if (!validarFormulario()) {
            event.preventDefault();
        }
    });

    //  Estado inicial
    validarFormulario();

})();