<?php
// Static contract test; no DB connection or network request.
$source = file_get_contents(dirname(__DIR__) . '/api/mcp.php');
foreach (['initialize','tools/list','tools/call','create_article_draft','update_article_draft','publish_article','list_article_categories','list_articles','get_article',"getenv('MCP_API_TOKEN')",'hash_equals(', 'MCP_ALLOW_PUBLISH', 'status = 0'] as $token) {
    if (strpos($source, $token) === false) {fwrite(STDERR, "Missing article MCP contract: $token\n"); exit(1);}
}
$new = file_get_contents(dirname(__DIR__) . '/api/content-tools.php');
foreach (['article','brand_article','brand','category','create_content','update_content','list_content','get_content','describe_content_fields','set_content_published','image_base64','contact_number','keyword','description','brand_id','post_id','MCP_ALLOW_PUBLISH','SHOW COLUMNS FROM'] as $required) {
    if (strpos($new,$required)===false) {fwrite(STDERR,"Missing content capability: $required\\n");exit(1);}
}
if (strpos($source,"require_once __DIR__ . '/content-tools.php'") === false) {
    fwrite(STDERR,"Content extension is not included\\n");exit(1);
}
echo "Article MCP static contract passed\n";
