<?php

// base url for the website, used for redirects
const PROTOCOL = 'http';

const DEV_URL = 'localhost:9000';
const PROD_URL = 'lgbt-hs-ansbach.de';
const URL = DEV_URL; // TODO: change to production domain
const BASE_URL = PROTOCOL . '://' . URL;


// server name for the website, used for cookies
const DEV_SERVERNAME = 'localhost';
const SERVERNAME = DEV_SERVERNAME; // TODO: change to production domain

const ENV = 'dev'; // dev or prod