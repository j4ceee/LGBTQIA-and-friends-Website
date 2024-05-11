SET
SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET
time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- create table accounts -> for storing staff user credentials
CREATE TABLE IF NOT EXISTS `accounts`
(
    `id`       int(11)      NOT NULL AUTO_INCREMENT, -- primary key
    `username` varchar(20)  NOT NULL,                -- username
    `password` varchar(255) NOT NULL,                -- password
    `email`    varchar(50) NOT NULL,                -- email

    PRIMARY KEY (`id`),
    UNIQUE (`username`), -- every account should have a unique username (no duplicates)
    UNIQUE (`email`)     -- every account should have a unique email (no duplicates)
    ) ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARSET = utf8;


-- create table event types -> summary in ics file
CREATE TABLE IF NOT EXISTS `event_types`
(
    `id`        int(11) NOT NULL AUTO_INCREMENT, -- primary key
    `name_de`   varchar(50) NOT NULL,            -- name of the event type in german
    `name_en`   varchar(50) NOT NULL,            -- name of the event type in english
    `desc_de`   varchar(255) NULL,               -- description of the event type in german
    `desc_en`   varchar(255) NULL,               -- description of the event type in english

    PRIMARY KEY (`id`),
    UNIQUE (`name_de`), -- every event type should have a unique name in german (no duplicates)
    UNIQUE (`name_en`)  -- every event type should have a unique name in english (no duplicates)
    ) ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARSET = utf8;

-- create table event locations
CREATE TABLE IF NOT EXISTS `event_locations`
(
    `id`        int(11) NOT NULL AUTO_INCREMENT, -- primary key
    `name`      varchar(100) NOT NULL,            -- name of the location

    PRIMARY KEY (`id`),
    UNIQUE (`name`) -- every location should have a unique name (no duplicates)
    ) ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARSET = utf8;

-- create table events
CREATE TABLE IF NOT EXISTS `events`
(
    `id`                int(11) NOT NULL AUTO_INCREMENT, -- primary key
    `event_type_id`     int(11) NOT NULL,                -- foreign key to event_types table
    `event_location_id` int(11) NOT NULL,                -- foreign key to event_locations table
    `date_start`        datetime NOT NULL,               -- start date of the event
    `date_end`          datetime NOT NULL,               -- end date of the event
    -- `date_stamp`     datetime NOT NULL,               -- date of creation of the calendar -> set to current date in php
    `uid`               varchar(65) NOT NULL,            -- unique identifier of the event
    `date_created`      datetime NOT NULL,               -- date of creation of the event
    `date_modified`     datetime NOT NULL,               -- date of last modification of the event
    `desc_de_override`  varchar(255) NULL,               -- description of the event in german (override) -> if NULL use default, if '-' use no description, else use the override
    `desc_en_override`  varchar(255) NULL,               -- description of the event in english (override) -> if NULL use default, if '-' use no description, else use the override
    `sequence`          int(11) NOT NULL DEFAULT 0,      -- sequence number of the event / "version" of the event
    -- `transp`            varchar(10) NOT NULL,         -- transparency of the event (OPAQUE, TRANSPARENT) -> set to OPAQUE in php

    PRIMARY KEY (`id`),
    FOREIGN KEY (`event_type_id`) REFERENCES `event_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`event_location_id`) REFERENCES `event_locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE (`uid`) -- every event should have a unique uid (no duplicates)
    ) ENGINE = InnoDB
    AUTO_INCREMENT = 2
    DEFAULT CHARSET = utf8;


/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
