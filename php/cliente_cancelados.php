<?php
session_start();
include_once('conexao.php');

if (!isset($_SESSION['verifica_cliente'])) {
    // Se não estiver autenticado, redireciona para a página de login
    header("Location: ../login.php");
    exit;
}

?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DUMONT - Cancelamentos</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../img/favicon_logo_dumont_32x32.png" type="image/x-icon">
</head>

<style>
    body {
        background-image: url('../img/login_background.svg');
        background-repeat: no-repeat;
        background-size: cover;
        color: white;
    }
</style>

<body>
    <!--INICIO BARRA DE NAVEGAÇÃO-->
    <nav style="background-color: #460AC6;" class="navbar navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php"><img id="logo_navbar_dumont" width="150"
                    src="../img/dumont_logo_nav_765x625.png" alt="Logo da Empresa DUMONT"></a>

            <div id="links_navbar">
                <a style="color: white;" id="link_nav1" class="nav-link" href="../index.php">Home</a>
                <a style="color: white;" id="link_nav2" class="nav-link" href="../reserva.php">Passagens</a>
                <a style="color: white;" id="link_nav3" class="nav-link" href="../ofertas.php">Ofertas</a>
                <a style="color: white;" id="link_nav4" class="nav-link" href="../login.php">Entrar</a>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 style="color: #460AC6;" class="offcanvas-title" id="offcanvasNavbarLabel">DUMONT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">


                        <button id="btn-burguer"><a style="color: white; text-decoration: none;" href="login.php"> Entre
                                ou
                                cadastre-se</a></button>

                        <hr style="margin-top: 20px; margin-bottom: 20px;">

                        <li class="nav-item">
                            <a style="color: #460AC6;" class="nav-link" href="../perfil.php"> <img id="icon_aviao"
                                    src="../img/person-circle.svg" alt="Ícone de login"> Minha conta</a>
                        </li>

                        <li class="nav-item">
                            <a style="color: #460AC6;" class="nav-link" href="minhas_viagens.php"> <img id="icon_aviao"
                                    src="../img/airplane-engines.svg" alt="Ícone de um avião"> Minhas viagens</a>
                        </li>

                        <li class="nav-item">
                            <a style="color: #460AC6;" class="nav-link" href="cliente_cancelados.php"> <img
                                    src="../img/x.svg" alt="Ícone de Cancelamento">Cancelados</a>
                        </li>

                        <li class="nav-item">
                            <a style="color: #460AC6;" class="nav-link" href="../reserva.php"> <img id="icon_ticket"
                                    src="../img/ticket.svg" alt="Ícone de um ticket"> Passagens aéreas</a>
                        </li>

                        <li class="nav-item">
                            <a style="color: #460AC6;" class="nav-link" href="../ofertas.php"> <img id="icon_fogo"
                                    src="../img/fire.svg" alt="Ícone Fogo"> Ofertas</a>
                        </li>

                        <li class="nav-item">
                            <a style="color: #460AC6;" class="nav-link" href="../contato.html"> <img id="icon_tel"
                                    src="../img/telephone.svg" alt="Ícone Telefone"> Contato</a>
                        </li>



                        <?php
                        if (isset($_SESSION['verifica_cliente']) or isset($_SESSION['verifica_login'])) {
                            echo "<li class='nav-item'>
                                        <a style='color: #460AC6;' class='nav-link' href='PHP/proc_sair_conta.php'> <img id='icon_tel' src='img/iconsaida.svg'
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

    <!--CONTEÚDO PRINCIPAL DA PÁGINA-->
    <h1 style="color: red; text-align: center; width: 100%; margin-top: 20px;">Passagens Canceladas</h1>

    <div
        style="width: 100%; min-height: 100vh; padding: 20px; display: flex; flex-direction: column; justify-content: center; align-items:flex-start;">


        <?php

        $cpf = $_SESSION['cpf_cliente'];
        $id = $_SESSION['verifica_cliente'];

        $sql = "SELECT * FROM cliente INNER JOIN passagem ON cliente.pk_cliente = passagem.fk_cliente INNER JOIN pagamento ON passagem.pk_passagem = pagamento.fk_passagem INNER JOIN aviao ON passagem.aviao_ida = aviao.pk_aviao INNER JOIN gestao_voo ON aviao.pk_aviao = gestao_voo.fk_aviao WHERE cliente.pk_cliente = '$id' AND passagem.cancelado = 'SIM'";

        $query = mysqli_query($conn, $sql);

        if (mysqli_affected_rows($conn) > 0) {
            while ($linha = mysqli_fetch_assoc($query)) {
                echo "
            

            <div style='background-color: white;
            width: 93%;
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 2px 2px 2px 2px rgba(49, 49, 49, 0.2)'>

            NOME: $linha[nome]
            $linha[sobrenome]
            <br>
            CPF: $linha[cpf_passagem]
            <br>
            POLTRONA: $linha[poltrona]
            <br>
            CLASSE: $linha[classe]
            <br>
            ORIGEM: $linha[local_voo]
            <br>
            DESTINO: $linha[local_pouso]
            <br>
            HORA DA PARTIDA: $linha[hora_partida]
            <br>
            HORA DA CHEGADA: $linha[hora_chegada]
            <br>
            VALOR: $linha[valor_pag]

            <br>
            FORMA DE PAGAMENTO: $linha[forma_pag]
            
            
            </div>
            <hr> ";
            }
        } else {
            echo "<div style='height: 100vh; width: 100%; display: flex; justify-content: center; align-items: center;'><h1>NENHUM CANCELAMENTO</h1></div>";
        }

        ?>
    </div>

    <!--FIM CONTEÚDO-->

    <!--INICIO RODAPÉ-->
    <div id="rodape" class="container-fluid">
        <div class="row">

            <div id="lugares_viajar" class="col-sm">
                <p><strong>Lugares para viajar</strong></p>
                <a class="text-white text-decoration-none" href="../ofertas.php">Salvador</a>
                <br>
                <a class="text-white text-decoration-none" href="../ofertas.php">Rio de Janeiro</a>
                <br>

                <a class="text-white text-decoration-none" href="../ofertas.php">Porto de Galinhas</a>
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
                        src="../img/instagram.svg" alt="Ícone do Instagram">Instagram</a>
                <br>
                <a class="text-white text-decoration-none" href="https://pt-br.facebook.com/" target="_blank"><img
                        src="../img/facebook.svg" alt="Ícone do Facebook">
                    Facebook</a>
                <br>
                <a class="text-white text-decoration-none" href="https://twitter.com/" target="_blank"><img
                        src="../img/twitter.svg" alt="Ícone do Twitter">
                    Twitter</a>
            </div>

        </div>
    </div>
    <!--FIM RODAPÉ-->
</body>

</html>