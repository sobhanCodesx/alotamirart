<?php
/** Bounded, read-only site intelligence for the private MCP agent.
 * Mirrors the read-before-write principle of PlayNexus without Laravel,
 * a database migration, arbitrary SQL, or Composer on shared hosting.
 */
declare(strict_types=1);

function aloGraphTools(): array {
    return [
        ['name'=>'describe_alo_graph',
         'description'=>'Describe the read-only site content graph and known relationships.',
         'inputSchema'=>['type'=>'object','properties'=>new stdClass()]],
        ['name'=>'get_article_titles_by_ids',
         'description'=>'Read only published refrigerator-article titles for an exact list of ids; returns no full article text or draft titles.',
         'inputSchema'=>['type'=>'object','properties'=>[
             'ids'=>['type'=>'array','items'=>['type'=>'integer','minimum'=>1],'minItems'=>1,'maxItems'=>50],
         ],'required'=>['ids']]],
        ['name'=>'query_alo_graph',
         'description'=>'Search bounded published/draft article, brand and category summaries with category/brand relationships; read-only, no SQL input.',
         'inputSchema'=>['type'=>'object','properties'=>[
             'type'=>['type'=>'string','enum'=>['all','article','brand_article','brand','category']],
             'query'=>['type'=>'string','maxLength'=>100],
             'limit'=>['type'=>'integer','minimum'=>1,'maximum'=>25],
             'include_drafts'=>['type'=>'boolean']
         ]]],
    ];
}

function aloGraphValidate(array $args): array {
    $type=$args['type']??'all';
    $types=['all','article','brand_article','brand','category'];
    if (!is_string($type)||!in_array($type,$types,true))throw new InvalidArgumentException('Invalid graph entity type.');
    $q=$args['query']??'';
    if (!is_string($q)||mb_strlen($q,'UTF-8')>100)throw new InvalidArgumentException('Graph query must be at most 100 characters.');
    $limit=$args['limit']??10;
    if (!is_int($limit)||$limit<1||$limit>25)throw new InvalidArgumentException('Graph limit must be 1..25.');
    $drafts=$args['include_drafts']??false;
    if (!is_bool($drafts))throw new InvalidArgumentException('include_drafts must be a boolean.');
    return ['type'=>$type,'query'=>trim($q),'limit'=>$limit,'include_drafts'=>$drafts];
}

function aloGraphRead(PDO $db,string $sql,array $params): array {
    $stmt=$db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function aloGraphExecute(string $name,array $args,?PDO $db=null): array {
    if ($name==='describe_alo_graph')return [
        'name'=>'AloTamiratchi private content graph',
        'read_only'=>true,
        'entities'=>['article','brand_article','brand','category'],
        'relationships'=>['article.post_id -> category.id','brand_article.brand_id -> brand.id'],
        'max_page_size'=>25,
        'usage'=>'Use query_alo_graph to discover, then get_content to inspect the complete selected record before writing.',
    ];
    if ($name==='get_article_titles_by_ids') {
        $ids=$args['ids']??null;
        if (!is_array($ids) || count($ids)<1 || count($ids)>50 || count(array_unique($ids, SORT_REGULAR))!==count($ids)) {
            throw new InvalidArgumentException('Supply 1..50 unique article ids.');
        }
        foreach ($ids as $id) if (!is_int($id) || $id<1) {
            throw new InvalidArgumentException('Article ids must be positive integers.');
        }
        $db??=mcpDb();
        $placeholders=implode(',',array_fill(0,count($ids),'?'));
        $rows=aloGraphRead($db,'SELECT id,title,slug,status FROM posts WHERE post_id=9 AND status=1 AND id IN ('.$placeholders.') ORDER BY id ASC',$ids);
        return ['articles'=>$rows,'requested_count'=>count($ids),'matched_count'=>count($rows)];
    }
    if ($name!=='query_alo_graph')throw new InvalidArgumentException('Unknown graph tool.');
    $v=aloGraphValidate($args);
    $db??=mcpDb();
    $type=$v['type'];
    $limit=$v['limit'];
    $query=$v['query'];
    $result=['filter'=>$v,'entities'=>[]];
    $like='%'.str_replace(['\\','%','_'],['\\\\','\\%','\\_'],$query).'%';
    if ($type==='all'||$type==='article') {
        $where=[];
        $params=[];
        if (!$v['include_drafts'])$where[]='p.status=1';
        if ($query!==''){$where[]="p.title LIKE ? ESCAPE '!'";$params[]='%'.str_replace(['!','%','_'],['!!','!%','!_'],$query).'%';}
        $sql='SELECT p.id,p.title,p.slug,p.description,p.post_id,p.status, m.title AS category_title '.
            'FROM posts p LEFT JOIN menu m ON m.id=p.post_id '.
            ($where?'WHERE '.implode(' AND ',$where).' ':'').'ORDER BY p.id DESC LIMIT '.$limit;
        $result['entities']['articles']=aloGraphRead($db,$sql,$params);
    }
    if ($type==='all'||$type==='brand_article') {
        $where=[];$params=[];
        if (!$v['include_drafts'])$where[]='p.status=1';
        if ($query!==''){$where[]="p.title LIKE ? ESCAPE '!'";$params[]='%'.str_replace(['!','%','_'],['!!','!%','!_'],$query).'%';}
        $sql='SELECT p.id,p.title,p.slug,p.des,p.brand_id,p.status, b.name AS brand_name '.
            'FROM post_brand p LEFT JOIN items_brands b ON b.id=p.brand_id '.
            ($where?'WHERE '.implode(' AND ',$where).' ':'').'ORDER BY p.id DESC LIMIT '.$limit;
        $result['entities']['brand_articles']=aloGraphRead($db,$sql,$params);
    }
    if ($type==='all'||$type==='brand') {
        $params=[];$where='';
        if ($query!==''){$where="WHERE name LIKE ? ESCAPE '!' ";$params[]='%'.str_replace(['!','%','_'],['!!','!%','!_'],$query).'%';}
        $result['entities']['brands']=aloGraphRead($db,'SELECT id,name,des FROM items_brands '.$where.'ORDER BY id DESC LIMIT '.$limit,$params);
    }
    if ($type==='all'||$type==='category') {
        $params=[];$where='';
        if ($query!==''){$where="WHERE title LIKE ? ESCAPE '!' ";$params[]='%'.str_replace(['!','%','_'],['!!','!%','!_'],$query).'%';}
        $result['entities']['categories']=aloGraphRead($db,'SELECT id,title FROM menu '.$where.'ORDER BY id DESC LIMIT '.$limit,$params);
    }
    return $result;
}
