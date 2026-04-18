<footer class="main-footer">
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> João Costa. Todos os direitos reservados.</p>
        <ul class="footer-links">
            <li><a href="privacy.php">Política de Privacidade</a></li>
            <li><a href="terms.php">Termos e Condições</a></li>
            <li><a href="cookies.php">Política de Cookies</a></li>
        </ul>
    </div>
</footer>

<div id="cookie-consent" class="cookie-banner">
    <div class="cookie-content">
        <p>Este site utiliza cookies para melhorar a sua experiência e mostrar publicidade personalizada através do Google AdSense. Ao continuar, aceita a nossa <a href="privacy.php">Política de Privacidade</a>.</p>
        <button id="accept-cookies" class="btn-submit">Aceitar</button>
    </div>
</div>

<script>
// Lógica simples para o banner de cookies
document.addEventListener("DOMContentLoaded", function() {
    if (!localStorage.getItem("cookiesAceites")) {
        document.getElementById("cookie-consent").style.display = "block";
    }

    document.getElementById("accept-cookies").addEventListener("click", function() {
        localStorage.setItem("cookiesAceites", "true");
        document.getElementById("cookie-consent").style.display = "none";
    });
});
</script>