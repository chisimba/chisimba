function exportChange(value) {
    if (value == "0") {
        document.getElementById("modDrop").style.display = "";
        document.getElementById("modStr").style.display = "";
    } else {
        document.getElementById("modDrop").style.display = "none";
        document.getElementById("modStr").style.display = "none";
    }
}

function importChange(value) {
    if (value == "0") {
        document.getElementById("importDrop").style.display = "";
        document.getElementById("importStr").style.display = "";
    } else {
        document.getElementById("importDrop").style.display = "none";
        document.getElementById("importStr").style.display = "none";
    }
}