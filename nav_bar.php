<?php
header('Content-Type: text/html; charset=UTF-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_logged = isset($_SESSION['user_id']);
?>

<nav class="nav_bar">
    <!-- Menu esquerdo -->
    <ul>
        <li><a href="index.php">Início</a></li>
        <li><a href="suggestions.php">Sugestões</a></li>
        <li><a href="about.php">Sobre mim</a></li>
        <li><a href="podcasts.php">Conversas</a></li>
        <li><a href="musics.php">As minhas músicas</a></li>
    </ul>

    <!-- Menu direito -->
    <div class="nav_bar-right">
        <!-- Dropdown de idiomas -->
        <select id="customLang">
            <option value="">🌍 Idioma</option>
            <option value="en">English</option>
            <option value="es">Español</option>
            <option value="fr">Français</option>
            <option value="de">Deutsch</option>
            <option value="it">Italiano</option>
            <option value="pt">Português</option>
            <option value="nl">Nederlands</option>
            <option value="sv">Svenska</option>
            <option value="no">Norsk</option>
            <option value="da">Dansk</option>
            <option value="fi">Suomi</option>
            <option value="pl">Polski</option>
            <option value="cs">Čeština</option>
            <option value="hu">Magyar</option>
            <option value="el">Ελληνικά</option>
            <option value="ro">Română</option>
            <option value="bg">Български</option>
            <option value="hr">Hrvatski</option>
            <option value="sk">Slovenčina</option>
            <option value="sl">Slovenščina</option>
            <option value="et">Eesti</option>
            <option value="lv">Latviešu</option>
            <option value="lt">Lietuvių</option>
            <option value="zh-CN">中文 (简体)</option>
            <option value="zh-TW">中文 (繁體)</option>
            <option value="ja">日本語</option>
            <option value="ko">한국어</option>
        </select>

        <?php if ($user_logged): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Google Translate invisível -->
<div id="google_translate_element" style="display:none;"></div>

<style>
/* ============================
   Navbar Vintage Atualizada
============================ */
.nav_bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 25px;
    background: rgba(0, 0, 0, 0); /* transparente */
    color: #f2e8d5;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(212,178,106,0.4);
    height: 55px;
    font-family: 'EB Garamond', serif; 
}

.nav_bar ul {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

.nav_bar ul li {
    margin-right: 20px;
}

.nav_bar a {
    color: #f2e8d5;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95em;
    transition: color 0.3s;
}

.nav_bar a:hover {
    color: #d4b26a; /* dourado vintage */
}

.nav_bar-right {
    display: flex;
    align-items: center;
}

#customLang {
    background: rgba(120, 100, 80, 0.9);
    color: #f2e8d5;
    border: 1px solid rgba(212,178,106,0.4);
    border-radius: 6px;
    padding: 4px 10px;
    margin-right: 10px;
    cursor: pointer;
    font-family: 'EB Garamond', serif;
    font-size: 0.9em;
}

#customLang:hover {
    background: rgba(212,178,106,0.2);
}

/* Oculta totalmente a barra do Google Translate */
.goog-te-banner-frame.skiptranslate,
body > .skiptranslate,
iframe.goog-te-menu-frame {
    display: none !important;
}

body { top: 0 !important; }
</style>

<script type="text/javascript">
// Inicializa Google Translate
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'pt',
        autoDisplay: false
    }, 'google_translate_element');
}

// Carrega script apenas uma vez
if (!document.getElementById('gtScript')) {
    var gtScript = document.createElement('script');
    gtScript.id = 'gtScript';
    gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(gtScript);
}

// Função para trocar idioma
function changeLanguage(lang) {
    if (!lang) return;
    let attempts = 0;
    const interval = setInterval(() => {
        const combo = document.querySelector('.goog-te-combo');
        if (combo) {
            combo.value = lang;
            combo.dispatchEvent(new Event('change'));
            clearInterval(interval);
        } else if (++attempts > 20) {
            clearInterval(interval);
        }
    }, 500);
}

// Listener para o dropdown
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('customLang').addEventListener('change', function() {
        changeLanguage(this.value);
    });
});
</script>