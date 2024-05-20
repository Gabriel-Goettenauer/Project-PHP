<?php
date_default_timezone_set('America/Sao_Paulo');

$users = [
    ['login' => 'admin', 'password' => '1234']
];

$loggedUser = null;
$sales = [];
$log = [];
$value = 0;
$item = "";
function login()
{
    global $loggedUser, $users, $log;
    system('clear');

    echo "Login:\n";
    echo "Usuário: ";
    $username = trim(fgets(STDIN));
    echo "Senha: ";
    $password = trim(fgets(STDIN));


    foreach ($users as $user) {
        if ($user['login'] === $username && $user['password'] === $password) {
            $loggedUser = $user;
            $log[] = date('d/m/Y H:i') . " - Usuário {$user['login']} logou .\n";
            return;
        }
    }

    echo "Senha ou o usuário estão incorretos .\n";
}

function menu()
{
    global $loggedUser, $sales;
    system('clear');

    echo "Login realizado: {$loggedUser['login']}\n";
    echo "Valor de vendas: " . getTotalSales() ."\n";
    echo "1. Vender \n";
    echo "2. Cadastrar novo usuário \n";
    echo "3. Verificar log\n";
    echo "4. Deslogar\n";
    echo "Escolha uma opção: ";

    $option = trim(fgets(STDIN));
    switch ($option) {

        case '1':
            sell();
            break;
        case '2':
            registerUser();
            break;
        case '3':
            viewLog();
            break;
        case '4':
            logout();
            break;
        default:
            echo "Opção inválida .\n";

    }

}


function askInitialCash()
{
    global $initialCash;
    echo "Informe o valor inicial do caixa: ";
    $initialCash = floatval(trim(fgets(STDIN)));

}
function sell()
{
    global $loggedUser, $sales, $log, $initialCash, $item, $value;
    //$askInitialCash = 0;
    echo "Nome do item vendido: ";
    $item = trim(fgets(STDIN));
    echo "Valor da venda: ";
    $value = trim(fgets(STDIN));

    if (empty($initialCash)) {
        askInitialCash();
    }

    echo "Informe o valor entregue pelo cliente: ";
    $received = floatval(trim(fgets(STDIN)));

    $change = $received - $value;
    if ($change < 0) {
        echo "Valor insuficiente. Venda cancelada .\n";
        sleep(5);

        return;
    }

    if ($change > $initialCash) {
        echo "Não há troco suficiente no caixa. Venda cancelada .\n";
        sleep(5);

        return;
    }

    if (is_numeric($value) && $value > 0) {
        $sales[] = ['item' => $item, 'value' => $value, 'user' => $loggedUser['login'], 'time' => date('d/m/Y H:i:s')];
        $log[] = date('D/M/Y H:i:s') . " - Usuário {$loggedUser['login']} fez uma venda do item {$item} com valor de {$value}.\\n";
        echo "Venda registrada com sucesso .\n";
    } else {
        echo "Valor inválido .\n";
    }

    $initialCash -= $change; // Subtrai o troco do valor do caixa
    echo "Troco: $change \n";
    arch();
    sleep(5);
}



function registerUser()
{
    global $users, $log;

    echo "Novo usuário - login: ";
    $login = trim(fgets(STDIN));

    echo "Novo usuário - senha: ";
    $password = trim(fgets(STDIN));

    foreach ($users as $user) {
        if ($user['login'] === $login) {
            echo "Usuário já existente .\n";

            return;
        }
    }

    $users[] = ['login' => $login, 'password' => $password];
    $log[] = date('d/m/Y H:i:s') . " - Novo usuário cadastrado: {$login}.\\n";
    echo "Novo usuário cadastrado com sucesso .\n";

}

function viewLog()
{
    global $log;
    system('clear');

    echo "Login do sistema:\n";

    foreach ($log as $logEntry) {
        echo $logEntry;
    }
    echo "\n Enter para retornar ao menu.";
    fgets(STDIN);
}

function logout()
{

    global $loggedUser, $log;

    $log[] = gmdate('m/d/Y H:i:s') . " - Usuário {$loggedUser['login']} deslogou .\n";
    $loggedUser = null;
}

function getTotalSales()
{

    global $sales;
    $total = 0;
    foreach ($sales as $sale) {
        $total += $sale['value'];

    }
    return $total;
}
//////////////////////////////////
function arch()
{
    global $loggedUser, $item, $value;

    $arquivo = fopen("log.txt", "w");
    $text1 = "{$loggedUser['login']} fez uma venda no valor de {$value} do item {$item}  ";
    fwrite($arquivo, $text1);
    fclose($arquivo);

}

//////////////////////////////////

function runAplicattion()
{
    global $loggedUser;
    while (true) {
        if ($loggedUser) {
            menu();

        } else {
            login();
        }
    }
}

runAplicattion();
