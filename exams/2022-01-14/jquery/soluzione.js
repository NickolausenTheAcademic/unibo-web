const ROWS = 6;
const COLS = 7;

const CELLS_BG = {
    1: "red",
    2: "blue"
}

function generateMatrix() {
    const matrix = []
    for (let r = 0; r < ROWS; r++) {
        matrix.push([])
        for (let c = 0; c < COLS; c++) {
            matrix[r].push(Math.round(Math.random()) + 1)
        }
    }
    return matrix
}

document.addEventListener('DOMContentLoaded', () => {
    const matrix = generateMatrix();
    const firstTable = document.querySelector("table");
    matrix.forEach((row, rowIdx) => {
        const newRow = document.createElement("tr");
        row.forEach((cell, cellIdx) => {
            const newCell = document.createElement("td")
            newCell.innerText = cell;
            newCell.setAttribute("style", `background-color: ${CELLS_BG[cell]}`);
            newCell.addEventListener('click', () => {
                newCell.setAttribute("style", "background-color: inherit;")
                matrix[rowIdx][cellIdx] = 0
            })
            newRow.appendChild(newCell);
        })
        firstTable.appendChild(newRow)
    })

    const generateBtn = document.querySelector("button")
    generateBtn.addEventListener('click', () => {
        const copyTable = document.querySelectorAll("table")[1];
        copyTable.innerHTML = "";
        matrix.forEach((row, rowIdx) => {
            const newRow = document.createElement("tr");
            row.forEach((cell, cellIdx) => {
                const newCell = document.createElement("td")
                newCell.innerText = cell;
                newRow.appendChild(newCell);
            })
            copyTable.appendChild(newRow)
        })
    })
})