<?php
session_start();
include_once('php/conexao.php');

// Selecionando a quantidade de passagens no banco
$qtd_passagens = "SELECT COUNT(*) AS qtd_registro FROM passagem";
$executar_contagem = mysqli_query($conn, $qtd_passagens);
$num_passagem = mysqli_fetch_assoc($executar_contagem);

// Esvaziando os $_SESSIONs
for ($esvaziar = 1; $esvaziar <= $num_passagem['qtd_registro']; $esvaziar++) {
    $_SESSION["poltrona_cliente$esvaziar"] = '';
}

if ($_SESSION['retorno'] != 'apenas ida') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id_aviao_volta = filter_input(INPUT_POST, 'retorno', FILTER_SANITIZE_NUMBER_INT);
        $_SESSION['aviao_volta'] = $id_aviao_volta;
    }
}

// Variáveis que vão identificar o avião com base no ID dele. Este ID do avião estará como o valor dos inputs do tipo radio na tela anteriror, no momento em que é gerado os vôos disponíveis.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aviao_ida = filter_input(INPUT_POST, 'partida', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['aviao_ida'] = $id_aviao_ida;
}

// Selecionando os dados do avião com base no seu ID
$encontrar_aviao = "SELECT * FROM aviao INNER JOIN gestao_voo ON aviao.pk_aviao = gestao_voo.fk_aviao WHERE pk_aviao = " . $_SESSION['aviao_ida'];
$executa_consulta = mysqli_query($conn, $encontrar_aviao);
$linha_aviao = mysqli_fetch_assoc($executa_consulta);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="css/assentos.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="img/favicon_logo_dumont_32x32.png" type="image/x-icon">
    <title>DUMONT- Assentos</title>
</head>

<body>

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

    <main class="my-5">
        <div class="row p-0 m-0">
            <h1 class="text-center mb-5 fw-bold">ESCOLHA SEU ASSENTO</h1>
        </div>
        <!-- Condição onde, se o passageiro escolher apenas uma viagem de ida, o form a levará para a tela de informar as informações dos passageiros. Se for ida e volta, o sistema o levará para a tela de assentos do avião de retorno. o input que vai determinar isso está na tela proc_reserva.php, na linha 303-->
        <form action="<?php if ($_SESSION['retorno'] == 'apenas ida') {
            echo 'info_passageiro.php';
        } else {
            echo 'assento_retorno.php';
        } ?>" method="post">
            <input type="hidden" name="id_aviao_retorno" value="<?php echo $id_aviao_volta; ?>">
            <div class="container">

                <hr>
                <p class="text-uppercase">Informe o assento dos passageiros</p>
                <hr>

                <div class="row">
                    <?php
                    // Looping que vai gerar os inputs para o passageiro informar qual assento deseja com base na quantidade de passagens solicitada
                    for ($inserir = 1; $inserir <= $_SESSION['qtd_passagem']; $inserir++) {
                        echo "
                            <div id='passageiros' class='col-sm-2 mb-2'>
                                <label for='passageiro$inserir' class='form-label'>Passageiro $inserir</label>
                                <input id='passageiro$inserir' name='passageiro$inserir' class='form-control' type='number' min='1' max='" . $linha_aviao['num_assento'] . "' value='";

                        if (isset($_SESSION["assento$inserir"])) {
                            echo $_SESSION["assento$inserir"];
                            unset($_SESSION["assento$inserir"]);
                        }

                        echo "' required>";

                        if (isset($_SESSION["msg_assento$inserir"])) {
                            echo $_SESSION["msg_assento$inserir"];
                            unset($_SESSION["msg_assento$inserir"]);
                        }

                        echo "</div>";
                    }
                    ?>

                </div>
                <button class='btn btn-success text-uppercase' type="submit">confirmar</button>
                <a href="reserva.php" id="voltar" class="btn btn-danger text-uppercase">voltar</a>
            </div>
        </form>

        <div class="container mt-4">
            <h4 class="text-uppercase fw-bold">veja qual assento você deseja</h4>
        </div>

        <div id="poltronas" class="container-fluid d-block mx-auto">

            <?php
            $cliente = 0;
            // Selecionando passagens que estejam no mesmo avião de escolha
            $verificar_assento_ocupado = "SELECT * FROM passagem WHERE aviao_ida = " . $_SESSION['aviao_ida'] . " ORDER BY CONVERT(poltrona_ida, UNSIGNED) ASC";
            // echo $verificar_assento_ocupado;
            $executa_verificacao = mysqli_query($conn, $verificar_assento_ocupado);

            // Looping que vai selecionar o assento de cada cliente
            while ($linha_passagem = mysqli_fetch_assoc($executa_verificacao)) {
                $cliente++;
                $_SESSION["poltrona_cliente$cliente"] = $linha_passagem['poltrona_ida'];
            }

            $cliente = 1;
            // Looping que vai gerar o número de poltronas com base na quantidade de assentos que está especificado no banco de dados
            for ($mostrar = 1; $mostrar <= $linha_aviao['num_assento']; $mostrar++) {
                echo "<button id='assento' class='";

                // Se o assento já estiver sido escolhido, ele terá uma cor cinza
                if (($mostrar == $_SESSION["poltrona_cliente$cliente"])) {
                    echo "btn btn-secondary p-0 rounded-4 text-center";
                    $cliente++;
                } else {
                    echo "btn btn-warning p-0 rounded-4 text-center";
                }

                echo "' value='$mostrar'>$mostrar</button>";

                if ((($mostrar % 3) == 0) && (($mostrar % 6) != 0)) {
                    echo "<canva class='text-uppercase' style='padding-right: 3rem'></canva>";
                }

                if (($mostrar % 6) == 0) {
                    echo '<br>';
                }
            }
            ?>

        </div>
    </main>

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
        <a class="text-white text-decoration-none" href="https://www.latamairlines.com/" target="_blank">Latam</a>
        <br>
        <a class="text-white text-decoration-none" href="https://www.voegol.com.br/" target="_blank">Gol</a>
        <br>
        <a class="text-white text-decoration-none" href="https://www.voeazul.com.br/" target="_blank">Azul</a>
      </div>

      <div class="col-sm">
        <p><strong>Redes Socias</strong></p>

        <a class="text-white text-decoration-none" href="https://www.instagram.com/"><img src="img/instagram.svg"
            alt="Ícone do Instagram">Instagram</a>
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