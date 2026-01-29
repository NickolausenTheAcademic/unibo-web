const ROWS = 9
const COLS = 9

document.addEventListener('DOMContentLoaded', (event) => {
    const form = document.querySelector("form");
    const evalBtn = [ ...document.querySelectorAll("button") ]
        .find(btn => btn.textContent === "Valuta Soluzione");
    const spans = document.querySelectorAll("span");

    [ ...spans, form, evalBtn ].forEach(HTMLelement => {
        HTMLelement.style.visibility = "hidden";
    })

    const newGameBtn = [ ...document.querySelectorAll("button") ]
        .find(btn => btn.textContent === "Nuova partita")
    newGameBtn.addEventListener('click', () => {
        const url = "../php/index.php"
        fetch(url)
            .then(res => res.json(), err => console.error("Error in JSON parsing: ".concat(err)))
            .then(data => {
                const table = document.querySelector("table")
                appendToTable(table, ROWS, COLS, data.state)
                form.style.visibility = "visible";
                evalBtn.style.visibility = "visible";
                form.reset()
            })
    })

    const addBtn = document.querySelector(`input[type="submit"]`);
    addBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const rowInput = document.querySelector("#riga").value
        if (rowInput < 1 || rowInput > ROWS) {
            alert("Errore! Inserita riga non compresa in [1,9]")
            return;
        }
        const colInput = document.querySelector("#colonna").value
        if (colInput < 1 || colInput > COLS) {
            alert("Errore! Inserita colonna non compresa in [1,9]")
            return;
        }
        const valInput = document.querySelector("#valore").value
        if (valInput < 1 || valInput > 9) {
            alert("Errore! Inserito valore non compreso in [1,9]")
            return;
        }
        const table = document.querySelector("table")
        insertIntoTable(table, 
            new Number(rowInput) - 1, 
            new Number(colInput) - 1, 
            valInput)
    })

    evalBtn.addEventListener('click', () => {
        let tableValues = ""
        document.querySelector("table").childNodes.forEach(rowElement => 
            rowElement.childNodes.forEach(cellElement => {
                tableValues += cellElement.innerText || "0"; 
            })
        )

        const url = "../php/getResult.php"
        fetch(url, {
            method: "POST",
            body: { 
                values: tableValues
            }
        }).then(res => res.json())
        .then(data => {
            if (data.success) {

            }
        })
    })
})

function toContinuousIndex(row, col) {
    return row * ROWS + col
}

/**
 * 
 * @param {HTMLTableElement} tableElement 
 * @param {*} row 
 * @param {*} col 
 * @param {*} value 
 */
function insertIntoTable(tableElement, row, col, value) {
    const selectedRow = tableElement.getElementsByTagName("tr")[row]
    const selectedCell = selectedRow.getElementsByTagName("td")[col]
    selectedCell.innerText = value
}

function appendToTable(tableElement, rows, cols, data) {
    for (let r = 0; r < rows; r++) {
        const row = document.createElement("tr")
        for (let c = 0; c < cols; c++) {
            const cell = document.createElement("td")
            cell.setAttribute("style", "border: 2px solid black; width: 30px; height: 30px; text-align: center;")
            cell.innerText = data[toContinuousIndex(r, c)];
            row.appendChild(cell)
        }
        tableElement.appendChild(row)
    }
}