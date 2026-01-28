<?php
// app/Traits/Encryptable.php
namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    public function getRouteKey()
    {
        return Crypt::encrypt($this->getKey());
    }
    
    public function resolveRouteBinding($value, $field = null)
    {
        $id = Crypt::decrypt($value);
        return $this->where($this->getKeyName(), $id)->firstOrFail();
    }
}