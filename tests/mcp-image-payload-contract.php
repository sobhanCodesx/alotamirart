<?php
// Guard the MCP HTTP body limit: featured images are sent as base64 JSON.
// A 2.5 MB image needs roughly 3.4 MB in the request body.
$source = file_get_contents(dirname(__DIR__) . '/api/mcp.php');
if (!preg_match('/strlen\\(\\$raw\\)\\s*>\\s*(\\d+)/', $source, $match)) {
    fwrite(STDERR, "Unable to find MCP request size guard\n");
    exit(1);
}
if ((int) $match[1] < 3500000) {
    fwrite(STDERR, "MCP request size guard rejects valid featured images\n");
    exit(1);
}
echo "MCP image payload contract passed\n";
