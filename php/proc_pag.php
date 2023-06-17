<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar aeronaves</title>
    <link rel="stylesheet" href="../css/listar_aeronaves.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/buscar.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../img/favicon_logo_dumont_32x32.png" type="image/x-icon">
</head>

</html>
<?php
    session_start();
    include_once('conexao.php');

    echo $_SESSION['forma_pag'];

    // Função que vai validar o cartão de crédito ou débito
    function cardIsValid($cardNumber){
        $number = substr($cardNumber, 0, -1);
        $doubles = [];

        for ($i = 0, $t = strlen($number); $i < $t; ++$i) {
            $doubles[] = substr($number, $i, 1) * ($i % 2 == 0? 2: 1);
        }

        $sum = 0;

        foreach ($doubles as $double) {
            for ($i = 0, $t = strlen($double); $i < $t; ++$i) {
                $sum += (int) substr($double, $i, 1);
            }
        }

        return substr($cardNumber, -1, 1) == (10-$sum%10)%10;
    }

    // Função que vai validar o vencimento do cartão
    function validarValidade($mes, $ano){
        $ano_atual = date('Y');
        $mes_atual = date('m');

        if(($ano < $ano_atual)||(($ano == $ano_atual)&&($mes < $mes_atual))){
            return false;
        }else{
            return true;
        }
    }

    function validarCPF($cpf_cliente){
        $cpf_separado = '';
        $cpf_digito = substr($cpf_cliente, -2);
        $soma_peso = 0;
        $peso = 10;

        // 1º Passo - Separar os primeiros 9 dígitos dos 2 dígitos verificadores
        for ($separar = 0; $separar < (strlen($cpf_cliente) - 1); $separar++) {
            $cpf_separado = $cpf_separado . $cpf_cliente[$separar];
        }

        // 2º Passo - Multiplicar os pesos
        for ($selecionar = 0; $selecionar < strlen($cpf_separado); $selecionar++) {
            $converter_int = intval($cpf_separado[$selecionar]);
            $soma_peso += $converter_int * $peso;
            $peso--;

            if ($peso == 1) {
                break;
            }
        }

        // 3º Passo - achar o 1º dígito verificador e juntá-lo com os 9 dígitos
        if (($soma_peso  % 11) < 2) {
            $digito1 = '0';
        } else {
            $digito1 = strval(11 - ($soma_peso % 11));
        }

        // FIM DO PRIMEIRO CICLO

        $soma_peso = 0;
        $peso = 11;

        // 5º Passo - Multiplicar novamente os pesos
        for ($selecionar = 0; $selecionar < strlen($cpf_separado); $selecionar++) {
            $converter_int = intval($cpf_separado[$selecionar]);
            $soma_peso += $converter_int * $peso;
            $peso--;

            if ($peso == 1) {
                break;
            }
        }

        // 6º Passo - Achar o 2º dígito e juntá-lo com os 10 dígitos e com o 1º dígito verificador
        if (($soma_peso % 11) < 2) {
            $digito2 = '0';
        } else {
            $digito2 = strval(11 - ($soma_peso % 11));
        }

        $digito_verificador = $digito1 . $digito2;

        // 5° Passo - Validar
        if (($cpf_cliente == '00000000000') || ($cpf_cliente == '11111111111') || ($cpf_cliente == '22222222222') || ($cpf_cliente == '33333333333') ||
            ($cpf_cliente == '44444444444') || ($cpf_cliente == '55555555555') || ($cpf_cliente == '66666666666') || ($cpf_cliente == '77777777777') ||
            ($cpf_cliente == '88888888888') || ($cpf_cliente == '99999999999')
        ) {
            return false;
        } else if ($cpf_digito == $digito_verificador) {
            return true;
        } else {
            return false;
        }
    }
    if(($_SESSION['forma_pag'] == 'CREDITO')||($_SESSION['forma_pag'] == 'DEBITO')){
        $num_cartao = filter_input(INPUT_POST, 'num_cartao', FILTER_SANITIZE_NUMBER_INT);
        $nome_titular = filter_input(INPUT_POST, 'nome_titular', FILTER_SANITIZE_STRING);
        $mes_vencimento = filter_input(INPUT_POST, 'mes_vencimento', FILTER_SANITIZE_STRING);
        $ano_vencimento = filter_input(INPUT_POST, 'ano_vencimento', FILTER_SANITIZE_STRING);
        $cod_seguranca = filter_input(INPUT_POST, 'cod_seguranca', FILTER_SANITIZE_STRING);
        $cpf_pag = filter_input(INPUT_POST, 'cpf_pag', FILTER_SANITIZE_STRING);
    
        $_SESSION['num_cartao'] = $num_cartao;
        $_SESSION['nome_titular'] = $nome_titular;
        $_SESSION['mes'] = $mes_vencimento;
        $_SESSION['ano'] = $ano_vencimento;
        $_SESSION['cvv'] = $cod_seguranca;
        $_SESSION['cpf_pag'] = $cpf_pag;
    
    
        // Condicionais que irão verificar e fazer as validações da informação.
        if (cardIsValid($num_cartao)) {
            if(validarValidade($mes_vencimento, $ano_vencimento)){
                if(validarCPF($cpf_pag)){
                    // Looping que vai inserir os dados de passagem
    
                    for($inserir = 1; $inserir <= $_SESSION["qtd_passagem"]; $inserir++){
                        $forma_pag = $_SESSION["forma_pag"];
                        $nome = $_SESSION["nome_passagem$inserir"];
                        $sobrenome = $_SESSION["sobrenome_passagem$inserir"];
                        $cpf_passagem = $_SESSION["cpf_passagem$inserir"];
                        $assento_ida = $_SESSION["assento_passagem_ida$inserir"];
                        $cliente = $_SESSION['verifica_cliente'];
                        $aviao_ida = $_SESSION['aviao_ida'];
    
    
                        //  Condição que verfica se a viagem de de ida e volta ou somente ida
                        if ($_SESSION["retorno"] != 'apenas ida') {
                            // Se for de ida e volta, duas variáveis recebem o assento de retorno e avião de retorno e eles são inseridos no banco junto com as demais informações
                            $assento_volta = $_SESSION["assento_passagem_volta$inserir"];
                            $aviao_volta = $_SESSION['aviao_volta'];
    
                            $criar_passagem = "INSERT INTO passagem (sobrenome, nome, cpf_passagem, poltrona_ida, poltrona_volta, fk_cliente, aviao_ida, aviao_volta) VALUES ('$sobrenome', '$nome', '$cpf_passagem', '$assento_ida', '$assento_volta', $cliente, $aviao_ida, $aviao_volta)";
                            $executa_criacao = mysqli_query($conn, $criar_passagem);
    
                            if(mysqli_insert_id($conn)){
                                // Processo de inserção de pagamento
                                $armazenar_pagamento = "INSERT INTO pagamento (forma_pag, valor_pag, data_pag, fk_passagem) SELECT '$forma_pag', 988.95, NOW(), pk_passagem FROM passagem WHERE fk_cliente = $cliente AND cpf_passagem = $cpf_passagem AND poltrona_ida = $assento_ida AND aviao_ida = $aviao_ida";
                                $executa_armazenagem = mysqli_query($conn, $armazenar_pagamento);

                                if(mysqli_insert_id($conn)){
                                    $_SESSION['msg'] = "<h5 class='text-uppercase text-success text-center'>compra realizada com sucesso</h5>";
                                    header('Location: minhas_viagens.php');
                                }else{
                                    $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                                    header('Location: ../pagamento.php');
                                }
                            }else{
                                $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                                header('Location: ../pagamento.php');
                            }
                        }else{
                            $criar_passagem = "INSERT INTO passagem (sobrenome, nome, cpf_passagem, poltrona_ida, fk_cliente, aviao_ida) VALUES ('$sobrenome', '$nome', '$cpf_passagem', '$assento_ida', $cliente, $aviao_ida)";
                            $executa_criacao = mysqli_query($conn, $criar_passagem);
    
                            if (mysqli_insert_id($conn)) {
                                // Processo de inserção de pagamento
                                $armazenar_pagamento = "INSERT INTO pagamento (forma_pag, valor_pag, data_pag, fk_passagem) SELECT '$forma_pag', 988.95, NOW(), pk_passagem FROM passagem WHERE fk_cliente = $cliente AND cpf_passagem = $cpf_passagem AND poltrona_ida = $assento_ida AND aviao_ida = $aviao_ida";
                                $executa_armazenagem = mysqli_query($conn, $armazenar_pagamento);
    
                                if(mysqli_insert_id($conn)){
                                    $_SESSION['msg'] = "<h5 class='text-uppercase text-success text-center'>compra realizada com sucesso</h5>";
                                    header('Location: minhas_viagens.php');
                                }else{
                                    $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                                    header('Location: ../pagamento.php');
                                }
                            } else {
                                $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                                header('Location: ../forma_pagamento.php');
                            }
                        }
                    }
    
                }else{
                    $_SESSION['cpf_pag'] = '';
                    $_SESSION['msg'] = "<h5 class='text-danger text-uppercase'>CPF inválido</h5>";
                    header('Location: ../pagamento.php');
                }
            }else{
                $_SESSION['mes'] = '';
                $_SESSION['ano'] = '';
                $_SESSION['msg'] = "<h5 class='text-danger text-uppercase'>cartão vencido</h5>";
                header('Location: ../pagamento.php');
            };
        }else{
            $_SESSION['num_cartao'] = '';
            $_SESSION['msg'] = "<h5 class='text-danger text-uppercase'>número inválido</h5>";
            header('Location: ../pagamento.php');
        }

    }elseif($_SESSION['forma_pag'] == 'PIX'){
        $cpf_pag = filter_input(INPUT_POST, 'cpf_pag', FILTER_SANITIZE_STRING);
        $nome_pag = filter_input(INPUT_POST, 'nome_pagador', FILTER_SANITIZE_STRING);

        if(validarCPF($cpf_pag)){
            // Looping que vai inserir os dados de passagem

            for($inserir = 1; $inserir <= $_SESSION["qtd_passagem"]; $inserir++){
                $forma_pag = $_SESSION["forma_pag"];
                $nome = $_SESSION["nome_passagem$inserir"];
                $sobrenome = $_SESSION["sobrenome_passagem$inserir"];
                $cpf_passagem = $_SESSION["cpf_passagem$inserir"];
                $assento_ida = $_SESSION["assento_passagem_ida$inserir"];
                $cliente = intval($_SESSION['verifica_cliente']);
                $aviao_ida = $_SESSION['aviao_ida'];


                //  Condição que verfica se a viagem de de ida e volta ou somente ida
                if ($_SESSION["retorno"] != 'apenas ida') {
                    // Se for de ida e volta, duas variáveis recebem o assento de retorno e avião de retorno e eles são inseridos no banco junto com as demais informações
                    $assento_volta = $_SESSION["assento_passagem_volta$inserir"];
                    $aviao_volta = $_SESSION['aviao_volta'];

                    $criar_passagem = "INSERT INTO passagem (sobrenome, nome, cpf_passagem, poltrona_ida, poltrona_volta, fk_cliente, aviao_ida, aviao_volta) VALUES ('$sobrenome', '$nome', '$cpf_passagem', '$assento_ida', '$assento_volta', $cliente, $aviao_ida, $aviao_volta)";
                    $executa_criacao = mysqli_query($conn, $criar_passagem);

                    if(mysqli_insert_id($conn)){
                        // Processo de armazenar pagamento
                        $armazenar_pagamento = "INSERT INTO pagamento (forma_pag, valor_pag, data_pag, fk_passagem) SELECT '$forma_pag', 988.95, NOW(), pk_passagem FROM passagem WHERE fk_cliente = $cliente AND cpf_passagem = $cpf_passagem AND poltrona_ida = $assento_ida AND aviao_ida = $aviao_ida";
                        print_r($armazenar_pagamento);
                        $executa_armazenagem = mysqli_query($conn, $armazenar_pagamento);
                
                        if(mysqli_insert_id($conn)){
                            $_SESSION['msg'] = "<h5 class='text-uppercase text-success text-center'>compra realizada com sucesso</h5>";
                            header('Location: minhas_viagens.php');
                        }else{
                            $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                            header('Location: ../pagamento.php');
                        }
                    }else{
                        $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                        header('Location: ../pagamento.php');
                    }
                }else{
                    $criar_passagem = "INSERT INTO passagem (sobrenome, nome, cpf_passagem, poltrona_ida, fk_cliente, aviao_ida) VALUES ('$sobrenome', '$nome', '$cpf_passagem', '$assento_ida', $cliente, $aviao_ida)";
                    $executa_criacao = mysqli_query($conn, $criar_passagem);

                    if (mysqli_insert_id($conn)) {
                        // Processo de armazenar pagamento
                        $armazenar_pagamento = "INSERT INTO pagamento (forma_pag, valor_pag, data_pag, fk_passagem) SELECT '$forma_pag', 988.95, NOW(), pk_passagem FROM passagem WHERE fk_cliente = $cliente AND cpf_passagem = $cpf_passagem AND poltrona_ida = $assento_ida AND aviao_ida = $aviao_ida";
                        $executa_armazenagem = mysqli_query($conn, $armazenar_pagamento);

                        if(mysqli_insert_id($conn)){
                            $_SESSION['msg'] = "<h5 class='text-uppercase text-success text-center'>compra realizada com sucesso</h5>";
                            header('Location: minhas_viagens.php');
                        }else{
                            $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                            header('Location: ../pagamento.php');
                        }
                    } else {
                        $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
                        header('Location: ../pagamento.php');
                    }
                }
            }

        }else{
            $_SESSION['cpf_pag'] = '';
            $_SESSION['msg'] = "<h5 class='text-danger text-uppercase'>CPF inválido</h5>";
            header('Location: ../pagamento.php');
        }
    }

    // echo $num_cartao . '<br>';
    // echo $nome_titular . '<br>';
    // echo $mes_vencimento . '<br>';
    // echo $ano_vencimento . '<br>';
    // echo $cod_seguranca . '<br>';
    // echo $cpf_pag . '<br>';


    // if(mysqli_insert_id($conn)){
    //     // Inserindo as informações de pagamento

    //     $armazenar_pagamento = "INSERT INTO pagamento (forma_pag, valor_pag, data_pag, fk_passagem) VALUES ($forma_pag', 988.95, NOW(), $cliente)";
    //     $executa_armazenagem = mysqli_query($conn, $armazenar_pagamento);

    //     if(mysqli_insert_id($conn)){
    //         $_SESSION['msg'] = "<h5 class='text-uppercase text-success text-center'>compra realizada com sucesso</h5>";
    //         header('Location: minhas_viagens.php');
    //     }else{
    //         $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
    //         header('Location: forma_pagamento.php');
    //     }
    // }else{
    //     $_SESSION['msg'] = "<h5 class='text-uppercase text-danger text-center'>não foi possível realizar a compra - tente novamente mais tarde</h5>";
    //     header('Location: forma_pagamento.php');
    // }
?>