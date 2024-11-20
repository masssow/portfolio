require('./bootstrap-datepicker/bootstrap-select.js');
require('./flipclock/timer.js');
require('./isotope/isotope-min.js');
require('./jquery-ui/jquery-ui.js');
require('./nice-select/js/jquery.nice-select.js');


// effet mecanografia

document.addEventListener("DOMContentLoaded", function() {
    const textElement = document.getElementById("typing-effect");
    const text = textElement.textContent;
    // textElement.textContent = ""; // Limpia el contenido inicial

    let index = 0;

    function type() {
        if (index < text.length) {
            textElement.textContent += text.charAt(index);
            index++;
            setTimeout(type, 300); // Ajusta la velocidad del efecto
        }
    }

    type(); // Inicia el efecto de mecanografía
});