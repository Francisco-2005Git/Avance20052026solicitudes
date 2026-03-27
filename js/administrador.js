var titulosSecciones = {
    bitacora:          'Bitácora',
    'generar-reporte': 'Generar Reporte',
    'admin-usuarios':  'Administrar Usuarios'
};

inicializarNavegacion(titulosSecciones);

function openModal(action, name, role) {
    name = name || '';
    role = role || '';

    var title = document.getElementById('modal-title');
    var nameInput = document.getElementById('user-name');
    var roleSelect = document.getElementById('user-role');

    if (action === 'add') {
        title.textContent = 'Agregar Usuario';
        nameInput.value = '';
        roleSelect.value = 'usuario';
    } else if (action === 'edit') {
        title.textContent = 'Editar Usuario';
        nameInput.value = name;
        roleSelect.value = role;
    }

    document.getElementById('userModal').classList.add('abierto');
}

function closeModal() {
    document.getElementById('userModal').classList.remove('abierto');
}

document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function deleteUser(name) {
    if (confirm('¿Eliminar a ' + name + '?')) {
        alert(name + ' eliminado.');
        // Aquí podrías remover el item de la lista
    }
}

// Filtrado de tabla de usuarios
(function () {
    var inputBuscar = document.getElementById("buscar-usuario");
    var selectRol   = document.getElementById("filtro-rol");

    if (!inputBuscar || !selectRol) return;

    function filtrar() {
        var texto = inputBuscar.value.toLowerCase().trim();
        var rol   = selectRol.value.toLowerCase();

        document.querySelectorAll("#tabla-usuarios tr").forEach(function (fila) {
            if (fila.querySelector("td[colspan]")) {
                fila.style.display = "";
                return;
            }

            var nombre   = (fila.cells[0]?.textContent || "").toLowerCase();
            var username = (fila.cells[1]?.textContent || "").toLowerCase();
            var rolFila  = (fila.cells[3]?.textContent || "").toLowerCase();

            var coincideTexto = nombre.includes(texto) || username.includes(texto);
            var coincideRol   = rol === "" || rolFila.includes(rol);

            fila.style.display = coincideTexto && coincideRol ? "" : "none";
        });
    }
    inputBuscar.addEventListener("input", filtrar);
    selectRol.addEventListener("change", filtrar);
})();