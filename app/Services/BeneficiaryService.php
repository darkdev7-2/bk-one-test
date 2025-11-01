<?php

namespace App\Services;

use App\Models\Beneficiary;

class BeneficiaryService
{
    public function store($data)
    {
        return Beneficiary::create($data);
    }

    public function update($id, $data)
    {
        $beneficiary = Beneficiary::find($id);
        if ($data['bank_id'] == 'null') {
            $data['bank_id'] = null;
        }

        return $beneficiary->update($data);
    }

    public function delete($id)
    {
        Beneficiary::destroy($id);
    }
}
