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
  
  import react from"react";
  import GoogleLogin from "react-google-login";
  
  