const periodoSelect = document.getElementById("periodo-select");
const rankingList = document.querySelector(".list-group");

periodoSelect.addEventListener("change", function () {
    const selectedPeriodo = periodoSelect.value;

    if (selectedPeriodo !== "") {
        fetch(`/home/rankingAjax?periodo=${selectedPeriodo}`)
            .then(response => response.json())
            .then(data => {
                const title = document.getElementById("ranking-title");
                title.textContent = `Mejores jugadores por ${selectedPeriodo}`;

                while (rankingList.firstChild) {
                    rankingList.removeChild(rankingList.firstChild);
                }

                if (data.length > 0) {
                    data.forEach(entry => {
                        const listItem = document.createElement("li");
                        listItem.className = "list-group-item d-flex justify-content-between align-items-start";
                        listItem.innerHTML = `
                            <div class="ms-2 me-auto">
                                <a href="/usuario/datosUsuario?nombre=${entry.usuario}" class="link-dark link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover">${entry.usuario}</a>
                            </div>
                            <span class="badge bg-success text-light rounded-pill">${entry.puntaje_total}</span>
                        `;
                        rankingList.appendChild(listItem);
                    });
                } else {
                    const noDataItem = document.createElement("p");
                    noDataItem.textContent = "No hay datos por este período de tiempo.";
                    rankingList.appendChild(noDataItem);
                }
            })
            .catch(error => {
                console.error("Error al cargar el ranking:", error);
            });
    }
});