const highlightClr = "#cacaca";

function isHighlighted(cell) {
    return cell.getAttribute("style").includes(highlightClr);
}

function toggleBackground(cell) {
    cell.setAttribute("style", "background-color: ".concat(isHighlighted(cell) ? "inherit" : highlightClr));
}

function handleBigTableCellClick(bigTableCells, clickedCell) {
    let previousSelectedCell = [ ...bigTableCells ].find(cell => isHighlighted(cell));
    if (previousSelectedCell && previousSelectedCell !== clickedCell) {
        previousSelectedCell.setAttribute("style", "background-color: inherit;");
    }
    toggleBackground(clickedCell);
}

function log(message) {
    document.querySelector(".log").innerText = message;
}

function handleNumbersTableCellClick(clickedCell) {
    const bigTableCells = document.querySelectorAll("table.tabellone tr td");
    const selectedCell = [ ...bigTableCells ].find(cell => isHighlighted(cell));
    if (!selectedCell) {
        log("Cella non selezionata");
        return;
    }
    selectedCell.innerText = clickedCell.innerText;
    selectedCell.setAttribute("style", "background-color: inherit");
    log("Numero inserito correttamente");
}

document.addEventListener('DOMContentLoaded', () => {
    const main = document.querySelector("main");
    const numbersTable = document.createElement("table");
    numbersTable.setAttribute("id", "numeri");
    const row = document.createElement("tr");
    for (let number = 1; number <= 9; number++) {
        const cell = document.createElement("td");
        cell.innerText = number;
        cell.addEventListener("click", () => handleNumbersTableCellClick(cell));
        row.appendChild(cell);
    } 
    numbersTable.appendChild(row);
    main.appendChild(numbersTable);

    const bigTableCells = document.querySelectorAll("table.tabellone tr td");
    [ ...bigTableCells ].forEach(cell => {
        cell.setAttribute("style", "");
        cell.addEventListener('click', () => handleBigTableCellClick(bigTableCells, cell));
    })
})