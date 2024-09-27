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
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errors', $e->getMessage()], 500);
        }
    }

    public function update($id) {
        try {
            $responses = $this->repo->update($id);

            if(!$responses['success']) {
                return response()->json(['errors', __('messages.translation_not_found')], 500);
            }
            return response()->json(['message' => __('messages.phrase_updated')],200);
        } catch (\Exception $e) {
            return response()->json(['errors', $e->getMessage()], 500);
        }
    }

    public function translate($id) {
        try {
            $responses = $this->repo->translate($id);

            if(!$responses['success']) {
                return response()->json(['errors', __('messages.translation_not_found')], 500);
            }

            $data = $responses['data'];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['errors', $e->getMessage()], 500);
        }
    }
}
