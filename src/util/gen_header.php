<?php /** @noinspection HtmlUnknownTarget */
/** @noinspection HtmlRequiredTitleElement */
/** @noinspection CssUnknownTarget */
function template_header($dbConnection, string $lang, $lang_title = "home"): void
{
    // TODO: support for switching between dark and light mode

    $PDO = $dbConnection->getConnection();

    require(__DIR__ . '/auth_login_check.php'); // check if user is logged in
    /* @var $loggedIn */

    $index = '';
    $calendar = '';

    switch ($lang_title) {
        case 'home':
            $index = 'class="active"';
            break;
        case 'cal':
            $calendar = 'class="active"';
            break;
        default:
            break;
    }

    $title = lang_strings[$lang_title];

    echo <<<EOT
    <!DOCTYPE html>
    <html lang="$lang">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="dark">
    EOT;
    if ($lang_title !== 'home') {
        echo "<title>$title — ".lang_strings['title']."</title>";
        if (isset(lang_strings['meta_desc_'.$lang_title])) {
            echo '<meta name="description" content="'.lang_strings['meta_desc_'.$lang_title].'">';
        } else {
            echo '<meta name="description" content="'.lang_strings['meta_desc_home'].'">';
        }
    } else {
        echo "<title>".lang_strings['title']." | ".lang_strings['uni']."</title>";
        echo '<meta name="description" content="'.lang_strings['meta_desc_home'].'">';
    }

    echo <<<EOT
        <link rel="stylesheet" href="./css/min/style_common.min.css">
        <link rel="icon" type="image/png" sizes="32x32" href="../img/lgbt_bunny.png">
    </head>
    <body style="--lgbt-text: #eeeeee; --lgbt-bg: #333333; background-color: var(--lgbt-bg); color: var(--lgbt-text);">
    <div class="page_wrap">
        <header>
            <div class="cont_logo_nav">
                <div class="logo-header">
                    <a href="./">
    EOT;
                        echo '<img class="logo" src="./img/lgbt_bunny_white_opt.svg" alt="'.lang_strings['alt_signet_link'].'">';
    echo <<<EOT
                    </a>
                </div>
                
                <nav class="navbar nav-top">
                <ul class="nav_list">
    EOT;

    echo '<li><a href="./" '.$index.'>'.lang_strings['home'].'</a></li>';

    if ($PDO !== null && $dbConnection->checkDBSchema() === true) {
        echo '<li><a href="./calendar.php" '.$calendar.'>'.lang_strings['cal'].'</a></li>';
    }

    $l_de = '<a href="./util/lang_change.php?lang=de" aria-label="Sprache wechseln: Deutsch">🇩🇪 DE</a>';
    $l_en = '<a href="./util/lang_change.php?lang=en" aria-label="Change language: English">🇬🇧 EN</a>';

    if ($lang === 'de') {
        $l_de = "<div aria-label='Sprache: Deutsch (aktiv)' class='active'>🇩🇪 DE</div>";
    }
    if ($lang === 'en') {
        $l_en = "<div aria-label='Language: English (active)' class='active'>🇬🇧 EN</div>";
    }

    echo <<<EOT
                </ul>
                </nav>
            </div>
            
            <div class="cont_lang">
                <ul class="lang_selection">
                    <li lang="de" class="lang_option">$l_de</li>
                    <li lang="en" class="lang_option">$l_en</li>
                </ul>
            </div>
    </header>
    EOT;

    if ($lang_title === 'home') {
        echo "<main style='padding: 0 0 6rem 0;'>";
    }
    else {
        echo "<main style='padding: 5rem 0 6rem 0;'>";
    }
}