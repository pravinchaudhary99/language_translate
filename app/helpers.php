<?php

use App\Models\Translation;
use Stichoza\GoogleTranslate\GoogleTranslate;

if (! function_exists('getPhraseParameters')) {
    function getPhraseParameters(string $phrase): ?array
    {
        preg_match_all('/(?<!\w):(\w+)/', $phrase, $matches);

        if (empty($matches[1])) {
            return null;
        }

        return $matches[1];
    }
}

if(! function_exists('translate')) {
    function translate($sourceLang, $targetLang, $text) {
        return (new GoogleTranslate())
            ->preserveParameters()
            ->setSource($sourceLang)
            ->setTarget($targetLang)
            ->translate($text);
    }
}

if (! function_exists('buildPhrasesTree')) {
    function buildPhrasesTree($phrases, $locale): array
    {
        $tree = [];

        foreach ($phrases as $phrase) {
            if ($phrase->file->file_name === "$locale.json") {
                $tree[$locale][$phrase->file->file_name][$phrase->key] = ! blank($phrase->value) ? $phrase->value : $phrase->source->value;

                continue;
            }
            setArrayValue(
                array: $tree[$locale][$phrase->file->file_name],
                key: $phrase->key,
                value: ! blank($phrase->value) ? $phrase->value : $phrase->source->value
            );
        }

        return $tree;
    }
}


if (! function_exists('setArrayValue')) {
    function setArrayValue(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = preg_split('/\.(?=[^.]*[^.])/', $key);

        foreach ($keys as $i => $key) {
            if (blank($value)) {
                dd($key, $value);
            }

            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $lastKey = array_shift($keys);

        if (! blank($lastKey)) {
            $array[$lastKey] = $value;
        }

        return $array;
    }
}

if(! function_exists('translateLanguage')) {
    function translateLanguage(){
        return Translation::with('language:id,name,code')->get()->map(function($query) {
            return [
                'name' => $query->language->name ?? '',
                'code' => $query->language->code ?? ''
            ];
        });
    }
}