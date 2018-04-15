<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CardService
{
    public function createCard($data)
    {
        $isCardExist = DB::table('cards')->where([
            ['use','=',$data['use']],
            ['picture','=',$data['picture']]
        ])->first();
        if($isCardExist)
            return false;

        DB::table('cards')->insert($data);
        return true;
    }

    public function updateCard($cardId,$data)
    {
        DB::table('cards')->where('id',$cardId)->update($data);
    }

    public function getAllCard()
    {
        $cards = DB::table('cards')->get();
        return $cards;
    }

    public function getCardById($cardId)
    {
        $card = DB::table('cards')->where('id',$cardId)->first();
        return $card;
    }

    public function hasCardOrNot($userId,$cardId)
    {
        $isExist = DB::table('user_cards')->where([
            ['user_id','=',$userId],
            ['card_id','=',$cardId]
        ])->first();
        if($isExist)
            return true;
        else
            return false;
    }

    public function addUserCard($userId,$cardId,$cardNumber = 1)
    {
        $isHave = $this->hasCardOrNot($userId,$cardId);
        if ($isHave)
        {
            DB::table('user_cards')
                ->where('user_id',$userId)
                ->where('card_id',$cardId)
                ->increment('number',$cardNumber);
        }
        else
        {
            DB::table('user_cards')
                ->insert([
                'user_id' => $userId,
                'card_id' => $cardId,
                'number' => $cardNumber
                ]
            );
        }
    }

    public function getUserCards($userId)
    {
        $cards = DB::table('user_cards')
            ->where('user_id',$userId)
            ->pluck('card_id', 'number');
        return $cards;
    }

    public function removeUserCard($userId,$cardId,$cardNumber = 1)
    {
        $isExist = DB::table('user_cards')->where([
            ['user_id','=',$userId],
            ['card_id','=',$cardId]
        ])->first();
        if ($isExist != null && $isExist->number >= $cardNumber)
        {
            DB::table('user_cards')
                ->where('user_id',$userId)
                ->where('card_id',$cardId)
                ->decrement('number',$cardNumber);
        }
    }

}