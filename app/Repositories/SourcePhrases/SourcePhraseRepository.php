<?php

namespace App\Repositories\SourcePhrases;

use App\Models\Phrase;
use App\Models\Translation;
use Illuminate\Http\Request;
use App\Models\TranslationFile;
use App\Actions\CreateSourceKeyAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Repositories\SourcePhrases\SourcePhraseInterface;

class SourcePhraseRepository implements SourcePhraseInterface
{
    protected $responsesData = array();

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index() {
        $translation = Translation::where('source', true)->first();

        $phrases = $translation->phrases()->newQuery();

        $files = [];
        foreach (collect($phrases->where('translation_id', $translation->id)->get())->unique('translation_file_id') as $value) {
            $files[] = TranslationFile::where('id', $value->translation_file_id)->first();
        }

        $this->responsesData['success'] = true;
        $this->responsesData['data'] = [
            'translation' => $translation,
            'files' => $files
        ];
        return $this->responsesData;
    }

    public function list() {
        $direction = $this->request->order[0]['dir'];
        $skip = $this->request->start;
        $take = $this->request->length;
        $searchValue = $this->request->search['value'];
        $translationFile = $this->request->translationFile ?? null;
        $status = $this->request->status ?? null;

        $source = Translation::where('source', true)->first();

        $phrases = $source->phrases()->newQuery();

        $recordsTotal = $phrases->count();

        if ($searchValue) {
            $phrases->whereAny(['key', 'value'], 'like', '%' . $searchValue . '%');
        }

        if ($translationFile) {
            $phrases->where(
                $translationFile
                    ? fn (Builder $query) => $query->where('translation_file_id', $translationFile)
                    : fn (Builder $query) => $query->whereNull('translation_file_id')
            );
        }

        if ($status) {
            $phrases->where(
                $status === 'translated'
                    ? fn (Builder $query) => $query->whereNotNull('value')
                    : fn (Builder $query) => $query->whereNull('value')
            );
        }

        $recordsFiltered = $phrases->count();

        $phrases = $phrases->orderBy('key', $direction)->skip($skip)->take($take)->get();
        
        $this->responsesData['data'] = [
            'data' => isset($phrases) ? $phrases : [],
            'recordsTotal' => isset($recordsTotal) ? $recordsTotal : 0,
            'recordsFiltered' => isset($recordsFiltered) ? $recordsFiltered : 0,
        ];
        return $this->responsesData;
    }

    public function store() {
        $key = ['required', 'regex:/^[\w.]+$/u'];
        if (TranslationFile::find($this->request->file)?->extension === 'json') {
            $key = ['required', 'string', 'unique:phrases,key,NULL,id,translation_file_id,' . $this->request->file];
        }

        $rules = [
            'key' => $key,
            'file' => ['required', 'integer', 'exists:translation_files,id'],
            'content' => ['required', 'string'],
        ];
    
        // Create a validator instance
        $validator = Validator::make($this->request->all(), $rules);
    
        if($validator->fails()){
            $this->responsesData['success'] = false;
            $this->responsesData['errors'] =  $validator->errors();
            return $this->responsesData;
        }


        CreateSourceKeyAction::execute(
            key: $this->request->key,
            file: $this->request->file,
            content: $this->request->content
        );
        
        $this->responsesData['success'] = true;
        return $this->responsesData;
    }
}