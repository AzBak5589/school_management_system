namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'Receipt Number',
            'Student Name',
            'Fee Type',
            'Amount',
            'Payment Method',
            'Payment Date'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->receipt_number,
            $payment->studentFee->student->full_name ?? 'N/A',
            $payment->studentFee->feeStructure->feeType->type_name ?? 'N/A',
            $payment->amount_paid,
            $payment->payment_method,
            $payment->payment_date->format('Y-m-d')
        ];
    }
}