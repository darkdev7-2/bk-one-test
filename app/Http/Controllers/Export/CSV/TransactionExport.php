<?php

namespace App\Http\Controllers\Export\CSV;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionExport extends Controller implements FromCollection, WithHeadingRow, WithHeadings, WithMapping
{
    use Exportable;

    public $trnxQuery;

    public $isAdmin;

    public function __construct($trnxQuery, $isAdmin = false)
    {
        $this->trnxQuery = $trnxQuery;
        $this->isAdmin = $isAdmin;
    }

    public function collection()
    {
        return $this->trnxQuery;
    }

    public function map($transaction): array
    {
        $data = [
            $transaction->created_at,
            $transaction->description,
            $transaction->tnx,
            ucfirst(str_replace('_', ' ', $transaction->type->value)),
            ((isPlusTransaction($transaction->type) == true ? '+' : '-').$transaction->amount.' '.transaction_currency($transaction)),
            $transaction->charge.' '.transaction_currency($transaction),
            ucwords($transaction->status->value),
            $transaction->method,
        ];
        if ($this->isAdmin) {
            array_unshift($data, $transaction->user->username);
        }

        return $data;
    }

    public function headings(): array
    {
        $headings = [
            'Date',
            'Description',
            'Transaction ID',
            'Type',
            'Amount',
            'Charge',
            'Status',
            'Method',
        ];
        if ($this->isAdmin) {
            array_unshift($headings, 'User');
        }

        return $headings;
    }
}
