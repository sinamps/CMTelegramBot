<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestsModel extends Model
{
    protected $table = 'requests';
    protected $fillable = ['text', 'doc_file_id', 'chat_id', 'username', 'first_name', 'last_name', 'user_id'];
}
