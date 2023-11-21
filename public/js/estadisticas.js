const periodoSelect = document.getElementById("periodo-select");
const rankingList = document.querySelector(".list-group");

periodoSelect.addEventListener("change", function () {
    const selectedPeriodo = periodoSelect.value;

    if (selectedPeriodo === "cantidad_jugadores") {
        fetch(`/home/cantidadJugadoresAjax`)
            .then(response => response.json())
            .then(data => {
                const title = document.getElementById("ranking-title");
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
                drawChart(data, options);
            })
            .catch(error => {
                console.error("Error al cargar el ranking:", error);
            });
    } else if (selectedPeriodo === "cantidad_partidas"){
        fetch(`/home/cantidadPartidasAjax`)
            .then(response => response.json())
            .then(data => {
                const title = document.getElementById("ranking-title");
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
                drawChartBar(data, options);
                console.log(datosGrafico);
            })
            .catch(error => {
                console.error("Error al cargar el ranking:", error);
            });
    }
    else if (selectedPeriodo === "cantidad_preguntas"){
        fetch(`/home/cantidadPreguntasAjax`)
            .then(response => response.json())
            .then(data => {
                const title = document.getElementById("ranking-title");
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
                drawChartBar(data, options);
                console.log(datosGrafico);
            })
            .catch(error => {
                console.error("Error al cargar el ranking:", error);
            });
    }
});
function drawChart(inData, inOption) {

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));

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
    var chart = new google.visualization.ColumnChart(document.getElementById("piechart"));
    chart.draw(view, inOption);
}
