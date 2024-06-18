<?php
require_once(__DIR__ . '/util/gen_header.php');
require_once(__DIR__ . '/util/gen_footer.php');
require_once(__DIR__ . '/util/utils.php'); // include utility functions
require_once(__DIR__ . '/util/conf.php'); // include configuration file
require_once(__DIR__ . '/util/gen_calendar.php');

// ------------------- DATABASE CONNECTION -------------------

require_once(__DIR__ . '/util/conn_db.php'); // include database connection file

$dbConnection = DBConnection::getInstance();
$PDO = $dbConnection->useDB();

// ----------------- DATABASE CONNECTION END -------------------

require_once(__DIR__ . '/util/auth_session_start.php'); // start session
require_once(__DIR__ . '/util/auth_login_check.php'); // check if user is logged in
/* @var $loggedIn */

require_once(__DIR__ . '/util/lang_get.php'); // get language
/* @var string $lang */

template_header($dbConnection, $lang, 'privacy');
?>

<section class="page_content">
    <div class="section_header">
        <h1 class="section_heading"><?php echo lang_strings['privacy'] ?></h1>
        <div class="section_header_underline"></div>
    </div>

    <div class="about_txt_container privacy_text">

        <?php if ($lang === 'de'): ?>
            <h2>Grundlegendes</h2>
            <p>Diese Datenschutzerklärung soll die Nutzer dieser Website über die Art, den Umfang und den Zweck der
                Erhebung
                und Verwendung personenbezogener Daten durch den Websitebetreiber (<a
                        href="mailto:contact@lgbt-hs-ansbach.de">contact@lgbt-hs-ansbach.de</a>) und
                Serverhost (Hetzner) informieren. Der Websitebetreiber nimmt Ihren Datenschutz sehr ernst und behandelt
                Ihre
                personenbezogenen Daten vertraulich und entsprechend den gesetzlichen Vorschriften. Da durch neue
                Technologien und die ständige Weiterentwicklung dieser Webseite Änderungen an dieser
                Datenschutzerklärung
                vorgenommen werden können, empfehlen wir Ihnen sich die Datenschutzerklärung in regelmäßigen Abständen
                wieder durchzulesen. Definitionen der verwendeten Begriffe (z.B. “personenbezogene Daten” oder
                “Verarbeitung”) finden Sie in Art. 4 DSGVO.</p>

            <h2>Zugriffsdaten</h2>
            <p>Hetzner, der Serverhost, erhebt Daten über Zugriffe auf die Website und speichert diese als
                „Server-Logfiles“
                auf dem Server der Website ab. <br>Folgende Daten werden so protokolliert:
            </p>
            <ul>
                <li>Besuchte Website</li>
                <li>Uhrzeit zum Zeitpunkt des Zugriffes</li>
                <li>Vorherige Website (falls es eine gibt)</li>
                <li>Verwendeter Browser & Browserversion</li>
                <li>Verwendetes Betriebssystem</li>
                <li>Verwendete IP-Adresse (anonymisiert, letzten 3 Zahlen zufallsgeneriert)</li>
                <li>Zeit, die auf der Webseite verbracht wird</li>
            </ul>
            <p>Die Server-Logfiles werden für maximal 14 Tage gespeichert und anschließend gelöscht (durch uns
                festgelegt).
                Der Mailserverlog hat 7 Tage Vorhaltezeit, Backups jeglicher Art werden 14 Tage aufbewahrt. Durch uns
                gelöschte Mails &
                Spam-Mails werden nach 14 Tagen endgültig gelöscht. Die Speicherung der Daten erfolgt aus
                Sicherheitsgründen, um z. B. Missbrauchsfälle aufklären zu können. Müssen Daten aus Beweisgründen
                aufgehoben
                werden, sind sie so lange von der Löschung ausgenommen bis der Vorfall endgültig geklärt ist.
            </p>

            <h2>Cookies</h2>
            <p>Diese Website verwendet Session Cookies zur Speicherung der Spracheinstellungen, die entweder von unserem
                Server oder dem Server unseres Serverhosts an den Browser des Nutzers übertragen werden. Bei Cookies
                handelt
                es sich um kleine Dateien, welche auf Ihrem Endgerät gespeichert werden. Ihr Browser greift auf diese
                Dateien zu. Durch den Einsatz von Cookies erhöht sich die Benutzerfreundlichkeit und Sicherheit dieser
                Website.<br><br>

                Der Websitebetreiber erhebt, nutzt und gibt Ihre personenbezogenen Daten nur dann weiter, wenn dies im
                gesetzlichen Rahmen erlaubt ist oder Sie in die Datenerhebung einwilligen. Als personenbezogene Daten
                gelten
                sämtliche Informationen, welche dazu dienen, Ihre Person zu bestimmen und welche zu Ihnen zurückverfolgt
                werden können – also beispielsweise Ihr Name, Ihre E-Mail-Adresse und Telefonnummer.<br><br>

                Auf dieser Webseite können sie keine persönlichen Angaben machen, die von uns gespeichert werden. Zur
                Besseren Navigation der Webseite speichern wir jedoch die Sprache Ihres Webbrowsers oder Ihre
                Sprachauswahl
                in einem Session Cookie. Für Sie hat dies den Nutzen, dass Sie alle Seiten unserer Webseite durchgehend
                in
                der gleichen Sprache benutzen können, ohne diese auf jeder Seite ändern zu müssen.<br>
                Folgende Daten werden für Sie gespeichert:
            </p>
            <ul>
                <li>Sitzungsbezeichner (Session-ID)</li>
                <li>Sprachauswahl (selbst gewählt oder durch Browsersprache)</li>
            </ul>
            <p>Die Session-ID dient lediglich dazu, mehrere Anfragen eines Nutzers auf einer Seite dessen Sitzung
                zuzuordnen. Es werden keine Hinweise, die der Identifikation des Users dienen, gespeichert. Die Session
                Cookies werden nach dem Schließen des Browsers gelöscht.</p>
            <h2>Umgang mit Kontaktdaten</h2>
            <p>Nehmen Sie mit uns als Websitebetreiber durch die angebotenen Kontaktmöglichkeiten Verbindung auf, gelten
                die
                Datenschutzerklärungen der jeweiligen Plattformen (WhatsApp, Instagram, GitHub und Discord).
                Nachrichten, deren Absender sowie Inhalt über unsere E-Mail-Postfächer sind von allen unseren
                Teammitgliedern aufrufbar und können auf deren Endgeräte synchronisiert werden. Wir speichern E-Mails
                nur so
                lange wie nötig.
            </p>

            <h2>Rechte des Nutzers</h2>
            <p>Sie haben als Nutzer das Recht, auf Antrag eine kostenlose Auskunft darüber zu erhalten, welche
                personenbezogenen Daten über Sie gespeichert wurden. Sie haben außerdem das Recht auf Löschung Ihrer
                personenbezogenen Daten. Falls zutreffend, können Sie auch Ihr Recht auf Datenportabilität geltend
                machen.
                Sollten Sie annehmen, dass Ihre Daten unrechtmäßig verarbeitet wurden, können Sie eine Beschwerde bei
                der
                zuständigen Aufsichtsbehörde einreichen.</p>
            <h3>Löschung von Daten</h3>
            <p>Sofern Ihr Wunsch nicht mit einer gesetzlichen Pflicht zur Aufbewahrung von Daten (z. B.
                Vorratsdatenspeicherung) kollidiert, haben Sie ein Anrecht auf Löschung Ihrer Daten. Von Hetzner
                gespeicherte Daten werden, sollten sie für ihre Zweckbestimmung nicht mehr vonnöten sein und es keine
                gesetzlichen Aufbewahrungsfristen geben nach 14 Tagen, gelöscht. Falls eine Löschung nicht durchgeführt
                werden kann, da die Daten für zulässige gesetzliche Zwecke erforderlich sind, werden die Daten gesperrt
                und
                nicht für andere Zwecke verarbeitet.
                Wenn Sie eine Berichtigung, Sperrung, Löschung oder Auskunft über die zu Ihrer Person gespeicherten
                personenbezogenen Daten wünschen oder Fragen bzgl. der Erhebung, Verarbeitung oder Verwendung Ihrer
                personenbezogenen Daten haben oder erteilte Einwilligungen widerrufen möchten, wenden Sie sich bitte an
                folgende E-Mail-Adresse: <a href="mailto:contact@lgbt-hs-ansbach.de">contact@lgbt-hs-ansbach.de</a>
            </p>

        <?php else: ?>

            <h2>Basic Information</h2>
            <p>This privacy policy is intended to inform the users of this website about the nature, scope, and purpose
                of the
                collection and use of personal data by the website operator (<a
                        href="mailto:contact@lgbt-hs-ansbach.de">contact@lgbt-hs-ansbach.de</a>) and the
                server host (Hetzner). The website operator takes your data protection very seriously and treats your
                personal data confidentially and in accordance with legal regulations. Since new technologies and the
                constant
                development of this website may result in changes to this privacy policy, we recommend that you read the
                privacy policy at regular intervals. Definitions of the terms used (e.g. “personal data” or
                “processing”) can
                be found in art. 4 GDPR.</p>
            <h2>Access Data</h2>
            <p>Hetzner, the server host, collects data about access to the website and stores it as “server log files”
                on the
                server of the website. <br>The following data is logged:
            </p>
            <ul>
                <li>Visited website</li>
                <li>Time at the time of access</li>
                <li>Previous website (if there is one)</li>
                <li>Browser used & browser version</li>
                <li>Operating system used</li>
                <li>IP address used (anonymized, last 3 digits randomly generated)</li>
                <li>Time spent on the website</li>
            </ul>
            <p>The server log files are stored for a maximum of 14 days and then deleted (determined by us). The mail
                server log
                has a retention period of 7 days, backups of any kind are kept for 14 days. Mails deleted by us & spam
                mails are finally
                deleted after 14 days. The data is stored for security reasons, for example to clarify cases of misuse.
                If data
                must be retained for evidential purposes, it is exempt from deletion until the incident is finally
                clarified.
            </p>

            <h2>Cookies</h2>
            <p>This website uses session cookies to store language settings, which are transmitted to the user's browser
                by
                either our server or the server of our server host. Cookies are small files that are stored on your end
                device.
                Your browser accesses these files. By using cookies, the user-friendliness and security of this website
                are
                increased.<br><br>

                The website operator collects, uses, and passes on your personal data only if this is permitted by law
                or if you
                consent to the collection of data. Personal data is all information that serves to determine your person
                and
                that can be traced back to you – for example, your name, your e-mail address, and telephone
                number.<br><br>

                On this website, you cannot provide any personal information that is stored by us. However, for you to
                be able to better navigate
                the website, we store the language of your web browser or your language selection in a session cookie.
                This has
                the benefit for you that you can use all pages of our website continuously in the same language without
                having
                to change it on every page.<br>
                The following data is stored for you:
            </p>
            <ul>
                <li>Session identifier (session ID)</li>
                <li>Language selection (self-selected or by browser language)</li>
            </ul>
            <p>The session ID is only used to assign multiple requests from a user on one page to his session. No hints
                that serve to identify the user are stored. The session cookies are deleted after the browser is
                closed.</p>
            <h2>Handling of contact data</h2>
            <p>If you contact us as the website operator through the contact options offered, the privacy policies of
                the respective platforms (WhatsApp, Instagram, GitHub, and Discord) apply.
                Messages, their senders, and content sent to our email mailboxes can be accessed by all our team members
                and can be synchronized to their devices. We only store emails as long as necessary.
            </p>

            <h2>User Rights</h2>
            <p>As a user, you have the right to request free information about which personal data about you has been
                stored. You also have the right to have your personal data deleted. If applicable, you can also assert
                your right to data portability. If you believe that your data has been processed unlawfully, you can
                lodge a complaint with the corresponding supervisory authority.</p>
            <h3>Deletion of data</h3>
            <p>Unless your request conflicts with a legal obligation to store data (e.g. data retention), you have a
                right to have your data deleted. Data stored by Hetzner will be deleted after 14 days if it is no longer
                needed for its intended purpose and there are no legal retention periods. If deletion is not possible
                because the data is required for legitimate legal purposes, the data will be blocked and not processed
                for other purposes.
                If you wish to correct, block, delete or receive information about the personal data stored about you,
                or if you have any questions regarding the collection, processing, or use of your personal data, or if
                you wish to revoke any consents given, please contact the following email address: <a
                        href="mailto:contact@lgbt-hs-ansbach.de">contact@lgbt-hs-ansbach.de</a>
            </p>

        <?php endif; ?>

    </div>

</section>

<?php
template_footer($dbConnection, [], $loggedIn);
?>

