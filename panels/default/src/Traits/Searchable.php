<?php

namespace App\DefaultPanel\Traits;

trait Searchable {
    static public function search($attribute, $term) {
        $translations = ['ar', 'en'];

        $qry = static::query();

        foreach ($translations as $translation) {
            $qry->orWhere($attribute . '->' . $translation, 'like', '%' . $term . '%');
        }
        return $qry;
    }
}
