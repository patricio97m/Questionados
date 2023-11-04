document.addEventListener('DOMContentLoaded', function() {
    var tiempoRestanteElement = document.getElementById('tiempo-restante');

    var interval = setInterval(function() {
        if (!modalActivo()) {
            tiempoRestante--;
            tiempoRestanteElement.textContent = tiempoRestante;

            if (tiempoRestante <= 0) {
                clearInterval(interval);
                simularSubmit();
            }
        }
    }, 1000);

    function modalActivo() {
        var miModal = document.getElementById('miModal');
        var modalReporte = document.getElementById('modalReporte');

        return (
            (miModal && miModal.classList.contains('show')) ||
            (modalReporte && modalReporte.classList.contains('show'))
        );
    }

    function simularSubmit() {
        var form = document.createElement('form');
        form.method = 'post';
        form.action = '/juego/verificarRespuesta';

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'esCorrecta';
        input.value = '0';

        form.appendChild(input);
        document.body.appendChild(form);

        form.submit();
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const animateElements = document.querySelectorAll('.animate');
    animateElements.forEach(element => {
        element.classList.add('active');
    });
});