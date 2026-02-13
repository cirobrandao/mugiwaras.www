<?php

declare(strict_types=1);

namespace App\Core;

final class Markdown
{
    public static function toHtml(string $markdown): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($markdown));
        if ($text === '') {
            return '';
        }
        $lines = preg_split('/\n/u', $text) ?: [];
        $htmlBlocks = [];
        $count = count($lines);
        $idx = 0;

        while ($idx < $count) {
            $line = rtrim((string)$lines[$idx]);
            $trimmed = trim($line);

            if ($trimmed === '') {
                $idx++;
                continue;
            }

            if (preg_match('/^```([a-zA-Z0-9_-]*)\s*$/u', $trimmed, $codeOpen) === 1) {
                $lang = trim((string)($codeOpen[1] ?? ''));
                $codeLines = [];
                $idx++;
                while ($idx < $count) {
                    $current = rtrim((string)$lines[$idx]);
                    if (preg_match('/^```\s*$/u', trim($current)) === 1) {
                        break;
                    }
                    $codeLines[] = $current;
                    $idx++;
                }
                $classAttr = $lang !== '' ? ' class="language-' . htmlspecialchars($lang, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"' : '';
                $htmlBlocks[] = '<pre><code' . $classAttr . '>' . htmlspecialchars(implode("\n", $codeLines), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code></pre>';
                $idx++;
                continue;
            }

            if (preg_match('/^(#{1,6})\h*(.+)$/u', $trimmed, $h) === 1) {
                $level = mb_strlen((string)$h[1]);
                $title = trim((string)$h[2]);
                if ($title === '') {
                    $title = '#';
                }
                $htmlBlocks[] = '<h' . $level . '>' . self::parseInline($title) . '</h' . $level . '>';
                $idx++;
                continue;
            }

            if (preg_match('/^(?:[-*_]\h*){3,}$/u', $trimmed) === 1) {
                $htmlBlocks[] = '<hr>';
                $idx++;
                continue;
            }

            if (preg_match('/^>\h?.+/u', $trimmed) === 1) {
                $quoteLines = [];
                while ($idx < $count) {
                    $current = trim((string)$lines[$idx]);
                    if ($current === '' || preg_match('/^>\h?.+/u', $current) !== 1) {
                        break;
                    }
                    $quoteLines[] = preg_replace('/^>\h?/u', '', $current) ?? $current;
                    $idx++;
                }
                $quote = implode('<br>', array_map(static fn (string $quoteLine): string => self::parseInline($quoteLine), $quoteLines));
                $htmlBlocks[] = '<blockquote>' . $quote . '</blockquote>';
                continue;
            }

            if (preg_match('/^[-*+]\h+.+/u', $trimmed) === 1) {
                $items = [];
                while ($idx < $count) {
                    $current = trim((string)$lines[$idx]);
                    if (preg_match('/^[-*+]\h+.+/u', $current) !== 1) {
                        break;
                    }
                    $itemText = preg_replace('/^[-*+]\h+/u', '', $current) ?? $current;
                    $items[] = '<li>' . self::parseInline($itemText) . '</li>';
                    $idx++;
                }
                $htmlBlocks[] = '<ul>' . implode('', $items) . '</ul>';
                continue;
            }

            if (preg_match('/^\d+\.\h+.+/u', $trimmed) === 1) {
                $items = [];
                while ($idx < $count) {
                    $current = trim((string)$lines[$idx]);
                    if (preg_match('/^\d+\.\h+.+/u', $current) !== 1) {
                        break;
                    }
                    $itemText = preg_replace('/^\d+\.\h+/u', '', $current) ?? $current;
                    $items[] = '<li>' . self::parseInline($itemText) . '</li>';
                    $idx++;
                }
                $htmlBlocks[] = '<ol>' . implode('', $items) . '</ol>';
                continue;
            }

            $paragraphLines = [];
            while ($idx < $count) {
                $currentRaw = rtrim((string)$lines[$idx]);
                $current = trim($currentRaw);
                if ($current === '') {
                    break;
                }
                if (
                    preg_match('/^(#{1,6})\h*(.+)$/u', $current) === 1
                    || preg_match('/^```([a-zA-Z0-9_-]*)\s*$/u', $current) === 1
                    || preg_match('/^>\h?.+/u', $current) === 1
                    || preg_match('/^[-*+]\h+.+/u', $current) === 1
                    || preg_match('/^\d+\.\h+.+/u', $current) === 1
                    || preg_match('/^(?:[-*_]\h*){3,}$/u', $current) === 1
                ) {
                    if (!empty($paragraphLines)) {
                        break;
                    }
                }
                $paragraphLines[] = self::parseInline($currentRaw);
                $idx++;
            }
            if (!empty($paragraphLines)) {
                $htmlBlocks[] = '<p>' . implode('<br>', $paragraphLines) . '</p>';
                continue;
            }

            $idx++;
        }

        return implode("\n", $htmlBlocks);
    }

    public static function toPlainText(string $markdown): string
    {
        $html = self::toHtml($markdown);
        if ($html === '') {
            return '';
        }
        $decoded = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/\s+/u', ' ', $decoded) ?? $decoded;
        return trim($normalized);
    }

    private static function parseInline(string $input): string
    {
        $codeTokens = [];
        $text = preg_replace_callback('/`([^`]+)`/u', static function (array $m) use (&$codeTokens): string {
            $token = '§§C' . count($codeTokens) . '§§';
            $codeTokens[$token] = '<code>' . htmlspecialchars((string)($m[1] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</code>';
            return $token;
        }, $input) ?? $input;

        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $inlineHtmlTokens = [];

        $text = preg_replace_callback('/!\[([^\]]*)\]\(([^)]+)\)/u', static function (array $m) use (&$inlineHtmlTokens): string {
            $alt = htmlspecialchars((string)($m[1] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $url = self::safeUrl((string)($m[2] ?? ''));
            if ($url === '') {
                return (string)($m[0] ?? '');
            }
            $token = '§§H' . count($inlineHtmlTokens) . '§§';
            $inlineHtmlTokens[$token] = '<img src="' . $url . '" alt="' . $alt . '">';
            return $token;
        }, $text) ?? $text;

        $text = preg_replace_callback('/\[([^\]]+)\]\(([^)]+)\)/u', static function (array $m) use (&$inlineHtmlTokens): string {
            $label = htmlspecialchars((string)($m[1] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $url = self::safeUrl((string)($m[2] ?? ''));
            if ($url === '') {
                return (string)($m[0] ?? '');
            }
            $token = '§§H' . count($inlineHtmlTokens) . '§§';
            $inlineHtmlTokens[$token] = '<a href="' . $url . '" target="_blank" rel="noopener noreferrer">' . $label . '</a>';
            return $token;
        }, $text) ?? $text;

        $text = preg_replace('/\*\*(.+?)\*\*/us', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/__(.+?)__/us', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/~~(.+?)~~/us', '<del>$1</del>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/us', '<em>$1</em>', $text) ?? $text;
        $text = preg_replace('/(?<!_)_(?!_)(.+?)(?<!_)_(?!_)/us', '<em>$1</em>', $text) ?? $text;

        foreach ($inlineHtmlTokens as $token => $html) {
            $text = str_replace($token, $html, $text);
        }

        foreach ($codeTokens as $token => $html) {
            $text = str_replace($token, $html, $text);
        }

        return $text;
    }

    private static function safeUrl(string $url): string
    {
        $trimmed = trim($url);
        if ($trimmed === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $trimmed) === 1) {
            $parts = parse_url($trimmed);
            $path = (string)($parts['path'] ?? '');
            if ($path !== '' && str_starts_with($path, '/uploads/')) {
                $normalized = $path;
                if (!empty($parts['query'])) {
                    $normalized .= '?' . $parts['query'];
                }
                if (!empty($parts['fragment'])) {
                    $normalized .= '#' . $parts['fragment'];
                }
                return htmlspecialchars($normalized, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
        }

        if (preg_match('/^(https?:\/\/|\/|#|\.\/|\.\.\/|[a-zA-Z0-9_\-]+\/)/i', $trimmed) !== 1) {
            return '';
        }
        return htmlspecialchars($trimmed, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function allMatch(array $items, string $pattern): bool
    {
        foreach ($items as $item) {
            if (preg_match($pattern, (string)$item) !== 1) {
                return false;
            }
        }
        return true;
    }
}