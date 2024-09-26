<?php

namespace App;

use App\Models\Translation;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TranslationsManager
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    public function getAllLangFiles(): array
    {
        $langPath = lang_path(); 
        $translations = [];

        if ($this->filesystem->exists($langPath)) {
            $languageDirs = $this->filesystem->directories($langPath);

            foreach ($languageDirs as $dir) {
                $locale = basename($dir); 

                $files = $this->filesystem->files($dir);

                foreach ($files as $file) {
                    $extension = $this->filesystem->extension($file);

                    try {
                        if ($extension === 'php') {
                            $translations[$locale][$file->getFilename()] = $this->filesystem->getRequire($file->getPathname());
                        } elseif ($extension === 'json') {
                            $translations[$locale][$file->getFilename()] = json_decode($this->filesystem->get($file->getPathname()), true);
                        }
                    } catch (FileNotFoundException $e) {
                        $translations[$locale][$file->getFilename()] = [];
                    }
                }
            }
        }

        return $translations;
    }

    public function getTranslations(string $locale) {
        if (blank($locale)) {
            $locale = config('translations.source_language');
        }

        $translations = [];
        $rootFileName = "$locale.json";

        $files = [];

        if ($this->filesystem->exists(lang_path($locale))) {
            $files = $this->filesystem->allFiles(lang_path($locale));
        }

        collect($files)
            ->map(function (SplFileInfo $file) use ($locale) {
                if ($file->getRelativePath() === '') {
                    return $locale.DIRECTORY_SEPARATOR.$file->getFilename();
                }

                return $locale.DIRECTORY_SEPARATOR.$file->getRelativePath().DIRECTORY_SEPARATOR.$file->getFilename();
            })
            ->when($this->filesystem->exists(lang_path($rootFileName)), function ($collection) use ($rootFileName) {
                return $collection->prepend($rootFileName);
            })
            ->filter(function ($file) {
                return $this->filesystem->extension($file) == 'php' || $this->filesystem->extension($file) == 'json';
            })
            ->each(function ($file) use (&$translations) {
                try {
                    if ($this->filesystem->extension($file) == 'php') {
                        $translations[$file] = $this->filesystem->getRequire(lang_path($file));
                    }

                    if ($this->filesystem->extension($file) == 'json') {
                        $translations[$file] = json_decode($this->filesystem->get(lang_path($file)), true);
                    }
                } catch (FileNotFoundException $e) {
                    $translations[$file] = [];
                }
            });

        return $translations;
    }

    public function export($download = false){
        $translations = Translation::with('phrases')->get();

        foreach ($translations as $translation) {
            $phrasesTree = buildPhrasesTree(
                $translation->phrases()->with('file')->whereNotNull('value')->get(),
                $translation->language->code
            );

            foreach ($phrasesTree as $locale => $groups) {
                foreach ($groups as $file => $phrases) {
                    // Use public_path for files that need to be accessible publicly
                    $langPath = ($file === "$locale.json")
                        ? ($download ? public_path("translations/$file") : lang_path($file))
                        : ($download ? public_path("translations/$locale/$file") : lang_path("$locale/$file"));

                    // Ensure the directory exists
                    $directory = dirname($langPath);
                    if (!$this->filesystem->isDirectory($directory)) {
                        $this->filesystem->makeDirectory($directory, 0755, true);
                    }

                    // Create the file if it doesn't exist
                    if (!$this->filesystem->exists($langPath)) {
                        $this->filesystem->put($langPath, "<?php\n\nreturn [];\n");
                    }

                    // Handle the file content based on its extension
                    try {
                        if ($this->filesystem->extension($langPath) === 'php') {
                            $this->filesystem->put($langPath, "<?php\n\nreturn " . var_export($phrases, true) . ";\n");
                        } elseif ($this->filesystem->extension($langPath) === 'json') {
                            $this->filesystem->put($langPath, json_encode($phrases, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                        }
                    } catch (\Exception $e) {
                        logger()->error($e->getMessage());
                    }
                }
            }
        }
    }
}