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

trait ISO8583FDTableExtend
{
    /**
     * Adding context to help us understand the result better
     */
    private static $aciCorresponds = [
        "A" => "Card present",
        "E" => "Card present with merchant name and location data",
        "C" => "Card present with merchant name and location data (cardholder activated, self service terminal)",
        "V" => "Card-notpresent (AVS)",
        "K" => "Key Entered Transaction. A problem was encountered during reading of magnetic stripe data.",
        "R" => "Card-notpresent (AVS not required)",
        "T" => "Transaction cannot participate in CPS programs",
        "P" => "Card-notpresent (preferred customer participation)",
        "S" => "Card not present, 3-D secure attempt",
        "I" => "Incremental authorization",
        "F" => "Card not present, Account Funding",
        "U" => "Card not present, 3-D secure",
        "W" => "Card not present, non-3D secure",
        "N" => "Not a custom payment service transaction",
        "B" => "Tokenized ecommerce with mobile device (Payment Token)",
        "J" => "Card not presentRecurring bill payment",
    ];

    /**
     * Bit 63 Table 14 Additional VISA
     * @var array
     */
    private static $bit63Table14AdditionalVISA = [
        "aci" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
        ],
        "tranID" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 15,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "validationCode" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 4,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "marketIndicator" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "rps" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "firstAuthorizedAmount" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "totalAuthorizedAmount" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
    ];

    /**
     * Bit 63 Table 14 Additional MC
     * @var array
     */
    private static $bit63Table14AdditionalMC = [
        "aci" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
        ],
        "bankNetDate" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 4,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "bankNetReference" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 9,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter1" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 2,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "cvcErrorCode" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "posEntryMode" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "transactionEditErrorCode" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter2" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "marketIndicator" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter3" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 13,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "totalAuthorizedAmount" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
    ];

    /**
     * Bit 63 Table 14 Additional DS
     * @var array
     */
    private static $bit63Table14AdditionalDS = [
        "cardType" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
        ],
        "transactionID" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 15,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter1" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 6,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter2" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "totalAuthorizedAmount" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
    ];

    /**
     * Bit 63 Table 14 Additional Amex
     * @var array
     */
    private static $bit63Table14AdditionalAmex = [
        "indicator" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 1,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
        ],
        "transactionID" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 15,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter1" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 6,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "posData" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "filter2" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 12,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "sellerID" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 20,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_RIGHT,
        ],
    ];

    private static $bit63TableSK = [
        "indicator" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
            ISO8583::DATA_ELEMENT_SIZE => 2,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => " ",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ],
        "aesk" => [
            ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_BCD,
            ISO8583::DATA_ELEMENT_SIZE => 20,
            ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
            ISO8583::DATA_ELEMENT_FILL => "0",
            ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
        ]
    ];

    /**
     * decodeDataTable
     *
     * @param string $encodedISO
     * @return array
     * @throws \Exception
     */
    public static function decodeDataTable(string $encodedISO) : array
    {
        $tables = [];
        $totalUsed = 0;
        $dataLLLDefinition = self::$dataElements[self::ISO8583_DATA_TYPE_LLL];

        list($totalLength, $used) = self::getDataFromEncodedISO($encodedISO, $dataLLLDefinition[self::DATA_ELEMENT_SIZE], $dataLLLDefinition[self::DATA_ELEMENT_TYPE]);
        $encodedISO = substr($encodedISO, $used);
        $totalUsed += $used;

        $consumedBytes = 0;
        while ($consumedBytes < (int) $totalLength) {
            list($tableDataLength, $used) = self::getDataFromEncodedISO($encodedISO, $dataLLLDefinition[self::DATA_ELEMENT_SIZE], $dataLLLDefinition[self::DATA_ELEMENT_TYPE]);
            $encodedISO = substr($encodedISO, $used);
            $consumedBytes += 2;
            $totalUsed += $used;

            list($tableId, $used) = self::getDataFromEncodedISO($encodedISO, 2, self::ISO8583_DATA_TYPE_ALPHA_NUMERIC);
            $encodedISO = substr($encodedISO, $used);
            $totalUsed += $used;

            list($tableData, $used) = self::getDataFromEncodedISO($encodedISO, (int) $tableDataLength-2, self::ISO8583_DATA_TYPE_ALPHA_NUMERIC);
            $encodedTableData = substr($encodedISO, 0, $used);
            $encodedISO = substr($encodedISO, $used);
            $consumedBytes += $tableDataLength;
            $totalUsed += $used;

            //$this->setPrivateTableData($bit, $tableId, (int) $tableDataLength, $tableData, $encodedTableData);
            $tables[$tableId] = [
                'tableLength' => (int) $tableDataLength,
                'data' => $tableData,
                'encodedData' => $encodedTableData
            ];
        }

        return [ $tables, $totalUsed ];
    }

    /**
     * getBit63Table49CardCodeValue
     * @param string $cvv
     * @param string $presenceIndicator
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table49CardCodeValue(string $cvv, string $presenceIndicator = "1") :array
    {
        $tableLength = 7;
        $definitionTable = [
            "presenceIndicator" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 1,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => $presenceIndicator
            ],
            "cardCodeValue" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 4,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                ISO8583::DATA_ELEMENT_FILL => " ",
                ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
                "data" => $cvv,
            ]
        ];

        return self::getUnifiedTableData($tableLength, $definitionTable);
    }

    /**
     * @param $orderNumber
     * @param string $customerServiceNumber
     * @param string $eComURL
     * @param string $version
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table36AdditionalData($orderNumber, $customerServiceNumber = "", $eComURL = "", $version = "1")
    {
        $customerServiceNumber = preg_replace("/[^0-9]/", "", $customerServiceNumber ?? "");
        if (strlen($customerServiceNumber) === 11) {
            $customerServiceNumber = substr($customerServiceNumber, 1);
        } else if (strlen($customerServiceNumber) > 11) {
            $customerServiceNumber = "";
        }

        if ($eComURL === null || strlen($eComURL) > 32) {
            $eComURL = "";
        }

        $tableLength = 60;
        $definitionTable = [
            "version" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 1,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => $version,
            ],
            "customerServiceNumber" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 10,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                ISO8583::DATA_ELEMENT_FILL => " ",
                ISO8583::DATA_ELEMENT_PAD => STR_PAD_RIGHT,
                "data" => $customerServiceNumber,
            ],
            "orderNumber" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 15,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                ISO8583::DATA_ELEMENT_FILL => " ",
                ISO8583::DATA_ELEMENT_PAD => STR_PAD_RIGHT,
                "data" => $orderNumber,
            ],
            "eComURL" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 32,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                ISO8583::DATA_ELEMENT_FILL => " ",
                ISO8583::DATA_ELEMENT_PAD => STR_PAD_RIGHT,
                "data" => $eComURL,
            ],
        ];

        return self::getUnifiedTableData($tableLength, $definitionTable);
    }

    /**
     * decodeBit63Table49CardCodeValue
     *
     * @param string $encodedData
     * @return array
     */
    public static function decodeBit63Table49CardCodeValue(string $encodedData) : array
    {
        $responseCorresponds = [
            "M" => "CVV2/CVC2/CID Match",
            "N" => "CVV2/CVC2/CID No Match",
            "P" => "Not Processed",
            "S" => "CVV2 should be on the card, but merchant indicated that it was not",
            "U" => "Unknown/Issuer does not participate",
            "X" => "Server provider did not respond (Default)",
        ];
        list($responseValue, $used) = self::getDataFromEncodedISO($encodedData, 1, ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC);
        return [
            "responseValue" => $responseValue,
            "responseValue-context" => $responseCorresponds[$responseValue] ?? ""
        ];
    }

    /**
     * getBit63Table60ECommerceInfo
     * @param $indicator
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table60ECommerceInfo($indicator = "07")
    {
        $tableLength = 4;
        $definitionTable = [
            "indicator" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 2,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => $indicator
            ]
        ];

        return self::getUnifiedTableData($tableLength, $definitionTable);
    }

    /**
     * getBit63TableVICompliance
     * @return array
     * @throws \Exception
     */
    public static function getBit63TableVIMCDSCompliance()
    {
        $tableLength = 2;
        $definitionTable = [];

        return self::getUnifiedTableData($tableLength, $definitionTable);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table55MerchantAdviceCode()
    {
        $tableLength = 5;
        $definitionTable = [
            "flag" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 1,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "1"
            ],
            "indicator" => [
                    ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                    ISO8583::DATA_ELEMENT_SIZE => 2,
                    ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                    "data" => "  "
            ],
        ];

        return self::getUnifiedTableData($tableLength, $definitionTable);
    }

    /**
     * getBit63TableSPTransArmorToken
     *
     * @param string|null $token
     * @param string|null $providerId
     * @return array
     * @throws \Exception
     */
    public static function getBit63TableSPTransArmorToken(string $token = null, string $providerId = null) :array
    {
        $definitionTable = [
            "tag01" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 2,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "01"
            ],
            "tag01Length" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 3,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "001"
            ],
            "tag01Indicator" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 1,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "3"
            ],
            "tag06" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 2,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "06"
            ],
            "tag06Length" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 3,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "004"
            ],
            "tag06TokenType" => [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 4,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "EATX"
            ],
        ];
        if ($token !== null && $providerId !== null) {
            $definitionTable["tag07"] = [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 2,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "07"
            ];
            $definitionTable["tag07Length"] = [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 3,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                ISO8583::DATA_ELEMENT_FILL => "0",
                ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
                "data" => (string) strlen($token),
            ];
            $definitionTable["tag07Data"] = [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => strlen($token),
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => $token
            ];

            $definitionTable["tag10"] = [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 2,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => "10"
            ];
            $definitionTable["tag10Length"] = [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => 3,
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                ISO8583::DATA_ELEMENT_FILL => "0",
                ISO8583::DATA_ELEMENT_PAD => STR_PAD_LEFT,
                "data" => (string) strlen($providerId),
            ];
            $definitionTable["tag10Data"] = [
                ISO8583::DATA_ELEMENT_TYPE  => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC,
                ISO8583::DATA_ELEMENT_SIZE => strlen($providerId),
                ISO8583::DATA_ELEMENT_IS_VARIABLE => ISO8583::ISO8583_BIT_FIXED_LENGTH,
                "data" => $providerId
            ];
            //strlen(tableId) + strlen(tags)
            $tableLength = 2 + 15 + 5 + strlen($token) + 5 + strlen($providerId);
        } else {
            //strlen(tableId) + strlen(tags)
            $tableLength = 2 + 15;
        }

        return self::getUnifiedTableData($tableLength, $definitionTable);
    }

    /**
     * decodeBit63TableSPTransArmorToken
     * @param string $encodedData
     * @return array
     */
    public static function decodeBit63TableSPTransArmorToken(string $encodedData) :array
    {
        $results = [];
        while (strlen($encodedData) > 0) {
            list($tag, $used) = self::getDataFromEncodedISO($encodedData, 2, ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC);
            $encodedData = substr($encodedData, $used);

            list($tagLength, $used) = self::getDataFromEncodedISO($encodedData, 3, ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC);
            $encodedData = substr($encodedData, $used);

            list($tagData, $used) = self::getDataFromEncodedISO($encodedData, (int)$tagLength, ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC);
            $encodedData = substr($encodedData, $used);
            $result = [
                "tag" => $tag,
                "tagLength" => $tagLength,
                "tagData" => $tagData,
            ];
            switch ($tag) {
                case "07": //we knew it is token
                    $result["tagContext"] = "Token";
                    break;
                case "10":
                    $result["tagContext"] = "Provider Id";
                    break;
                default:
                    break;
            }

            $results[] = $result;
        }

        return $results;
    }

    /**
     * getBit63Table14AdditionalVisaData
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table14AdditionalVisaData(array $data = []) :array
    {
        $tableLength = 48;
        $definitions = self::$bit63Table14AdditionalVISA;
        foreach ($definitions as $key => &$definition) {
            if (isset($data[$key])) {
                $definition['data'] = $data[$key];
            }
        }

        return self::getUnifiedTableData($tableLength, $definitions);
    }

    /**
     * getBit63Table14AdditionalMCData
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table14AdditionalMCData(array $data = []) :array
    {
        $tableLength = 48;
        $definitions = self::$bit63Table14AdditionalMC;
        foreach ($definitions as $key => &$definition) {
            if (isset($data[$key])) {
                $definition['data'] = $data[$key];
            }
        }

        return self::getUnifiedTableData($tableLength, $definitions);
    }


    /**
     * getBit63Table14AdditionalDCData
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table14AdditionalDCData(array $data = []) :array
    {
        $tableLength = 48;
        $definitions = self::$bit63Table14AdditionalDS;
        foreach ($definitions as $key => &$definition) {
            if (isset($data[$key])) {
                $definition['data'] = $data[$key];
            }
        }

        return self::getUnifiedTableData($tableLength, $definitions);
    }

    /**
     * getBit63Table14AdditionalAMEXData
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public static function getBit63Table14AdditionalAMEXData(array $data = []) :array
    {
        $definitions = self::$bit63Table14AdditionalAmex;
        if (isset($data['sellerID'])) {
            $tableLength = 68;
        } else {
            $tableLength = 48;
            unset($definitions['sellerID']);
        }
        foreach ($definitions as $key => &$definition) {
            if (isset($data[$key])) {
                $definition['data'] = $data[$key];
            }
        }

        return self::getUnifiedTableData($tableLength, $definitions);
    }

    /**
     * getBit63TableSKAmexSafeKey
     * @param string $indicator
     * @param string $aesk
     * @return array
     * @throws \Exception
     */
    public static function getBit63TableSKAmexSafeKey($indicator = "07", $aesk = "0") :array
    {
        $tableLength = 24;
        $definitions = self::$bit63TableSK;
        foreach ($definitions as $key => &$definition) {
            if ($key === "indicator") {
                $definition['data'] = $indicator;
            }
            if ($key === "aesk") {
                $definition['data'] = $aesk;
            }
        }

        return self::getUnifiedTableData($tableLength, $definitions);
    }

    /**
     * decodeBit63Table14AdditionalVisaData
     * @param string $encodedData
     * @return array
     */
    public static function decodeBit63Table14AdditionalVisaData(string $encodedData) :array
    {
        $results = self::decodeTableData(self::$bit63Table14AdditionalVISA, $encodedData);

        foreach ($results as $k => $result) {
            if ($k === "aci") {
                $results[$k . "-context"] =  self::$aciCorresponds[$result] ?? "";
            }
        }
        return $results;
    }

    /**
     * decodeBit63Table14AdditionalMCData
     * @param string $encodedData
     * @return array
     */
    public static function decodeBit63Table14AdditionalMCData(string $encodedData) :array
    {
        $results = self::decodeTableData(self::$bit63Table14AdditionalMC, $encodedData);
        foreach ($results as $k => $result) {
            if ($k === "aci") {
                $results[$k . "-context"] =  self::$aciCorresponds[$result] ?? "";
            }
        }
        return $results;
    }

    /**
     * decodeBit63Table14AdditionalDSData
     * @param string $encodedData
     * @return array
     */
    public static function decodeBit63Table14AdditionalDSData(string $encodedData) :array
    {
        $results = self::decodeTableData(self::$bit63Table14AdditionalDS, $encodedData);

        return $results;
    }

    /**
     * decodeBit63Table14AdditionalAmexData
     * @param string $encodedData
     * @return array
     */
    public static function decodeBit63Table14AdditionalAmexData(string $encodedData) :array
    {
        $results = self::decodeTableData(self::$bit63Table14AdditionalAmex, $encodedData);

        return $results;
    }

    /**
     * getUnifiedTableData
     *
     * @param int $tableLength
     * @param array $definitionDataTable
     * @return array
     * @throws \Exception
     */
    private static function getUnifiedTableData(int $tableLength, array $definitionDataTable) :array
    {
        $data = "";
        $encodedData = "";

        foreach ($definitionDataTable as $field => $definitionDT) {
            list($resultData, $resultEncodedData) = ISO8583::getDataValue($definitionDT['data'] ?? "", $definitionDT);
            $data .= $resultData;
            $encodedData .= $resultEncodedData;
        }

        $tableData = [];
        $tableData['tableLength'] = $tableLength;
        $tableData["data"] = $data;
        $tableData["encodedData"] = $encodedData;

        return $tableData;
    }

    /**
     * decodeTableData
     * @param array $tableDefinitions
     * @param string $encodedData
     * @return array
     */
    private static function decodeTableData(array $tableDefinitions, string $encodedData) :array
    {
        $results = [];

        foreach ($tableDefinitions as $k => $tableDefinition) {
            if ($encodedData == ""){
                return $results;
            }
            list($data, $used) = self::getDataFromEncodedISO($encodedData, $tableDefinition[ISO8583::DATA_ELEMENT_SIZE], $tableDefinition[ISO8583::DATA_ELEMENT_TYPE]);
            $results[$k] = $data;
            $encodedData = substr($encodedData, $used);
        }

        return $results;
    }
}