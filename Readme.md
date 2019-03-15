[![Build Status](https://travis-ci.org/revenuewire/ISO8583.svg?branch=master)](https://travis-ci.org/revenuewire/ISO8583)
[![Coverage Status](https://coveralls.io/repos/github/revenuewire/ISO8583/badge.svg?branch=master)](https://coveralls.io/github/revenuewire/ISO8583?branch=master)
[![Latest Stable Version](https://poser.pugx.org/revenuewire/iso8583/v/stable)](https://packagist.org/packages/revenuewire/iso8583)
[![License](https://poser.pugx.org/revenuewire/iso8583/license)](https://packagist.org/packages/revenuewire/iso8583)
[![composer.lock](https://poser.pugx.org/revenuewire/iso8583/composerlock)](https://packagist.org/packages/revenuewire/iso8583)

# Quick Start
## Install
```bash
composer require revenuewire/ISO8583
```

# Specs
**Warning**: This library is not a generic ISO8583 implementation. The library is specially coded to implement the following specs
1. FirstData ISO 8583 Global Specification - Version 2017-2a
2. FirstData ISO 8583 Global TransArmor Addendum Document - Version 2016-1a

Also, not all tables are implemented. Many tables such as Canadian Debit support has no business values at our company at the moment. Token support such as ApplePay and AndroidPay are items that under road map.

## Supported Bitmaps
 |  Bitmap | Note   |
 |---|---|
 |  MTI | Message Type ID  |  
 |  Bitmap 2 | Primary Account Number  |  
 |  Bitmap 3 | Processing Code  |  
 |  Bitmap 4 | Amount of Transaction  |  
 |  Bitmap 7 | Transmission Date/Time  |  
 |  Bitmap 11 | System Trace/Debit Reg E Receipt Number  |  
 |  Bitmap 12 | Time, Local Transmission  |  
 |  Bitmap 13 | Date, Local Trans. (Debit/EBT)/Sales Date (Credit)  |  
 |  Bitmap 14 | Card Expiration Date  |  
 |  Bitmap 18 | Merchant Category Code  |  
 |  Bitmap 22 | POS Entry Mode+ PIN Capability |  
 |  Bitmap 23 | Card Sequence Number |  
 |  Bitmap 24 | Network International ID (NII) |  
 |  Bitmap 25 | Point of Service (POS) Condition Code  |   
 |  Bitmap 31 |  Acquirer Reference Data  |  
 |  Bitmap 35 |  Acquiring ID  |    
 |  Bitmap 37 |  Retrieval Reference Number  |   
 |  Bitmap 39 |  Response  |   
 |  Bitmap 41 |  Terminal ID  |   
 |  Bitmap 42 |  Merchant ID  |   
 |  Bitmap 43 |  Alternative Merchant Name/Location  |   
 |  Bitmap 44 |  AVS  |   
 |  Bitmap 49 |  Transaction Currency Code  |   
 |  Bitmap 59 |  Merchant Zip/Postal Code  |   
 |  Bitmap 63 |  First Data Private Use Data Element  |   
 |  Bitmap 70  | Network Management Information Code  |   

## Supported Tables
| Bitmap | Table | Note |
| --- | --- | --- |
| Bitmap 63 | Table 14 | Additional VISA/MC/DS/AMEX Info |
| Bitmap 63 | Table SK | AMEX Safe Key |
| Bitmap 63 | Table 49 | Card Code Value (CCV) |
| Bitmap 63 | Table 36 | Additional Addendum Data |
| Bitmap 63 | Table 55 | Merchant Advice Code |
| Bitmap 63 | Table 60 | eCommerce Info |
| Bitmap 63 | Table VI | Visa Compliance Field Identifier Table |
| Bitmap 63 | Table MC | Mastercard Compliance Field Identifier Table |
| Bitmap 63 | Table DS | Discovery Compliance Field Identifier Table |
| Bitmap 63 | Table SP | TransArmor Token |

# Unit tests
```bash
docker-compose build unittest
sh ./bin/go-test.sh
```

# Examples
## pre-auth only transaction
```php
 /**
 * ISO8583 Payload
 */
$iso8583 = new ISO8583();

/**
* MTI
*/
$iso8583->setMTI(ISO8583::FD_MTI_CREDIT_AUTH_REQUEST);

/**
 * Bit 2
 */
$iso8583->setData(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER, ISO8583::getBit2PrimaryAccountNumber($cardNumber));

/**
* Bit 3
*/
$iso8583->setData(ISO8583::FD_BIT_3_PROCESSING_CODE, ISO8583::FD_PC_000000_CREDIT_CARD_PURCHASE);

/**
 * Bit 4
 */
$iso8583->setData(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION, $amount);

/**
 * Bit 7
 */
$iso8583->setData(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME, date("mdHis"));

/**
 * Bit 11
 */
$iso8583->setData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER, (string) $systemTraceNumber);
        
/**
 * Bit 14
 */
 $iso8583->setData(ISO8583::FD_BIT_14_CARD_EXPIRATION_DATE, $expiredDate);
 
 /**
  * Bit 22
  */
 $iso8583->setData(ISO8583::FD_BIT_22_POS_ENTRY_MODE, "010");

 /**
  * Bit 24
  */
 $iso8583->setData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID, "0001");
 
 /**
  * Bit 31
  */
 $iso8583->setData(ISO8583::FD_BIT_31_ACQUIRER_REFERENCE_DATA, ISO8583::FD_ARD_0_AUTHORIZATION_ONLY);
 
 /**
  * Bit 41,42
  */
 $iso8583->setData(ISO8583::FD_BIT_41_TERMINAL_ID, $tid);
 $iso8583->setData(ISO8583::FD_BIT_42_MERCHANT_ID, $mid);
 
 /**
  * Bit 49
  */
 $iso8583->setData(ISO8583::FD_BIT_49_TRANSACTION_CURRENCY_CODE, $paymentCurrency);

 /**
  * Bit 59
  */
 $iso8583->setData(ISO8583::FD_BIT_59_MERCHANT_ZIP, $merchantZip);

 /**
  * Bit 60
  */
 $iso8583->setData(ISO8583::FD_BIT_60_ADDITIONAL_POS_INFO, "01");
 
 /**
  * Bit 63 Table 14, VISA Transaction
  */
 $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA, ISO8583::getBit63Table14AdditionalVisaData(["aci" => "Y"]));
 $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_VI_COMPLIANCE, ISO8583::getBit63TableVIMCDSCompliance());
         
/**
  * Bit 63 Table SP, using TransArmor
  */
 $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_SP_TRANS_ARMOR, ISO8583::getBit63TableSPTransArmorToken());
 
 return $iso8583->getEncodedISO();
```
