<?php
declare(strict_types=1);
function aloEightCityMatch(string $title):bool {
 $s=preg_replace('/\s+/u',' ',str_replace(['ي','ك','‌'],['ی','ک',' '],$title));
 return preg_match('/(?<![\p{L}\p{N}])(?:چمخاله|لنگرود|فومن|صومعه\s*سرا|رشت|لاهیجان|آستانه\s*اشرفیه|املش)(?![\p{L}\p{N}])/u',$s)===1;
}
function aloEightCityTool():array {
 return ['name'=>'set_eight_gilan_city_contacts','description'=>'Preview and update 09108731535 for eight named cities in article and brand article records',
 'inputSchema'=>['type'=>'object','properties'=>[
 'phone'=>['type'=>'string','const'=>'09108731535'],
 'dry_run'=>['type'=>'boolean'],'confirm'=>['type'=>'boolean']],
 'required'=>['phone','dry_run','confirm']]];
}
function aloEightCityReplacePhones(string $s):string {
 $patterns=[
 '~(?<![0-9])(?:\+98|0098|98)[\s.\-]*9(?:[\s.\-]*[0-9]){9}(?![0-9])~u',
 '~(?<![0-9])0[\s.\-]*9(?:[\s.\-]*[0-9]){9}(?![0-9])~u',
 '~(?<![0-9])0[\s.\-]*[1-8](?:[\s.\-]*[0-9]){9}(?![0-9])~u'];
 foreach($patterns as $p) {
  $next=preg_replace_callback($p,static function($m){
   if(str_starts_with($m[0],'+98'))return '+989108731535';
   if(str_starts_with($m[0],'0098'))return '00989108731535';
   if(str_starts_with($m[0],'98'))return '989108731535';
   return '09108731535';
  },$s);
  if($next===null)throw new RuntimeException('Invalid phone format');
  $s=$next;
 }
 return $s;
}
function aloEightCityExecute(array $args,?PDO $db=null):array {
 if(($args['phone']??null)!=='09108731535'||!is_bool($args['dry_run']??null)||!is_bool($args['confirm']??null))
  throw new InvalidArgumentException('Invalid scoped request');
 $dry=$args['dry_run'];
 if(!$dry && !$args['confirm'])throw new InvalidArgumentException('Confirmation required');
 $db??=mcpDb();
 $matched=['article'=>0,'brand_article'=>0];$updated=$matched;$changedFields=0;$sample=[];
 $db->beginTransaction();
 try{
  foreach(['article'=>'posts','brand_article'=>'post_brand'] as $type=>$table){
   $columns=aloContentColumns($db,$table);
   if(!isset($columns['contact_number'],$columns['title']))throw new RuntimeException('Missing columns');
   $fields=array_values(array_filter(['contact_number','content','description','des'],static fn($f)=>isset($columns[$f])));
   $rows=$db->query('SELECT id,title,'.implode(',',$fields).' FROM '.$table.' ORDER BY id FOR UPDATE')->fetchAll(PDO::FETCH_ASSOC);
   foreach($rows as $row){
    if(!aloEightCityMatch((string)$row['title']))continue;
    ++$matched[$type];$change=[];
    if((string)($row['contact_number']??'')!=='09108731535')$change['contact_number']='09108731535';
    foreach($fields as $f){
     if($f==='contact_number'||!is_string($row[$f])||$row[$f]==='')continue;
     $new=aloEightCityReplacePhones($row[$f]);
     if($new!==$row[$f])$change[$f]=$new;
    }
    if(count($sample)<80)$sample[]=['type'=>$type,'id'=>(int)$row['id'],'title'=>$row['title'],'fields'=>array_keys($change)];
    if(!$change)continue;
    ++$updated[$type];$changedFields+=count($change);
    if(!$dry){
     $sql='UPDATE '.$table.' SET '.implode(',',array_map(static fn($f)=>$f.'=?',array_keys($change))).' WHERE id=?';
     $st=$db->prepare($sql);$st->execute([...array_values($change),(int)$row['id']]);
     if($st->rowCount()!==1)throw new RuntimeException('Write conflict');
    }
   }
   if(!$dry){
    $check=$db->query('SELECT title,contact_number FROM '.$table)->fetchAll(PDO::FETCH_ASSOC);$count=0;
    foreach($check as $row)if(aloEightCityMatch((string)$row['title'])){
     ++$count;
     if((string)$row['contact_number']!=='09108731535')throw new RuntimeException('Failed contact readback');
    }
    if($count!==$matched[$type])throw new RuntimeException('Target set changed');
   }
  }
  if(array_sum($matched)===0)throw new RuntimeException('No city matches');
  if($dry)$db->rollBack();else $db->commit();
 }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
 return ['phone'=>'09108731535','matched'=>$matched,'changed_or_would_change'=>$updated,'fields_changed'=>$changedFields,
 'sample'=>$sample,'dry_run'=>$dry,'transaction_completed'=>!$dry];
}
