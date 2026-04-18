<?php 
header('Content-Type: text/html; charset=UTF-8');
session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta charset="UTF-8">
   
    <title>Privacidade | João Costa</title>
    <link rel="icon" href="icon.jpg" type="image/jpeg">
    <link rel="stylesheet" href="style.css?v=2.3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include "nav_bar.php"; ?>

    <div class="page-wrapper">
        <div class="content-block" style="text-align: left; max-width: 800px; margin: 0 auto;">
            
            <h1 style="color: #d4b26a; text-align: center; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 2px;">
                Política de Privacidade
            </h1>

            <section style="margin-bottom: 25px;">
                <p>
                    A sua privacidade é fundamental para nós. É política do <strong>João Costa Blog</strong> respeitar a sua privacidade em relação a qualquer informação que possamos recolher durante a sua navegação.
                </p>
            </section>

            <section style="margin-bottom: 25px;">
                <h2 style="color: #d4b26a; font-size: 1.5rem; margin-bottom: 10px;">
                    <i class="fa-solid fa-cookie-bite"></i> Cookies e Publicidade
                </h2>
                <p>
                    O Google, como fornecedor de terceiros, utiliza cookies para exibir anúncios no nosso site. Com o uso do cookie <strong>DART</strong>, o Google pode exibir anúncios personalizados com base nas visitas que o utilizador realizou a este e a outros sites na Internet.
                </p>
                <p style="margin-top: 10px;">
                    Poderá desativar o cookie DART visitando a política de privacidade da rede de conteúdo e anúncios do Google.
                </p>
            </section>

            <section style="margin-bottom: 25px;">
                <h2 style="color: #d4b26a; font-size: 1.5rem; margin-bottom: 10px;">
                    <i class="fa-solid fa-shield-halved"></i> Segurança de Dados
                </h2>
                <p>
                    Apenas retemos informações coletadas pelo tempo necessário para fornecer o serviço solicitado. Quando armazenamos dados, protegemo-los dentro de meios comercialmente aceitáveis ​​para evitar perdas e roubos, bem como acesso, divulgação, cópia, uso ou modificação não autorizados.
                </p>
            </section>

            <section style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(212, 178, 106, 0.2); text-align: center;">
                <h2 style="color: #d4b26a; font-size: 1.5rem; margin-bottom: 15px;">Contacto</h2>
                <p>Se tiver qualquer dúvida sobre como lidamos com dados do utilizador e informações pessoais, entre em contacto:</p>
                <a href="mailto:jpscosta.music@gmail.com" class="btn-auth" style="display: inline-block; margin-top: 15px;">
                    <i class="fa-solid fa-envelope"></i> jpscosta.music@gmail.com
                </a>
            </section>

        </div>
    </div>

<?php include 'footer.php'; ?></body>
</html>