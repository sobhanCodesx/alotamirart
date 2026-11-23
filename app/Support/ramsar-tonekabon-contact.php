<?php
declare(strict_types=1);

const ALO_WEST_MAZANDARAN_PHONE = '09960274770';

function aloRamsarTonekabonCity(array $city): bool
{
    return in_array($city['slug'] ?? '', ['ramsar', 'tonkabon', 'tonekabon'], true)
        || in_array(trim(str_replace('ك', 'ک', (string)($city['name'] ?? ''))), ['رامسر', 'تنکابن'], true);
}

function aloRamsarTonekabonPost(array $post): bool
{
    $title = str_replace('ك', 'ک', strip_tags((string)($post['title'] ?? '')));
    return preg_match('/(?<![\p{L}\p{N}])(?:تنکابن|رامسر)(?![\p{L}\p{N}])/u', $title) === 1
        || preg_match('/(?:^|[-_])(?:ramsar|tonkabon|tonekabon)(?:$|[-_])/i', (string)($post['slug'] ?? '')) === 1;
}

function aloRegionalContactText(string $html): string
{
    $mobile = '(?:[0۰٠]|(?:\+|00)?[9۹٩][8۸٨])[9۹٩][0-9۰-۹٠-٩]{9}';
    // Process contact attributes inside tags, then visible text outside tags.
    $html = preg_replace('~(tel:)' . $mobile . '(?![\p{N}])~iu', '${1}' . ALO_WEST_MAZANDARAN_PHONE, $html);
    $html = preg_replace('~((?:wa\.me/|api\.whatsapp\.com/send\?phone=))' . $mobile . '(?![\p{N}])~iu', '${1}989960274770', $html);
    return preg_replace('~<[^>]+>(*SKIP)(*F)|(?<![\p{N}])' . $mobile . '(?![\p{N}])~u', ALO_WEST_MAZANDARAN_PHONE, $html);
}
