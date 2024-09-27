<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\TranslationsManager;
use Illuminate\Console\Command;
use App\Actions\SyncPhrasesAction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportTranslationsCommand extends Command
{
    public $manager;

    protected $signature = 'translations:import';

    protected $description = 'Sync translation all keys from the translation files to the database';

    public function __construct(TranslationsManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }
    
    public function handle()
    {
        $this->importLanguages();
    }

    protected function importLanguages()
    {
        if (! Schema::hasTable('languages') || Language::count() === 0) {
            $this->error('The ltu_languages table does not exist or is empty, please run the translations:install command first.');
            exit;
        }

        $language = Language::where('code', config('app.locale'))->first();
        if (! $language) {
            $this->error('Language with code '.config('app.locale').' not found'.PHP_EOL);

            exit;
        }

        if (! is_dir(lang_path()) || count(scandir(lang_path())) <= 2) {
            $this->error('We can\'t find any languages in your project, please run the lang:publish command first.');

            exit;
        }

        $translation = Translation::firstOrCreate([
            'source' => true,
            'language_id' => $language->id,
        ]);

        $this->syncTranslations($translation, $language->code);

        return $translation;
    }

    public function syncTranslations(Translation $translation, string $locale)
    {
        foreach ($this->manager->getTranslations($locale) as $file => $translations) {
            foreach (Arr::dot($translations) as $key => $value) {
                SyncPhrasesAction::execute($translation, $key, $value, $locale, $file);
            }
        }

        $this->syncMissingTranslations($translation);
    }

    public function syncMissingTranslations(Translation $source)
    {
        $translations = Translation::query()
                    ->with('language')
                    ->whereHas('language', function ($query) {
                        $query->where('code', '!=', config('app.locale'));
                    })
                    ->get();
        foreach ($translations as $translation) {
            $source->load('phrases.translation', 'phrases.file');
            $locale = $translation->language->code;
            $source->phrases()->each(function ($phrase) use ($translation, $locale) {
                if (! $translation->phrases()->where('key', $phrase->key)->first()) {
                    $fileName = $phrase->file->name.'.'.$phrase->file->extension;
    
                    if ($phrase->file->name === config('app.locale')) {
                        $fileName = Str::replaceStart(config('app.locale').'.', "{$locale}.", $fileName);
                    } else {
                        $fileName = Str::replaceStart(config('app.locale').'/', "{$locale}/", $fileName);
                    }
                    SyncPhrasesAction::execute($phrase->translation, $phrase->key, '', $locale, $fileName);
                }
            });
        }
    }
}
