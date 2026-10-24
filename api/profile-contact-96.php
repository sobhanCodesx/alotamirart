<?php
/** Exact-owner authenticated bulk change for the gilan/96 profile only. */
declare(strict_types=1);
function aloProfilePhoneTool(): array {
  return ['name'=>'set_gilan_96_article_phones',
    'description'=>'Change only phone contacts inside articles owned by verified profile gilan/96; preserve all unrelated content.',
    'inputSchema'=>['type'=>'object','properties'=>[
      'profile_user_id'=>['type'=>'integer','const'=>96],
      'profile_username'=>['type'=>'string','const'=>'gilan'],
      'phone'=>['type'=>'string','pattern'=>'^09[0-9]{9}$'],
      'confirm'=>['type'=>'boolean','const'=>true],
      'inspect_only'=>['type'=>'boolean']],
      'required'=>['profile_user_id','profile_username','phone','confirm']]];
}
function aloProfilePhoneExecute(array $args,?PDO $db=null): array {
  if (($args['profile_user_id']??null)!==96 || ($args['profile_username']??null)!=='gilan' ||
      ($args['confirm']??null)!==true || ($args['phone']??null)!=='09040731535') {
    throw new InvalidArgumentException('Exact owner, requested number and confirmation required.');
  }
  $db??=mcpDb();
  $profile=$db->prepare('SELECT id,user_name,name FROM users WHERE id=? AND user_name=? LIMIT 1');
  $profile->execute([96,'gilan']);
  $owner=$profile->fetch(PDO::FETCH_ASSOC);
  $display=(string)($owner['name']??'');
  if ((!$owner || mb_strpos($display,'دانش')===false || mb_strpos($display,'اسدی')===false) && ($args['inspect_only']??false)!==true)
    throw new InvalidArgumentException('Profile identity verification failed; no updates');
  if (($args['inspect_only']??false)===true) {
    $meta=['profile_found'=>(bool)$owner,'username_matched'=>((string)($owner['user_name']??''))==='gilan',
      'name_has_danesh'=>mb_strpos($display,'دانش')!==false,'name_has_asadi'=>mb_strpos($display,'اسدی')!==false];
    foreach(['posts','post_brand'] as $table) {
      $cols=aloContentColumns($db,$table);
      $meta[$table.'_has_owner']=isset($cols['user_id']);
      $meta[$table.'_has_content']=isset($cols['content']);
      if(isset($cols['user_id'])){
        $count=$db->prepare('SELECT COUNT(*) FROM `'.$table.'` WHERE user_id=?');
        $count->execute([96]);
        $meta[$table.'_count']=(int)$count->fetchColumn();
      }
    }
    return ['success'=>true,'inspect_only'=>true,'checks'=>$meta];
  }
  $phone='09040731535';
  $patterns=[
    '~(?<![\d])(?:\+98|0098|98)[\s.\-]*9(?:[\s.\-]*[0-9]){9}(?![\d])~u',
    '~(?<![\d])0[\s.\-]*9(?:[\s.\-]*[0-9]){9}(?![\d])~u'
  ];
  $repl=static function(string $input) use ($patterns,$phone): string {
    foreach($patterns as $pat) {
      $next=preg_replace_callback($pat,static function(array $m)use($phone):string{
        $orig=$m[0];
        if (str_starts_with($orig,'+98'))return '+98'.substr($phone,1);
        if (str_starts_with($orig,'0098'))return '0098'.substr($phone,1);
        if (str_starts_with($orig,'98'))return '98'.substr($phone,1);
        return $phone;
      },$input);
      if($next===null)throw new RuntimeException('Cannot parse telephone contacts');
      $input=$next;
    }
    return $input;
  };
  $db->beginTransaction();
  try {
    $result=['articles'=>0,'brand_articles'=>0,'changed_articles'=>0,'changed_brand_articles'=>0,'fields_changed'=>0,'replaced_occurrences'=>0];
    foreach (['posts'=>'articles','post_brand'=>'brand_articles'] as $table=>$key) {
      $cols=aloContentColumns($db,$table);
      if(!isset($cols['user_id'],$cols['content']))throw new RuntimeException('Article owner/content fields unavailable.');
      $fields=array_values(array_filter(['content','description','des','contact_number'],static fn($name)=>isset($cols[$name])));
      $sql='SELECT id,'.implode(',',array_map(static fn($k)=>'`'.$k.'`',$fields)).' FROM `'.$table.'` WHERE user_id=? ORDER BY id ASC FOR UPDATE';
      $stmt=$db->prepare($sql);$stmt->execute([96]);$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
      $result[$key]=count($rows);
      foreach($rows as $row) {
        $changed=[];
        foreach($fields as $field) {
          $before=$row[$field];
          if(!is_string($before)||$before==='')continue;
          $after=$repl($before);
          if($before!==$after) {
            $changed[$field]=$after;
            $result['fields_changed']++;
            $result['replaced_occurrences']+=max(1,substr_count($after,$phone)-substr_count($before,$phone));
          }
        }
        if(!$changed)continue;
        $sets=implode(',',array_map(static fn($k)=>'`'.$k.'`=?',array_keys($changed)));
        $update=$db->prepare('UPDATE `'.$table.'` SET '.$sets.' WHERE id=? AND user_id=?');
        $update->execute([...array_values($changed),(int)$row['id'],96]);
        if($update->rowCount()!==1)throw new RuntimeException('Owner-scoped update failed');
        $verify=$db->prepare('SELECT '.implode(',',array_map(static fn($k)=>'`'.$k.'`',array_keys($changed))).' FROM `'.$table.'` WHERE id=? AND user_id=?');
        $verify->execute([(int)$row['id'],96]);
        $verified=$verify->fetch(PDO::FETCH_ASSOC);
        foreach($changed as $field=>$expected)if(!is_array($verified)||$verified[$field]!==$expected)throw new RuntimeException('Persisted number mismatch');
        $result[$table==='posts'?'changed_articles':'changed_brand_articles']++;
      }
      $check=$db->prepare('SELECT COUNT(*) FROM `'.$table.'` WHERE user_id=?');
      $check->execute([96]);
      if((int)$check->fetchColumn()!==count($rows))throw new RuntimeException('Owned article set changed during operation');
    }
    if($result['articles']+$result['brand_articles']<1)throw new RuntimeException('No verified owner articles, refused empty operation');
    $db->commit();
    return ['success'=>true,'owner_verified'=>true,'user_id'=>96,'username'=>'gilan','phone'=>$phone,'results'=>$result];
  }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
