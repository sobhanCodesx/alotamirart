<?php
/** Clean URL fallback for cPanel Apache when root rewrite is absent. */
declare(strict_types=1);
header('X-Alo-MCP-Route: clean-directory');
require dirname(__DIR__) . '/mcp.php';
