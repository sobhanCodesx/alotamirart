<?php
declare(strict_types=1);
function aloTwoCityHeatMatch(string $title): bool {
    $title=str_replace(['ي','ك','‌'],['ی','ک',' '],$title);
    return (str_contains($title,'چالوس') || str_contains($title,'نوشهر'))
        && (str_contains($title,'پکیج') || preg_match('/اجاق\s*گاز/u',$title)===1 || preg_match('/گاز\s*رومیزی/u',$title)===1);
}
function aloTwoCityHeatTool():array {
    return ['name'=>'set_two_city_heat_contact','description'=>'Update only Chalus and Nowshahr package and gas stove article contact numbers',
        'inputSchema'=>['type'=>'object','properties'=>['phone'=>['type'=>'string','const'=>'09901068051'],
        'confirm'=>['type'=>'boolean'],'dry_run'=>['type'=>'boolean']],
        'required'=>['phone','confirm','dry_run']]];
}
function aloTwoCityHeatExecute(array $args,?PDO $db=null):array {
    if(($args['phone']??null)!=='09901068051'||!is_bool($args['confirm']??null)||!is_bool($args['dry_run']??null))throw new InvalidArgumentException('Invalid parameters');
    $dry=$args['dry_run'];
    if(!$dry && $args['confirm']!==true)throw new InvalidArgumentException('Confirmation required');
    $db??=mcpDb();
    $matched=['article'=>0,'brand_article'=>0];
    $changed=['article'=>0,'brand_article'=>0];
    $sample=[];
    $db->beginTransaction();
    try {
        foreach(['article'=>'posts','brand_article'=>'post_brand'] as $type=>$table) {
            $rows=$db->query('SELECT id,title,contact_number FROM `'.$table.'` ORDER BY id FOR UPDATE')->fetchAll(PDO::FETCH_ASSOC);
            $stmt=$db->prepare('UPDATE `'.$table.'` SET contact_number=? WHERE id=?');
            foreach($rows as $row) {
                if(!aloTwoCityHeatMatch((string)$row['title']))continue;
                $matched[$type]++;
                if(count($sample)<50)$sample[]=['type'=>$type,'id'=>(int)$row['id'],'title'=>$row['title'],'old_phone'=>$row['contact_number']];
                if((string)$row['contact_number']==='09901068051')continue;
                $changed[$type]++;
                if(!$dry) {
                    $stmt->execute(['09901068051',(int)$row['id']]);
                    if($stmt->rowCount()!==1)throw new RuntimeException('Update conflict');
                }
            }
            if(!$dry) {
                $verify=$db->query('SELECT title,contact_number FROM `'.$table.'`')->fetchAll(PDO::FETCH_ASSOC);
                foreach($verify as $row)if(aloTwoCityHeatMatch((string)$row['title']) && (string)$row['contact_number']!=='09901068051')throw new RuntimeException('Verification failed');
            }
        }
        if(array_sum($matched)===0)throw new RuntimeException('No matches');
        if($dry)$db->rollBack();else $db->commit();
    }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
    return ['phone'=>'09901068051','dry_run'=>$dry,'matched'=>$matched,'changed_or_would_change'=>$changed,'sample'=>$sample,'transaction_completed'=>!$dry];
}
