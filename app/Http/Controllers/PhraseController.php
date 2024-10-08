<?php

namespace App\Http\Controllers;

use App\Repositories\Phrases\PhraseInterface;

class PhraseController extends Controller
{
    protected $repo;

    public function __construct(PhraseInterface $interface) {
        $this->repo = $interface;
    }

    public function index($id) {
        $responses = $this->repo->index($id);
        
        $data = $responses['data'];
        $translation = $data['translation'];

        $files = $data['files'];
        return view('translations.create', compact('translation', 'files'));
    }

    public function list($id) {
        try {
            $responses = $this->repo->list($id);

            $data = $responses['data'];
            return responseDataTable($data);
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $responses = $this->repo->update($id);

            if(!$responses['success']) {
                return errorResponses(__('messages.translation_not_found'));
            }
            return successResponses(__('messages.phrase_updated'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function translate($id) {
        try {
            $responses = $this->repo->translate($id);

            if(!$responses['success']) {
                return errorResponses(__('messages.translation_not_found'));
            }

            $data = $responses['data'];
            return successResponses('', $data);
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }
}
