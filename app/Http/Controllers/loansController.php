<?php

namespace App\Http\Controllers;

use App\Models\LoanRepayments;
use Validator;
use App\Models\Loans;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class loansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loans::with('repayments')->where('user_id', '=', Auth::id())->get();

        if (auth()->user()->role === 1) {
            $loans = Loans::all();
            $loans = Loans::with('repayments')->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'Loans data retrive successfuly.',
            'data' => $loans
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'amount' => 'required|integer',
            'term' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error.'
            ], 500);
        }

        $input['user_id'] = auth()->user()->id;

        $loan = Loans::create($input);

        $loanDetails = Loans::find($loan->id);
        if ($loanDetails) {
            $amount = $loanDetails->amount / $loanDetails->term;
            for ($i = 0; $i < $loanDetails->term; $i++) {
                $repayments[] = LoanRepayments::create([
                    'loan_id' => $loanDetails->id,
                    'amount' => $amount,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Loans data retrive successfuly.',
            'data' => [
                'loan' => $loan,
                'repayments' => $repayments
            ]
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = Loans::with('repayments')->where(['id' => $id, 'user_id' => Auth::id()])->get();

        if (auth()->user()->role === 1) {
            $loan = Loans::with('repayments')->where(['id' => $id])->get();
        }

        if (count($loan)) {
            return response()->json([
                'status' => true,
                'message' => 'Loans data retrive successfuly.',
                'data' => $loan
            ], 200);
        }
        return response()->json([
            'status' => true,
            'message' => 'No Loan found.'
        ], 200);
    }

    public function approveLoan($id)
    {
        if (auth()->user()->role === 1) {
            $loan = Loans::find($id);
            if ($loan) {
                $loan->status = 'APPROVED';
                $loan->save();
                return response()->json([
                    'status' => true,
                    'message' => 'loan approve successfully.'
                ], 200);
            }
            return response()->json([
                'status' => true,
                'message' => 'loan not found.'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'not authorized.'
        ], 401);
    }

    public function repayment(Request $request, $id)
    {
        $input = $request->all();
        $repayment = LoanRepayments::find($id);
        $validator = Validator::make($request->all(), [
            'amount' => [
                'required',
                'integer',
                'min:' . $repayment->amount
            ],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error.'
            ], 500);
        }
        if ($repayment) {
            $repayment->status = 'PAID';
            $repayment->amount_received = $input['amount'];
            $repayment->save();
            $remain_repayments = LoanRepayments::where('status', '=', 'PENDING')->count();
            if (0 === $remain_repayments) {
                Loans::where('id', $repayment->loan_id)->update(['status' => 'PAID']);
            }
            return response()->json([
                'status' => true,
                'remain_repayments' => $remain_repayments,
                'message' => 'loan repayment successfully.'
            ], 200);
        }
        return response()->json([
            'status' => true,
            'message' => 'loan not found.'
        ], 200);

        return response()->json([
            'status' => false,
            'message' => 'not authorized.'
        ], 401);
    }
}
