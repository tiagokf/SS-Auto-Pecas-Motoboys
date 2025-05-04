<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" href="style.css">
  <script src="script.js"></script>
  
  <title>Login - SSMotoboys</title>
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center align-items-center" style="height:100vh">
      <div class="col-4">
        <div class="card">
          <div class="card-body">
            <h2 class="card-title mb-4">Login - SS Auto Peças</h2>
            <form action="login.php" method="post">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
              </div>
              <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
              </div>
              <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
            <!--
            <button type="button" class="btn btn-link mt-3" data-bs-toggle="modal" data-bs-target="#modalCadastro">
              Criar novo usuário
            </button>-->
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCadastroLabel">Criar novo usuário</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <form action="cadastrar_usuario.php" method="post">
              <div class="mb-3">
                <label for="nome_cadastro" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome_cadastro" name="nome_cadastro" required>
              </div>
              <div class="mb-3">
                <label for="senha_cadastro" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha_cadastro" name="senha_cadastro" required>
              </div>
              <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>
</html>
