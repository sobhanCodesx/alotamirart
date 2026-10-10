<?php
/** Fast contract and safety checks for the PlayNexus-style shared-hosting MCP. */
declare(strict_types=1);

$root=dirname(__DIR__);
require_once $root.'/api/content-tools.php';

function check(bool $condition,string $message): void {
    if (!$condition) {fwrite(STDERR,$message.PHP_EOL);exit(1);}
}

foreach ([
    'http://localhost/image.jpg',
    'https://127.0.0.1/wikipedia/commons/a.jpg',
    'https://upload.wikimedia.org.evil.test/wikipedia/commons/5/5a/file.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/5/5a/file.jpg?redirect=1',
    'https://example.com/wikipedia/commons/5/5a/file.jpg',
] as $url) {
    $rejected=false;
    try {aloContentRemoteImage($url);} catch (InvalidArgumentException $e) {$rejected=true;}
    check($rejected,'Untrusted remote image URL was accepted: '.$url);
}
$schema=aloContentTools();
$names=array_column($schema,'name');
check(in_array('find_content',$names,true),'Exact slug lookup MCP tool is unavailable.');
check(in_array('create_content',$names,true),'Create content MCP tool is unavailable.');
check(in_array('update_content',$names,true),'Update content MCP tool is unavailable.');
check(in_array('set_content_published',$names,true),'Publish content MCP tool is unavailable.');
check(!in_array('city',$names,true) && !in_array('province',$names,true),'Location CRUD must not be exposed.');
$create=array_values(array_filter($schema,static fn($x)=>$x['name']==='create_content'))[0];
check(isset($create['inputSchema']['properties']['image_url']),'Remote featured image input not exposed.');
$update=array_values(array_filter($schema,static fn($x)=>$x['name']==='update_content'))[0];
check(isset($update['inputSchema']['properties']['image_url']),'Remote featured image update input not exposed.');

$htaccess=file_get_contents($root.'/.htaccess');
check(strpos($htaccess,'RewriteRule ^api/mcp/?$ api/mcp.php')!==false,'Clean MCP route missing.');
check(is_file($root.'/api/mcp/index.php'),'Clean MCP directory fallback missing.');

$main=file_get_contents($root.'/api/mcp.php');
check(strpos($main,"'find_content'")!==false,'MCP dispatcher cannot route exact content finder.');

$publisher=file_get_contents($root.'/scripts/mcp_content_job.py');
check(strpos($publisher,'https://alotamiratchi.ir/api/mcp/"')!==false,'Publisher uses an old direct-PHP URL.');
check(strpos($publisher,'def media_args(request):')!==false && strpos($publisher,'**media')!==false,'Publisher must accept a validated single featured-image source.');
check(strpos($publisher,'"find_content"')!==false,'Publisher cannot look up existing slug.');
$workflow=file_get_contents($root.'/.github/workflows/mcp-content-publish.yml');
check(strpos($workflow,"'content-requests/*.json'")!==false,'Content jobs do not trigger their own workflow.');

echo "PlayNexus-style MCP architecture contract passed\n";
