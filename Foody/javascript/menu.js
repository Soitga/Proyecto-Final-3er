function editMenu() {
    document.getElementById("edit-modal").style.display = "block";
}

function closeModal() {
    document.getElementById("edit-modal").style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById("edit-modal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
