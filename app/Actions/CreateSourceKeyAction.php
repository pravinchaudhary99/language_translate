<?php

namespace App\Actions;

use App\Models\Translation;
use App\Models\TranslationFile;

class CreateSourceKeyAction
{
    public static function execute(string $key, string $file, string $content): void
    {
        $sourceTranslation = Translation::where('source', true)->first();

        $sourceKey = $sourceTranslation->phrases()->create([
            'key' => $key,
            'value' => $content,
            'uuid' => str()->uuid(),
            'translation_file_id' => $file,
            'parameters' => getPhraseParameters($content),
            'group' => TranslationFile::find($file)?->name,
        ]);

        Translation::where('source', false)->get()->each(function ($translation) use ($sourceKey) {
            $isRoot = TranslationFile::find($sourceKey->file->id)?->is_root;
            $locale = $translation->language()->first()?->code;
            $translation->phrases()->create([
                'value' => null,
                'key' => $sourceKey->key,
                'group' => ($isRoot ? $locale : $sourceKey->group),
                'parameters' => $sourceKey->parameters,
                'translation_file_id' => ($isRoot ? TranslationFile::firstWhere('name', $locale)?->id : $sourceKey->file->id),
            ]);
        });
    }
}
