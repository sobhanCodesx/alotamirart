<?php
/** No database required: enforce safe bounded graph discovery contract. */
declare(strict_types=1);
require dirname(__DIR__).'/api/site-graph.php';
function assertion(bool $ok,string $reason): void {
    if (!$ok) {fwrite(STDERR,$reason.PHP_EOL);exit(1);}
}
$names=array_column(aloGraphTools(),'name');
assertion(in_array('query_alo_graph',$names,true),'Missing graph query tool.');
assertion(in_array('describe_alo_graph',$names,true),'Missing graph schema tool.');
$schema=aloGraphExecute('describe_alo_graph',[]);
assertion($schema['read_only']===true,'Graph should be read-only.');
assertion($schema['max_page_size']===25,'Graph must cap responses.');
assertion(aloGraphValidate(['type'=>'article','limit'=>20,'include_drafts'=>true])['limit']===20,'Expected valid graph args.');
foreach ([['type'=>'users'],['limit'=>999],['limit'=>'15'],['query'=>str_repeat('x',101)],['include_drafts'=>1]] as $bad) {
    $rejected=false;
    try {aloGraphValidate($bad);}catch(InvalidArgumentException $e){$rejected=true;}
    assertion($rejected,'Unsafe graph query was accepted.');
}
echo "Private site graph contract passed\n";
