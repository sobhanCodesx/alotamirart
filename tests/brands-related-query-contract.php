<?php
// Regression: Brand detail related-item query must bind explicit named parameters
// and never dereference a failed SELECT result.
$source=file_get_contents(__DIR__.'/../classes/app/Brands.php');
if ($source===false) { fwrite(STDERR,"Brands source missing\n");exit(1); }
$checks=[
  "brand_id = :brand_id",
  "id <> :excluded_id",
  "'brand_id' =>",
  "'excluded_id' =>",
  "instanceof PDOStatement",
  "$side_brand = []",
];
foreach($checks as $required){
  if (strpos($source,$required)===false) {
     fwrite(STDERR,"Brands related-query regression: missing ".$required."\n");
     exit(1);
  }
}
echo "BRANDS_RELATED_QUERY_CONTRACT_OK\n";
