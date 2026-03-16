<?php

declare(strict_types=1);

namespace Tigusigalpa\Ebay\Enums;

/**
 * Evidence Type Enum
 * 
 * @link https://developer.ebay.com/api-docs/sell/fulfillment/types/api:EvidenceTypeEnum
 */
enum EvidenceTypeEnum: string
{
    case PROOF_OF_DELIVERY = 'PROOF_OF_DELIVERY';
    case PROOF_OF_AUTHENTICITY = 'PROOF_OF_AUTHENTICITY';
    case PROOF_OF_ITEM_AS_DESCRIBED = 'PROOF_OF_ITEM_AS_DESCRIBED';
    case PROOF_OF_RETURN_POLICY = 'PROOF_OF_RETURN_POLICY';
    case PROOF_OF_SHIPMENT = 'PROOF_OF_SHIPMENT';
    case PROOF_OF_REFUND = 'PROOF_OF_REFUND';
    case PROOF_OF_COMMUNICATION = 'PROOF_OF_COMMUNICATION';
    case OTHER = 'OTHER';

    public function title(): string
    {
        return match($this) {
            self::PROOF_OF_DELIVERY => 'Proof of Delivery',
            self::PROOF_OF_AUTHENTICITY => 'Proof of Authenticity',
            self::PROOF_OF_ITEM_AS_DESCRIBED => 'Proof of Item as Described',
            self::PROOF_OF_RETURN_POLICY => 'Proof of Return Policy',
            self::PROOF_OF_SHIPMENT => 'Proof of Shipment',
            self::PROOF_OF_REFUND => 'Proof of Refund',
            self::PROOF_OF_COMMUNICATION => 'Proof of Communication',
            self::OTHER => 'Other',
        };
    }
}
