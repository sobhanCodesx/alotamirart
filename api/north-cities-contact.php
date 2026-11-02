<?php
/** Scoped, transactional operator contact change for 13 named northern cities.
 * Only contact_number on posts/post_brand; never rewrites article body or other service pages.
 */
declare(strict_types=1);
function aloNorthContactTool():array {
 return ['name'=>'set_north_cities_operator_contact','description'=>'Preview or atomically set contact_number for 13 exact northern city names in article/brand article titles; exclude gas stoves, heaters, water heaters, boilers.',
 'inputSchema'=>['type'=>'object','properties'=>[
 'phone'=>['type'=>'string','const'=>'09351211661'],
 'confirm'=>['type'=>'boolean'],
 'dry_run'=>['type'=>'boolean']
 ],'required'=>['phone','confirm']]];
}
function aloNorthContactExecute(array $args,?PDO $db=null):array {
 if(($args['phone']??null)!=='09351211661'||!is_bool($args['confirm']??null))
   throw new InvalidArgumentException('Explicit exact phone and confirmation required');
 $dry=($args['dry_run']??false)===true;
 if(!$dry && $args['confirm']!==true)throw new InvalidArgumentException('Confirmation required for writing');
 $cities=['آمل','سرخرود','بابلسر','فریدونکنار','محمودآباد','ایزدشهر','نور','متل قو','رویان','نوشهر','چالوس','قائمشهر','بابل'];
 $excluded=['اجاق گاز','اجاق‌گاز','اجاق','بخاری','آبگرمکن','آب گرمکن','آب‌گرمکن','پکیج'];
 $db??=mcpDb();
 $matched=[];$changed=[];$excludedCount=0;$samples=[];
 $db->beginTransaction();
 try {
  foreach(['article'=>'posts','brand_article'=>'post_brand'] as $type=>$table) {
   $query=$db->query('SELECT id,title,contact_number FROM `'.$table.'` ORDER BY id ASC FOR UPDATE');
   $rows=$query->fetchAll(PDO::FETCH_ASSOC);
   $matched[$type]=0;$changed[$type]=0;
   $update=$db->prepare('UPDATE `'.$table.'` SET contact_number=? WHERE id=?');
   foreach($rows as $row) {
    $title=(string)$row['title'];
    $titleNorm=str_replace(['ي','ك','‌'],['ی','ک',' '],$title);
    $hit=false;
    foreach($cities as $city) {
      if(mb_strpos($titleNorm,$city,'UTF-8')!==false){$hit=true;break;}
    }
    if(!$hit)continue;
    $skip=false;
    foreach($excluded as $word) {
      $w=str_replace('‌',' ',$word);
      if(mb_strpos($titleNorm,$w,'UTF-8')!==false){$skip=true;break;}
    }
    if($skip){++$excludedCount;continue;}
    ++$matched[$type];
    if(count($samples)<40)$samples[]=['type'=>$type,'id'=>(int)$row['id'],'title'=>$title,'old_phone'=>$row['contact_number']];
    if((string)($row['contact_number']??'')==='09351211661')continue;
    if(!$dry) {
      $update->execute(['09351211661',(int)$row['id']]);
      if($update->rowCount()!==1)throw new RuntimeException('Concurrent update conflict');
    }
    ++$changed[$type];
   }
  }
  if(array_sum($matched)===0)throw new RuntimeException('No matching service pages; refusing empty operation');
  if($dry){$db->rollBack();}
  else {$db->commit();}
 }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
 return ['dry_run'=>$dry,'phone'=>'09351211661','cities'=>$cities,'excluded_service_terms'=>$excluded,'matched'=>$matched,'changed_or_would_change'=>$changed,'excluded_count'=>$excludedCount,'sample'=>$samples,'transaction_completed'=>!$dry];
}
