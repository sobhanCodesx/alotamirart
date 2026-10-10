<?php
/**
 * Correct user-visible Persian punctuation without changing stored content.
 * URL hosts, email addresses, version numbers and decimal values remain intact.
 */
declare(strict_types=1);

function aloPersianTypography(string $text): string
{
    if ($text === '') return $text;

    // Split out technical fragments that must never be rewritten.
    $parts = preg_split(
        '~((?:https?://|www\.)[^\s<>]+|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}|(?<![\p{L}\d])\d+(?:[.,]\d+)+(?![\p{L}\d]))~iu',
        $text,
        -1,
        PREG_SPLIT_DELIM_CAPTURE
    );
    if (!is_array($parts)) return $text;
    foreach ($parts as $i => &$part) {
        if ($i % 2 === 1 || $part === '') continue;
        // ASCII commas between Persian words are common in old CMS records.
        $part = preg_replace('/(?<=\p{Arabic})\s*,\s*(?=\p{Arabic})/u', '، ', $part) ?? $part;
        $part = preg_replace('/\s*،\s*/u', '، ', $part) ?? $part;
        // Old descriptions contain sequences of periods, e.g. "و....".
        $part = preg_replace('/\.{2,}/u', '…', $part) ?? $part;
        // A full stop follows its preceding Persian word and precedes a space.
        $part = preg_replace('/(?<=\p{Arabic})\s*\.\s*(?=\p{Arabic})/u', '. ', $part) ?? $part;
        $part = preg_replace('/(?<=\p{Arabic})\s+\./u', '.', $part) ?? $part;
        // Also separate the label from its description when written "شامل:موضوع".
        $part = preg_replace('/(?<=\p{Arabic})\s*:\s*(?=\p{Arabic})/u', ': ', $part) ?? $part;
    }
    unset($part);
    return implode('', $parts);
}
