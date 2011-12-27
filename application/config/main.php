<?php

/**
 * Global Event Logging
 * If this is false, the Event class will ignore all events.
 */
\System\Event::$log_events = false;

/**
 * Database Configuration
 * \System\Database::connect(identifier, driver, server, username, password, database);
 */
\System\Database::$log_events = false;
\System\Database::connect('main', 'mysql', 'localhost', 'root', '', 'database');
