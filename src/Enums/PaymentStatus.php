<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * eBay Payment Status Code Type
 * 
 * @link https://developer.ebay.com/Devzone/XML/docs/Reference/eBay/types/PaymentStatusCodeType.html
 */
enum PaymentStatus: string
{
    case NO_PAYMENT_FAILURE = 'NoPaymentFailure';
    case BUYER_ECHECK_BOUNCED = 'BuyerECheckBounced';
    case BUYER_CREDIT_CARD_FAILED = 'BuyerCreditCardFailed';
    case BUYER_FAILED_PAYMENT_REPORT_EBAY = 'BuyerFailedPaymentReportedByEbay';
    case PAYMENT_IN_PROCESS = 'PaymentInProcess';
    case PAID_PENDING_TRANSFER = 'PaidPendingTransfer';
    case PAID = 'Paid';

    public function title(): string
    {
        return match($this) {
            self::NO_PAYMENT_FAILURE => 'No Payment Failure',
            self::BUYER_ECHECK_BOUNCED => 'Buyer eCheck Bounced',
            self::BUYER_CREDIT_CARD_FAILED => 'Buyer Credit Card Failed',
            self::BUYER_FAILED_PAYMENT_REPORT_EBAY => 'Buyer Failed Payment Reported by eBay',
            self::PAYMENT_IN_PROCESS => 'Payment In Process',
            self::PAID_PENDING_TRANSFER => 'Paid Pending Transfer',
            self::PAID => 'Paid',
        };
    }
}
