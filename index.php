<?php
/**
 * SIGEDOC - Bridge Index
 * This file acts as a fallback when .htaccess is not working or mod_rewrite is disabled.
 */

// If we are at the root, just include the public index
require_once __DIR__ . '/public/index.php';
