<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('user-access:engineering');
    }
    

    
    public function index()
    {
        return view('customer.index');
    }
    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {

        $jumlah_customer =  count($request->customer_id);
        
        if($jumlah_customer < 0){
            return redirect('customer/create')->with('error', 'Add at least 1 customer !');
        }else{
            try {
                for ($i = 0; $i < $jumlah_customer; $i++) {
                    // Check if the `id` already exists
                    $exists = DB::table('customer')->where('customer_id', $request->customer_id[$i])->exists();
    
                    if ($exists) {
                        return redirect()->back()->with('error', "Customer with ID {$request->customer_id[$i]} already exists.");
                    }
                    
                    // Insert the project if it doesn't exist
                    DB::table('customer')->insert([
                        'customer_id' => $request->customer_id[$i],
                        'customer_name' => $request->customer_name[$i],
                    ]);
                }
    
                return redirect('/customer/index')->with('success', 'Customer is successfully stored!');
            } catch (\Exception $e) {
                // Handle any other database exceptions
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    }


    public function edit($customer_id)
    {
        // Redirect back with a success message
        return view('customer.edit')->with('customer_id', $customer_id);
    }

    public function update(Request $request)
    {

        // Insert the data if validation passes
        DB::table('customer')->where('customer_id', $request->customer_id)->update([
            'customer_name' => $request->customer_name,
        ]);

        // Redirect back with a success message
        return redirect(route('customer.index'))->with('success', 'Customer updated successfully.');
    }

    public function destroy($customer_id){
        DB::table('customer')->where('customer_id', $customer_id)->delete();

        return redirect(route('customer.index'))->with('success', 'Customer is successfully deleted !');
    
    }
}
