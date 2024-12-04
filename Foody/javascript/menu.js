function editMenu() {
    document.getElementById("edit-modal").style.display = "block";
}

function closeModal() {
    document.getElementById("edit-modal").style.display = "none";
}

// Cierra el modal si el usuario hace clic fuera de él
window.onclick = function(event) {
    let modal = document.getElementById("edit-modal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
