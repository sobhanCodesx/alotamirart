<?php
$root = dirname(__DIR__);
$receiver = file_get_contents($root . '/api/deploy.php');
$sender = file_get_contents($root . '/scripts/alo_deploy.py');
$workflow = file_get_contents($root . '/.github/workflows/deploy-alo-production.yml');
foreach (['MCP_API_TOKEN','ALO_DEPLOY_ENABLED','hash_equals','ZipArchive','flock','deployAllowPath','backup','original','hash_hmac','X_DEPLOY_SIGNATURE'] as $part) {
    if (strpos($receiver,$part)===false) {fwrite(STDERR,"Deploy receiver contract missing $part\n");exit(1);}
}
foreach (['refs/heads/main','diff','deployment-auth/v1','deployment-package/v1','sha256','https://'] as $part) {
    if (strpos($sender,$part)===false) {fwrite(STDERR,"Deploy sender contract missing $part\n");exit(1);}
}
if (strpos($workflow,'secrets.MCP_API_TOKEN')===false
    || strpos($workflow,"github.ref == 'refs/heads/main'")===false
    || strpos($workflow,'scripts/alo_deploy_playnexus.py')===false
    || strpos($workflow,"'content-requests/*.json'")!==false) {
    fwrite(STDERR,"Deploy workflow is not main-only, content-separated or PlayNexus-compatible\n");exit(1);
}
$agent=file_get_contents($root.'/api/deployment-agent/index.php');
$client=file_get_contents($root.'/scripts/alo_deploy_playnexus.py');
foreach (['upload/chunk','upload/complete','/verify','/apply','/status','health','hash_equals','ZipArchive','flock','pnGuardTarget','pnRestore','deployment-manifest.sig','MCP_API_TOKEN'] as $token) {
    if (strpos($agent,$token)===false) {fwrite(STDERR,"Staged deployment receiver missing $token\n");exit(1);}
}
foreach (['512 * 1024','multipart/form-data','deployment-manifest.sig','source_sha','/verify','/apply','health','PLAYNEXUS_STYLE_DEPLOY_VERIFIED_OK'] as $token) {
    if (strpos($client,$token)===false) {fwrite(STDERR,"Staged deployment client missing $token\n");exit(1);}
}
echo "Deploy static contract passed\n";

$release=file_get_contents($root.'/scripts/alo_release.py');
foreach ([$release,$agent] as $policySource) {
    if (strpos($policySource,"'.htaccess'") === false && strpos($policySource,'".htaccess"') === false) {
        fwrite(STDERR,"Narrow root .htaccess deploy allowlist is missing\n"); exit(1);
    }
}
