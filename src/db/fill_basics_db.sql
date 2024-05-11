SET
SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET
time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

INSERT INTO `event_types` (`id`, `name_de`, `name_en`, `desc_de`, `desc_en`)
VALUES
    (1, 'Spieleabend', 'Game Night', 'Ob Brettspiele oder Kartenspiele, bringt gerne eure eigenen Spiele mit, die wir dann zusammen ausprobieren können!', 'Whether board games or card games, feel free to bring your own games that we can try out together!'),
    (2, 'Kreativabend', 'Art & Craft Night', 'Lasst eurer Kreativität freien Lauf beim Kreativabend! Ob Malen oder Basteln - alles ist möglich. Bringt eure eigenen Materialien mit oder tauscht sie mit anderen aus.', 'Let your creativity run wild at the Art & Craft Night! Whether painting or crafting - everything is possible. Bring your own materials or exchange them with others.'),
    (3, 'Filmabend', 'Movie Night', 'Entspanne dich bei einem Film nach eurer Wahl in gemütlicher Runde. Bringt gerne Snacks und Getränke mit.', 'Relax with a film of your choice in a cozy atmosphere. Feel free to bring snacks and drinks.'),
    (4, 'Bartour', 'Bar Tour', 'Entdecke die lokale Barszene in Ansbach mit uns.', 'Discover the local bar scene in Ansbach with us.'),
    (5, 'Gaming Abend', 'Gaming Night', 'Zockt zusammen die neusten Games oder eure Lieblingsklassiker beim Gaming Abend, von Mario Kart bis Jackbox ist alles dabei', 'Play the latest games or your favorite classics together at the Gaming Night, from Mario Kart to Jackbox, everything is included'),
    (6, 'Krimidinner', 'Murder Mystery Night', 'Schlüpft in eure Rolle und löst zusammen einen spannenden Kriminalfall!', 'Slip into your role and solve an exciting criminal case together!'),
    (7, 'Lasertag', 'Laser tag', 'Action und Spaß in der Lasertag-Arena! Jagt euch beim Lasertag durch die Dunkelheit und zeigt eure Treffsicherheit.', 'Action and fun in the laser tag arena! Chase your way through the darkness with laser tag and show off your accuracy.'),
    (8, 'Maiwanderung', 'May Hike', 'Genießt die Natur und die frische Luft bei einer gemeinsamen Maiwanderung während wir die Ansbacher Umgebung erkunden.', 'Enjoy nature and the fresh air on a May hike together while we explore the Ansbach area.'),
    (9, 'Escape Room', 'Escape Room', 'Knackt gemeinsam Rätsel und entkommt aus dem Escape Room. Teamwork und Köpfchen sind gefragt!', 'Solve puzzles together and escape the escape room. Teamwork and brains required!'),
    (10, 'Karaoke', 'Karaoke', 'Ob Solo oder als Duett, bei unserem Karaoke-Abend steht der Spaß im Vordergrund. Zeige dein Gesangstalent oder genieße einfach die Show.', 'Whether solo or as a duet, fun is the main focus of our karaoke evening. Show off your singing talent or just enjoy the show.'),
    (11, 'Theater', 'Theatre', '', ''),
    (12, 'Bowling', 'Bowling', 'Ein geselliger Abend auf der Bowlingbahn.', 'A sociable evening at the bowling alley.'),
    (13, 'Pride Party', 'Pride Party', '', ''),
    (14, 'Lerngruppe', 'Study Group', 'Gemeinsam lernen macht mehr Spaß! Tauscht euch aus, helft euch gegenseitig und motiviert euch.', 'Studying together is more fun! Exchange ideas, help each other and motivate each other.'),
    (15, 'Picknick', 'Picnic', 'Genießt die Sonne und die frische Luft bei einem gemütlichen Picknick im Park.', 'Enjoy the sun and fresh air with a cozy picnic in the park.'),
    (16, 'Workshop', 'Workshop', 'Lerne neue Fähigkeiten und Techniken in einem Workshop.', 'Learn new skills and techniques in a workshop.');

INSERT INTO `event_locations` (`id`, `name`)
VALUES
    (1, 'Hochschule Ansbach'),
    (2, 'Raum der Begegnung, Geb. 51, HS Ansbach'),
    (3, '54.2.1, HS Ansbach'),
    (4, '70.1.5, HS Ansbach'),
    (5, '70.1.4, HS Ansbach'),
    (6, '70.1.4 / 70.1.5, HS Ansbach'),
    (7, '92.1.27, HS Ansbach'),
    (8, '54.0.2, HS Ansbach'),
    (9, '92.0.13, HS Ansbach');

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
