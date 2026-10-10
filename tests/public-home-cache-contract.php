<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/Support/public-home-cache.php';
function cacheAssert(bool $yes, string $message): void {
    if (!$yes) { fwrite(STDERR, 'Homepage cache contract failed: '.$message.PHP_EOL); exit(1); }
}
$base = [
 'REQUEST_METHOD'=>'GET', 'HTTP_HOST'=>'alotamiratchi.ir',
 'REQUEST_URI'=>'/', 'QUERY_STRING'=>'', 'HTTP_COOKIE'=>'',
];
cacheAssert(aloHomeCacheEligible($base),'canonical anonymous GET should be eligible');
foreach ([
 ['HTTP_HOST'=>'www.alotamiratchi.ir'],
 ['HTTP_HOST'=>'malicious.example'],
 ['REQUEST_METHOD'=>'POST'],
 ['REQUEST_METHOD'=>'HEAD'],
 ['REQUEST_URI'=>'/post/490'],
 ['REQUEST_URI'=>'/admin/dashboard'],
 ['REQUEST_URI'=>'/?search=fridge', 'QUERY_STRING'=>'search=fridge'],
 ['HTTP_COOKIE'=>'PHPSESSID=secret'],
 ['HTTP_COOKIE'=>'analytics=1'],
 ['HTTP_AUTHORIZATION'=>'Bearer secret'],
 ['REDIRECT_HTTP_AUTHORIZATION'=>'Bearer secret'],
 ['HTTP_RANGE'=>'bytes=1-10'],
 ['HTTP_CACHE_CONTROL'=>'no-cache'],
] as $override) {
 cacheAssert(!aloHomeCacheEligible(array_replace($base, $override)),'unsafe request cacheable: '.json_encode($override));
}
$root=dirname(__DIR__);
$tmp=sys_get_temp_dir().'/alo-unit-'.bin2hex(random_bytes(5));
cacheAssert(@mkdir($tmp,0700),'private test dir creation');
$dir=aloHomeCacheDirectory($root,$tmp);
cacheAssert(str_starts_with($dir,$tmp.'/alo-home-'),'cache must live in private temp');
cacheAssert($dir===aloHomeCacheDirectory($root,$tmp),'stable cache directory');
$file=aloHomeCacheFile($root,$tmp);
cacheAssert($file===aloHomeCacheFile($root,$tmp),'stable cache key');
cacheAssert(!aloHomeCacheHtmlIsSafe('Warning: PHP database exception'),'errors must not be cached');
cacheAssert(!aloHomeCacheHtmlIsSafe('<html>partial</html>'),'partial output must not be cached');
cacheAssert(@mkdir($dir,0700,true),'cache directory preparation');
$publicPage='<!doctype html><html lang="fa"><body>'.str_repeat('Public repair services ',50).'</body></html>';
cacheAssert(aloHomeCacheHtmlIsSafe($publicPage),'valid public homepage HTML');
aloHomeCacheStore($file,$publicPage);
cacheAssert(@file_get_contents($file)===$publicPage,'atomic cache file writing');
@unlink($file); @rmdir($dir); @rmdir($tmp);
$index=file_get_contents($root.'/index.php');
cacheAssert(strpos($index,"aloHomeCacheStart(")!==false,'cache must be wired before database setup');
$start=strpos($index,'aloHomeCacheStart(');
cacheAssert($start<strpos($index,'session_start();'),'cache must run BEFORE session');
cacheAssert($start<strpos($index,"require __DIR__ . '/config/database.php'"),'cache must run BEFORE database');
echo "Safe anonymous homepage cache contract passed\n";
