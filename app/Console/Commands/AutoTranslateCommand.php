<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoTranslateCommand extends Command
{
    protected $signature = 'translations:auto-translate {translateId?}';

    protected $description = 'Selected language all value will be translated';

    public function handle()
    {
        $translateId = $this->argument('translateId');

        $translation = Translation::with(['phrases' => function ($query) {
            $query->whereNull('value');
        }])->find($translateId);

        if (!$translation) {
            $this->error('Translation not found.');
            return;
        }

        $sourceTranslation = Translation::where('source', true)->with('phrases')->first();

        if (!$sourceTranslation) {
            $this->error('Source translation not found.');
            return;
        }

        $translation->phrases->each(function ($phrase) use ($sourceTranslation) {
            $enValue = $sourceTranslation->phrases->firstWhere('key', $phrase->key)?->value;

            if ($enValue) {
                $translatedValue = translate(
                    $sourceTranslation->language->code,
                    $phrase->translation->language->code,
                    $enValue
                );

                $phrase->update(['value' => $translatedValue]);
            }
        });
    }
}
