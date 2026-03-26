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