<?php

namespace App\Modules\Credits\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditLedger;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CreditsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:credits.view'),
        ];
    }

    public function __construct(
        protected CreditLedger $ledger
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $transactions = CreditTransaction::query()
            ->forUser($user->id)
            ->latest()
            ->paginate(20);

        return view('credits::user.index', [
            'balance' => $this->ledger->balance($user),
            'transactions' => $transactions,
        ]);
    }

    public function buy(Request $request): View
    {
        return view('credits::user.buy', [
            'balance' => $this->ledger->balance($request->user()),
        ]);
    }
}
