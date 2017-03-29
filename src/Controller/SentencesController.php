<?php

namespace App\Controller;

class SentencesController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->loadModel('Adjectives');
        $this->loadModel('Nouns');
        $this->loadModel('Verbs');
        $this->loadModel('Places');
        $this->loadModel('Sentences');
        $this->loadModel('Votes');
    }

    public function generate() {
        $adjective = $this->Adjectives->find('all')
            ->order('rand()')
            ->first();
        $noun = $this->Nouns->find('all')
            ->order('rand()')
            ->first();
        $verb = $this->Verbs->find('all')
            ->order('rand()')
            ->first();
        $place = $this->Places->find('all')
            ->order('rand()')
            ->first();

        $result = "$adjective->word $noun->word $verb->word $place->word";

        $sentence = $this->Sentences->newEntity([
            'adjective_id' => $adjective->id,
            'noun_id' => $noun->id,
            'verb_id' => $verb->id,
            'place_id' => $place->id
        ]);

        $this->Sentences->save($sentence);

        $this->set("sentence", $result);
        $this->set("id", $sentence->id);
    }

    public function vote($id = 0) {
        $vote = $this->Votes->newEntity([
            'sentence_id' => $id
        ]);

        $this->Votes->save($vote);
        $this->set('saved', $vote);
    }

    public function show() {
        $sentences = $this->Sentences
            ->find()
            ->contain(['Adjectives', 'Nouns', 'Verbs', 'Places'])
            ->contain(['Votes' => function($q) {
                return $q->select([
                    'Votes.sentence_id',
                    'count' => $q->func()->count('Votes.sentence_id')
                ])->group(['Votes.sentence_id']);
            }])
            ->toArray();

        $result = [];

        foreach($sentences as $sentence) {
            $result[] = [
                'id' => $sentence->id,
                'sentence' => $sentence->adjective->word.' '.$sentence->noun->word.' '.$sentence->verb->word.' '.$sentence->place->word,
                'votes' => isset($sentence->votes[0]->count) ? $sentence->votes[0]->count : 0
            ];
        }

        $this->set('sentences', $result);
    }
}