<?php
// config/app.php

// Define the base URL of the application.
// This allows for easy management of links and asset paths.
// It's determined by checking environment variables first, then falling back to a default.
define('BASE_URL', getenv('BASE_URL') ?: '/');
