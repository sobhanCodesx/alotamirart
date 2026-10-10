<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/api/refrigerator-links.php';

function fridgeAssert(bool $ok, string $message): void {
    if (!$ok) { fwrite(STDERR, "Refrigerator URL migration test failed: $message\n"); exit(1); }
}
fridgeAssert(aloRefrigeratorLinkTool()['name'] === 'replace_refrigerator_o3am_links', 'tool registered');
$main = file_get_contents(dirname(__DIR__) . '/api/mcp.php');
fridgeAssert(strpos($main, "require_once __DIR__ . '/refrigerator-links.php'") !== false, 'MCP loads tool');
fridgeAssert(strpos($main, "aloRefrigeratorLinkExecute(\$a)") !== false, 'MCP dispatches tool');
$good = ['category_id'=>9,'from_host'=>'o3am.com','to_host'=>'o3am.ir','confirm'=>true];
aloRefrigeratorLinkValidate($good);
foreach ([
    ['category_id'=>1], ['from_host'=>'google.com'], ['to_host'=>'other.ir'], ['confirm'=>false],
] as $change) {
    try { aloRefrigeratorLinkValidate(array_replace($good, $change)); fridgeAssert(false, 'invalid scope was accepted'); }
    catch (InvalidArgumentException $expected) {}
}

function checkSwap(string $before, string $after, int $count): void {
    $n=0; $actual=aloRefrigeratorReplaceHost($before,$n);
    fridgeAssert($actual===$after && $n===$count, 'URL host boundary: '.$before.' => '.$actual);
}
checkSwap('<a href="https://www.o3am.com/refrigerator?x=1#t">link</a>',
          '<a href="https://www.o3am.ir/refrigerator?x=1#t">link</a>',1);
checkSwap('<img src="http://o3am.com/a.png"> and //cdn.o3am.com/b',
          '<img src="http://o3am.ir/a.png"> and //cdn.o3am.ir/b',2);
checkSwap('o3am.com/path www.o3am.com', 'o3am.ir/path www.o3am.ir',2);
checkSwap('https://other.ir/path/o3am.com/file person@o3am.com o3am.com.evil bad-o3am.com',
          'https://other.ir/path/o3am.com/file person@o3am.com o3am.com.evil bad-o3am.com',0);
checkSwap('https://other.ir/post?url=https://o3am.com/a',
          'https://other.ir/post?url=https://o3am.ir/a',1);

fridgeAssert(in_array('sqlite', PDO::getAvailableDrivers(), true), 'SQLite CI driver is required');
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY,post_id INTEGER,title TEXT,content TEXT,description TEXT,tags TEXT,status INTEGER,contact_number TEXT)');
$insert = $db->prepare('INSERT INTO posts VALUES(?,?,?,?,?,?,?,?)');
$insert->execute([82,9,'تعمیر یخچال','<a href="https://o3am.com/a?b=2">متن</a> <a href="https://www.o3am.com/b">بیشتر</a>','https://o3am.com/info','other, o3am.com',1,'09169522521']);
$insert->execute([99,9,'پیش‌نویس','http://cdn.o3am.com/c','untouched',null,0,'09998887766']);
$insert->execute([100,9,'مقاله بدون لینک','<a href="https://google.com">test</a>',null,'tag',1,'111']);
$insert->execute([101,1,'لباسشویی','https://o3am.com/untouched','https://o3am.com/d','o3am.com',1,'222']);
$insert->execute([102,9,'سایت مشابه','person@o3am.com and https://other.com/o3am.com/path',null,null,1,'333']);
$result = aloRefrigeratorLinkExecute($good,$db);
fridgeAssert($result['verified'] && $result['articles_checked']===4, 'all refrigerator posts including drafts scanned');
fridgeAssert($result['articles_updated']===2 && $result['links_replaced']===5, 'only exactly targeted URLs updated');
fridgeAssert($result['article_ids_updated']===[82,99], 'correct ids changed');
$changed=$db->query('SELECT * FROM posts WHERE id=82')->fetch(PDO::FETCH_ASSOC);
fridgeAssert(strpos($changed['content'],'https://o3am.ir/a?b=2')!==false, 'path kept');
fridgeAssert(strpos($changed['content'],'https://www.o3am.ir/b')!==false, 'www kept');
fridgeAssert(strpos($changed['description'],'https://o3am.ir/info')!==false, 'description updated');
fridgeAssert($changed['contact_number']==='09169522521' && (int)$changed['status']===1, 'phone and status unchanged');
$unrelated=$db->query('SELECT * FROM posts WHERE id=101')->fetch(PDO::FETCH_ASSOC);
fridgeAssert(strpos($unrelated['content'],'https://o3am.com/untouched')!==false, 'unrelated category untouched');
$similar=$db->query('SELECT * FROM posts WHERE id=102')->fetch(PDO::FETCH_ASSOC);
fridgeAssert(strpos($similar['content'],'person@o3am.com')!==false, 'email address untouched');
$second=aloRefrigeratorLinkExecute($good,$db);
fridgeAssert($second['articles_updated']===0 && $second['links_replaced']===0, 'idempotent');
echo "Refrigerator URL migration contract passed\n";
