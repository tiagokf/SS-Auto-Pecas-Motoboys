# Sistema de Gerenciamento de Motoboys

Um sistema para gerenciar saídas e retornos de motoboys, desenvolvido com PHP e Semantic UI.

## Características

- Controle de usuários e autenticação
- Gerenciamento de motoboys
- Registro de saídas e retornos
- Painel administrativo
- Interface moderna com Semantic UI
- Design responsivo para dispositivos móveis

## Requisitos

- PHP 7.2 ou superior
- MySQL 5.7 ou MariaDB 10.0 ou superior
- Servidor web (Apache, Nginx, etc)

## Instalação

1. Clone ou faça download deste repositório
2. Importe o arquivo `banco.sql` para seu servidor MySQL/MariaDB
3. Configure o arquivo `config/database.php` com os dados de acesso ao banco
4. Certifique-se de que o servidor web tenha permissões adequadas para as pastas do projeto
5. Acesse o sistema pelo navegador

## Configuração Inicial

Por padrão, o sistema não possui usuários cadastrados. Para criar o primeiro usuário, execute o seguinte SQL:

```sql
INSERT INTO usuarios (nome, senha) VALUES ('admin', '$2y$10$7r4mglwgxVWqXGO1Hp0H5euOu3RVk2XqhL7OylSfX3JsPBM7JoYCy');
```

Isso criará um usuário com:

- Nome: admin
- Senha: admin123

**Importante:** Altere a senha após o primeiro login por questões de segurança.

## Uso

1. Faça login com suas credenciais
2. Na página inicial, você verá o dashboard com estatísticas gerais
3. Para registrar uma saída, preencha o formulário na seção "Registrar Saída"
4. Para registrar um retorno, clique no botão "Registrar Retorno" na lista de movimentos
5. Para gerenciar motoboys, acesse o menu "Motoboys"

## Estrutura do Projeto

- `assets/` - Arquivos CSS e JavaScript
- `config/` - Arquivos de configuração
- `includes/` - Funções e componentes reutilizáveis
- `*.php` - Arquivos principais do sistema

## Suporte

Para relatar problemas ou sugestões, abra uma issue neste repositório.

## Licença

Este projeto é licenciado sob a licença MIT.
