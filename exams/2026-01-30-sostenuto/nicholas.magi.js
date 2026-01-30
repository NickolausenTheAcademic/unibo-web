async function loadStudents() {
    return fetch("studenti.json").then(res => res.json())
}

function clearStudentTable(table, header) {
    table.innerHTML = "";
    table.appendChild(header);
}

function displayStudents(students, studTable, predicate) {
    students.filter(stud => predicate(stud)).forEach(student => {
        const studRow = document.createElement("tr");
        Object.keys(students[0]).forEach(key => {
            const td = document.createElement("td");
            td.innerText = student[key];
            studRow.appendChild(td);
        })
        studTable.appendChild(studRow);
    });
}

document.addEventListener("DOMContentLoaded", async () => {
    /**
     * Serve solo per attivare l'intellisense
     * @type [{ "matricola", "nome", "cognome", "corso", "media" }]
     */
    const students = await loadStudents();
    const main = document.querySelector("main");
    const studTable = document.createElement("table");
    const headerRow = document.createElement("tr");

    Object.keys(students[0]).forEach(field => {
        const th = document.createElement("th");
        th.innerText = field.toUpperCase();
        headerRow.appendChild(th);
    })

    studTable.appendChild(headerRow)
    displayStudents(students, studTable, () => true);
    main.appendChild(studTable);
    /**
     * @type HTMLSelectElement
     */
    const select = document.querySelector("#corsoSelect")
    
    new Set(students.map(stud => stud["corso"])).forEach(corso => {
        const opt = document.createElement("option");
        opt.innerText = corso;
        opt.value = corso;
        select.appendChild(opt)
    })
    
    select.addEventListener('change', (e) => {
        clearStudentTable(studTable, headerRow);
        if (e.target.value === "") {
            displayStudents(students, studTable, (stud) => true);
        } else {
            displayStudents(students, studTable, (stud) => stud["corso"] === e.target.value);
        }
    })
})