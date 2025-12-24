<?php
// config/app.php

// Define the base URL of the application.
// This allows for easy management of links and asset paths.
// It's determined by checking environment variables first, then falling back to a default.
// IMPORTANT: Change '/pkl/' to '/' if you are running this from the web server's root directory.
define('BASE_URL', getenv('BASE_URL') ?: '/pkl/');
