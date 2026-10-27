<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeeController extends Controller {
    public function index(Request $request) {
        $query = Fee::with('student.user', 'feeType');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('student', fn($q) => $q->where('admission_number', 'like', "%$search%"));
        }
        $fees = $query->latest()->paginate(15);
        $summary = [
            'total_collected' => Fee::where('status', 'paid')->sum('paid_amount'),
            'total_pending'   => Fee::where('status', 'pending')->sum('amount'),
            'total_overdue'   => Fee::where('status', 'overdue')->count(),
        ];
        return view('fees.index', compact('fees', 'summary'));
    }

    public function create() {
        $students = Student::with('user')->where('status', 'active')->get();
        $feeTypes = FeeType::all();
        return view('fees.create', compact('students', 'feeTypes'));
    }

    public function store(Request $request) {
        $request->validate([
            'student_id'  => 'required|exists:students,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'amount'      => 'required|numeric|min:0',
            'due_date'    => 'required|date',
            'month'       => 'nullable|string',
        ]);
        $receiptNumber = 'RCP-' . date('Ymd') . '-' . str_pad(Fee::count() + 1, 4, '0', STR_PAD_LEFT);
        Fee::create([
            'student_id'     => $request->student_id,
            'fee_type_id'    => $request->fee_type_id,
            'amount'         => $request->amount,
            'discount'       => $request->discount ?? 0,
            'fine'           => $request->fine ?? 0,
            'due_date'       => $request->due_date,
            'month'          => $request->month,
            'receipt_number' => $receiptNumber,
            'status'         => 'pending',
        ]);
        return redirect()->route('dashboard.fees.index')->with('success', 'Fee created successfully!');
    }

    public function show(Fee $fee) {
        $fee->load('student.user', 'feeType');
        return view('fees.show', compact('fee'));
    }

    public function edit(Fee $fee) {
        $students = Student::with('user')->where('status', 'active')->get();
        $feeTypes = FeeType::all();
        return view('fees.edit', compact('fee', 'students', 'feeTypes'));
    }

    public function update(Request $request, Fee $fee) {
        $request->validate([
            'amount'   => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);
        $fee->update($request->all());
        return redirect()->route('dashboard.fees.index')->with('success', 'Fee updated successfully!');
    }

    public function destroy(Fee $fee) {
        $fee->delete();
        return redirect()->route('dashboard.fees.index')->with('success', 'Fee deleted successfully!');
    }

    public function collect(Request $request, Fee $fee) {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);
        $totalDue = $fee->amount + $fee->fine - $fee->discount;
        $status   = $request->paid_amount >= $totalDue ? 'paid' : 'partial';
        $fee->update([
            'paid_amount' => $request->paid_amount,
            'status'      => $status,
            'paid_date'   => now()->toDateString(),
        ]);
        // Notify about fee payment
        \App\Models\SystemNotification::notifyAdmins(
            'Fee Payment Received',
            'PKR ' . number_format($request->paid_amount) . ' fee collected',
            route('dashboard.fees.index'),
            'fas fa-money-bill-wave',
            '#10b981'
        );
        return redirect()->back()->with('success', 'Payment collected successfully!');
    }

    public function receipt(Fee $fee) {
        $fee->load('student.user', 'feeType');
        return view('fees.receipt', compact('fee'));
    }
}