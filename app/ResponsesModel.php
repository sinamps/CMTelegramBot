<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResponsesModel extends Model
{
    protected $table = 'responses';
    protected $fillable = ['profanity', 'spell_error', 'incorrect_words', 'is_duplicated', 'duplication_reference', 'topic', 'request_id'];
}
