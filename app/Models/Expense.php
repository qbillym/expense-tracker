<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'category',
        'date',
        'notes',
        'user_id',
        'mobile_money_message',
        'detected_balance',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'detected_balance' => 'decimal:2',
    ];

    // Predefined expense categories
    public const CATEGORIES = [
        'Food & Dining' => ['food', 'restaurant', 'dining', 'lunch', 'dinner', 'snack', 'meal'],
        'Transportation' => ['transport', 'taxi', 'bus', 'train', 'fuel', 'gas', 'parking', 'uber'],
        'Shopping' => ['shopping', 'clothes', 'grocery', 'supermarket', 'market', 'store'],
        'Entertainment' => ['entertainment', 'movie', 'cinema', 'game', 'party', 'event', 'concert'],
        'Bills & Utilities' => ['bill', 'utility', 'electricity', 'water', 'internet', 'phone', 'rent'],
        'Healthcare' => ['health', 'medical', 'doctor', 'pharmacy', 'hospital', 'medicine'],
        'Education' => ['education', 'school', 'book', 'course', 'tuition', 'training'],
        'Personal Care' => ['personal', 'haircut', 'salon', 'cosmetic', 'beauty'],
        'Home & Garden' => ['home', 'garden', 'furniture', 'repair', 'maintenance'],
        'Travel' => ['travel', 'hotel', 'flight', 'vacation', 'trip', 'tour'],
        'Business' => ['business', 'office', 'meeting', 'conference', 'work'],
        'Other' => ['other', 'miscellaneous', 'general'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parse mobile money message and extract expense details
     */
    public static function parseMobileMoneyMessage(string $message): array
    {
        $result = [
            'amount' => null,
            'balance' => null,
            'merchant' => null,
            'category' => 'Other',
            'title' => 'Mobile Money Transaction',
        ];

        // Clean the message
        $message = strtolower($message);

        // Extract amount (look for patterns like "RWF 50,000", "UGX 50,000", "$50", "50000 RWF", etc.)
        $amountPatterns = [
            '/(?:ugx|rwf)\s*([\d,]+)/i',
            '/\$?\s*([\d,]+(?:\.\d{2})?)/',
            '/([\d,]+(?:\.\d{2})?)\s*(?:ugx|rwf)/i',
            '/([\d,]+(?:\.\d{2})?)\s*\$/',
            '/amount[:\s]+([\d,]+(?:\.\d{2})?)/i',
            '/paid[:\s]+([\d,]+(?:\.\d{2})?)/i',
        ];

        foreach ($amountPatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $amount = str_replace(',', '', $matches[1]);
                $result['amount'] = (float) $amount;
                break;
            }
        }

        // Extract balance
        $balancePatterns = [
            '/balance[:\s]+(?:ugx|rwf)\s*([\d,]+)/i',
            '/bal[:\s]+([\d,]+(?:\.\d{2})?)/i',
            '/available[:\s]+([\d,]+(?:\.\d{2})?)/i',
            '/new balance[:\s]+([\d,]+(?:\.\d{2})?)/i',
        ];

        foreach ($balancePatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $balance = str_replace(',', '', $matches[1]);
                $result['balance'] = (float) $balance;
                break;
            }
        }

        // Extract merchant/transaction details
        $merchantPatterns = [
            '/sent to[:\s]+([^\n\r]+)/i',
            '/to[:\s]+([^\n\r]+)/i',
            '/paid to[:\s]+([^\n\r]+)/i',
            '/merchant[:\s]+([^\n\r]+)/i',
            '/received from[:\s]+([^\n\r]+)/i',
            '/from[:\s]+([^\n\r]+)/i',
            '/withdrawn at[:\s]+([^\n\r]+)/i',
        ];

        foreach ($merchantPatterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                $merchant = trim($matches[1]);
                // Remove common suffixes
                $merchant = preg_replace('/\s*\([^)]*\)$/', '', $merchant);
                $merchant = preg_replace('/\s*-\s*.*/', '', $merchant);
                $result['merchant'] = $merchant;
                $result['title'] = "Payment to {$merchant}";
                break;
            }
        }

        // Auto-detect category based on merchant name or message content
        $result['category'] = self::detectCategory($message, $result['merchant']);

        return $result;
    }

    /**
     * Detect expense category based on message content and merchant
     */
    private static function detectCategory(string $message, ?string $merchant = null): string
    {
        $text = $message . ' ' . ($merchant ?? '');

        foreach (self::CATEGORIES as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($text, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'Other';
    }

    /**
     * Get category color for UI display
     */
    public static function getCategoryColorStatic(string $category): string
    {
        $colors = [
            'Food & Dining' => 'success',
            'Transportation' => 'primary',
            'Shopping' => 'info',
            'Entertainment' => 'warning',
            'Bills & Utilities' => 'secondary',
            'Healthcare' => 'danger',
            'Education' => 'info',
            'Personal Care' => 'primary',
            'Home & Garden' => 'success',
            'Travel' => 'warning',
            'Business' => 'secondary',
            'Other' => 'dark',
        ];

        return $colors[$category] ?? 'dark';
    }

    public function getCategoryColor(): string
    {
        return self::getCategoryColorStatic($this->category);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(): string
    {
        return 'RWF ' . number_format($this->amount, 0);
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalance(): string
    {
        if ($this->detected_balance) {
            return 'RWF ' . number_format($this->detected_balance, 0);
        }
        return 'N/A';
    }
}
