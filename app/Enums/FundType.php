<?php

namespace App\Enums;

enum FundType: string
{
    case BANK_LOAN = 'Bank Loan';
    case ANGEL_INVESTOR = 'Angel Investor';
    case VENTURE_CAPITAL = 'Venture Capital';
    case GOVERNMENT_GRANT = 'Government Grant';
    case PERSONAL_LOAN = 'Personal Loan';
    case LINE_OF_CREDIT = 'Line of Credit';
    case EQUITY_INVESTMENT = 'Equity Investment';
    case OTHER = 'Other';

    /**
     * Get all values as an array for dropdowns
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if the fund type requires interest tracking
     */
    public function requiresInterest(): bool
    {
        return in_array($this, [
            self::BANK_LOAN,
            self::PERSONAL_LOAN,
            self::LINE_OF_CREDIT,
        ]);
    }

    /**
     * Get badge color for display
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::BANK_LOAN, self::PERSONAL_LOAN, self::LINE_OF_CREDIT => 'danger',
            self::ANGEL_INVESTOR, self::VENTURE_CAPITAL, self::EQUITY_INVESTMENT => 'success',
            self::GOVERNMENT_GRANT => 'info',
            self::OTHER => 'secondary',
        };
    }
}
