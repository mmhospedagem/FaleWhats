# SDK API Falewhats PHP

<h4 align="left">
	SDK desenvolvido para ajudar nossos clientes com integrações da API Whatsapp com seus projetos em PHP. <br>ATENÇÃO: ESTE PACOTE UTILIZA O PADRÃO PSR 4 DO PHP!
</h4>

```bash
# Primeiro use o composer para importar o projeto:
composer require mmhospedagem/fale-whats
```

<h4 align="left">Configuração e Hello World</h4>

```bash
# Inclua o arquivo vendor/autoload.php caso não tenha feito e use o namespace MMHospedagem\FaleWhats EXEMPLO:
# index.php
require_once "vendor/autoload.php";
use MMHospedagem\FaleWhats\Api;

$url = "https://api.falewhats.com.br";
$licenca = "Minha Licença";
$id_sessao = "Meu ID";
$simular_presenca = true; // Simula a presença "Está digitando..."
$log = true; // Caso queira salvar os logs deixe em true;

//Chame o objeto

$api = new Api($url, $licenca, $id_sessao, $simular_presenca, $log);

//Para enviar uma Mensagem:

$telefone = "5584991137536";
$texto = "Olá, mundo! 233333333333333333";

//Em seguida use a função send_texto do objeto Api, Exemplo:

$resultadoEnvioTexto = $api->send_texto("$telefone", $texto);
var_dump($resultadoEnvioTexto);

//Caso queira recuperar o log por data use:
//OBS: PODE USAR A DATA NO PADRÃO BRASILEIRO OU AMERICANO!

$log_por_data = $api->viewLog("16/01/2024");
echo $log_por_data;

//Caso queira puxar todos os logs:

$todos_os_logs = $api->viewLog("16/01/2024");
echo $todos_os_logs;

```
Em breve: suporte total para todas as funções.<br> Para um uso "direto" via URL, consulte este repositório no GitHub. https://github.com/mmhospedagem/SDK-Falewhats-PHP

<div align="center">
    <a href="https://www.mmhospedagem.com.br">
        <img style="border-radius: 50%;" src="https://www.mmhospedagem.com.br/templates/mmhospedagem/assets/imagens/logo-tipo.png" width="100px;" alt=""/>
    </a>
</div>
<div align="center">
    Feito com ❤️ por MMHospedagem 👋🏽 [Entre em contato conosco!](https://www.mmhospedagem.com.br)
</div>
