const periodoSelect = document.getElementById("periodo-select");
const rankingList = document.querySelector(".list-group");
const graficosContainer = document.getElementById("graficos-container");
const exportButton = document.getElementById("export-button");
const title = document.getElementById("ranking-title");
const cantJugadoresDiv = document.getElementById("cant-jugadores-div");
const cantPartidasDiv = document.getElementById("cant-partidas-div");
const cantPreguntasDiv = document.getElementById("cant-preguntas-div");
const usuariosPorSexoDiv = document.getElementById("usuarios-por-sexo-div");
const usuariosPorEdadDiv = document.getElementById("usuarios-por-edad-div");
const usuariosPorPaisDiv = document.getElementById("usuarios-por-pais-div");
const porcentajePregUsuariosDiv = document.getElementById("porcentaje-preg-usuarios-div");
const graficosDivs = ["cant-jugadores-div", "cant-partidas-div", "cant-preguntas-div", "usuarios-por-sexo-div", "usuarios-por-edad-div", "usuarios-por-pais-div", "porcentaje-preg-usuarios-div"];

exportButton.addEventListener("click", exportarPDF);
periodoSelect.addEventListener("change", function () {
    const selectedPeriodo = periodoSelect.value;

    // Oculta todos los divs de gráficos
    graficosDivs.forEach(divId => {
        const div = document.getElementById(divId);
        div.style.display = "none";
    });

    if (selectedPeriodo === "cantidad_jugadores") {
        cantidadJugadores();
        exportButton.hidden = false;
        cantJugadoresDiv.style.display = "block";
    } else if (selectedPeriodo === "cantidad_partidas"){
        cantidadPartidas();
        exportButton.hidden = false;
        cantPartidasDiv.style.display = "block";
    }
    else if (selectedPeriodo === "cantidad_preguntas"){
        cantidadPreguntas();
        exportButton.hidden = false;
        cantPreguntasDiv.style.display = "block";
    }
    else if (selectedPeriodo === "usuarios_por_sexo") {
        usuariosPorSexo();
        exportButton.hidden = false;
        usuariosPorSexoDiv.style.display = "block";
    } else if (selectedPeriodo === "usuarios_por_edad") {
        usuariosPorEdad();
        exportButton.hidden = false;
        usuariosPorEdadDiv.style.display = "block";
    }else if (selectedPeriodo === "usuarios_por_pais") {
        usuariosPorPais();
        exportButton.hidden = false;
        usuariosPorPaisDiv.style.display = "block";
    }
    else if (selectedPeriodo === "porcentaje_preguntas_usuarios") {
        porcentajePreguntasUsuarios();
        exportButton.hidden = false;
        porcentajePregUsuariosDiv.style.display = "block";
    }
    else if (selectedPeriodo === ""){
        exportButton.hidden = true;
        title.textContent = "SELECCIONE UN GRÁFICO";
    }
    else if (selectedPeriodo === "ver_graficos") {
        cantidadJugadores();
        cantidadPartidas();
        cantidadPreguntas();
        usuariosPorSexo();
        usuariosPorEdad();
        usuariosPorPais();
        porcentajePreguntasUsuarios();
        exportButton.hidden = false;
        graficosDivs.forEach(divId => {
            const div = document.getElementById(divId);
            div.style.display = "block";
        });
        title.textContent = "Ver todos los gráficos";
    }
});


function cantidadJugadores() {
    fetch(`/home/cantidadJugadoresAjax`)
        .then(response => response.json())
        .then(data => {
            title.textContent = `Grafico cantidad jugadores`;
            var datosGrafico = [['Task', 'Hours per Day' ]]

            if (data.length > 0) {
                data.forEach(entry => {
                    datosGrafico.push([entry.periodo.toUpperCase(),entry.cantidad_jugadores*1])
                });
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
            }
            var data = google.visualization.arrayToDataTable(datosGrafico);
            var options = {
                title: 'Cantidad Jugadores'
            };
            drawChart(data, options, cantJugadoresDiv);
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function cantidadPartidas(){
    fetch(`/home/cantidadPartidasAjax`)
        .then(response => response.json())
        .then(data => {
            title.textContent = `Grafico cantidad Partidas`;
            var datosGrafico = [["Element", "Partidas", { role: "style" } ]];

            if (data.length > 0) {
                data.forEach(entry => {
                    datosGrafico.push([entry.periodo.toUpperCase(),entry.cantidad_partidas*1, "#"+Math.floor(Math.random()*16777215).toString(16)])
                });
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                rankingList.appendChild(noDataItem);
            }
            var data = google.visualization.arrayToDataTable(datosGrafico);
            var options = {
                title: 'Cantidad Partidas',
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: { position: "none" },
            };
            drawChartBar(data, options, cantPartidasDiv);
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function cantidadPreguntas() {
    fetch(`/home/cantidadPreguntasAjax`)
        .then(response => response.json())
        .then(data => {
            title.textContent = `Grafico cantidad Preguntas`;
            var datosGrafico = [["Element", "Preguntas", { role: "style" } ]];

            if (data.length > 0) {
                data.forEach(entry => {
                    datosGrafico.push([entry.periodo.toUpperCase(),entry.cantidad_preguntas*1, "#"+Math.floor(Math.random()*16777215).toString(16)])
                });
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                rankingList.appendChild(noDataItem);
            }
            var data = google.visualization.arrayToDataTable(datosGrafico);
            var options = {
                title: 'Cantidad Preguntas',
                width: 600,
                height: 400,
                bar: {groupWidth: "95%"},
                legend: { position: "none" },
            };
            drawChartBar(data, options, cantPreguntasDiv);
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function usuariosPorSexo() {
    fetch(`/home/usuariosPorSexo`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por sexo";

            if (data.length > 0 && graficosContainer) {
                const datosGrafico = [['Sexo', 'Cantidad de Usuarios']];

                data.forEach(entry => {
                    datosGrafico.push([entry.sexo, parseInt(entry.cantidad_usuarios)]);
                });

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Usuarios por Sexo',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'Cantidad de Usuarios', minValue: 0 },
                    vAxis: { title: 'Sexo' },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.BarChart(usuariosPorSexoDiv);
                chart.draw(chartData, chartOptions, usuariosPorSexoDiv);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (usuariosPorSexoDiv) {
                    usuariosPorSexoDiv.innerHTML = '';
                    usuariosPorSexoDiv.appendChild(noDataItem);
                }
            }
        });
}
function usuariosPorEdad() {
    fetch(`/home/usuariosPorEdad`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por grupo de edad";

            if (data.length > 0 && graficosContainer) {
                const datosGrafico = [['Edad', 'Cantidad de Usuarios']];

                data.forEach(entry => {
                    datosGrafico.push(['Menores', parseInt(entry.Menores)]);
                    datosGrafico.push(['Medios', parseInt(entry.Medios)]);
                    datosGrafico.push(['Jubilados', parseInt(entry.Jubilados)]);
                });

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Usuarios por Edad',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'Cantidad de Usuarios', minValue: 0 },
                    vAxis: { title: 'Edad' },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.BarChart(usuariosPorEdadDiv);
                chart.draw(chartData, chartOptions, usuariosPorEdadDiv);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (usuariosPorEdadDiv) {
                    usuariosPorEdadDiv.innerHTML = '';
                    usuariosPorEdadDiv.appendChild(noDataItem);
                }
            }
        });
}
function usuariosPorPais(){
    fetch(`/home/usuariosPorPais`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por país";

            if (data.length > 0 && graficosContainer) {
                const datosGrafico = [['País', 'Cantidad de Usuarios']];

                // Agregar datos al array para el gráfico
                data.forEach(entry => {
                    datosGrafico.push([entry.pais, parseInt(entry.cantidad_usuarios)]);
                });

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Usuarios por País',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'País' },
                    vAxis: { title: 'Cantidad de Usuarios', minValue: 0 },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.ColumnChart(usuariosPorPaisDiv);
                chart.draw(chartData, chartOptions, usuariosPorPaisDiv);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (usuariosPorPaisDiv) {
                    usuariosPorPaisDiv.innerHTML = '';
                    usuariosPorPaisDiv.appendChild(noDataItem);
                }
            }
        });
}
function porcentajePreguntasUsuarios() {
    fetch(`/home/usuariosPorPorcentajeDePreguntas`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por % de resp. correctas";

            if (data.length > 0 && graficosContainer) {
                const datosGrafico = [['Usuario', 'Porcentaje Correctas']];

                data.forEach(entry => {
                    datosGrafico.push([entry.usuario, parseFloat(entry.porcentaje_correctas)]);
                });

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Porcentaje de Respuestas Correctas por Usuario',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'Porcentaje Correctas', minValue: 0, maxValue: 100 },
                    vAxis: { title: 'Usuario' },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.BarChart(porcentajePregUsuariosDiv);
                chart.draw(chartData, chartOptions, porcentajePregUsuariosDiv);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (porcentajePregUsuariosDiv) {
                    porcentajePregUsuariosDiv.innerHTML = '';
                    porcentajePregUsuariosDiv.appendChild(noDataItem);
                }
            }
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function drawChart(inData, inOption, contenedor) {
    var chart = new google.visualization.PieChart(contenedor);
    chart.draw(inData, inOption);
}
function drawChartBar(inData, inOption, contenedor) {
    var view = new google.visualization.DataView(inData);
    view.setColumns([0, 1,
        { calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation" },
        2]);
    var chart = new google.visualization.ColumnChart(contenedor);
    chart.draw(view, inOption);
}
function exportarPDF() {
    var opt = {
        margin:       0,
        filename:     'Estadistica.pdf',
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
        image:        { type: 'jpeg', quality: 0.98},
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'legal', orientation: 'landscape' }
    };

    html2pdf().set(opt).from(graficosContainer).save();
}