document.getElementById('togglePass').addEventListener('click', function () {
    var input = document.getElementById('password');
    var isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    this.textContent = isPass ? '>.<' : '👁'; // Aun ando viendo como poner lo de los ojitos (Por lo pronto inutilizable)
});
