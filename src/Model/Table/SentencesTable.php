<?php

namespace App\Model\Table;

use Cake\ORM\Table;


class SentencesTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Adjectives');
        $this->belongsTo('Nouns');
        $this->belongsTo('Places');
        $this->belongsTo('Verbs');
        $this->hasMany('Votes');
    }
}