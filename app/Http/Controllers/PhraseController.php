<?php

namespace App\Http\Controllers;

use App\Repositories\Phrases\PhraseInterface;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PhraseController extends Controller implements HasMiddleware
{
    protected $repo;

    public static function middleware(): array
    {
        return [
            new Middleware('permissions:translate-list,translate-create,translate-edit', only: ['index', 'list']),
            new Middleware('permissions:translate-create,translate-edit', only: ['update', 'translate']),
        ];
    }

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
