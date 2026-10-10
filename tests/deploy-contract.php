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
if (strpos($workflow,'secrets.MCP_API_TOKEN')===false || strpos($workflow,'environment: production')===false) {
    fwrite(STDERR,"Deploy workflow missing mandatory guard\n");exit(1);
}
echo "Deploy static contract passed\n";
