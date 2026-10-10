<?php
/** Authenticated, narrowly scoped Mahshahr contact update. No SQL supplied by callers. */
declare(strict_types=1);

function aloCityContactTool(): array {
    return [
        'name'=>'set_mahshahr_contact_numbers',
        'description'=>'Set only the contact_number field on all articles and brand articles whose title includes Mahshahr (published and draft). Requires explicit confirmation.',
        'inputSchema'=>[
            'type'=>'object',
            'properties'=>[
                'city'=>['type'=>'string','const'=>'ماهشهر'],
                'phone'=>['type'=>'string','pattern'=>'^09[0-9]{9}$'],
                'confirm'=>['type'=>'boolean','const'=>true],
            ],
            'required'=>['city','phone','confirm']
        ],
    ];
}

function aloCityContactValidate(array $args): string {
    if (($args['confirm'] ?? null) !== true || ($args['city'] ?? null) !== 'ماهشهر') {
        throw new InvalidArgumentException('Explicit Mahshahr city confirmation required.');
    }
    $phone=$args['phone'] ?? null;
    if (!is_string($phone) || !preg_match('/^09[0-9]{9}$/D', $phone)) {
        throw new InvalidArgumentException('Contact must be an 11-digit Iranian mobile number.');
    }
    return $phone;
}

function aloCityContactExecute(array $args, ?PDO $db=null): array {
    $phone=aloCityContactValidate($args);
    $db ??= mcpDb();
    $tables=['article'=>'posts','brand_article'=>'post_brand'];
    $pattern='%ماهشهر%';
    $planned=[];
    $counts=[];
    $updated=[];
    $db->beginTransaction();
    try {
        foreach ($tables as $type=>$table) {
            $stmt=$db->prepare('SELECT id,contact_number FROM `'.$table.'` WHERE title LIKE ? ORDER BY id ASC');
            $stmt->execute([$pattern]);
            $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $counts[$type]=count($rows);
            $updated[$type]=[];
            foreach ($rows as $row) {
                $planned[]=[$type,$table,(int)$row['id'],$row['contact_number']];
            }
        }
        if (!$planned) throw new RuntimeException('No Mahshahr posts found; refusing empty bulk update.');
        foreach ($planned as [$type,$table,$id,$previous]) {
            if ((string)($previous ?? '') === $phone) continue;
            $stmt=$db->prepare('UPDATE `'.$table.'` SET contact_number=? WHERE id=? AND title LIKE ?');
            $stmt->execute([$phone,$id,$pattern]);
            if ($stmt->rowCount() !== 1) throw new RuntimeException('A matching post changed while updating contact number.');
            $updated[$type][]=$id;
        }
        // Verify all records (including those already correct) inside the same transaction.
        foreach ($tables as $type=>$table) {
            $stmt=$db->prepare('SELECT COUNT(*) FROM `'.$table.'` WHERE title LIKE ? AND (contact_number IS NULL OR contact_number <> ?)');
            $stmt->execute([$pattern,$phone]);
            if ((int)$stmt->fetchColumn() !== 0) throw new RuntimeException('Contact number verification failed.');
            $stmt=$db->prepare('SELECT COUNT(*) FROM `'.$table.'` WHERE title LIKE ?');
            $stmt->execute([$pattern]);
            if ((int)$stmt->fetchColumn() !== $counts[$type]) throw new RuntimeException('Mahshahr post set changed; rolling back.');
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        throw $e;
    }
    return [
        'city'=>'ماهشهر',
        'phone'=>$phone,
        'matched'=>$counts,
        'updated_count'=>['article'=>count($updated['article']),'brand_article'=>count($updated['brand_article'])],
        'updated_ids'=>$updated,
        'verified'=>true,
    ];
}
