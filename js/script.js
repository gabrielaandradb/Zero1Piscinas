function mostrarLogin() {
  console.log("Mostrando login"); // Adiciona um log para depurar
  document.getElementById("login-form").style.display = "block";
  document.getElementById("cadastro-form").style.display = "none";
}

function mostrarCadastro() {
  console.log("Mostrando cadastro"); // Adiciona um log para depurar
  document.getElementById("login-form").style.display = "none";
  document.getElementById("cadastro-form").style.display = "block";
}

  
  function validarLogin() {
    const email = document.getElementById("email_login").value;
    const senha = document.getElementById("senha_login").value;
    return email && senha;
  }
  
  function validarCadastro() {
    const nome = document.getElementById("nome_cad").value;
    const email = document.getElementById("email_cad").value;
    const senha = document.getElementById("senha_cad").value;
    return nome && email && senha;
  }
  
  /*FACEBOOK*/
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }
  
  function statusChangeCallback(response) {
    if (response.status === 'connected') {
      FB.api('/me', {fields: 'name,email'}, function(user) {
        document.getElementById('status').innerHTML = 
          'Bem-vindo(a), ' + user.name + ' (' + user.email + ')!';
      });
    } else {
      document.getElementById('status').innerHTML = 
        '';
    }
  }
  