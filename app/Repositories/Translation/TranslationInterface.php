<?php

namespace App\Repositories\Translation;

interface TranslationInterface
{
    public function list();

    public function store();

    public function autoTranslation($id);

    public function destroy($id);
}