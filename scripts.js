document.addEventListener("DOMContentLoaded", function() {
    const togglePasswordButton = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    togglePasswordButton.addEventListener("mousedown", function() {
        passwordInput.type = "text";
    });

    togglePasswordButton.addEventListener("mouseup", function() {
        passwordInput.type = "password";
    });

    togglePasswordButton.addEventListener("mouseout", function() {
        passwordInput.type = "password";
    });
});

const fechaNacimientoInput = document.getElementById('fecha-nacimiento');
    const fechaError = document.getElementById('fecha-error');

    fechaNacimientoInput.addEventListener('change', function() {
        const fecha = new Date(this.value);
        const year = fecha.getFullYear();
        if (year < 1950 || year > 2006) {
            fechaError.style.display = 'block';
            this.setCustomValidity('La fecha de nacimiento debe estar entre 1950 y 2006.');
        } else {
            fechaError.style.display = 'none';
            this.setCustomValidity('');
        }
    });


document.addEventListener("DOMContentLoaded", function() {
            // Obtener la lista de elementos del menú
            var links = document.querySelectorAll("nav ul li a");

            // Iterar sobre los enlaces y agregar el evento click
            links.forEach(function(link) {
                link.addEventListener("click", function() {
                    // Eliminar la clase activa de todos los enlaces
                    links.forEach(function(link) {
                        link.classList.remove("active");
                    });

                    // Agregar la clase activa al enlace clicado
                    this.classList.add("active");
                });
            });

            // Inicializar el carrusel de imágenes
            showSlides(slideIndex);
        });

        // Variables del carrusel
        let slideIndex = 0;

        // Función para mostrar la diapositiva actual
        function showSlides(n) {
            let slides = document.getElementsByClassName("carousel-item");
            if (n >= slides.length) { slideIndex = 0; }
            if (n < 0) { slideIndex = slides.length - 1; }
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex].style.display = "block";
        }

        // Función para cambiar de diapositiva
        function plusSlides(n) {
            slideIndex += n;
            showSlides(slideIndex);
        }
