<?php

namespace App\Controller;

use Cake\Network\Exception\BadRequestException;

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

    public function add($key = "") {
        $ids = explode('-', $key);

        if(count($ids) != 4) {
            throw new BadRequestException();
            return;
        }
        $sentence = $this->Sentences->newEntity([
            'adjective_id' => $ids[0],
            'noun_id' => $ids[1],
            'verb_id' => $ids[2],
            'place_id' => $ids[3]
        ]);

        $this->Sentences->save($sentence);
        $this->set('data', $sentence);
        $this->set('_serialize', ['data']);
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

        $sentence = "$adjective->word $noun->word $verb->word $place->word";
        $key = "$adjective->id-$noun->id-$verb->id-$place->id";

        $this->set("sentence", $sentence);
        $this->set("key", $key);
    }

    public function vote($vote = 'up',$id = 0) {
        $vote == 'down' ? $vote = -1 : $vote = 1;
        $vote = $this->Votes->newEntity([
            'sentence_id' => $id,
            'vote' => $vote
        ]);

        $this->Votes->save($vote);
        $this->set('saved', $vote);
    }

    public function show($order = 'new') {
        $order == 'new' ? $order = ['Sentences.id' => 'DESC'] : $order = ['Sentences.up_votes_count' => 'DESC'];
        $sentences = $this->Sentences
            ->find()
            ->contain(['Adjectives', 'Nouns', 'Verbs', 'Places'])
            ->order($order)
            ->limit(100)
            ->toArray();

        $result = [];

        foreach($sentences as $sentence) {
            $result[] = [
                'id' => $sentence->id,
                'sentence' => $sentence->adjective->word.' '.$sentence->noun->word.' '.$sentence->verb->word.' '.$sentence->place->word,
                'up_votes' => $sentence->up_votes_count,
                'down_votes' => $sentence->down_votes_count
            ];
        }

        $this->set('sentences', $result);
    }
}