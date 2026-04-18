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
            <li><a href="suggestions.php">Sugestões</a></li>
            <li><a href="about.php">Sobre mim</a></li>
            <li><a href="podcasts.php">Conversas</a></li>
            <li><a href="musics.php">As minhas músicas</a></li>
        </ul>

        <div class="nav_bar-right">
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
                <option value="zh-CN">中文</option>
                <option value="ja">日本語</option>
            </select>

            <div class="auth-link">
                <?php if ($user_logged): ?>
                    <a href="logout.php" class="btn-auth">Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn-auth">Login</a>
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
        autoDisplay: false
    }, 'google_translate_element');
}

if (!document.getElementById('gtScript')) {
    var gtScript = document.createElement('script');
    gtScript.id = 'gtScript';
    gtScript.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.head.appendChild(gtScript);
}

function changeLanguage(lang) {
    if (!lang) return;
    const combo = document.querySelector('.goog-te-combo');
    if (combo) {
        combo.value = lang;
        combo.dispatchEvent(new Event('change'));
    }
}

document.addEventListener('change', function(e) {
    if(e.target && e.target.id == 'customLang'){
        changeLanguage(e.target.value);
    }
});
</script>