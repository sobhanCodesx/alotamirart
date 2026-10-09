<?php
// Static contract test; no DB connection or network request.
$source = file_get_contents(dirname(__DIR__) . '/api/mcp.php');
foreach (['initialize','tools/list','tools/call','create_article_draft','update_article_draft','publish_article','list_article_categories','list_articles','get_article',"getenv('MCP_API_TOKEN')",'hash_equals(', 'MCP_ALLOW_PUBLISH', 'status = 0'] as $token) {
    if (strpos($source, $token) === false) {fwrite(STDERR, "Missing article MCP contract: $token\n"); exit(1);}
}
echo "Article MCP static contract passed\n";
