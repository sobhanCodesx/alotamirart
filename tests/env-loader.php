<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/Support/env.php';
$file = tempnam(sys_get_temp_dir(), 'alo-test-');
if ($file === false) exit(1);
file_put_contents($file, "ALO_TEST_VALUE=hello\nALO_TEST_QUOTE=\"hello # world\"\n");
try {
    aloLoadEnv($file);
    if (getenv('ALO_TEST_VALUE') !== 'hello' || getenv('ALO_TEST_QUOTE') !== 'hello # world') exit(1);
} finally {
    unlink($file);
    putenv('ALO_TEST_VALUE');
    putenv('ALO_TEST_QUOTE');
}
echo "Env loader passed\n";