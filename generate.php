<?php

if ($argc < 2) {
    throw new RuntimeException('Missing filename');
}

$filename = $argv[1];
$themes = ['Generiek'];
$types = [];
$modifiers = [];
$matchTypes = [
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "+${modifier} +${type}";
        }
        return "+${theme} +${modifier} +${type}";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "+${modifier}" . strtolower($type);
        }
        return "+${theme} +${modifier}" . strtolower($type);
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "\"${modifier} ${type}\"";
        }
        return "\"${theme} ${modifier} ${type}\"";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "\"${modifier}" . strtolower($type) . "\"";
        }
        return "\"${theme} ${modifier}" . strtolower($type) . "\"";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "";
        }
        return "\"${modifier} ${type} ${theme}\"";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "";
        }
        return "\"${modifier}" . strtolower($type) .  " ${theme}\"";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "[${modifier} ${type}]";
        }
        return "[${theme} ${modifier} ${type}]";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return "[${modifier}" . strtolower($type) . "]";
        }
        return "[${theme} ${modifier}" . strtolower($type) . "]";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return '';
        }
        return "[${modifier} ${type} ${theme}]";
    },
    function ($theme, $modifier, $type) {
        if ($theme === 'Generiek') {
            return '';
        }
        return "[${modifier}" . strtolower($type) . " ${theme}]";
    },
];
$stdout = fopen('php://stdout', 'w');

$handle = fopen($filename, "r");
if ($handle) {
    $count = 0;
    while (($line = fgets($handle)) !== false) {
        if ($count !== 0) {
            $values = str_getcsv($line);
            if ($values[0] !== '') {
                $themes[] = $values[0];
            }
            if ($values[1] !== '') {
                $types[] = $values[1];
            }
            if ($values[2] !== '') {
                $modifiers[] = $values[2];
            }
        }
        ++$count;
    }

    fclose($handle);
} else {
    throw new RuntimeException('Could not read file');
}

foreach ($themes as $theme) {
    foreach ($types as $type) {
        foreach ($modifiers as $modifier) {
            foreach ($matchTypes as $matchType) {
                $result = $matchType($theme, $modifier, $type);
                if ($result !== '') {
                    fputcsv($stdout, [
                        $theme,
                        $result,
                    ]);
                }
            }
        }
    }
}
