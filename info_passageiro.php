<?php
session_start();
include_once('php/conexao.php');

// Selecionando a quantidade de passagens no banco
$qtd_passagens = "SELECT COUNT(*) AS qtd_registro FROM passagem";
$executar_contagem = mysqli_query($conn, $qtd_passagens);
$num_passagem = mysqli_fetch_assoc($executar_contagem);

// Caso seja uma viagem seja apenas de ida, a sessão abaixo irá armazenar os assentos escolhidos pelo cliente ta tela de escolher assentos
if ($_SESSION['retorno'] == 'apenas ida') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Looping que vai coletar os dados de assentos
        for ($coletar = 1; $coletar <= $_SESSION['qtd_passagem']; $coletar++) {
            $_SESSION["assento$coletar"] = $_POST["passageiro$coletar"];

            // Looping que verifica se o assento já foi escolhido
            for ($cliente = 1; $cliente <= $num_passagem['qtd_registro']; $cliente++) {
                if ($_SESSION["assento$coletar"] == $_SESSION["poltrona_cliente$cliente"]) {
                    $_SESSION["assento$coletar"] = '';
                    $_SESSION["msg_assento$coletar"] = "<p style='font-size: 13px' class='text-danger'>* Esse assento já está ocupado</p>";
                    header('Location: assentos.php');
                    break;
                }
            }

            // A partir do segundo assento, haverá copmparação para verificar se os assentos são iguais
            if ($coletar > 1) {
                $assento = $_POST["passageiro$coletar"];

                // Looping que vai comparar os assentos para verificar se há algum igual
                for ($comparar = 1; $comparar <= $_SESSION['qtd_passagem']; $comparar++) {
                    // Se os assentos forem iguais, será emitido um aviso para o usuário
                    if (($assento == $_POST["passageiro$comparar"]) && ($coletar != $comparar)) {
                        $_SESSION["assento$coletar"] = '';
                        $_SESSION["msg_assento$coletar"] = "<p style='font-size: 13px' class='text-danger'>* assentos no podem ser iguais</p>";
                        header('Location: assentos.php');
                        break;
                    } else {
                        $_SESSION["passageiro_ida$coletar"] = $_POST["passageiro$coletar"];
                    }
                }
            } else {
                $_SESSION["passageiro_ida$coletar"] = $_POST["passageiro$coletar"];
            }
        }
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Looping que vai coletar os dados de assentos do avião de retorno
        for ($coletar = 1; $coletar <= $_SESSION['qtd_passagem']; $coletar++) {
            $_SESSION["assento_retorno$coletar"] = $_POST["passageiro_retorno$coletar"];

            // Looping que verifica se o assento já foi escolhido
            for ($cliente = 1; $cliente <= $num_passagem['qtd_registro']; $cliente++) {
                if ($_SESSION["assento_retorno$coletar"] == $_SESSION["poltrona_retorno_cliente$cliente"]) {
                    $_SESSION["assento_retorno$coletar"] = '';
                    $_SESSION["msg_assento_retorno$coletar"] = "<p style='font-size: 13px' class='text-danger'>* Esse assento já está ocupado</p>";
                    header('Location: assento_retorno.php');
                }
            }

            // A partir do segundo assento, haverá comparação para verificar se os assentos são iguais
            if ($coletar > 1) {
                $assento = $_POST["passageiro_retorno$coletar"];

                // Looping que vai comparar os assentos para verificar se há algum igual
                for ($comparar = 1; $comparar <= $_SESSION['qtd_passagem']; $comparar++) {
                    // Se os assentos forem iguais, será emitido um aviso para o usuário
                    if (($assento == $_POST["passageiro_retorno$comparar"]) && ($coletar != $comparar)) {
                        $_SESSION["assento_retorno$coletar"] = '';
                        $_SESSION["msg_assento_retorno$coletar"] = "<p style='font-size: 13px' class='text-danger'>* Assentos não podem ser iguais</p>";
                        header('Location: assento_retorno.php');
                        break;
                    } else {
                        $_SESSION["passageiro_retorno$coletar"] = $_POST["passageiro_retorno$coletar"];
                    }
                }
            } else {
                $_SESSION["passageiro_retorno$coletar"] = $_POST["passageiro_retorno$coletar"];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/info_passageiro.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="shortcut icon" href="img/favicon_logo_dumont_32x32.png" type="image/x-icon">
    <title>DUMONT - Informações dos passageiros</title>
</head>

<body>
    <!--INICIO BARRA DE NAVEGAÇÃO-->
    <header>
        <!--INICIO BARRA DE NAVEGAÇÃO-->
        <nav style="background-color: #460AC6;" class="navbar navbar-dark sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><img id="logo_navbar_dumont" width="150"
                        src="img/dumont_logo_nav_765x625.png" alt="Logo da Empresa DUMONT"></a>

                <div id="links_navbar">
                    <a style="color: white;" id="link_nav1" class="nav-link" href="index.php">Home</a>
                    <a style="color: white;" id="link_nav2" class="nav-link" href="reserva.php">Passagens</a>
                    <a style="color: white;" id="link_nav3" class="nav-link" href="ofertas.php">Ofertas</a>

                    <?php
                    if (!isset($_SESSION['verifica_cliente'])) {
                        echo "<a style='color: white; !important' id='link_nav4' class='nav-link' href='login.php'>Entrar</a>";

                    } else if (!isset($_SESSION['verifica_login'])) {
                        echo "<a style='color: white; !important' id='link_nav4' class='nav-link' href='login.php'>Entrar</a>";

                    } else {
                        echo "<a style='color: white; !important' id='link_nav4' class='nav-link' href='php/proc_sair_conta.php'>Sair</a>";
                    }
                    ?>

                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                    aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h2 style="color: #460AC6;" class="offcanvas-title" id="offcanvasNavbarLabel">DUMONT</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">

                            <?php

                            if (isset($_SESSION['verifica_cliente'])) {

                                echo "<h3 style='color: #460AC6; padding-left: 16px; !important'>$_SESSION[nome_cliente]</h3>";


                            } else if (isset($_SESSION['verifica_login'])) {

                                echo "<h3 style='color: #460AC6; padding-left: 16px; !important'>$_SESSION[verifica_login]</h3>";

                            } else {

                                echo " <button id='btn-burguer'><a style='color: white; text-decoration: none;' href='login.php'> Entre ou
              cadastre-se</a></button>
              <hr style='margin-top: 20px; margin-bottom: 20px;'>";

                            }

                            ?>



                            <li class="nav-item">
                                <a style="color: #460AC6;" class="nav-link" href="perfil.php"> <img id="icon_aviao"
                                        src="img/person-circle.svg" alt="Ícone de login"> Minha conta</a>
                            </li>

                            <li class="nav-item">
                                <a style="color: #460AC6;" class="nav-link" href="php/minhas_viagens.php"> <img
                                        id="icon_aviao" src="img/airplane-engines.svg" alt="Ícone de um avião"> Minhas
                                    viagens</a>
                            </li>

                            <li class="nav-item">
                                <a style="color: #460AC6;" class="nav-link" href="reserva.php"> <img id="icon_ticket"
                                        src="img/ticket.svg" alt="Ícone de um ticket"> Passagens aéreas</a>
                            </li>

                            <li class="nav-item">
                                <a style="color: #460AC6;" class="nav-link" href="ofertas.php"> <img id="icon_fogo"
                                        src="img/fire.svg" alt="Ícone Fogo"> Ofertas</a>
                            </li>

                            <li class="nav-item">
                                <a style="color: #460AC6;" class="nav-link" href="contato.php"> <img id="icon_tel"
                                        src="img/telephone.svg" alt="Ícone Telefone"> Contato</a>
                            </li>

                            <?php
                            if (isset($_SESSION['verifica_cliente']) or isset($_SESSION['verifica_login'])) {
                                echo "<li class='nav-item'>
                                        <a style='color: #460AC6;' class='nav-link' href='php/proc_sair_conta.php'> <img id='icon_tel' src='img/iconsaida.svg'
                                        alt='Ícone Saída'> Sair</a>
                                        </li>
                                        ";
                            }

                            ?>


                    </div>
                </div>
            </div>
        </nav>
        <!--FIM BARRA DE NAVEGAÇÃO-->

    </header>
    <!--FIM BARRA DE NAVEGAÇÃO-->


    <div class="container my-5">
        <h1 class="text-uppercase fs-2">insira as informações de cada passageiro</h1>
        <form action="php/proc_passagem.php" method="post">
            <?php
            // Looping que vai gerar os input para ser inserido as informações de cada passageiro
            for ($selecionar = 1; $selecionar <= $_SESSION['qtd_passagem']; $selecionar++) {
                echo "
                    <div class='row mb-4'>
                        <div class='col-md-4 mb-2'>
                            <label class='form-label m-0' for='nome_passageiro$selecionar'>Nome</label>
                            <input type='text' class='form-control' name='nome_passageiro$selecionar' value='";

                if (isset($_SESSION["passageiro_nome$selecionar"])) {
                    echo $_SESSION["passageiro_nome$selecionar"];
                    unset($_SESSION["passageiro_nome$selecionar"]);
                }

                echo "' required>";

                if (isset($_SESSION["msg_nome$selecionar"])) {
                    echo $_SESSION["msg_nome$selecionar"];
                    unset($_SESSION["msg_nome$selecionar"]);
                }

                echo "
                        </div>
                        
                        <div class='col-md-4 mb-2'>
                            <label class='form-label m-0' for='sobrenome_passageiro$selecionar'>Sobrenome</label>
                            <input type='text' class='form-control' name='sobrenome_passageiro$selecionar' value='";

                if (isset($_SESSION["passageiro_sobrenome$selecionar"])) {
                    echo $_SESSION["passageiro_sobrenome$selecionar"];
                    unset($_SESSION["passageiro_sobrenome$selecionar"]);
                }

                echo "' required>
                        </div>
                        
                        <div class='col-1'>
                            <label class='form-label m-0' for='cpf_passageiro$selecionar'>CPF</label>
                            <input type='text' class='form-control' style='width: 8rem;' name='cpf_passageiro$selecionar' minlength='11' maxlength='11' value='";

                if (isset($_SESSION["passageiro_cpf$selecionar"])) {
                    echo $_SESSION["passageiro_cpf$selecionar"];
                    unset($_SESSION["passageiro_cpf$selecionar"]);
                }

                echo "' required>
                    ";


                if (isset($_SESSION["msg_cpf$selecionar"])) {
                    echo $_SESSION["msg_cpf$selecionar"];
                    unset($_SESSION["msg_cpf$selecionar"]);
                }

                echo "
                        </div>

                        <div class='col-1'>
                            <label class='form-label m-0' id='label_assento' for='assento$selecionar'>Assento</label>
                            <input type='text' class='form-control' id='input_assento' style='width: 4rem;' name='assento$selecionar' value='" . $_SESSION["passageiro_ida$selecionar"] . "' readonly>
                        </div>
                        ";

                // Se a viagem for de ida e volta, o sistema irá coletar os assentos do avião de retorno
                if ($_SESSION['retorno'] != 'apenas ida') {
                    echo "
                        <div class='col-1'>
                            <label class='form-label m-0' id='label_assento' for='assento_retorno$selecionar'>Assento</label>
                            <input type='text' class='form-control' id='input_assento' style='width: 4rem;' name='assento_retorno$selecionar' value='" . $_SESSION["passageiro_retorno$selecionar"] . "' readonly>
                        </div>";
                }

                echo "</div>";
            }
            ?>
            <button type="submit" class="btn btn-success text-uppercase">pagar</button>

            <a href="assento_retorno.php" id="voltar" class="btn btn-danger text-uppercase">voltar</a>
        </form>
    </div>


    <!--INICIO RODAPÉ-->
    <div id="rodape" class="container-fluid">
        <div class="row">

            <div id="lugares_viajar" class="col-sm">
                <p><strong>Lugares para viajar</strong></p>
                <a class="text-white text-decoration-none" href="ofertas.php">Salvador</a>
                <br>
                <a class="text-white text-decoration-none" href="ofertas.php">Rio de Janeiro</a>
                <br>

                <a class="text-white text-decoration-none" href="ofertas.php">Porto de Galinhas</a>
            </div>

            <div class="col-sm">
                <p><strong>Companhías Aéreas</strong></p>
                <a class="text-white text-decoration-none" href="https://www.latamairlines.com/"
                    target="_blank">Latam</a>
                <br>
                <a class="text-white text-decoration-none" href="https://www.voegol.com.br/" target="_blank">Gol</a>
                <br>
                <a class="text-white text-decoration-none" href="https://www.voeazul.com.br/" target="_blank">Azul</a>
            </div>

            <div class="col-sm">
                <p><strong>Redes Socias</strong></p>

                <a class="text-white text-decoration-none" href="https://www.instagram.com/"><img
                        src="img/instagram.svg" alt="Ícone do Instagram">Instagram</a>
                <br>
                <a class="text-white text-decoration-none" href="https://pt-br.facebook.com/" target="_blank"><img
                        src="img/facebook.svg" alt="Ícone do Facebook">
                    Facebook</a>
                <br>
                <a class="text-white text-decoration-none" href="https://twitter.com/" target="_blank"><img
                        src="img/twitter.svg" alt="Ícone do Twitter">
                    Twitter</a>
            </div>

        </div>
    </div>
    <!--FIM RODAPÉ-->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>

</html>