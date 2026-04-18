<?php
// Garante que a sessão está iniciada para verificar o login
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
                <select id="customLang" class="lang-select" style="background: rgba(0,0,0,0.5); color: #d4b26a; border: 1px solid rgba(212,178,106,0.3); border-radius: 4px; padding: 6px; font-family: inherit; cursor: pointer; outline: none;">
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
                    <a href="logout.php" class="btn-auth" style="min-width: 80px; display: inline-block;">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn-auth" style="min-width: 80px; display: inline-block;">Entrar</a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</nav>

<div id="google_translate_element" style="display:none;"></div>

<script type="text/javascript">
// 1. Inicialização do Google Translate
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'pt',
        includedLanguages: 'pt,en,es,fr,de,it,ja,zh-CN,nl,sv,no,da,fi,pl',
        autoDisplay: false
    }, 'google_translate_element');
}

// 2. Carregamento dinâmico do script do Google
if (!document.getElementById('gtScript')) {
    var gtScript = document.createElement('script');
    gtScript.id = 'gtScript';
    gtScript.type = 'text/javascript';
    gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(gtScript);
}

// 3. Função para disparar a tradução através do select personalizado
function triggerTranslation(lang) {
    var combo = document.querySelector('.goog-te-combo');
    if (combo) {
        combo.value = lang;
        combo.dispatchEvent(new Event('change'));
    } else {
        // Tenta novamente em 500ms caso o widget ainda esteja a carregar
        setTimeout(function() { triggerTranslation(lang); }, 500);
    }
}

// 4. Listener para mudanças no select
document.getElementById('customLang').addEventListener('change', function() {
    if (this.value !== "") {
        triggerTranslation(this.value);
    }
});
</script>