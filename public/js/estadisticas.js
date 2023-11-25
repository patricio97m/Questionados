const periodoSelect = document.getElementById("periodo-select");
const rankingList = document.querySelector(".list-group");
const listaUsuariosContainer = document.getElementById("lista-usuarios");
const pdf = document.getElementById("pdf");
const tabla_pdf = document.getElementById("tabla_pdf");
const exportButton = document.getElementById("export-button");
const title = document.getElementById("ranking-title");

exportButton.addEventListener("click", exportarPDF);
periodoSelect.addEventListener("change", function () {
    const selectedPeriodo = periodoSelect.value;

    while (listaUsuariosContainer.firstChild) {
        listaUsuariosContainer.removeChild(listaUsuariosContainer.firstChild);
    }
    while (tabla_pdf.firstChild) {
        tabla_pdf.removeChild(tabla_pdf.firstChild);
    }

    if (selectedPeriodo === "cantidad_jugadores") {
        cantidadJugadores();
        exportButton.hidden = false;
    } else if (selectedPeriodo === "cantidad_partidas"){
        cantidadPartidas();
        exportButton.hidden = false;
    }
    else if (selectedPeriodo === "cantidad_preguntas"){
        cantidadPreguntas();
        exportButton.hidden = false;
    }
    else if (selectedPeriodo === "usuarios_por_sexo") {
        usuariosPorSexo();
        exportButton.hidden = false;
    } else if (selectedPeriodo === "usuarios_por_edad") {
        usuariosPorEdad();
        exportButton.hidden = false;
    }else if (selectedPeriodo === "usuarios_por_pais") {
        usuariosPorPais();
        exportButton.hidden = false;
    }
    else if (selectedPeriodo === "porcentaje_preguntas_usuarios") {
        porcentajePreguntasUsuarios();
        exportButton.hidden = false;
    }
    else if (selectedPeriodo === ""){
        exportButton.hidden = true;
        title.textContent = "SELECCIONE UN GRÁFICO";
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
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <th>
                            ${entry.periodo}
                            </th>
                            <td>
                            ${entry.cantidad_jugadores*1}
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
                });
                //cantidadJugadoresPdf();
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
            }
            var data = google.visualization.arrayToDataTable(datosGrafico);
            var options = {
                title: 'Cantidad Jugadores'
            };
            drawChart(data, options);
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
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <th>
                            ${entry.periodo}
                            </th>
                            <td>
                            ${entry.cantidad_partidas*1}
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
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
            drawChartBar(data, options);
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
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <th>
                            ${entry.periodo}
                            </th>
                            <td>
                            ${entry.cantidad_preguntas*1}
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
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
            drawChartBar(data, options);
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

            if (data.length > 0 && listaUsuariosContainer) {
                const datosGrafico = [['Sexo', 'Cantidad de Usuarios']];

                data.forEach(entry => {
                    datosGrafico.push([entry.sexo, parseInt(entry.cantidad_usuarios)]);
                });
                usuariosPorSexoDetalle()

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Usuarios por Sexo',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'Cantidad de Usuarios', minValue: 0 },
                    vAxis: { title: 'Sexo' },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.BarChart(listaUsuariosContainer);
                chart.draw(chartData, chartOptions);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (listaUsuariosContainer) {
                    listaUsuariosContainer.innerHTML = '';
                    listaUsuariosContainer.appendChild(noDataItem);
                }
            }
        });
}
function usuariosPorSexoDetalle() {
    fetch(`/home/usuariosPorSexoDetalle`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const listItem = document.createElement("tr");
                listItem.innerHTML = `
                            <th>
                            Nombre
                            </th>
                            <th>
                            Apellido
                            </th>
                            <th>
                            Usuario
                            </th>
                            <th>
                            Sexo
                            </th>
                            `;
                tabla_pdf.appendChild(listItem);
                data.forEach(entry => {
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <td>
                            ${entry.nombre}
                            </td>
                            <td>
                            ${entry.apellido}
                            </td>
                            <td>
                            ${entry.usuario}
                            </td>
                            <td>
                            ${entry.sexo}
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
                });
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
            }
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function usuariosPorEdad() {
    fetch(`/home/usuariosPorEdad`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por grupo de edad";

            if (data.length > 0 && listaUsuariosContainer) {
                const datosGrafico = [['Edad', 'Cantidad de Usuarios']];

                data.forEach(entry => {
                    datosGrafico.push(['Menores', parseInt(entry.Menores)]);
                    datosGrafico.push(['Medios', parseInt(entry.Medios)]);
                    datosGrafico.push(['Jubilados', parseInt(entry.Jubilados)]);
                });
                usuariosPorEdadDetalle();

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Usuarios por Edad',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'Cantidad de Usuarios', minValue: 0 },
                    vAxis: { title: 'Edad' },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.BarChart(listaUsuariosContainer);
                chart.draw(chartData, chartOptions);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (listaUsuariosContainer) {
                    listaUsuariosContainer.innerHTML = '';
                    listaUsuariosContainer.appendChild(noDataItem);
                }
            }
        });
}

function usuariosPorEdadDetalle() {
    fetch(`/home/usuariosPorEdadDetalle`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const listItem = document.createElement("tr");
                listItem.innerHTML = `
                            <th>
                            Nombre
                            </th>
                            <th>
                            Apellido
                            </th>
                            <th>
                            Usuario
                            </th>
                            <th>
                            Edad
                            </th>
                            <th>
                            Grupo Edad
                            </th>
                            `;
                tabla_pdf.appendChild(listItem);
                data.forEach(entry => {
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <td>
                            ${entry.nombre}
                            </td>
                            <td>
                            ${entry.apellido}
                            </td>
                            <td>
                            ${entry.usuario}
                            </td>
                            <td>
                            ${entry.edad}
                            </td>
                            <td>
                            ${entry.grupo_edad}
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
                });
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
            }
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function usuariosPorPais(){
    fetch(`/home/usuariosPorPais`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por país";

            if (data.length > 0 && listaUsuariosContainer) {
                const datosGrafico = [['País', 'Cantidad de Usuarios']];

                // Agregar datos al array para el gráfico
                data.forEach(entry => {
                    datosGrafico.push([entry.pais, parseInt(entry.cantidad_usuarios)]);
                });
                usuariosPorPaisDetalle();

                // Crear el gráfico
                const chartData = google.visualization.arrayToDataTable(datosGrafico);
                const chartOptions = {
                    title: 'Usuarios por País',
                    chartArea: { width: '50%' },
                    hAxis: { title: 'País' },
                    vAxis: { title: 'Cantidad de Usuarios', minValue: 0 },
                    bars: 'horizontal',
                };

                const chart = new google.visualization.ColumnChart(listaUsuariosContainer);
                chart.draw(chartData, chartOptions);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (listaUsuariosContainer) {
                    listaUsuariosContainer.innerHTML = '';
                    listaUsuariosContainer.appendChild(noDataItem);
                }
            }
        });
}

function usuariosPorPaisDetalle() {
    fetch(`/home/usuariosPorPaisDetalle`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const listItem = document.createElement("tr");
                listItem.innerHTML = `
                            <th>
                            Nombre
                            </th>
                            <th>
                            Apellido
                            </th>
                            <th>
                            Usuario
                            </th>
                            <th>
                            Pais
                            </th>
                            `;
                tabla_pdf.appendChild(listItem);
                data.forEach(entry => {
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <td>
                            ${entry.nombre}
                            </td>
                            <td>
                            ${entry.apellido}
                            </td>
                            <td>
                            ${entry.usuario}
                            </td>
                            <td>
                            ${entry.pais}
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
                });
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
            }
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function porcentajePreguntasUsuarios() {
    fetch(`/home/usuariosPorPorcentajeDePreguntas`)
        .then(response => response.json())
        .then(data => {
            title.textContent = "Usuarios por % de resp. correctas";

            if (data.length > 0 && listaUsuariosContainer) {
                const datosGrafico = [['Usuario', 'Porcentaje Correctas']];
                const listItem = document.createElement("tr");
                listItem.innerHTML = `
                            <th>
                            Nombre
                            </th>
                            <th>
                            Apellido
                            </th>
                            <th>
                            Usuario
                            </th>
                            <th>
                            Total Respuestas
                            </th>
                            <th>
                            Respuestas Correctas
                            </th>
                            <th>
                            Porcentaje Correctas
                            </th>
                            `;
                tabla_pdf.appendChild(listItem);

                data.forEach(entry => {
                    datosGrafico.push([entry.usuario, parseFloat(entry.porcentaje_correctas)]);
                    const listItem = document.createElement("tr");
                    listItem.innerHTML = `
                            <td>
                            ${entry.nombre}
                            </td>
                            <td>
                            ${entry.apellido}
                            </td>
                            <td>
                            ${entry.usuario}
                            </td>
                            <td>
                            ${entry.total_respuestas}
                            </td>
                            <td>
                            ${entry.respuestas_correctas}
                            </td>
                            <td>
                            ${entry.porcentaje_correctas*1} %
                            </td>
                            `;
                    tabla_pdf.appendChild(listItem);
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

                const chart = new google.visualization.BarChart(listaUsuariosContainer);
                chart.draw(chartData, chartOptions);
            } else {
                const noDataItem = document.createElement("p");
                noDataItem.textContent = "No hay datos por este período de tiempo.";
                if (listaUsuariosContainer) {
                    listaUsuariosContainer.innerHTML = '';
                    listaUsuariosContainer.appendChild(noDataItem);
                }
            }
        })
        .catch(error => {
            console.error("Error al cargar el ranking:", error);
        });
}
function drawChart(inData, inOption) {
    var chart = new google.visualization.PieChart(document.getElementById('lista-usuarios'));
    chart.draw(inData, inOption);
}
function drawChartBar(inData, inOption) {
    var view = new google.visualization.DataView(inData);
    view.setColumns([0, 1,
        { calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation" },
        2]);
    var chart = new google.visualization.ColumnChart(document.getElementById("lista-usuarios"));
    chart.draw(view, inOption);
}
function exportarPDF() {
    var opt = {
        margin:       1,
        filename:     'Estadistica.pdf',
        pagebreak: {mode: ['avoid-all','css','legacy']},
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'legal', orientation: 'landscape' }
    };
    html2pdf().set(opt).from(pdf).save();
}