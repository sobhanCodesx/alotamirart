<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/api/site-graph.php';
function titleAssert(bool $condition, string $message): void {
    if (!$condition) { fwrite(STDERR, "Title lookup contract failed: $message\n"); exit(1); }
}
$toolNames = array_column(aloGraphTools(), 'name');
titleAssert(in_array('get_article_titles_by_ids', $toolNames, true), 'published-only title tool declared');
titleAssert(in_array('sqlite', PDO::getAvailableDrivers(), true), 'SQLite driver must be available');
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY,title TEXT,slug TEXT,post_id INTEGER,status INTEGER)');
$db->exec("INSERT INTO posts VALUES (74,'تعمیر یخچال مدل آزمایشی','sample-74',9,1)");
$db->exec("INSERT INTO posts VALUES (82,'نمایندگی تعمیرات یخچال در بندر ماهشهر',NULL,9,1)");
$db->exec("INSERT INTO posts VALUES (83,'پیش نویس خصوصی','private',9,0)");
$db->exec("INSERT INTO posts VALUES (84,'لباسشویی','washing',1,1)");
$result=aloGraphExecute('get_article_titles_by_ids',['ids'=>[84,83,82,74]],$db);
titleAssert($result['requested_count']===4 && $result['matched_count']===2, 'only public refrigerator posts returned');
titleAssert((int)$result['articles'][0]['id']===74 && (int)$result['articles'][1]['id']===82, 'title IDs sorted');
titleAssert(strpos(json_encode($result,JSON_UNESCAPED_UNICODE),'خصوصی')===false, 'draft title not leaked');
foreach ([[],[0],[74,74],[true],[74,'75'],range(1,51)] as $ids) {
    try { aloGraphExecute('get_article_titles_by_ids',['ids'=>$ids],$db); titleAssert(false,'invalid ids accepted'); }
    catch (InvalidArgumentException $expected) {}
}
echo "Article title lookup contract passed\n";
