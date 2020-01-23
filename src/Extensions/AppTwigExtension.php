<?php

namespace App\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppTwigExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('truncate', '\app\extensions\twig_truncate_filter', ['needs_environment' => true]),
            new TwigFilter('wordwrap', '\app\extensions\twig_wordwrap_filter', ['needs_environment' => true]),
            new TwigFilter('format_text', '\app\extensions\twig_format_text_filter', ['needs_environment' => true]),
        ];
    }
}

    use Twig\Environment;

    function twig_format_text_filter(Environment $env, $value) {
        $value = nl2br($value);

        return $value;
    }

    function twig_truncate_filter(Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length, $env->getCharset())) . $separator;
        }

        return $value;
    }

    function twig_wordwrap_filter(Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $sentences = [];

        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());

        $pieces = mb_split($separator, $value);
        mb_regex_encoding($previous);

        foreach ($pieces as $piece) {
            while (!$preserve && mb_strlen($piece, $env->getCharset()) > $length) {
                $sentences[] = mb_substr($piece, 0, $length, $env->getCharset());
                $piece = mb_substr($piece, $length, 2048, $env->getCharset());
            }

            $sentences[] = $piece;
        }

        return implode($separator, $sentences);
    }
