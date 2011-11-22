<?php

/**
 * Application Identifier
 * This is used to prevent conflicts between multiple IXT applications on the same server.
 * Currently only used in the Session class.
 */
$IXT->config->application_id = 'my_application';

/**
 * Database Configuration
 * \System\Database::connect(identifier, driver, server, username, password, database);
 */
\System\Database::connect('main', 'mysql', 'localhost', 'root', '', 'database');