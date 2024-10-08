<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SourcePhrases\SourcePhraseInterface;

class SourcePhraseController extends Controller
{
    protected $repo;

    public function __construct(SourcePhraseInterface $interface) {
        $this->repo = $interface;
    }

    public function index() {
        $responses = $this->repo->index();
        
        $data = $responses['data'];
        $translation = $data['translation'];

        $files = $data['files'];
        return view('translations.source.index', compact('translation', 'files'));
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
                return errorResponses($responses['errors'] ?? 'Something went wrong');
            }

            return successResponses(__('messages.source_translate_created'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }

    public function update($id) {
        try {
            $responses = $this->repo->update($id);

            if(!$responses['success']) {
                return errorResponses($responses['errors'] ?? 'Something went wrong');
            }

            return successResponses(__('messages.source_translate_updated'));
        } catch (\Exception $e) {
            return errorResponses($e->getMessage());
        }
    }
}
