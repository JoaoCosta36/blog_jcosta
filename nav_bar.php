<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_logged = isset($_SESSION['user_id']);
?>

<nav class="nav_bar">
    <div class="nav-container">
        <ul class="nav-links">
            <li><a href="index.php">Início</a></li>
            <li><a href="musics.php">Músicas</a></li>
            <li><a href="podcasts.php">Conversas</a></li>
            <li><a href="suggestions.php">Sugestões</a></li>
            <li><a href="about.php">Sobre mim</a></li>
        </ul>

        <div class="nav_bar-right">
            <div class="lang-wrapper">
                <select id="customLang" class="lang-select">
                    <option value="">🌍 Idioma</option>
                    <option value="pt">Português</option>
                    <option value="en">English</option>
                    <option value="es">Español</option>
                    <option value="fr">Français</option>
                    <option value="de">Deutsch</option>
                    <option value="it">Italiano</option>
                    <option value="ja">日本語</option>
                    <option value="zh-CN">中文</option>
                </select>
            </div>

            <div class="auth-link">
                <?php if ($user_logged): ?>
                    <a href="logout.php" class="btn-auth">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn-auth">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div id="google_translate_element" style="display:none;"></div>

<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'pt',
        includedLanguages: 'pt,en,es,fr,de,it,ja,zh-CN,nl,sv,no,da,fi,pl',
        autoDisplay: false
    }, 'google_translate_element');
}

// Carregar o script do Google apenas uma vez
if (!document.getElementById('gtScript')) {
    var gtScript = document.createElement('script');
    gtScript.id = 'gtScript';
    gtScript.type = 'text/javascript';
    gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(gtScript);
}

// Função para disparar a tradução através do nosso select customizado
function triggerTranslation(lang) {
    var combo = document.querySelector('.goog-te-combo');
    if (combo) {
        combo.value = lang;
        combo.dispatchEvent(new Event('change'));
    } else {
        // Se o combo ainda não carregou, tenta novamente em 500ms
        setTimeout(function() { triggerTranslation(lang); }, 500);
    }
}

document.getElementById('customLang').addEventListener('change', function() {
    triggerTranslation(this.value);
});
</script>