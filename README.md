## **Ozzy Router**

- Um simples roteador.

### **Desenvolvimento**

- Ainda sem suporte a rotas dinâmicas , mas pretendo implementá-las em breve, entre vários outros recursos.

### **Ambiente Necessário**

- PHP (>= 8.1)

### **Uso**

Crie um arquivo .htaccess simples em seu diretório raiz se estiver usando o Apache com mod_rewrite habilitado.

Esta configuração vai redirecionar todas as solicitações para seu arquivo index.php.

```apache
<IfModule mod_rewrite.c>

    RewriteEngine On
    Options +FollowSymlinks
    Options -Indexes
    RewriteCond %{SCRIPT_FILENAME} !-f
    RewriteCond %{SCRIPT_FILENAME} !-d
    RewriteCond %{SCRIPT_FILENAME} !-l
    RewriteRule ^(.*)$ index.php/$1

</IfModule>
```

É possível chamar os métodos HTTP tanto em minúsculo quanto em maiúsculo;

```php
Router::get();
Router::GET();
Router::post();
Router::POST();
```

Exemplo de nomeação de rotas.

```php
require __DIR__.'/vendor/autoload.php';

use OzzyRouter\Router;
use Http\Request;
use Http\Controllers\AlgumControlador;

$router = new Router(new Request());

Router::get('/users', [AlgumControlador::class, 'index']);
Router::post('/users/create', [AlgumControlador::class, 'create']);

try {
    $router->dispatch();
} catch (Exception $e) {
    http_response_code(404);
    echo 'NOT FOUND';
}
```

É possível nomear um middleware a um grupo de rotas.

Exemplo de middleware `(sempre deve ter o método estático handle)`.

```php
namespace Http\Middlewares;

class RedirectSeNaoEstiverLogado
{
    public static function handle(): void
    {
        if (empty($_SESSION['logado'])) {
            header('Location: /login');
            die;
        }
    }
}
```

Exemplo de nomeação de um middleware a um grupo de rotas.

```php
require __DIR__.'/vendor/autoload.php';

use OzzyRouter\Router;
use Http\Request;
use Http\Controllers\AlgumControlador;
use Http\Middlewares\RedirectSeNaoEstiverLogado;

$router = new Router(new Request());

Router::middleware(RedirectSeNaoEstiverLogado::class, function () {
    Router::get('/users', [AlgumControlador::class, 'index']);
    Router::post('/users/create', [AlgumControlador::class, 'create']);
});

try {
    $router->dispatch();
} catch (Exception $e) {
    http_response_code(404);
    echo 'NOT FOUND';
}
```

### **Licença**

- Este projeto está sob a licença (MIT) - veja o arquivo - [LICENSE.md](https://github.com/Ozzy005/ozzy-router/blob/main/LICENSE) para detalhes.

### **Autores**

- [Rafael Arend](https://github.com/Ozzy005)

### **Email**

- rafinhaarend123@hotmail.com
