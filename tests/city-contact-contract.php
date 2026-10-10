<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/api/city-contact.php';

function cityAssert(bool $condition, string $reason): void {
    if (!$condition) { fwrite(STDERR, "Mahshahr contact contract failed: ".$reason."\n"); exit(1); }
}

cityAssert(aloCityContactValidate(['city'=>'ماهشهر','phone'=>'09169522521','confirm'=>true]) === '09169522521', 'exact scoped input');
foreach ([
  ['city'=>'آمل','phone'=>'09169522521','confirm'=>true],
  ['city'=>'ماهشهر','phone'=>'09169522521','confirm'=>false],
  ['city'=>'ماهشهر','phone'=>'009169522521','confirm'=>true],
] as $invalid) {
    try { aloCityContactValidate($invalid); cityAssert(false, 'invalid input accepted'); }
    catch (InvalidArgumentException $expected) {}
}
cityAssert(aloCityContactTool()['name']==='set_mahshahr_contact_numbers','tool name');
$source=file_get_contents(dirname(__DIR__).'/api/mcp.php');
cityAssert(strpos($source,"require_once __DIR__ . '/city-contact.php'")!==false, 'MCP loads city tool');
cityAssert(strpos($source,"aloCityContactExecute(\$a)")!==false, 'MCP dispatches city tool');

cityAssert(in_array('sqlite',PDO::getAvailableDrivers(),true), 'PDO SQLite must be available in CI');
$pdo=new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY,title TEXT,status INTEGER,content TEXT,contact_number TEXT)');
$pdo->exec('CREATE TABLE post_brand (id INTEGER PRIMARY KEY,title TEXT,status INTEGER,content TEXT,contact_number TEXT)');
$pdo->exec("INSERT INTO posts VALUES(81,'نمایندگی تعمیرات لباسشویی در بندر ماهشهر',1,'keep text','09111111111')");
$pdo->exec("INSERT INTO posts VALUES(82,'نمایندگی تعمیر یخچال ماهشهر',0,'keep draft',NULL)");
$pdo->exec("INSERT INTO posts VALUES(15,'نمایندگی تعمیر یخچال در آمل',1,'unrelated','09999999999')");
$pdo->exec("INSERT INTO post_brand VALUES(377,'نمایندگی کولر گری بندرماهشهر',1,'brand text','09100000000')");
$pdo->exec("INSERT INTO post_brand VALUES(371,'نمایندگی کولر در تهران',1,'unrelated brand','09111111111')");
$result=aloCityContactExecute(['city'=>'ماهشهر','phone'=>'09169522521','confirm'=>true],$pdo);
cityAssert($result['verified']===true,'verified');
cityAssert($result['matched']['article']===2 && $result['matched']['brand_article']===1,'matches both tables and drafts');
cityAssert($result['updated_count']['article']===2 && $result['updated_count']['brand_article']===1,'updated counts');
$check=$pdo->query("SELECT title,status,content,contact_number FROM posts WHERE id=82")->fetch(PDO::FETCH_ASSOC);
cityAssert($check['status']==0 && $check['content']==='keep draft' && $check['contact_number']==='09169522521','draft and other fields preserved');
cityAssert($pdo->query("SELECT contact_number FROM posts WHERE id=15")->fetchColumn()==='09999999999','unrelated article unchanged');
cityAssert($pdo->query("SELECT contact_number FROM post_brand WHERE id=371")->fetchColumn()==='09111111111','unrelated brand unchanged');
$again=aloCityContactExecute(['city'=>'ماهشهر','phone'=>'09169522521','confirm'=>true],$pdo);
cityAssert(array_sum($again['updated_count'])===0,'idempotent');
echo "Mahshahr contact contract passed\n";
