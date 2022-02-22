<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Utility;
use Illuminate\Http\Request;
use DB;
use Log;
use App\Models\LoanDetails;
use App\Models\LoanTermDetails;

class LoanProcessController extends Controller {

    //put your code here
    public function __construct() {
        
    }

    public function loanStatusCheck(Request $request) {
        try {
            DB::BeginTransaction();
            $data = $request->all();
            $user = User::find(config('user_id'));
            if (!$user) {
                return Utility::genErrResp("internal_err");
            }
            $obj = LoanDetails::select('*');
            if ($user->is_admin == 'N') {
                $obj = $obj->where('loan_status', 'A')
                        ->where('loan_borrower_id', $user->user_id);
            }
            $return_data = $obj->get();
            return Utility::genSuccessResp('listSuccess', $return_data, false);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("internal_err");
        }
    }

    public function loanStatusUpdate(Request $request) {
        try {
            DB::BeginTransaction();
            $data = $request->all();
            $user = User::find(config('user_id'));
            if (!$user) {
                return Utility::genErrResp("internal_err");
            }
            if ($user->is_admin != 'Y') {
                return Utility::genErrResp("invalid_user");
            }
            $loan = LoanDetails::find($data['loan_id']);
            if ($loan) {
                $loan->loan_status = $data['loan_status'];
                $loan->loan_approve_date = date("Y-m-d h:i:s");
                $loan->loan_approver_id = $user->id;
                $loan->save();
                if ($loan->loan_status == 'A') {
                    $this->addLoanTermDetails($data, $loan);
                }
            }
            DB::commit();
            return Utility::genSuccessResp('loan_status_updated');
        } catch (Exception $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("internal_err");
        }
    }

    public function addLoanTermDetails($request, $loan) {
        $term = [
            [
                'payment_date' => date('Y-m-d', strtotime($loan->loan_approve_date . ' + 7 days')),
                'payment_status' => 'P',
                'loan_details_id' => $loan->id,
                'amount' => 0
            ],
            [
                'payment_date' => date('Y-m-d', strtotime($loan->loan_approve_date . ' + 14 days')),
                'payment_status' => 'P',
                'loan_details_id' => $loan->id,
                'amount' => 0
            ],
            [
                'payment_date' => date('Y-m-d', strtotime($loan->loan_approve_date . ' + 21 days')),
                'payment_status' => 'P',
                'loan_details_id' => $loan->id,
                'amount' => 0
            ],
        ];
        return LoanTermDetails::insert($term);
    }

    public function payLoanEMI(Request $request) {
        try {
            DB::BeginTransaction();
            $data = $request->all();
            $user = User::find(config('user_id'));
            if (!$user) {
                return Utility::genErrResp("internal_err");
            }
            $loan = LoanDetails::find($data['loan_id']);
            if ($loan->loan_status == 'A') {
                $loanTermObj = LoanTermDetails::find($data['loan_term_id']);
                $loanTermObj->payment_date = date("Y-m-d");
                $loanTermObj->amount = $data['amount'];
                $loanTermObj->payment_status = 'P';
                $loanTermObj->save();
            }
            DB::commit();
            return Utility::genSuccessResp('loan_emi_paid');
        } catch (Exception $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("internal_err");
        }
    }

    public function loanRequest(Request $request) {
        try {
            DB::BeginTransaction();
            $data = $request->all();
            $user = User::find($data['loan_borrower_id']);
            if (!$user) {
                return Utility::genErrResp("internal_err");
            }
            $loan = new LoanDetails();
            $loan->loan_amount = $data['loan_amount'];
            $loan->loan_term = 3;
            $loan->loan_status = 'P';
            $loan->loan_borrower_id = $user->id;
            $loan->save();
            DB::commit();
            return Utility::genSuccessResp('loan_request_submitted_successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            Utility::logException($ex);
            return Utility::genErrResp("internal_err");
        }
    }

}
