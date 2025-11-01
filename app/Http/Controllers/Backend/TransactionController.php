<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Export\CSV\TransactionExport;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\UserWallet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware('permission:transaction-list');
    }

    /**
     * @return Application|Factory|View|JsonResponse
     *
     * @throws \Exception
     */
    public function transactions(Request $request)
    {
        $transactions = $this->getTransactionData($request, false);
        $queries = $request->query();

        return view('backend.transaction.index', compact('transactions', 'queries'));
    }

    public function getTransactionData(Request $request, $export = false)
    {
        $perPage = $request->perPage ?? 15;

        $status = $request->status ?? 'all';
        $search = $request->search ?? null;
        $type = $request->type ?? 'all';
        $transactions = Transaction::with('user')
            ->search($search)
            ->status($status)
            ->type($type)
            ->when(in_array(request('sort_field'), ['created_at', 'final_amount', 'type', 'description']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->when(! request()->has('sort_field'), function ($query) {
                $query->latest();
            });

        if ($export) {
            return $transactions->take(request('limit', 15))->get();
        }
        $transactions = $transactions->paginate(request('limit', 15))
            ->withQueryString();

        return $transactions;
    }

    public function transactionExportCSV(Request $request)
    {
        $transactions = $this->getTransactionData($request, true);

        return (new TransactionExport($transactions, true))->download('transactions.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function wallets()
    {
        $wallets = UserWallet::with('currency', 'user')
            ->when(in_array(request('sort_field'), ['created_at', 'balance']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->when(in_array(request('sort_field'), ['currency_name', 'currency_symbol']), function ($query) {
                $query->whereHas('currency', function ($currencyQuery) {
                    $currencyQuery->orderBy(request('sort_field'), request('sort_dir'));
                });
            })
            ->latest()
            ->paginate();

        return view('backend.wallets.index', compact('wallets'));
    }

    public function virtualCardsList()
    {
        $cards = Card::with('user')
            ->when(in_array(request('sort_field'), ['created_at', 'balance', 'card_number', 'expiration_year']), function ($query) {
                $query->orderBy(request('sort_field'), request('sort_dir'));
            })
            ->when(request('sort_field') == 'user', function ($query) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->orderBy('username', request('sort_dir'));
                });
            })
            ->latest()
            ->paginate();

        return view('backend.virtual_cards.index', compact('cards'));
    }
}
