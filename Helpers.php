<?php

namespace App\Helpers;

/**
 * Fee Management Helper Functions
 */
class FeeHelpers
{
    /**
     * Convert a number to words
     * 
     * @param float $number
     * @return string
     */
    public static function numberToWords($number) 
    {
        $number = number_format($number, 2, '.', '');
        $parts = explode('.', $number);
        $wholePart = $parts[0];
        $decimalPart = $parts[1];
        
        $wholeWords = self::convertWholePart($wholePart);
        $decimalWords = "and " . $decimalPart . "/100";
        
        return $wholeWords . " " . $decimalWords;
    }
    
    /**
     * Convert the whole part of a number to words
     * 
     * @param int $number
     * @return string
     */
    private static function convertWholePart($number) 
    {
        $words = [];
        $units = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 
                  'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
        $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        
        if ($number == 0) {
            return 'zero';
        }
        
        // For handling billions
        if ($number >= 1000000000) {
            $words[] = self::convertWholePart(floor($number / 1000000000)) . ' billion';
            $number %= 1000000000;
        }
        
        // For handling millions
        if ($number >= 1000000) {
            $words[] = self::convertWholePart(floor($number / 1000000)) . ' million';
            $number %= 1000000;
        }
        
        // For handling thousands
        if ($number >= 1000) {
            $words[] = self::convertWholePart(floor($number / 1000)) . ' thousand';
            $number %= 1000;
        }
        
        // For handling hundreds
        if ($number >= 100) {
            $words[] = self::convertWholePart(floor($number / 100)) . ' hundred';
            $number %= 100;
        }
        
        // For handling tens and units
        if ($number > 0) {
            if ($number < 20) {
                $words[] = $units[$number];
            } else {
                $word = $tens[floor($number / 10)];
                $number %= 10;
                if ($number > 0) {
                    $word .= '-' . $units[$number];
                }
                $words[] = $word;
            }
        }
        
        return implode(' ', $words);
    }
    
    /**
     * Generate an invoice number
     * 
     * @param string $prefix Prefix for the invoice number
     * @param string $tableName Table name to check for existing invoices
     * @param string $columnName Column name to check for existing invoices
     * @return string
     */
    public static function generateInvoiceNumber($prefix = 'INV', $tableName = 'student_fees', $columnName = 'invoice_number')
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last invoice number for this month and year
        $lastInvoice = \DB::table($tableName)
                        ->where($columnName, 'like', $prefix . $year . $month . '%')
                        ->orderBy($columnName, 'desc')
                        ->first();
        
        if ($lastInvoice) {
            // Extract the numeric part and increment
            $lastNumber = intval(substr($lastInvoice->$columnName, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format with leading zeros
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Calculate overdue days
     * 
     * @param string $dueDate
     * @return int
     */
    public static function calculateOverdueDays($dueDate)
    {
        $due = new \DateTime($dueDate);
        $now = new \DateTime();
        
        if ($now > $due) {
            return $due->diff($now)->days;
        }
        
        return 0;
    }
    
    /**
     * Format currency amount
     * 
     * @param float $amount
     * @return string
     */
    public static function formatCurrency($amount)
    {
        return number_format($amount, 2);
    }
    
    /**
     * Calculate fee percentage
     * 
     * @param float $paid
     * @param float $total
     * @return float
     */
    public static function calculatePercentage($paid, $total)
    {
        if ($total == 0) {
            return 0;
        }
        
        return round(($paid / $total) * 100, 2);
    }
    
    /**
     * Return status badge HTML
     * 
     * @param string $status
     * @return string
     */
    public static function getStatusBadge($status)
    {
        switch ($status) {
            case 'paid':
                return '<span class="badge badge-success">Paid</span>';
            case 'partially_paid':
                return '<span class="badge badge-warning">Partially Paid</span>';
            case 'unpaid':
                return '<span class="badge badge-danger">Unpaid</span>';
            default:
                return '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
        }
    }
    
    /**
     * Generate a random unique payment reference
     * 
     * @param int $length
     * @return string
     */
    public static function generatePaymentReference($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $reference = '';
        
        for ($i = 0; $i < $length; $i++) {
            $reference .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Add current timestamp hash to ensure uniqueness
        $reference .= substr(md5(time()), 0, 5);
        
        return $reference;
    }
}