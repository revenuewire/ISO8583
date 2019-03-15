<?php
/**
 * Created by IntelliJ IDEA.
 * User: swang
 * Date: 2018-06-05
 * Time: 3:47 PM
 */

declare(strict_types=1);

/**
 *  Implement the following spec
 *
 *  1. FirstData ISO 8583 Global Specification - Version 2017-2a
 *  2. FirstData Secure Transport XML Interface Reference Guide - Product Version 3.2 Document Revision 1.12
 *  3. FirstData ISO 8583 Global TransArmor Addendum Document - Version 2016-1a
 */
namespace RW\ISO8583;

/**
 * Class ISO8583FDBitDataExtend
 * The purpose of this class is to extend the ISO8583 standard data bit to work with FD Spec including FD validation
 * check.
 *
 * @package RW\PayAPI\Helpers
 */
trait ISO8583FDBitDataExtend
{
    /**
     * Bitmap 3 - Processing Code Position 1-2
     * @var array
     */
    public static $processCode12 = [
        "00" => "Goods and Services (Authorizations and Full Reversals)/Pre-Authorization Purchase with Cash Back ( Canada Debit only )",
        "01" => "Withdrawal/cash advance",
        "02" => "Adjustment of a return (Canada Debit only)",
        "04" => "Check verification",
        "09" => "Purchase with Cash Back ( excluding Canada Debit )",
        "20" => "Return/Refund (Debit and Credit Host Capture) Partial reversal (Credit only)",
        "21" => "Payment (only specified private label formats)",
        "22" => "Payment Reversal (only specified private label formats) Adjustment of a Sale (Debit only)",
        "28" => "Load or Load/Activate Prepaid Card Account (Visa Only)",
        "30" => "Available Funds Inquiry (non-captured)",
        "31" => "Balance Inquiry on Prepaid Debit/ EBT cards",
        "45" => "Capture Only/Force Post Transaction (Host Draft Capture Merchants Only)",
        "50" => "Debit 'Bill Payment' Purchase for Card Not Present transactions (Internet, VRU, Call-Center, and Recurring). Not applicable for EMV transactions.",
        "54" => "Debit 'POS PINless' Purchase for Card Present, Magstripe Swipe transactions only. Not applicable for EMV transactions.",
        "55" => "Debit 'Bill-Payment' Refund/Return for Card Not Present transactions (Internet, VRU, Call-Center, and Recurring) Not applicable for EMV transactions.",
        "59" => "Debit 'POS PINless' Refund/Return for Card Present, Magstripe Swipe transactions only. Not applicable for EMV transactions.",
        "72" => "Activate Prepaid Card Account (Visa Only)",
        "90" => "Signature Capture Data Present or Debit Key Exchange",
        "99" => "Test transaction (0800 - Network Admin)",
    ];

    /**
     * Bitmap 3 - Processing Code Position 3-4
     * @var array
     */
    public static $processCode34 = [
        "00" => "Default Account (Credit and check only)",
        "10" => "Savings Account (Debit — Canada only)",
        "20" => "Checking Account (Debit — Canada only)",
        "90" => "Default Account (Debit only)",
        "96" => "Cash Benefits Account (EBT Only)",
        "98" => "Food Stamps Account (EBT Only)",
    ];

    /**
     * Bitmap 3 - Processing Code Position 5-6
     * @var array
     */
    public static $processCode56 = [
        "00" => "Default Account (Credit, Debit, check, and EBT)",
        "10" => "Savings Account (Debit — Canada only)",
        "20" => "Checking Account (Debit — Canada only)",
        "46" => "Force Post Transaction (logged to Billing File, not switched to Association) for debit only",
    ];

    /**
     * Encode bit string
     *
     * @param string $bitString
     * @return string
     */
    public static function hexToAscii(string $bitString):string
    {
        if ($bitString === "") {
            return "";
        }

        $result = "";
        $bits = str_split($bitString, 2);
        foreach ($bits as $hex) {
            $ascii = mb_convert_encoding(hex2bin($hex), "ASCII");
            $result .= ctype_alnum($ascii) ? $ascii : "|" . $hex; //if printable ascii, use it
        }

        return $result;
    }

    /**
     * getBit2PrimaryAccountNumber
     *
     * @param string $data
     * @return string
     * @throws \Exception
     */
    public static function getBit2PrimaryAccountNumber(string $data) :string
    {
        if (!is_numeric($data) || strlen($data) > 19) {
            throw new \Exception("Invalid primary account number.");
        }
        $dataLength = $actualLength = strlen($data);

        if ($dataLength % 2 !== 0) {
            $dataLength += 1;
        }

        $data = str_pad((string) $actualLength, 2, "0", STR_PAD_LEFT)
                    . str_pad($data, $dataLength, "0", STR_PAD_RIGHT);
        return $data;
    }

    /**
     * setBit3ProcessCode
     *
     * @param string $processCode
     * @throws \Exception
     */
    public static function checkBit3ProcessCode(string $processCode)
    {
        if (!isset(self::$processCode12[substr($processCode, 0, 2)])){
            throw new \Exception("Invalid Bit3ProcessCode in position 1 & 2");
        }

        if (!isset(self::$processCode34[substr($processCode, 2, 2)])){
            throw new \Exception("Invalid Bit3ProcessCode in position 3 & 4");
        }

        if (!isset(self::$processCode56[substr($processCode, 4, 2)])){
            throw new \Exception("Invalid Bit3ProcessCode in position 5 & 6");
        }
    }

    /**
     * getBit43AlternativeMerchantName
     * @param string $merchantName
     * @param string $streetAddress
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string $countryCode 2-char country code [US/CA] only
     * @param string $serviceEntitlementNumber
     * @return string
     * @throws \Exception
     */
    public static function getBit43AlternativeMerchantName(string $merchantName, string $streetAddress, string $city,
                                                           string $state, string $zip, string $countryCode,
                                                           string $serviceEntitlementNumber = "") :string
    {
        $fields = [
            'merchantName' => ["min" => 1, "max" => 30, "data" => trim($merchantName)],
            'streetAddress' => ["min" => 1, "max" => 25, "data" => trim($streetAddress)],
            'city' => ["min" => 1, "max" => 20, "data" => trim($city)],
            'state' => ["min" => 2, "max" => 2, "data" => ($countryCode == "US" || $countryCode == "CA") ? trim($state) : "  "],
            'country' => ["min" => 0, "max" => 3, "data" => ""],
            'zip' => ["min" => 5, "max" => 9, "data" => trim($zip)],
            'countryCode' => ["min" => 3, "max" => 3, "data" => isset(self::$countryCodeMap[$countryCode]) ?  self::$countryCodeMap[$countryCode] : trim($countryCode)],
            'serviceEntitlementNumber' => ["min" => 0, "max" => 15, "data" => trim($serviceEntitlementNumber)],
        ];
        $data = "";
        foreach ($fields as $k => $fieldCheck) {
            if ($countryCode === "US" && $k === "state" && !in_array($state, self::$americaStates)) {
                throw new \Exception("Invalid state for US. [$state]");
            }
            if ($countryCode === "CA" && $k === "state" && !in_array($state, self::$canadianStates)) {
                throw new \Exception("Invalid state for CA. [$state]");
            }
            if (strlen($fieldCheck['data']) < $fieldCheck["min"] || strlen($fieldCheck['data']) > $fieldCheck["max"]) {
                throw new \Exception("$k data is invalid. The size of the string must be between {$fieldCheck["min"]} and {$fieldCheck["max"]}");
            }
            $data .= str_pad($fieldCheck['data'], $fields[$k]['max'], " ", STR_PAD_RIGHT);
        }

        return strtoupper($data);
    }

    /**
     * getBit48MessageDataForAddressVerification
     * @param string $cardholderZip
     * @param string $cardholderStreet
     * @return string
     * @throws \Exception
     */
    public static function getBit48MessageDataForAddressVerification(string $cardholderZip, string $cardholderStreet = "") :string
    {
        $cardholderZip = trim($cardholderZip);
        if (empty($cardholderZip)) {
            throw new \Exception("Cardholder Zip is required.");
        }

        if (strlen($cardholderZip) > 9) {
            throw new \Exception("Cardholder Zip cannot be longer than 9 characters.");
        }

        $cardholderStreet = trim($cardholderStreet);
        if (strlen($cardholderStreet) > 20) {
            throw new \Exception("Cardholder street cannot be longer than 20 characters.");
        }

        $data = "99" . str_pad($cardholderZip, 9, " ", STR_PAD_RIGHT)
            . str_pad($cardholderStreet, 20, " ", STR_PAD_RIGHT);

        return $data;
    }
    /**
     * getDataValue
     *
     * @param string $data
     * @param array $dataDefinition
     * @return array
     * @throws \Exception
     */
    public static function getDataValue(string $data, array $dataDefinition) : array
    {
        if (!isset($dataDefinition[self::DATA_ELEMENT_TYPE])
            || !isset($dataDefinition[self::DATA_ELEMENT_SIZE])
            || !is_int($dataDefinition[self::DATA_ELEMENT_SIZE])) {
            throw new \Exception("Invalid data definition");
        }

        /**
         * Generic data type check
         */
        switch ($dataDefinition[self::DATA_ELEMENT_TYPE]) {
            case self::ISO8583_DATA_TYPE_BCD:
                $maxSize = $dataDefinition[self::DATA_ELEMENT_SIZE]*2;
                if (strlen($data) > $maxSize) {
                    throw new \Exception("Invalid data size");
                }

                if (!ctype_digit($data)) {
                    throw new \Exception("Invalid data format");
                }
                break;
            case self::ISO8583_DATA_TYPE_ALPHA_NUMERIC:
                $maxSize = $dataDefinition[self::DATA_ELEMENT_SIZE];
                if (strlen($data) > $maxSize) {
                    throw new \Exception("Invalid data size");
                }
                break;
            default:
                $maxSize = $dataDefinition[self::DATA_ELEMENT_SIZE];
                break;
        }

        /**
         * See if we need pad the string
         */
        $result = $data;
        if (isset($dataDefinition[self::DATA_ELEMENT_FILL]) && isset($dataDefinition[self::DATA_ELEMENT_PAD])) {
            $fill = $dataDefinition[self::DATA_ELEMENT_FILL];
            $pad = $dataDefinition[self::DATA_ELEMENT_PAD];
            $result = str_pad($result, $maxSize, $fill, $pad);
        }

        /**
         * See if we need prefix the length indicator
         */
        $length = "";
        if (isset($dataDefinition[self::DATA_ELEMENT_LENGTH_INDICATOR]) && is_numeric($dataDefinition[self::DATA_ELEMENT_LENGTH_INDICATOR])) {
            $length = str_pad((string)strlen($result), $dataDefinition[self::DATA_ELEMENT_LENGTH_INDICATOR] * 2, "0", STR_PAD_LEFT);
        }

        if ($dataDefinition[self::DATA_ELEMENT_TYPE] === self::ISO8583_DATA_TYPE_BCD) {
            $encodedResult = self::hexToAscii($result);
        } else {
            $encodedResult = $result;
        }

        return [$length . $result, self::hexToAscii($length) . $encodedResult];
    }

    /**
     * decodeEncodedISO
     *
     * @param string $encodedISO
     * @return ISO8583
     * @throws \Exception
     */
    public static function decodeEncodedISO(string $encodedISO) : ISO8583
    {
        /**
         * Parse out the MTI
         */
        $iso8583 = new ISO8583();

        $mtiDefinition = self::$dataElements[self::FD_MTI];
        list($mti, $used) = self::getDataFromEncodedISO($encodedISO, $mtiDefinition[self::DATA_ELEMENT_SIZE], $mtiDefinition[self::DATA_ELEMENT_TYPE]);
        $iso8583->setMTI($mti);
        $encodedISO = substr($encodedISO, $used);

        /**
         * Try to parse out Bitmap, the bitmap should start at pos 6, with a length up to 4 chars
         */
        $bitmapDefinition = self::$dataElements[self::FD_BITMAP];
        list($bitmap, $used) = self::getDataFromEncodedISO($encodedISO, $bitmapDefinition[self::DATA_ELEMENT_SIZE], $bitmapDefinition[self::DATA_ELEMENT_TYPE]);
        $encodedISO = substr($encodedISO, $used);
        $binaryBitmap = str_pad(decbin(hexdec($bitmap)), 64, "0", STR_PAD_LEFT);
        if ($binaryBitmap[0] === "1") { //extended bitmap
            list($bitmapExtended, $used) = self::getDataFromEncodedISO($encodedISO, $bitmapDefinition[self::DATA_ELEMENT_SIZE], $bitmapDefinition[self::DATA_ELEMENT_TYPE]);
            $encodedISO = substr($encodedISO, $used);
            $bitmap .= $bitmapExtended;
            $binaryBitmap .= str_pad(decbin(hexdec($bitmapExtended)), 64, "0", STR_PAD_LEFT);
        }
        $iso8583->setBitmap($bitmap);
        $bitmaps = str_split($binaryBitmap);

        foreach ($bitmaps as $k => $active) {
            if ($active === "1") {
                $bit = $k + 1;
                if (!isset(self::$dataElements[$bit])){
                    continue;
                }

                if ($bit === self::FD_BIT_63_FD_PRIVATE_USE && $iso8583->getBitData(self::FD_BIT_3_PROCESSING_CODE) !== ISO8583::FD_PC_990000_TEST){
                    list($tables, $used) = $iso8583->decodeDataTable($encodedISO);
                    foreach ($tables as $tableId => $tableData) {
                        $iso8583->addDataTable($bit, (string) $tableId, $tableData);
                    }
                } else {
                    $bitDefinition = self::$dataElements[$bit];
                    $type = $bitDefinition[self::DATA_ELEMENT_TYPE];

                    if (isset($bitDefinition[self::DATA_ELEMENT_LENGTH_INDICATOR])) {
                        list($data, $used) = self::getDataFromEncodedISO($encodedISO, $bitDefinition[self::DATA_ELEMENT_LENGTH_INDICATOR], self::ISO8583_DATA_TYPE_BCD);
                        $size = (int) $data; //including the data size plus length indicator
                        $encodedISO = substr($encodedISO, $used);
                    } else {
                        $size = $bitDefinition[self::DATA_ELEMENT_SIZE];
                    }

                    list($data, $used) = self::getDataFromEncodedISO($encodedISO, $size, $type);
                    $iso8583->setData($bit, $data);
                }

                $encodedISO = substr($encodedISO, $used);
            }
        }

        return $iso8583;
    }

    /**
     * getDataFromEncodedISO
     *
     * @param string $encodedISO
     * @param int $lengthInBytes
     * @param string $type
     * @return array
     * @throws \Exception
     */
    public static function getDataFromEncodedISO(string $encodedISO, int $lengthInBytes, string $type) : array
    {
        $numOfBytes = 0;
        $used = 0;
        $data = "";
        while($numOfBytes < $lengthInBytes) {
            if ($encodedISO[0] === "|") {
                $byte = substr($encodedISO, 1, 2);
                $encodedISO = substr($encodedISO, 3);
                $used += 3;
            } else {
                $byte = bin2hex($encodedISO[0]);
                $encodedISO = substr($encodedISO, 1);
                $used += 1;
            }

            //switch hex to the result string
            switch ($type) {
                case self::ISO8583_DATA_TYPE_BCD:
                case self::ISO8583_DATA_TYPE_HEX_BINARY:
                    $data .= $byte;
                    break;
                case self::ISO8583_DATA_TYPE_ALPHA_NUMERIC:
                case self::ISO8583_DATA_TYPE_ALPHA_NUMERIC_SPECIAL_CHAR:
                    $data .= hex2bin($byte);
                    break;
                default:
                    throw new \Exception("Unknown format");
            }

            $numOfBytes++;
        }

        return [$data, $used];
    }

    /**
     * decodeBit44AVS
     *
     * @param string $cardType
     * @param string $encodedData
     * @return array
     * @throws \Exception
     */
    public static function decodeBit44AVS(string $cardType, string $encodedData)
    {
        list($length, $used) = self::getDataFromEncodedISO($encodedData, 2, ISO8583::ISO8583_DATA_TYPE_BCD);
        $encodedData = substr($encodedData, $used);

        list($avs, $used) = self::getDataFromEncodedISO($encodedData, (int) $length, ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC);

        return [
            "avs" => $avs,
            "avs-context" => self::$avs[$cardType][$avs] ?? ""
        ];

    }
}