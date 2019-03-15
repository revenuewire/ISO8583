<?php
declare(strict_types=1);


/**
 *  Implement the following spec
 *
 *  1. FirstData ISO 8583 Global Specification - Version 2017-2a
 *  2. FirstData Secure Transport XML Interface Reference Guide - Product Version 3.2 Document Revision 1.12
 *  3. FirstData ISO 8583 Global TransArmor Addendum Document - Version 2016-1a
 */
namespace RW;

class ISO8583
{
    use ISO8583FDBitDataExtend;
    use ISO8583FDTableExtend;

    /**
     * Constants Appendix C from ISO 8583 Global Spec
     */
    const COUNTRY_US_NUMERIC_CODE = "840";
    const COUNTRY_CA_NUMERIC_CODE = "124";

    const CURRENCY_USD = "840";
    const CURRENCY_CAD = "124";

    public static $countryCodeMap = [
        "US" => self::COUNTRY_US_NUMERIC_CODE,
        "CA" => self::COUNTRY_CA_NUMERIC_CODE,
    ];

    public static $americaStates = [
        "AL","LA","AK","ME","AZ","MD","AR","MA","CA","MI","CO","MN","CT","MS","DE","MO","DC","MT","FL","NE","GA",
        "NV","HI","NH","ID","NJ","IL","NM","IN","NY","IA","NC","KS","ND","KY","OH","OK","UT","OR","VT","PA","VI",
        "PR","VA","RI","WA","SC","WV","SD","WI","TN","WY","TX","GU","GV","ML","SS"
    ];

    public static $canadianStates = [
        "AB","QC","BC","SK","CB","CG","MB","NB","NT","NS","ON","PE","YT"
    ];

    /**
     * First Data Constants
     */
    const FD_MTI = "mti";
    const FD_MTI_CREDIT_AUTH_REQUEST = "0100";
    const FD_MTI_CREDIT_AUTH_RESPONSE = "0110";
    const FD_MTI_DEBIT_AUTH_REQUEST = "0200";
    const FD_MTI_DEBIT_AUTH_RESPONSE = "0210";
    const FD_MTI_REVERSAL_REQUEST = "0400";
    const FD_MTI_REVERSAL_RESPONSE = "0410";
    const FD_MTI_NETWORK_MANAGEMENT_REQUEST = "0800";
    const FD_MTI_NETWORK_MANAGEMENT_RESPONSE = "0810";

    /**
     * BIT code
     */
    const FD_BITMAP = "bitmap";
    const FD_BIT_2_PRIMARY_ACCOUNT_NUMBER = 2;
    const FD_BIT_3_PROCESSING_CODE = 3;
    const FD_BIT_4_AMOUNT_OF_TRANSACTION = 4;
    const FD_BIT_7_TRANSMISSION_DATETIME = 7;
    const FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER = 11;
    const FD_BIT_12_TIME_LOCAL_TRANSMISSION = 12;
    const FD_BIT_13_SALE_DATE = 13;
    const FD_BIT_14_CARD_EXPIRATION_DATE = 14;
    const FD_BIT_18_MERCHANT_CATEGORY_CODE = 18;
    const FD_BIT_22_POS_ENTRY_MODE = 22;
    const FD_BIT_23_CARD_SEQUENCE_NUMBER = 23;
    const FD_BIT_24_NETWORK_INTERNATIONAL_ID = 24;
    const FD_BIT_25_POS_CONDITION_CODE = 25;
    const FD_BIT_31_ACQUIRER_REFERENCE_DATA = 31;
    const FD_BIT_32_ACQUIRER_ID = 32;
    const FD_BIT_35_TRACK_2_DATA = 35;
    const FD_BIT_37_RETRIEVAL_REFERENCE_DATA = 37;
    const FD_BIT_38_AUTHORIZATION_IDENTIFICATION_RESPONSE = 38;
    const FD_BIT_39_RESPONSE_CODE = 39;
    const FD_BIT_41_TERMINAL_ID = 41;
    const FD_BIT_42_MERCHANT_ID = 42;
    const FD_BIT_43_ALTERNATIVE_MERCHANT_NAME = 43;
    const FD_BIT_44_ADDITIONAL_RESPONSE_DATA = 44;
    const FD_BIT_45_TRACK_1_DATA = 45;
    const FD_BIT_48_FD_PRIVATE_USE = 48;
    const FD_BIT_49_TRANSACTION_CURRENCY_CODE = 49;
    const FD_BIT_52_ENCRYPTED_PIN_DATA = 52;
    const FD_BIT_54_ADDITIONAL_AMOUNT = 54;
    const FD_BIT_55_EMV_DATA = 55;
    const FD_BIT_59_MERCHANT_ZIP = 59;
    const FD_BIT_60_ADDITIONAL_POS_INFO = 60;
    const FD_BIT_62_FD_PRIVATE_USE = 62;
    const FD_BIT_63_FD_PRIVATE_USE = 63;
    const FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE = 70;
    const FD_BIT_93_RESPONSE_INDICATOR = 93;
    const FD_BIT_96_KEY_MANAGEMENT_DATA = 96;
    const FD_BIT_100_RECEIVING_INSTITUTION_CODE = 100;

    /**
     * Bitmap 3 — Processing Code
     */
    const FD_PC_000000_CREDIT_CARD_PURCHASE = "000000";
    const FD_PC_200000_CREDIT_REFUND = "200000";
    const FD_PC_500000_BILL_PAYMENT_RECURRING = "500000";
    const FD_PC_559000_BILL_PAYMENT_REFUND = "559000";
    const FD_PC_009000_DEBIT_PURCHASE_DEFAULT_ACCOUNT = "009000";
    const FD_PC_209000_DEBIT_REFUND = "209000";
    const FD_PC_990000_TEST = "990000";

    /**
     * Bitmap 25 — Point of Service (POS) Condition Code
     */
    const FD_POS_CC_01_CUSTOMER_NOT_PRESENT = "01";
    const FD_POS_CC_04_CUSTOMER_NOT_PRESENT_RECURRING = "04";
    const FD_POS_CC_08_MAIL_PHONE_ORDER = "08";
    const FD_POS_CC_51_ACCOUNT_VERIFICATION_WITHOUT_AVS = "51"; //bit 4, amount must be 0
    const FD_POS_CC_52_ACCOUNT_VERIFICATION_WITH_AVS = "52"; //bit 4, amount must be 0
    const FD_POS_CC_59_E_COMMERCE = "59";  //Required for Visa, MasterCard and Amex and Debit PIN-Less E-Commerce transactions. POS Condition Code value of “08” should be used for all other card types.

    /**
     * Bitmap 31 — Acquirer Reference Data
     */
    const FD_ARD_0_AUTHORIZATION_ONLY = "0";
    const FD_ARD_1_AUTHORIZATION_CAPTURE = "1";
    const FD_ARD_2_CAPTURE_ONLY = "2";

    /**
     * Bitmap 39 - Response
     */
    const FD_RESPONSE_CODE_00_APPROVED = "00";
    const FD_RESPONSE_CODE_76_APPROVED_NON_CAPTURE = "76";
    const FD_RESPONSE_CODE_85_NO_REASON_TO_DECLINE = "85";
    const FD_RESPONSE_CODE_01_REFERRAL = "01";
    const FD_RESPONSE_CODE_51_DECLINE = "51";

    /**
     * Bitmap 44 - AVS
     */
    public static $avs = [
        "VISA" => [
            "A" => "Address matches, ZIP does not. Acquirer rights not implied.",
            "B" => "Street addresses match. Postal code not verified due to incompatible formats. (Acquirer sent both street address and postal code).",
            "C" => "Street address and postal code not verified due to incompatible formats. (Acquirer sent both street address and postal code).",
            "D" => "Street addresses and postal codes match.",
            "F" => "Street addresses and postal codes match. U.K. only.",
            "G" => "Address information not verified for international transaction. Issuer is not an AVS participant, or, AVS data was present in the request, but issuer did not return an AVS result, or, VISA performs AVS on behalf of the issuer, and there was no address record on file for the account.",
            "I" => "Address information not verified.",
            "M" => "Street address and postal code match.",
            "N" => "No match. Acquirer sent postal/ZIP code only, or, street address only, or, both postal code and street address. Also used when acquirer requests AVS, but sends no AVS data in Field 123.",
            "P" => "Postal code match. Acquirer sent both postal code and street address, but street address not verified due to incompatible formats.",
            "R" => "Retry: System unavailable or, timed out. Issuer ordinarily performs AVS, but was unavailable. The code R is used by V.I.P. when issuers are unavailable. Issuers should refrain from using this code.",
            "S" => "Not applicable. If present, replaced with “U” for domestic, and “G” for international by V.I.P. Available for U.S. issuers only",
            "U" => "Address not verified for domestic transaction. Issuer is not an AVS participant, or, AVS data was present in the request, but the issuer did not return an AVS result, or, VISA performs AVS on behalf of the issuer, and there was no address on file for this account.",
            "W" => "Not applicable. If present, replaced with “Z” by V.I.P. Available to U.S. issuers only",
            "X" => "Not applicable. If present, replaced with”Y” by V.I.P. Available for U.S. issuers only.",
            "Y" => "Street address and postal code match.",
            "Z" => "Postal/ZIP matches; street address does not match or Street address not included in request.",
        ],
        "MASTERCARD" => [
            "X" => "Exact: Address and 9-digit ZIP Match",
            "Y" => "Yes: Address and 5-digit ZIP Match",
            "A" => "Address: Address Matches ZIP Does Not Match",
            "W" => "Whole Zip: 9-digit ZIP Matches, Address Does Not Match",
            "Z" => "Zip: 5-digit ZIP Matches, Address Does Not Match",
            "N" => "No: Address and ZIP Do Not Match",
            "U" => "Address Info is Unavailable",
            "R" => "Retry: System Unavailable or Timeout",
            "E" => "Error: Transaction ineligible for address verification or edit error found in the message that prevents AVS from being performed",
            "S" => "Service Not Supported: Issuer does not support address verification",
        ],
        "DISCOVER" => [
            "X" => "Address matches, 9 digit Zip Code matches",
            "Y" => "Address matches, 5 digit Zip Code matches",
            "A" => "Address matches, Zip Code does not",
            "W" => "9-digit Zip matches, address does not",
            "Z" => "5-digit Zip matches, address does not",
            "N" => "Nothing matches",
            "U" => "No Data from Issuer/Auth System",
            "R" => "Retry, system unable to process",
            "S" => "AVS not supported at this time",
            "G" => "Address information not verified for international transaction",
        ],
        "JCB" => [
            "X" => "Address matches, 9 digit Zip Code matches",
            "Y" => "Address matches, 5 digit Zip Code matches",
            "A" => "Address matches, Zip Code does not",
            "W" => "9-digit Zip matches, address does not",
            "Z" => "5-digit Zip matches, address does not",
            "N" => "Nothing matches",
            "U" => "No Data from Issuer/Auth System",
            "R" => "Retry, system unable to process",
            "S" => "AVS not supported at this time",
            "G" => "Address information not verified for international transaction",
        ],
        "AMEX" => [
            "Y" => "Address and ZIP Match",
            "A" => "Address Matches ZIP Does Not Match",
            "Z" => "9 or 5 digit ZIP Matches, Address Does Not Match",
            "N" => "Address and ZIP Do Not Match",
            "U" => "Address Information Is Unavailable",
            "R" => "System Unavailable or Timeout",
            "S" => "Issuer Does Not Support Address Verification",
            "L" => "Cardmember Name and Billing Postal Code match",
            "M" => "Cardmember Name, Billing Address and Postal Code match",
            "O" => "Cardmember Name and Billing Address match",
            "K" => "Cardmember Name matches",
            "D" => "Cardmember Name incorrect, Billing Postal Code matches",
            "E" => "Cardmember Name incorrect, Billing Address and Postal Code match",
            "F" => "Cardmember Name incorrect, Billing Address matches",
            "W" => "No, Cardmember Name, Billing Address and Postal Code are all incorrect",
        ],
    ];

    /**
     * Bitmap 70 - Network Management
     */
    const FD_NETWORK_MANAGEMENT_0811_NEW_KEY_REQUEST = "0811";
    const FD_NETWORK_MANAGEMENT_0301_KEEP_ALIVE = "0301";
    const FD_NETWORK_MANAGEMENT_0001_LOG_ON = "0001";
    const FD_NETWORK_MANAGEMENT_0842_SECURITY_KEY_UPDATE = "0842";
    const FD_NETWORK_MANAGEMENT_0887_TOKEN_ONLY_REQUEST = "0887";
    const FD_NETWORK_MANAGEMENT_0940_FILE_DOWNLOAD_REQUEST = "0940";

    /**
     * Data length
     */
    const ISO8583_BIT_FIXED_LENGTH = 0;
    const ISO8583_BIT_DYNAMIC_LENGTH = 1;

    /**
     * Data Type
     */
    const ISO8583_DATA_TYPE_ALPHA = "a"; //Alphabetic characters
    const ISO8583_DATA_TYPE_BCD = "bcd"; //Binary-Coded Decimal: Binary representation of each digit by 4 bits, which in general represent the values/digits/characters 0-9.
    const ISO8583_DATA_TYPE_NUMERIC = "n"; //Numeric characters in the ASCII/EBCDIC text range
    const ISO8583_DATA_TYPE_ALPHA_NUMERIC = "an"; //Alphanumeric (alphabetic and numeric characters)
    const ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR = "ans"; //Alphabetic, numeric, and special characters
    const ISO8583_DATA_TYPE_HEX_BINARY = "hb"; //Hex Binary representation of data
    const ISO8583_DATA_TYPE_BINARY = "b"; //Binary representation of data
    const ISO8583_DATA_TYPE_LLL = "lll"; //Length of variable field that follows. ‘LL’-Two-digit length indicator (1 byte BCD) ‘LLL-3-digit length indicator (2 bytes BCD)
    const ISO8583_DATA_TYPE_LL = "ll"; //Length of variable field that follows. ‘LL’-Two-digit length indicator (1 byte BCD) ‘LLL-3-digit length indicator (2 bytes BCD)
    const ISO8583_DATA_TYPE_Z = "z"; //Tracks 2 and 3 code set as defined in ISO 7811 and ISO 7813

    /**
     * ISO DATA Element
     *  Attributes:
     *      size: required, in byte
     *      type: required, [n, hb, an, ans]
     *      isVariable: required, [FIXED_LENGTH, DYNAMIC_LENGTH], indicating whether the data is fixed length or dynamic length
     *      fill: optional, apply to fixed length only
     *      pad: optional, apply to fixed length only
     *      lengthIndicator: optional, num of bytes to prefix the data to indicate the length of the data
     *      errorCheck: optional, custom error check code
     */
    const DATA_ELEMENT_TYPE = "type";
    const DATA_ELEMENT_SIZE = "size";
    const DATA_ELEMENT_IS_VARIABLE = "isVariable";
    const DATA_ELEMENT_FILL = "fill";
    const DATA_ELEMENT_PAD = "pad";
    const DATA_ELEMENT_LENGTH_INDICATOR = "lengthIndicator";
    const DATA_ELEMENT_ERROR_CHECK = "errorCheck";

    public static $dataElements = [
        self::FD_MTI => [
            self::DATA_ELEMENT_TYPE  => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH
        ],
        self::FD_BITMAP => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_HEX_BINARY,
            self::DATA_ELEMENT_SIZE => 8,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH
        ],
        self::ISO8583_DATA_TYPE_LLL => [
            self::DATA_ELEMENT_TYPE  => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::ISO8583_DATA_TYPE_LL => [
            self::DATA_ELEMENT_TYPE  => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 1,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 10,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH,
            //self::DATA_ELEMENT_LENGTH_INDICATOR => 1,
        ],
        self::FD_BIT_3_PROCESSING_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 3,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_ERROR_CHECK => "checkBit3ProcessCode",
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_4_AMOUNT_OF_TRANSACTION => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 6,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_7_TRANSMISSION_DATETIME => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 5,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 3,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_12_TIME_LOCAL_TRANSMISSION => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 3,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_13_SALE_DATE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_14_CARD_EXPIRATION_DATE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_18_MERCHANT_CATEGORY_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_22_POS_ENTRY_MODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_23_CARD_SEQUENCE_NUMBER => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_24_NETWORK_INTERNATIONAL_ID => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_25_POS_CONDITION_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 1,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_31_ACQUIRER_REFERENCE_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            self::DATA_ELEMENT_SIZE => 99,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH,
            self::DATA_ELEMENT_LENGTH_INDICATOR => 1,
        ],
        self::FD_BIT_32_ACQUIRER_ID => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 6,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_35_TRACK_2_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_Z,
            self::DATA_ELEMENT_SIZE => 37,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_37_RETRIEVAL_REFERENCE_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            self::DATA_ELEMENT_SIZE => 12,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_38_AUTHORIZATION_IDENTIFICATION_RESPONSE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            self::DATA_ELEMENT_SIZE => 6,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_39_RESPONSE_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_41_TERMINAL_ID => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 8,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_42_MERCHANT_ID => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 15,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_43_ALTERNATIVE_MERCHANT_NAME => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 107,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH
        ],
        self::FD_BIT_44_ADDITIONAL_RESPONSE_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 3,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_LENGTH_INDICATOR => 2,
        ],
        self::FD_BIT_45_TRACK_1_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 76,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_48_FD_PRIVATE_USE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 119,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH,
            self::DATA_ELEMENT_LENGTH_INDICATOR => 2,
        ],
        self::FD_BIT_49_TRANSACTION_CURRENCY_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_52_ENCRYPTED_PIN_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_HEX_BINARY,
            self::DATA_ELEMENT_SIZE => 8,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH
        ],
        self::FD_BIT_54_ADDITIONAL_AMOUNT => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 12,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_55_EMV_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 999,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_59_MERCHANT_ZIP => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 9,
            self::DATA_ELEMENT_LENGTH_INDICATOR => 1,
            self::DATA_ELEMENT_FILL => " ",
            self::DATA_ELEMENT_PAD => STR_PAD_RIGHT,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH
        ],
        self::FD_BIT_60_ADDITIONAL_POS_INFO => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 1,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_62_FD_PRIVATE_USE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 999,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_63_FD_PRIVATE_USE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 999,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH,
            self::DATA_ELEMENT_LENGTH_INDICATOR => 2,
        ],
        self::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 2,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH,
            self::DATA_ELEMENT_FILL => "0",
            self::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        self::FD_BIT_93_RESPONSE_INDICATOR => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR,
            self::DATA_ELEMENT_SIZE => 5,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_FIXED_LENGTH
        ],
        self::FD_BIT_96_KEY_MANAGEMENT_DATA => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BINARY,
            self::DATA_ELEMENT_SIZE => 18,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
        self::FD_BIT_100_RECEIVING_INSTITUTION_CODE => [
            self::DATA_ELEMENT_TYPE => self::ISO8583_DATA_TYPE_BCD,
            self::DATA_ELEMENT_SIZE => 11,
            self::DATA_ELEMENT_IS_VARIABLE => self::ISO8583_BIT_DYNAMIC_LENGTH
        ],
    ];

    /**
     * List of Tables for Bit 63
     */
    const FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA = "14";
    const FD_BIT_63_TABLE_36_ADDITIONAL_DATA = "36";
    const FD_BIT_63_TABLE_22_HOST_RESPONSE = "22";
    const FD_BIT_63_TABLE_49_CARD_CODE_VALUE = "49";
    const FD_BIT_63_TABLE_60_ELECTRONIC_COMMERCE_INDICATOR = "60";
    const FD_BIT_63_TABLE_VI_COMPLIANCE = "VI";
    const FD_BIT_63_TABLE_MC_COMPLIANCE = "MC";
    const FD_BIT_63_TABLE_DS_COMPLIANCE = "DS";
    const FD_BIT_63_TABLE_55_MERCHANT_ADVICE_CODE = "55";
    const FD_BIT_63_TABLE_SP_TRANS_ARMOR = "SP";
    const FD_BIT_63_TABLE_SK_AMEX_SAFE_KEY = "SK";

    const TABLE_60_INDICATOR_07_CHANNEL_ENCRYPTION = "07";
    const TABLE_60_INDICATOR_02_RECURRING_TRANSACTION = "02";

    /**
     * Private Variables
     */
    private $_mti	= '';
    private $_mtiEncoded = '';

    private $_data	= [];
    private $_dataEncoded = [];

    private $_bitmap = '';
    private $_bitmapEncoded = '';

    //a temporary holder to add private data with multi-tables
    private $_tableData = [];

    /**
     * setData
     *
     * @param int $bit
     * @param $data
     * @throws \Exception
     */
    public function setData(int $bit, string $data)
    {
        if ($bit < 1 || $bit > 128) {
            throw new \Exception("Invalid bit number");
        }

        if (!isset(self::$dataElements[$bit])) {
            throw new \Exception("The bitmap is not supported.");
        }

        /**
         * If we have custom error check, use it first
         */
        if (isset(self::$dataElements[$bit]['errorCheck'])) {
            $errorCheck = self::$dataElements[$bit]['errorCheck'];
            self::$errorCheck($data);
        }

        list($result, $encodedResult) = self::getDataValue($data, self::$dataElements[$bit]);
        $this->setBitData($bit, $result, $encodedResult);
    }

    /**
     * addDataTable
     * @param int $bit
     * @param string $tableId
     * @param array $tableData
     */
    public function addDataTable(int $bit, string $tableId, array $tableData)
    {
        $this->_tableData[$bit][$tableId] = $tableData;
        $this->resetDataTable($bit);
    }

    /**
     * setBitData
     *
     * @param int $bit
     * @param string $data
     * @param string $encodedData
     */
    private function setBitData(int $bit, string $data, string $encodedData)
    {
        $this->_data[$bit] =  $data;
        $this->_dataEncoded[$bit] = $encodedData;
    }

    /**
     * Add MTI
     * @param string $mti
     * @throws \Exception
     */
    public function setMTI(string $mti)
    {
        if (strlen($mti) !== 4 || !ctype_digit($mti)) {
            throw new \Exception("Invalid MTI length and format");
        }

        if (!in_array($mti, [
                self::FD_MTI_CREDIT_AUTH_REQUEST, self::FD_MTI_CREDIT_AUTH_RESPONSE,
                self::FD_MTI_NETWORK_MANAGEMENT_REQUEST,self::FD_MTI_NETWORK_MANAGEMENT_RESPONSE,
                self::FD_MTI_DEBIT_AUTH_REQUEST, self::FD_MTI_DEBIT_AUTH_RESPONSE,
                self::FD_MTI_REVERSAL_REQUEST, self::FD_MTI_REVERSAL_RESPONSE])) {
            throw new \Exception("Invalid MTI");
        }

        $this->_mti	= $mti;
        $this->_mtiEncoded	= self::hexToAscii($mti);
    }

    /**
     * getData
     * @return array
     */
    public function getData() :array
    {
        ksort($this->_data);
        return $this->_data;
    }

    /**
     * Get Bit Data
     * @param int $bit
     * @return string
     */
    public function getBitData(int $bit) :string
    {
        return $this->_data[$bit] ?? "";
    }

    /**
     * getBitDataTable
     * @param int $bit
     * @return array
     */
    public function getBitDataTable(int $bit) :array
    {
        return $this->_tableData[$bit] ?? [];
    }

    /**
     * getBitDataTableByTableId
     * @param int $bit
     * @param string $tableId
     * @return array
     */
    public function getBitDataTableByTableId(int $bit, string $tableId) :array
    {
        $bitDataTable = $this->getBitDataTable($bit);
        return $bitDataTable[$tableId] ?? [];
    }

    /**
     * getBitDataEncoded
     * @param int $bit
     * @return string
     */
    public function getBitDataEncoded(int $bit) :string
    {
        return $this->_dataEncoded[$bit] ?? "";
    }

    /**
     * getEncodedData
     * @return array
     */
    public function getEncodedData() :array
    {
        ksort($this->_dataEncoded);
        return $this->_dataEncoded;
    }

    /**
     * getBitmap
     * @return string
     */
    public function getBitmap() :string
    {
        if (empty($this->_bitmap)) {
            $this->calculateBitmap();
        }
        return $this->_bitmap;
    }

    /**
     * Get Encoded Bitmap
     * @return string
     */
    public function getEncodedBitmap() :string
    {
        if (empty($this->_bitmap)) {
            $this->calculateBitmap();
        }
        return $this->_bitmapEncoded;
    }

    /**
     * getMTI
     * @return string
     */
    public function getMTI() {
        return $this->_mti;
    }

    /**
     * getEncodedMTI
     * @return string
     */
    public function getEncodedMTI()
    {
        return $this->_mtiEncoded;
    }

    /**
     * Get ISO
     * @return string
     */
    public function getISO() :string
    {
        return $this->getMTI() . $this->getBitmap() . implode("", $this->getData());
    }

    /**
     * Get Encoded ISO
     *
     * @return string
     */
    public function getEncodedISO() :string
    {
        return $this->getEncodedMTI() . $this->getEncodedBitmap() . implode("", $this->getEncodedData());
    }

    /**
     * resetDataTable
     * @param int $bit
     */
    private function resetDataTable(int $bit)
    {
        if (!empty($this->_tableData[$bit])) {
            ksort($this->_tableData[$bit]);

            $totalLength = 0;
            $iso = "";
            $encodedISO = "";
            foreach ($this->_tableData[$bit] as $tableID => $tableData) {
                $tableLength = str_pad((string) $tableData['tableLength'], 4, "0", STR_PAD_LEFT);
                $iso .= $tableLength . $tableID . $tableData['data'];
                $encodedISO .= self::hexToAscii($tableLength) . $tableID . $tableData['encodedData'];
                $totalLength += $tableData['tableLength'] + 2;
            }

            $tableLength = str_pad((string) $totalLength,4,"0", STR_PAD_LEFT);
            $this->setBitData($bit, $tableLength . $iso, self::hexToAscii($tableLength) . $encodedISO);
        }
    }

    /**
     * calculateBitmap
     */
    private function calculateBitmap()
    {
        $bitmap = array_fill(1, 64, 0);
        $extendBitmap = array_fill(65, 64, 0);
        foreach ($this->_data as $bit => $data) {
            if ($bit >= 65) {
                $bitmap[1] = 1;
                $extendBitmap[$bit] = 1;
            } else {
                $bitmap[$bit] = 1;
            }
        }
        if ($bitmap[1] === 1) {
            $bitmap += $extendBitmap;
        }
        $bytemap = array_chunk($bitmap, 4, true);
        $result = "";
        foreach ($bytemap as $bits){
            $result .= strtoupper(dechex(bindec(implode("", $bits))));
        }

        $this->setBitmap($result);
    }

    /**
     * Set bitmap and bitmap encoding
     * @param string $bitmap
     */
    private function setBitmap(string $bitmap)
    {
        $this->_bitmap	= $bitmap;
        $this->_bitmapEncoded = self::hexToAscii($bitmap);
    }
}

?>