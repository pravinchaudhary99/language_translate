<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\TranslationsManager;
use App\Repositories\Translation\TranslationInterface;

class TranslationController extends Controller
{
    protected $repo;

    public function __construct(TranslationInterface $interface) {
        $this->repo = $interface;
    }


    public function index() {
        $languages = Language::query()
                        ->leftJoin('translations', 'translations.language_id', '=', 'languages.id')
                        ->whereNull('translations.id')
                        ->get(['languages.*']);

        return view('translations.index', compact('languages'));
    }

    public function list() {
        try {
           $responses = $this->repo->list();

           $data = $responses['data'];
           return responseDataTable($data);
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function store() {
        try {
            $responses = $this->repo->store();

            if(!$responses['success']) {
                return errorResponses(__('messages.language_not_found'));
            }
            
            return successResponses(__('messages.translation_created'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function autoTranslation($id) {
        try {
            $this->repo->autoTranslation($id);

            return successResponses(__('messages.auto_translate_completed'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $responses = $this->repo->destroy($id);

            if(!$responses['success']) {
                return errorResponses(__('messages.translation_not_found'));
            }
            
            return successResponses(__('messages.translation_deleted'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function public() {
        try {
            app(TranslationsManager::class)->export();

            return successResponses(__('messages.translation_exported'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }
}
