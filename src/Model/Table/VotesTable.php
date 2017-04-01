<?php

namespace App\Model\Table;

use Cake\ORM\Table;


class VotesTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Sentences');
        $this->addBehavior('CounterCache', [
            'Sentences' => [
                'up_votes_count' => [
                    'conditions' => ['vote' => 1]
                ],
                'down_votes_count' => [
                    'conditions' => ['vote' => -1]
                ]
            ]
        ]);
    }
}