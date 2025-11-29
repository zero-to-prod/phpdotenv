<?php

namespace Zerotoprod\Phpdotenv;

/**
 * @link https://github.com/zero-to-prod/phpdotenv
 */
class Phpdotenv
{
    /**
     * A pure function that parses an array of lines into key-value pairs.
     *
     * @param  array  $lines
     *
     * @return array
     * @link https://github.com/zero-to-prod/phpdotenv
     */
    public static function parse(array $lines): array
    {
        $parsed = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (isset($line[0]) && $line[0] === '#') {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);

                $key = trim($key);
                $value = trim($value);

                if (strlen($value) >= 2) {
                    if (($value[0] === '"' && $value[-1] === '"') ||
                        ($value[0] === "'" && $value[-1] === "'")) {
                        $value = substr($value, 1, -1);
                    }
                }

                $parsed[$key] = $value;
            }
        }

        return $parsed;
    }
}