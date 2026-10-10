<?php
/** Typed content tools for the existing AloTamirArt tables; no arbitrary SQL or raw filesystem access. */
declare(strict_types=1);

function aloContentModels(): array {
    return [
        'article' => ['table'=>'posts','fields'=>['title'=>200,'slug'=>220,'content'=>120000,'description'=>1000,'keyword'=>250,'tags'=>1000,'contact_number'=>30,'post_id'=>0],'required'=>['title','content','post_id'],'relation'=>['post_id','menu'],'status'=>true,'image'=>true],
        'brand_article'=>['table'=>'post_brand','fields'=>['title'=>200,'slug'=>220,'content'=>120000,'des'=>1000,'tags'=>1000,'contact_number'=>30,'brand_id'=>0],'required'=>['title','slug','content','brand_id'],'relation'=>['brand_id','items_brands'],'status'=>true,'image'=>true],
        'brand'=>['table'=>'items_brands','fields'=>['name'=>200,'des'=>1000],'required'=>['name'],'relation'=>null,'status'=>false,'image'=>true],
        'category'=>['table'=>'menu','fields'=>['title'=>200,'description'=>1000,'sort'=>0],'required'=>['title'],'relation'=>null,'status'=>false,'image'=>false],
    ];
}
function aloContentModel(array $args): array {
    $name = $args['type'] ?? null;
    $models = aloContentModels();
    if (!is_string($name) || !isset($models[$name])) throw new InvalidArgumentException('Invalid content type.');
    return $models[$name];
}
function aloContentColumns(PDO $db, string $table): array {
    $columns=[];
    foreach ($db->query('SHOW COLUMNS FROM `' . $table . '`')->fetchAll() as $col) $columns[$col['Field']]=$col;
    return $columns;
}
function aloContentRead(PDO $db, string $table, int $id): array {
    $st=$db->prepare('SELECT * FROM `' . $table . '` WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $found=$st->fetch();
    if (!$found) throw new InvalidArgumentException('Content not found.');
    return $found;
}
function aloContentRelation(PDO $db, array $model, array $fields): void {
    $relation=$model['relation'];
    if (!$relation || !array_key_exists($relation[0],$fields)) return;
    $id=$fields[$relation[0]];
    $st=$db->prepare('SELECT id FROM `'.$relation[1].'` WHERE id=? LIMIT 1');
    $st->execute([$id]);
    if (!$st->fetchColumn()) throw new InvalidArgumentException('Selected category or brand does not exist.');
}
function aloContentValidate(PDO $db,array $model,array $args,bool $create): array {
    $columns=aloContentColumns($db,$model['table']);
    $fields=$args['fields'] ?? [];
    if (!is_array($fields) || array_is_list($fields)) throw new InvalidArgumentException('fields must be an object.');
    $result=[];
    foreach ($fields as $field=>$value) {
        if (!array_key_exists($field,$model['fields']) || !isset($columns[$field])) throw new InvalidArgumentException('Unknown/unavailable content field: '.$field);
        $max=$model['fields'][$field];
        if ($max===0) {
            if (!is_int($value) || $value<0 || $value>1000000000) throw new InvalidArgumentException('Invalid numeric field: '.$field);
        } else {
            if (!is_string($value) || strlen($value)>$max) throw new InvalidArgumentException('Invalid text field: '.$field);
            $value=trim($value);
        }
        $result[$field]=$value;
    }
    if ($create) foreach ($model['required'] as $field) {
        if (!array_key_exists($field,$result) || $result[$field]==='' || $result[$field]===0) throw new InvalidArgumentException('Missing required field: '.$field);
    }
    if (isset($result['slug'])) {
        if ($model['table']==='post_brand' && !preg_match('/^[a-zA-Z0-9._-]+$/D',$result['slug'])) throw new InvalidArgumentException('Brand article slug must be English letters, digits, dot, dash or underscore.');
        if ($model['table']==='posts' && !preg_match('/^[\pL\pN._-]+$/uD',$result['slug'])) throw new InvalidArgumentException('Invalid article slug.');
    }
    if ($model['table']==='posts' && !isset($result['slug']) && isset($result['title'])) {
        $slug=preg_replace('/[^\p{L}\p{N}-]+/u','',preg_replace('/\s+/u','-',$result['title']));
        $result['slug']=trim(mb_strtolower($slug,'UTF-8'),'-');
    }
    aloContentRelation($db,$model,$result);
    return $result;
}

/**
 * PlayNexus-style remote media: retrieve the image on the server rather than
 * forwarding base64 inside JSON-RPC. Only allow Wikimedia Commons media URLs.
 */
function aloContentRemoteImage(string $url): string {
    $p = parse_url($url);
    if (strlen($url) > 1000 || !is_array($p)
        || ($p['scheme'] ?? '') !== 'https'
        || strtolower($p['host'] ?? '') !== 'upload.wikimedia.org'
        || !str_starts_with($p['path'] ?? '', '/wikipedia/commons/')
        || isset($p['user']) || isset($p['pass']) || isset($p['port'])
        || isset($p['query']) || isset($p['fragment'])) {
        throw new InvalidArgumentException('Only direct HTTPS Wikimedia Commons image URLs are accepted.');
    }
    $max=2500000;
    if (function_exists('curl_init')) {
        $ch=curl_init($url);
        if ($ch===false) throw new RuntimeException('Image download unavailable.');
        $binary='';
        curl_setopt_array($ch,[
            CURLOPT_FOLLOWLOCATION=>false,
            CURLOPT_TIMEOUT=>25,
            CURLOPT_CONNECTTIMEOUT=>8,
            CURLOPT_SSL_VERIFYPEER=>true,
            CURLOPT_SSL_VERIFYHOST=>2,
            CURLOPT_USERAGENT=>'AloTamiratchi-MCP/1.0',
            CURLOPT_WRITEFUNCTION=>static function($handle,string $chunk) use (&$binary,$max): int {
                if (strlen($binary)+strlen($chunk)>$max) return 0;
                $binary.=$chunk;
                return strlen($chunk);
            },
        ]);
        $ok=curl_exec($ch);
        $http=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($ok===false || $http!==200) throw new RuntimeException('Featured image download failed (HTTP '.$http.').');
    } else {
        if (!filter_var(ini_get('allow_url_fopen'), FILTER_VALIDATE_BOOLEAN)) throw new RuntimeException('PHP cURL or allow_url_fopen is required for URL images.');
        $ctx=stream_context_create(['http'=>['timeout'=>20,'follow_location'=>0,'ignore_errors'=>true,
            'header'=>"User-Agent: AloTamiratchi-MCP/1.0\r\nAccept: image/*\r\n"]]);
        $binary=@file_get_contents($url,false,$ctx,0,$max+1);
        $status=$http_response_header[0]??'';
        if (!is_string($binary) || !preg_match('~^HTTP/\S+\s+200(?:\s|$)~',$status)) throw new RuntimeException('Featured image download failed.');
    }
    if (strlen($binary)<24 || strlen($binary)>$max) throw new InvalidArgumentException('Image size must be 24 bytes to 2.5 MB.');
    return $binary;
}

function aloContentImage(array $args,string $table): ?string {
    $image=$args['image_base64'] ?? null;
    $url=$args['image_url'] ?? null;
    if ($image!==null && $url!==null) throw new InvalidArgumentException('Use either image_url or image_base64.');
    if ($image===null && $url===null) return null;
    if ($url!==null) {
        if (!is_string($url)) throw new InvalidArgumentException('image_url must be a string.');
        $binary=aloContentRemoteImage($url);
    } else {
        if (!is_string($image) || strlen($image)>4000000 || !preg_match('~^[A-Za-z0-9+/=]+$~D',$image)) throw new InvalidArgumentException('Invalid image_base64.');
        $binary=base64_decode($image,true);
        if ($binary===false || strlen($binary)>2500000 || strlen($binary)<24) throw new InvalidArgumentException('Image size must be under 2.5 MB.');
    }
    $info=@getimagesizefromstring($binary);
    $mimes=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $mime=$info['mime'] ?? '';
    if (!isset($mimes[$mime]) || ($info[0]??0)>4000 || ($info[1]??0)>4000) throw new InvalidArgumentException('Only JPG/PNG/WebP images up to 4000px are supported.');
    $prefix=$table==='items_brands'?'brand/':($table==='post_brand'?'img':'img-');
    $relative='them/admin/dist/img/'.$prefix.bin2hex(random_bytes(16)).'.'.$mimes[$mime];
    $absolute=dirname(__DIR__).'/'.$relative;
    $dir=dirname($absolute);
    if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) throw new RuntimeException('Image directory unavailable.');
    if (file_put_contents($absolute,$binary,LOCK_EX)!==strlen($binary)) throw new RuntimeException('Image write failed.');
    return $relative;
}
function aloContentSchema(): array {
    $models=aloContentModels();
    $summary=[];
    foreach ($models as $name=>$model) $summary[$name]=['fields'=>array_keys($model['fields']),'required'=>$model['required'],'image_supported'=>$model['image'],'status_supported'=>$model['status']];
    return $summary;
}
function aloContentTools(): array {
    $type=['type'=>'string','enum'=>array_keys(aloContentModels())];
    $id=['type'=>'integer','minimum'=>1];
    $fields=['type'=>'object','description'=>'Allowed field keys depend on content type; call describe_content_fields first. No arbitrary database fields.'];
    return [
        ['name'=>'describe_content_fields','description'=>'Read allowed content types and form-field names, including article, brand article, brand and category.','inputSchema'=>['type'=>'object','properties'=>new stdClass()]],
        ['name'=>'list_content','description'=>'List records of the selected content type, including drafts.','inputSchema'=>['type'=>'object','properties'=>['type'=>$type,'limit'=>['type'=>'integer','minimum'=>1,'maximum'=>50]],'required'=>['type']]],
        ['name'=>'find_content','description'=>'Find exact article slug to prevent duplicate publication.', 'inputSchema'=>['type'=>'object','properties'=>['type'=>['type'=>'string','enum'=>['article','brand_article']],'slug'=>['type'=>'string','maxLength'=>220]],'required'=>['type','slug']]],
        ['name'=>'get_content','description'=>'Get all existing fields of a record.','inputSchema'=>['type'=>'object','properties'=>['type'=>$type,'id'=>$id],'required'=>['type','id']]],
        ['name'=>'create_content','description'=>'Create content using all supported form fields; status-bearing records become unpublished drafts; image_base64 can attach JPEG/PNG/WebP. Non-status records require confirm_public=true.','inputSchema'=>['type'=>'object','properties'=>['type'=>$type,'fields'=>$fields,'image_base64'=>['type'=>'string','description'=>'Optional base64 encoded image data, without data URL prefix'],'image_url'=>['type'=>'string','description'=>'Vetted Wikimedia Commons image URL; stored as local featured image'],'confirm_public'=>['type'=>'boolean']],'required'=>['type','fields']]],
        ['name'=>'update_content','description'=>'Update existing content fields; published records require confirm_public=true.','inputSchema'=>['type'=>'object','properties'=>['type'=>$type,'id'=>$id,'fields'=>$fields,'image_base64'=>['type'=>'string'],'image_url'=>['type'=>'string'],'confirm_public'=>['type'=>'boolean']],'required'=>['type','id','fields']]],
        ['name'=>'set_content_published','description'=>'Explicitly set publication state for an article or brand article; requires enable flag for publishing.','inputSchema'=>['type'=>'object','properties'=>['type'=>['type'=>'string','enum'=>['article','brand_article']],'id'=>$id,'published'=>['type'=>'boolean'],'confirm'=>['type'=>'boolean','const'=>true]],'required'=>['type','id','published','confirm']]],
    ];
}
function aloContentExecute(string $name,array $args): array {
    if ($name==='describe_content_fields') return ['models'=>aloContentSchema()];
    $model=aloContentModel($args);
    $db=mcpDb();$table=$model['table'];
    if ($name==='find_content') {
        if (!in_array($table,['posts','post_brand'],true)) throw new InvalidArgumentException('Only articles support exact slug lookup.');
        $slug=$args['slug'] ?? null;
        if (!is_string($slug) || trim($slug)==='' || strlen($slug)>220) throw new InvalidArgumentException('Invalid exact slug.');
        $st=$db->prepare('SELECT * FROM '.$table.' WHERE slug=? ORDER BY id DESC LIMIT 1');
        $st->execute([$slug]);
        return ['record'=>$st->fetch() ?: null];
    }
    if ($name==='list_content') {
        $limit=$args['limit']??20;
        if (!is_int($limit)||$limit<1||$limit>50) throw new InvalidArgumentException('Invalid limit.');
        $fields=aloContentColumns($db,$table);
        $selection=isset($fields['title'])?'title':(isset($fields['name'])?'name':'id');
        $res=$db->query('SELECT id,`'.$selection.'`'.(isset($fields['status'])?',status':'').' FROM `'.$table.'` ORDER BY id DESC LIMIT '.$limit)->fetchAll();
        return ['records'=>$res];
    }
    $id=isset($args['id'])?mcpInteger($args,'id',true):0;
    if ($name==='get_content') return ['record'=>aloContentRead($db,$table,$id)];
    if ($name==='set_content_published') {
        if (!$model['status']) throw new InvalidArgumentException('Content type does not support publication state.');
        if (($args['confirm']??false)!==true || !is_bool($args['published']??null)) throw new InvalidArgumentException('Explicit confirmation required.');
        $publish=$args['published'];
        if ($publish && getenv('MCP_ALLOW_PUBLISH')!=='1') throw new InvalidArgumentException('Publishing disabled on server.');
        $row=aloContentRead($db,$table,$id);
        if ($publish && (empty($row['title']) || empty($row['content']) || empty($row['img']) && $table==='post_brand')) throw new InvalidArgumentException('Content not ready to publish.');
        $st=$db->prepare('UPDATE `'.$table.'` SET status=? WHERE id=?');
        $st->execute([$publish?1:0,$id]);
        return ['record'=>aloContentRead($db,$table,$id)];
    }
    $create=$name==='create_content';
    if (!$create && $name!=='update_content') throw new InvalidArgumentException('Unknown content tool.');
    $before=$create?null:aloContentRead($db,$table,$id);
    if (!$create && (!$model['status'] || (int)($before['status']??0)===1) && ($args['confirm_public']??false)!==true) throw new InvalidArgumentException('Editing public content requires confirm_public=true.');
    if ($create && !$model['status'] && ($args['confirm_public']??false)!==true) throw new InvalidArgumentException('Creation is immediately public; confirm_public=true required.');
    $data=aloContentValidate($db,$model,$args,$create);
    $image=null;
    try {
        if (isset($args['image_base64']) || isset($args['image_url'])) {
            if (!$model['image']) throw new InvalidArgumentException('Images not supported for this content type.');
            $image=aloContentImage($args,$table);
            if ($image!==null) $data['img']=$image;
        }
        if ($create && $model['image'] && $table!=='posts' && !$image) throw new InvalidArgumentException('Brand and brand article creation requires an image.');
        if ($create) {
            if (in_array($table,['posts','post_brand'],true)) {
                $author=filter_var(getenv('MCP_AUTHOR_USER_ID'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
                if (!$author) throw new RuntimeException('MCP_AUTHOR_USER_ID is not configured.');
                $check=$db->prepare('SELECT id FROM users WHERE id=? LIMIT 1');
                $check->execute([$author]);
                if (!$check->fetchColumn()) throw new InvalidArgumentException('Configured author missing.');
                $data['user_id']=$author;
                $data['status']=0;
            }
            $cols=aloContentColumns($db,$table);
            if (isset($cols['created_at'])) $data['created_at']=date('Y-m-d H:i:s');
            $names=array_keys($data);
            $sql='INSERT INTO `'.$table.'` ('.implode(',',array_map(static fn($n)=>'`'.$n.'`',$names)).') VALUES ('.implode(',',array_fill(0,count($names),'?')).')';
            $db->prepare($sql)->execute(array_values($data));
            $id=(int)$db->lastInsertId();
        } else {
            if (!$data) throw new InvalidArgumentException('No changes provided.');
            $cols=aloContentColumns($db,$table);
            if (isset($cols['updated_at'])) $data['updated_at']=date('Y-m-d H:i:s');
            $names=array_keys($data);
            $sets=implode(',',array_map(static fn($n)=>'`'.$n.'`=?',$names));
            $values=array_values($data);$values[]=$id;
            $db->prepare('UPDATE `'.$table.'` SET '.$sets.' WHERE id=?')->execute($values);
        }
        return ['record'=>aloContentRead($db,$table,$id),'published'=>$model['status']?false:true];
    } catch(Throwable $e) {
        if ($image) @unlink(dirname(__DIR__).'/'.$image);
        throw $e;
    }
}
