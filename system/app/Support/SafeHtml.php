<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class SafeHtml
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'code', 'div', 'em', 'figcaption', 'figure',
        'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'img', 'li', 'ol', 'p', 'pre',
        'span', 'strong', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr',
        'u', 'ul',
    ];

    private const ALLOWED_ATTRIBUTES = [
        '*' => ['aria-label', 'class', 'id', 'title'],
        'a' => ['href', 'rel', 'target'],
        'img' => ['alt', 'decoding', 'height', 'loading', 'src', 'width'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan', 'scope'],
    ];

    private const DROP_WITH_CONTENT = ['iframe', 'object', 'script', 'style'];

    public static function clean(mixed $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);

        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="safe-html-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('safe-html-root');

        if (! $root instanceof DOMElement) {
            return e(strip_tags($html));
        }

        self::sanitizeChildren($root);

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return $output;
    }

    private static function sanitizeChildren(DOMNode $node): void
    {
        for ($child = $node->firstChild; $child !== null;) {
            $next = $child->nextSibling;

            if ($child instanceof DOMElement) {
                self::sanitizeElement($child);
            } elseif (! in_array($child->nodeType, [XML_TEXT_NODE, XML_CDATA_SECTION_NODE], true)) {
                $node->removeChild($child);
            }

            $child = $next;
        }
    }

    private static function sanitizeElement(DOMElement $element): void
    {
        $tag = strtolower($element->tagName);

        if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
            $element->parentNode?->removeChild($element);

            return;
        }

        if (! in_array($tag, self::ALLOWED_TAGS, true)) {
            self::unwrap($element);

            return;
        }

        self::sanitizeAttributes($element, $tag);
        self::sanitizeChildren($element);
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::ALLOWED_ATTRIBUTES['*'], self::ALLOWED_ATTRIBUTES[$tag] ?? []);

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (! in_array($name, $allowed, true) || ! self::validAttributeValue($tag, $name, $value)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $rel = collect(explode(' ', $element->getAttribute('rel')))
                ->merge(['noopener', 'noreferrer'])
                ->filter()
                ->unique()
                ->implode(' ');

            $element->setAttribute('rel', $rel);
        }
    }

    private static function validAttributeValue(string $tag, string $name, string $value): bool
    {
        if ($name === 'class') {
            return preg_match('/^[A-Za-z0-9:_\\-\\s]+$/', $value) === 1;
        }

        if ($name === 'id') {
            return preg_match('/^[A-Za-z][A-Za-z0-9:_\\-.]*$/', $value) === 1;
        }

        if (in_array($name, ['href', 'src'], true)) {
            return self::validUrl($value, $tag === 'img');
        }

        if ($name === 'target') {
            return in_array($value, ['_blank', '_self', '_parent', '_top'], true);
        }

        if (in_array($name, ['colspan', 'height', 'rowspan', 'width'], true)) {
            return preg_match('/^[1-9][0-9]{0,3}$/', $value) === 1;
        }

        if ($name === 'scope') {
            return in_array($value, ['col', 'colgroup', 'row', 'rowgroup'], true);
        }

        if ($name === 'loading') {
            return in_array($value, ['eager', 'lazy'], true);
        }

        if ($name === 'decoding') {
            return in_array($value, ['async', 'auto', 'sync'], true);
        }

        return true;
    }

    private static function validUrl(string $value, bool $image): bool
    {
        if ($value === '' || str_starts_with($value, '#') || str_starts_with($value, '/')) {
            return true;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if ($scheme === null) {
            return true;
        }

        $allowed = $image ? ['http', 'https'] : ['http', 'https', 'mailto', 'tel'];

        return in_array(strtolower($scheme), $allowed, true);
    }

    private static function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }
}
