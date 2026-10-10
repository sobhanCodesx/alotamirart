<?php
/**
 * PlayNexus-style staged deployment receiver for AloTamiratchi.
 *
 * Endpoint: /api/deployment-agent/?action=upload/chunk|upload/complete|
 *                                {id}/verify|{id}/apply|{id}/status|health
 *
 * No Laravel, SSH, FTP, Composer or root rewrite is required on cPanel.
 * The existing, already-bootstrapped /api/deploy.php remains untouched.
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/Support/env.php';
aloLoadEnv(dirname(__DIR__, 2) . '/.env');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');

function pnReply(int $http, array $body): never {
    http_response_code($http);
    echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function pnFail(int $http, string $message): never { pnReply($http, ['error' => $message]); }
function pnId(string $id): string {
    if (!preg_match('/^[a-f0-9]{32}$/D', $id)) pnFail(422, 'Invalid operation id.');
    return $id;
}
function pnSha($sha): string {
    if (!is_string($sha) || !preg_match('/^[a-f0-9]{40}$/D', $sha)) pnFail(422, 'Invalid source SHA.');
    return $sha;
}
function pnRoot(): string {
    $dir=sys_get_temp_dir().'/alotamirart-staged-deploy-'.substr(hash('sha256',__DIR__),0,16);
    if (!is_dir($dir) && !mkdir($dir,0700,true) && !is_dir($dir)) pnFail(503,'Private deployment directory unavailable.');
    return $dir;
}
function pnOperation(string $id): string {
    return pnRoot().'/'.pnId($id);
}
function pnState(string $id): array {
    $file=pnOperation($id).'/state.json';
    if (!is_file($file)) pnFail(404,'Unknown deployment operation.');
    $result=json_decode((string)file_get_contents($file),true);
    if (!is_array($result) || ($result['id']??null)!==$id) pnFail(503,'Invalid deployment state.');
    if (($result['created_at']??0) < time()-3600 && ($result['status']??'')!=='completed') pnFail(410,'Deployment operation expired.');
    return $result;
}
function pnSave(array $state): array {
    $path=pnOperation((string)$state['id']).'/state.json';
    $tmp=$path.'.'.bin2hex(random_bytes(5)).'.tmp';
    $json=json_encode($state,JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE);
    if (!is_string($json)||file_put_contents($tmp,$json,LOCK_EX)!==strlen($json)||!rename($tmp,$path)) {
        @unlink($tmp);pnFail(500,'Unable to save deployment state.');
    }
    return $state;
}
function pnLock() {
    $lock=fopen(pnRoot().'/lock','c');
    if (!$lock || !flock($lock,LOCK_EX|LOCK_NB)) pnFail(409,'Another deployment operation is active.');
    return $lock;
}
function pnSafe(string $path): bool {
    if ($path===''||strlen($path)>240||!preg_match('~^[A-Za-z0-9_./-]+$~D',$path))return false;
    $rootFiles=['index.php','404.php','robots.txt','city-services.php',
        'service-city.php','service-refrigerator.php','service-washing-machine.php','show-city.php'];
    if (in_array($path,$rootFiles,true))return true;
    $parts=explode('/',$path);
    $roots=['api','app','bootstrap','classes','config','database','public','reqires',
        'router','routes','them','mapbrand','mappost'];
    if(count($parts)<2||!in_array($parts[0],$roots,true))return false;
    $banned=['.git','.github','.idea','.env','cache','logs','log','storage','vendor',
        'node_modules','upload','uploads','backup','backups','secrets','secret','tmp','temp'];
    foreach ($parts as $part) {
        if ($part===''||$part==='.'||$part==='..'||str_starts_with($part,'.')||in_array(strtolower($part),$banned,true))return false;
    }
    if (str_starts_with($path,'them/admin/dist/img/'))return false;
    if (strtolower(basename($path))==='error_log')return false;
    return (bool)preg_match('~\\.(?:php|css|js|json|html|htm|txt|svg|png|jpg|jpeg|webp|ico|woff2?|ttf|eot|gif|xml|webmanifest)$~iD',$path);
}
function pnGuardTarget(string $root,string $path): string {
    if (!pnSafe($path)) pnFail(422,'Package contains a disallowed application file.');
    $walk=$root;
    foreach (explode('/',$path) as $segment) {
        $walk.='/'.$segment;
        if (is_link($walk)) pnFail(422,'Symlink target rejected.');
    }
    if (file_exists($walk)&&!is_file($walk)) pnFail(422,'Deployment target is not a regular file.');
    return $walk;
}
function pnRaw(): array {
    $input=file_get_contents('php://input',false,null,0,65537);
    if (!is_string($input)||strlen($input)>65536) pnFail(413,'JSON request too large.');
    $data=json_decode($input,true);
    if (!is_array($data)||array_is_list($data)) pnFail(400,'Invalid JSON request.');
    return $data;
}
function pnUpload(): never {
    $i=filter_var($_POST['chunk_index']??null,FILTER_VALIDATE_INT);
    $n=filter_var($_POST['total_chunks']??null,FILTER_VALIDATE_INT);
    $size=filter_var($_POST['size']??null,FILTER_VALIDATE_INT);
    $sha=pnSha($_POST['source_sha']??null);
    $run=(string)($_POST['run_id']??'');
    if ($i===false||$n===false||$size===false||$n<1||$n>80||$i<0||$i>=$n||$size<1||$size>20*1024*1024
        || ($_POST['source_ref']??'')!=='refs/heads/main'
        || !preg_match('/^[0-9]{1,20}$/D',$run)
        || !isset($_FILES['chunk'])||($_FILES['chunk']['error']??1)!==UPLOAD_ERR_OK
        || ($_FILES['chunk']['size']??0)>600000||($_FILES['chunk']['size']??0)<1) pnFail(422,'Invalid deployment chunk or metadata.');
    $id=(string)($_POST['operation_id']??'');
    if ($id==='') {
        $id=bin2hex(random_bytes(16));
        $dir=pnOperation($id);
        if (!mkdir($dir,0700) || !mkdir($dir.'/chunks',0700)) pnFail(500,'Could not initialize chunk storage.');
        $state=['id'=>$id,'status'=>'uploading','stage'=>'uploading','progress'=>0,
            'source_sha'=>$sha,'source_ref'=>'refs/heads/main','run_id'=>$run,
            'total_chunks'=>$n,'size'=>$size,'created_at'=>time()];
        pnSave($state);
    } else {
        $state=pnState(pnId($id));
        if (($state['status']??'')!=='uploading'||$state['source_sha']!==$sha||$state['run_id']!==$run
            ||$state['total_chunks']!==$n||$state['size']!==$size)pnFail(409,'Chunk metadata differs from existing operation.');
    }
    $file=pnOperation($id).'/chunks/'.$i.'.part';
    $uploaded=(string)$_FILES['chunk']['tmp_name'];
    if (is_file($file)) {
        if (!hash_equals(hash_file('sha256',$file),hash_file('sha256',$uploaded))) pnFail(409,'Retried chunk contents differ.');
    } elseif (!move_uploaded_file($uploaded,$file)) pnFail(500,'Could not store deployment chunk.');
    $received=count(glob(pnOperation($id).'/chunks/*.part')?:[]);
    $state['progress']=(int)floor(90*$received/$n);
    pnSave($state);
    pnReply(200,['id'=>$id,'status'=>'uploading','stage'=>'uploading','progress'=>$state['progress']]);
}
function pnComplete(): never {
    $data=pnRaw();$id=pnId((string)($data['operation_id']??''));
    $lock=pnLock();$state=pnState($id);
    if (in_array($state['status'],['uploaded','verified','running','completed'],true))pnReply(200,$state);
    if ($state['status']!=='uploading')pnFail(409,'Operation is not uploading.');
    $path=pnOperation($id).'/package.zip';
    $tmp=$path.'.tmp';
    $target=fopen($tmp,'wb');
    if (!$target)pnFail(500,'Could not allocate package.');
    for ($i=0;$i<$state['total_chunks'];$i++) {
        $file=pnOperation($id).'/chunks/'.$i.'.part';
        if (!is_file($file))pnFail(422,'Missing deployment chunk.');
        $stream=fopen($file,'rb');
        if (!$stream)pnFail(500,'Unreadable deployment chunk.');
        stream_copy_to_stream($stream,$target);
        fclose($stream);
    }
    fclose($target);
    if (filesize($tmp)!==$state['size']||!rename($tmp,$path))pnFail(422,'Assembled package size mismatch.');
    $state['status']='uploaded';$state['stage']='uploaded';$state['progress']=95;
    pnReply(200,pnSave($state));
}
function pnVerify(string $id): never {
    $lock=pnLock();$state=pnState($id);
    if (in_array($state['status'],['verified','running','completed'],true))pnReply(200,$state);
    if ($state['status']!=='uploaded')pnFail(409,'Package is not uploaded.');
    $zip=new ZipArchive();
    $archive=pnOperation($id).'/package.zip';
    if (!is_file($archive)||$zip->open($archive,ZipArchive::RDONLY)!==true)pnFail(422,'Invalid ZIP package.');
    if ($zip->numFiles<2||$zip->numFiles>402)pnFail(422,'Invalid entry count.');
    $raw=$zip->getFromName('deployment-manifest.json');
    $signature=$zip->getFromName('deployment-manifest.sig');
    $rootToken=(string)getenv('MCP_API_TOKEN');
    $key=hash_hmac('sha256','alotamirart/deployment-package/v1',$rootToken);
    if (!is_string($raw)||!is_string($signature)
        ||!hash_equals(hash_hmac('sha256',$raw,$key),trim($signature)))pnFail(422,'Package signature invalid.');
    $manifest=json_decode($raw,true);
    if (!is_array($manifest)||($manifest['app_id']??'')!=='alotamirart-production-v1'
        ||($manifest['protocol_version']??0)!==2
        ||($manifest['git_commit']??'')!==$state['source_sha']
        ||!is_array($manifest['files']??null))pnFail(422,'Package manifest does not match deployment.');
    $files=$manifest['files'];
    $deleted=$manifest['deleted']??null;
    $base=$manifest['base_commit']??null;
    if (($manifest['source_ref']??'')!=='refs/heads/main'
        ||!is_string($base)||!preg_match('/^[a-f0-9]{40}$/D',$base)
        ||!is_array($deleted)||count($files)+count($deleted)>400
        ||(!count($files)&&!count($deleted)))pnFail(422,'Invalid signed release plan.');
    $seen=[];
    foreach ($deleted as $path) {
        if (!is_string($path)||!pnSafe($path)||isset($files[$path])||isset($seen[$path]))pnFail(422,'Invalid deleted path.');
        $seen[$path]=true;
    }
    $previous=pnRoot().'/last-success.json';
    $last=is_file($previous)?json_decode((string)file_get_contents($previous),true):null;
    if (is_array($last)&&($last['sha']??null)!==$base)pnFail(409,'Production release base is stale.');
    $members=[];$stage=pnOperation($id).'/stage';
    if (!is_dir($stage)&&!mkdir($stage,0700))pnFail(500,'Staging directory unavailable.');
    $total=0;
    for ($i=0;$i<$zip->numFiles;$i++) {
        $entry=$zip->getNameIndex($i);
        if (!is_string($entry)||isset($members[$entry]))pnFail(422,'Duplicate ZIP entry.');
        $members[$entry]=true;
        if ($entry==='deployment-manifest.json'||$entry==='deployment-manifest.sig')continue;
        if (!isset($files[$entry])||!pnSafe($entry))pnFail(422,'Unlisted or disallowed ZIP path.');
        $stat=$zip->statIndex($i);
        $kind=($stat['external_attributes']??0)>>16 & 0170000;
        if ($kind===0120000)pnFail(422,'ZIP symlinks are forbidden.');
        $bytes=(int)($stat['size']??-1);
        if ($bytes<0||$bytes>3*1024*1024||($total+=$bytes)>35*1024*1024)pnFail(422,'ZIP file size limit exceeded.');
        $body=$zip->getFromIndex($i);
        if (!is_string($body)||!is_string($files[$entry])||!preg_match('/^[a-f0-9]{64}$/D',$files[$entry])
            ||!hash_equals($files[$entry],hash('sha256',$body)))pnFail(422,'File hash mismatch.');
        if (file_put_contents($stage.'/'.hash('sha256',$entry),$body,LOCK_EX)!==strlen($body))pnFail(500,'Failed staging package.');
    }
    if (count($members)!==count($files)+2)pnFail(422,'Package and manifest do not match.');
    $root=realpath(dirname(__DIR__,2));
    if (!$root)pnFail(500,'Web root unavailable.');
    foreach ($files as $relative=>$hash)pnGuardTarget($root,(string)$relative);
    foreach ($deleted as $relative)pnGuardTarget($root,$relative);
    $state['status']='verified';$state['stage']='verified';$state['progress']=100;
    $state['files']=$files;
    $state['deleted']=$deleted;
    $state['base_commit']=$base;
    $state['preflight']=[['label'=>'signature','status'=>'ok'],['label'=>'allowlist','status'=>'ok'],['label'=>'hashes','status'=>'ok']];
    $state['diff']=['changed'=>array_keys($files),'deleted'=>$deleted,'pending_migrations'=>[]];
    pnReply(200,pnSave($state));
}
function pnRestore(array $state): void {
    $root=realpath(dirname(__DIR__,2));
    if (!$root)return;
    foreach (($state['originals']??[]) as $relative=>$exists) {
        $target=pnGuardTarget($root,(string)$relative);
        $backup=pnOperation($state['id']).'/backup/'.hash('sha256',$relative);
        if ($exists && is_file($backup)) @copy($backup,$target);
        elseif (!$exists) @unlink($target);
    }
}
function pnApply(string $id): never {
    $lock=pnLock();$state=pnState($id);
    if ($state['status']==='completed')pnReply(200,$state);
    if (!in_array($state['stage'],['verified','backed_up','switched'],true))pnFail(409,'Operation is not verified or runnable.');
    $root=realpath(dirname(__DIR__,2));
    if (!$root)pnFail(500,'Website root is not readable.');
    $files=$state['files']??[];
    $deleted=$state['deleted']??[];
    if ($state['stage']==='verified') {
        $backup=pnOperation($id).'/backup';
        if (!is_dir($backup)&&!mkdir($backup,0700))pnFail(500,'Cannot create backups.');
        $originals=[];
        foreach (array_merge(array_keys($files),$deleted) as $relative) {
            $target=pnGuardTarget($root,$relative);
            $exists=is_file($target);
            $originals[$relative]=$exists;
            if ($exists&&!copy($target,$backup.'/'.hash('sha256',$relative)))pnFail(500,'Could not back up application file.');
        }
        $state['originals']=$originals;
        $state['status']='running';$state['stage']='backed_up';$state['progress']=30;
        pnReply(200,pnSave($state));
    }
    if ($state['stage']==='backed_up') {
        $changed=[];
        try {
            foreach ($files as $relative=>$hash) {
                $target=pnGuardTarget($root,$relative);
                $directory=dirname($target);
                if (!is_dir($directory)&&!mkdir($directory,0755,true)&&!is_dir($directory))throw new RuntimeException('Cannot create target directory.');
                if (is_link($directory))throw new RuntimeException('Symlink target directory rejected.');
                $staged=pnOperation($id).'/stage/'.hash('sha256',$relative);
                if (!is_file($staged)||!hash_equals($hash,hash_file('sha256',$staged)))throw new RuntimeException('Stage file changed.');
                $temporary=$target.'.deploy-'.substr($id,0,10).'.'.pathinfo($target,PATHINFO_EXTENSION);
                if (!copy($staged,$temporary)||!rename($temporary,$target))throw new RuntimeException('Unable to install staged file.');
                $changed[]=$relative;
            }
            foreach ($deleted as $relative) {
                $target=pnGuardTarget($root,$relative);
                if (is_file($target)&&!unlink($target))throw new RuntimeException('Unable to remove deleted application file.');
            }
        } catch (Throwable $e) {
            pnRestore($state);
            $state['status']='failed';$state['error']='Deployment could not apply all staged files.';
            pnSave($state);
            pnFail(500,$state['error']);
        }
        if (function_exists('opcache_reset'))@opcache_reset();
        $state['stage']='switched';$state['progress']=85;
        pnReply(200,pnSave($state));
    }
    foreach ($files as $relative=>$hash) {
        $target=pnGuardTarget($root,$relative);
        if (!is_file($target)||!hash_equals($hash,hash_file('sha256',$target))) {
            pnRestore($state);
            $state['status']='failed';$state['error']='Installed file hash mismatch; backup restored.';
            pnSave($state);pnFail(500,$state['error']);
        }
    }
    foreach ($deleted as $relative) {
        if (file_exists(pnGuardTarget($root,$relative))) {
            pnRestore($state);
            $state['status']='failed';$state['error']='Deleted app path remains; backup restored.';
            pnSave($state);pnFail(500,$state['error']);
        }
    }
    $last=['sha'=>$state['source_sha'],'at'=>gmdate('c'),'count'=>count($files),
        'files'=>$files,'deleted'=>$deleted,'base_commit'=>$state['base_commit']??''];
    $raw=json_encode($last,JSON_UNESCAPED_SLASHES);
    if (file_put_contents(pnRoot().'/last-success.json',$raw,LOCK_EX)!==strlen($raw))pnFail(500,'Could not record completed deployment.');
    $state['status']='completed';$state['stage']='completed';$state['progress']=100;
    pnReply(200,pnSave($state));
}
function pnHealth(): never {
    $expected=$_GET['expected_sha']??null;
    if ($expected!==null)pnSha($expected);
    $path=pnRoot().'/last-success.json';
    $last=is_file($path)?json_decode((string)file_get_contents($path),true):null;
    if (!is_array($last))pnReply(503,['status'=>'error','checks'=>['deployment'=>['ok'=>false,'blocking'=>true]]]);
    $root=realpath(dirname(__DIR__,2));
    $checks=['deployment'=>['ok'=>!$expected||$last['sha']===$expected,'blocking'=>true]];
    foreach (($last['files']??[]) as $relative=>$sha) {
        if (!pnSafe($relative)||!is_file($root.'/'.$relative)||!hash_equals($sha,hash_file('sha256',$root.'/'.$relative))) {
            $checks['files']=['ok'=>false,'blocking'=>true];break;
        }
    }
    foreach (($last['deleted']??[]) as $relative) {
        if (!pnSafe($relative)||file_exists($root.'/'.$relative)) {
            $checks['deleted']=['ok'=>false,'blocking'=>true];break;
        }
    }
    $checks['deleted']??=['ok'=>true,'blocking'=>true];
    $checks['files']??=['ok'=>true,'blocking'=>true];
    $healthy=$checks['deployment']['ok']&&$checks['files']['ok']&&$checks['deleted']['ok'];
    pnReply($healthy?200:503,['status'=>$healthy?'ok':'error','commit'=>$last['sha'],'checks'=>$checks]);
}

$token=(string)getenv('MCP_API_TOKEN');
if (strlen($token)<32)pnFail(503,'Deployment credential not configured.');
$expected=hash_hmac('sha256','alotamirart/deployment-auth/v1',$token);
$bearer=$_SERVER['HTTP_AUTHORIZATION']??$_SERVER['REDIRECT_HTTP_AUTHORIZATION']??'';
if (!preg_match('/^Bearer ([a-f0-9]{64})$/i',$bearer,$match)||!hash_equals($expected,strtolower($match[1])))pnFail(401,'Unauthorized.');
// A valid HMAC-derived deployment credential is the sole deployment gate;
// no separate production enable flag is required (PlayNexus parity).
$action=(string)($_GET['action']??'');
$method=$_SERVER['REQUEST_METHOD']??'GET';
if ($method==='GET'&&$action==='ready') {
    $lastFile=pnRoot().'/last-success.json';
    $last=is_file($lastFile)?json_decode((string)file_get_contents($lastFile),true):null;
    pnReply(200,['status'=>'ok','ready'=>true,'protocol_version'=>2,
        'last_commit'=>is_array($last)?($last['sha']??null):null]);
}
if ($method==='POST'&&$action==='upload/chunk')pnUpload();
if ($method==='POST'&&$action==='upload/complete')pnComplete();
if ($method==='POST'&&preg_match('~^([a-f0-9]{32})/verify$~D',$action,$m))pnVerify($m[1]);
if ($method==='POST'&&preg_match('~^([a-f0-9]{32})/apply$~D',$action,$m))pnApply($m[1]);
if ($method==='GET'&&preg_match('~^([a-f0-9]{32})/status$~D',$action,$m))pnReply(200,pnState($m[1]));
if ($method==='GET'&&$action==='health')pnHealth();
pnFail(404,'Unknown deployment action.');
