<?php
use \RW\ISO8583\ISO8583;

class ISO8583Test extends \PHPUnit\Framework\TestCase
{
    const MID = "445723054992";
    const TID = "1543323";

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid bit number
     */
    public function testBadBit()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(129, "a");
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage The bitmap is not supported.
     */
    public function testBadBit2()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(8, "a");
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid MTI length and format
     */
    public function testBadMTI()
    {
        $iso8583 = new ISO8583();
        $iso8583->setMTI("aa");
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid MTI
     */
    public function testBadMTI2()
    {
        $iso8583 = new ISO8583();
        $iso8583->setMTI("0820");
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid Bit3ProcessCode in position 1 & 2
     */
    public function testBadBit3_12()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(ISO8583::FD_BIT_3_PROCESSING_CODE, "440000");
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid Bit3ProcessCode in position 3 & 4
     */
    public function testBadBit3_34()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(ISO8583::FD_BIT_3_PROCESSING_CODE, "991100");
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid Bit3ProcessCode in position 5 & 6
     */
    public function testBadBit3_56()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(ISO8583::FD_BIT_3_PROCESSING_CODE, "990099");
    }

    /**
     * @throws Exception
     */
    public function testSetPan15()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER, ISO8583::getBit2PrimaryAccountNumber("411111111111111"));

        $this->assertSame("4000000000000000", $iso8583->getBitmap());
        $this->assertSame("154111111111111110", $iso8583->getBitData(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER));
        $this->assertSame("|15A|11|11|11|11|11|11|10", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER));
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Invalid primary account number.
     */
    public function testSetBadPan()
    {
        $iso8583 = new ISO8583();
        $iso8583->setData(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER, ISO8583::getBit2PrimaryAccountNumber("41111111111111199999"));
    }

    /**
     * @throws Exception
     */
    public function testExtendedBitmap()
    {
        $iso8583 = new ISO8583();
        $iso8583->setMTI("0800");
        $iso8583->setData(ISO8583::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE, "0");
        $this->assertSame("|80|00|00|00|00|00|00|00|04|00|00|00|00|00|00|00", $iso8583->getEncodedBitmap());
        $this->assertSame("80000000000000000400000000000000", $iso8583->getBitmap());

        $newISO = ISO8583::decodeEncodedISO($iso8583->getEncodedISO());
        $this->assertSame("0000", $newISO->getBitData(ISO8583::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE));
    }

    /**
     * @throws Exception
     */
    public function testEcho()
    {
        $iso8583 = new ISO8583();
        $iso8583->setMTI(ISO8583::FD_MTI_NETWORK_MANAGEMENT_REQUEST);
        $iso8583->setData(ISO8583::FD_BIT_3_PROCESSING_CODE, "990000");
        $iso8583->setData(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME, "1223122359");
        $iso8583->setData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER, (string) 123456);
        $iso8583->setData(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION, "122334");
        $iso8583->setData(ISO8583::FD_BIT_13_SALE_DATE, "0530");
        $iso8583->setData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID, "001");
        $iso8583->setData(ISO8583::FD_BIT_41_TERMINAL_ID, self::TID);
        $iso8583->setData(ISO8583::FD_BIT_42_MERCHANT_ID, self::MID);
        $iso8583->setData(ISO8583::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE, "0301");

        $this->assertSame("0800", $iso8583->getMTI());
        $this->assertSame("|08|00", $iso8583->getEncodedMTI());
        $this->assertSame("A238010000C000000400000000000000", $iso8583->getBitmap());
        $this->assertSame('|A28|01|00|00|C0|00|00|04|00|00|00|00|00|00|00', $iso8583->getEncodedBitmap());
        $this->assertSame("990000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("123456", $iso8583->getBitData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("122334", $iso8583->getBitData(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION));
        $this->assertSame("0530", $iso8583->getBitData(ISO8583::FD_BIT_13_SALE_DATE));
        $this->assertSame("0001", $iso8583->getBitData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("01543323", $iso8583->getBitData(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("000445723054992", $iso8583->getBitData(ISO8583::FD_BIT_42_MERCHANT_ID));
        $this->assertSame("0800A238010000C000000400000000000000990000122312235912345612233405300001015433230004457230549920301", $iso8583->getISO());
        $this->assertSame('|08|00|A28|01|00|00|C0|00|00|04|00|00|00|00|00|00|00|99|00|00|12|23|12|23Y|124V|12|234|050|00|0101543323000445723054992|03|01', $iso8583->getEncodedISO());
    }

    /**
     * @throws Exception
     */
    public function testEchoResponse()
    {
        $response = '|08|10|208|01|00|02|C0|00|02|99|00|00|124V|12|234|050|00|010001543323000445723054992|00|16APPROVAL|20|20|20|20|20|20|20|20';
        $iso8583 = ISO8583::decodeEncodedISO($response);
        $this->assertInstanceOf(ISO8583::class, $iso8583);
        $this->assertSame("0810", $iso8583->getMTI());
        $this->assertSame("|08|10", $iso8583->getEncodedMTI());
        $this->assertSame('2038010002C00002', $iso8583->getBitmap());
        $this->assertSame('|208|01|00|02|C0|00|02', $iso8583->getEncodedBitmap());
        $this->assertSame("990000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("123456", $iso8583->getBitData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("122334", $iso8583->getBitData(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION));
        $this->assertSame("0530", $iso8583->getBitData(ISO8583::FD_BIT_13_SALE_DATE));
        $this->assertSame("0001", $iso8583->getBitData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("00", $iso8583->getBitData(ISO8583::FD_BIT_39_RESPONSE_CODE));
        $this->assertSame("01543323", $iso8583->getBitData(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("000445723054992", $iso8583->getBitData(ISO8583::FD_BIT_42_MERCHANT_ID));
        $this->assertSame("0016APPROVAL        ", $iso8583->getBitData(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
    }

    /**
     * This test will touch as many as bitmaps possible..
     * @throws Exception
     */
    public function testExtendedEcho()
    {
        $currencyCode = "0840";
        $merchantZip = "V8P 3H8";
        $amount = "5000";
        $cardNumber = "4005550000000019";
        $cvv = "999";
        $exp = "2512";
        $mcc = "2741";

        //according to doc, 59 is for VISA,MS,AMEX, other card should use 08
        $bit25PosConditionCode = ISO8583::FD_POS_CC_59_E_COMMERCE;
        $bit31AcquirerReferenceData = ISO8583::FD_ARD_0_AUTHORIZATION_ONLY;

        $iso8583 = new ISO8583();
        $iso8583->setMTI(ISO8583::FD_MTI_NETWORK_MANAGEMENT_REQUEST);
        $iso8583->setData(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER, ISO8583::getBit2PrimaryAccountNumber($cardNumber));
        $iso8583->setData(ISO8583::FD_BIT_3_PROCESSING_CODE, "990000");
        $iso8583->setData(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION, $amount); //ok
        $iso8583->setData(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME, "1223122359");
        $iso8583->setData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER, "123456");
        $iso8583->setData(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION, "122334");
        $iso8583->setData(ISO8583::FD_BIT_13_SALE_DATE, "0530");
        $iso8583->setData(ISO8583::FD_BIT_14_CARD_EXPIRATION_DATE, $exp); //ok
        $iso8583->setData(ISO8583::FD_BIT_18_MERCHANT_CATEGORY_CODE, $mcc); //ok
        $iso8583->setData(ISO8583::FD_BIT_22_POS_ENTRY_MODE, "010"); //ok
        $iso8583->setData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID, "001");
        $iso8583->setData(ISO8583::FD_BIT_25_POS_CONDITION_CODE, $bit25PosConditionCode); //ok
        $iso8583->setData(ISO8583::FD_BIT_31_ACQUIRER_REFERENCE_DATA, $bit31AcquirerReferenceData); //ok
        $iso8583->setData(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA, "12345678abcd");
        $iso8583->setData(ISO8583::FD_BIT_41_TERMINAL_ID, self::TID);
        $iso8583->setData(ISO8583::FD_BIT_42_MERCHANT_ID, self::MID);
        $iso8583->setData(ISO8583::FD_BIT_49_TRANSACTION_CURRENCY_CODE, $currencyCode); //ok
        $iso8583->setData(ISO8583::FD_BIT_59_MERCHANT_ZIP, $merchantZip); //ok
        $iso8583->setData(ISO8583::FD_BIT_60_ADDITIONAL_POS_INFO, "01"); //ok
        $iso8583->setData(ISO8583::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE, "0301");
        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_49_CARD_CODE_VALUE,
            ISO8583::getBit63Table49CardCodeValue($cvv));
        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_60_ELECTRONIC_COMMERCE_INDICATOR,
            ISO8583::getBit63Table60ECommerceInfo(ISO8583::TABLE_60_INDICATOR_07_CHANNEL_ENCRYPTION));
        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_VI_COMPLIANCE,
            ISO8583::getBit63TableVIMCDSCompliance());
        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_55_MERCHANT_ADVICE_CODE,
            ISO8583::getBit63Table55MerchantAdviceCode());
        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_SP_TRANS_ARMOR,
            ISO8583::getBit63TableSPTransArmorToken("faketoken", "fakeid"));

        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA,
            ISO8583::getBit63Table14AdditionalVisaData(["aci" => "Y"]));

        $iso8583->addDataTable(ISO8583::FD_BIT_63_FD_PRIVATE_USE,
            ISO8583::FD_BIT_63_TABLE_SK_AMEX_SAFE_KEY,
            ISO8583::getBit63TableSKAmexSafeKey());

        $this->assertSame("0800", $iso8583->getMTI());
        $this->assertSame("|08|00", $iso8583->getEncodedMTI());

        $this->assertSame("164005550000000019", $iso8583->getBitData(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER));
        $this->assertSame("|16|40|05U|00|00|00|00|19", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_2_PRIMARY_ACCOUNT_NUMBER));
        $this->assertSame("990000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("|99|00|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("000000005000", $iso8583->getBitData(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION));
        $this->assertSame("|00|00|00|00P|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION));
        $this->assertSame("1223122359", $iso8583->getBitData(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME));
        $this->assertSame("|12|23|12|23Y", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME));
        $this->assertSame("123456", $iso8583->getBitData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("|124V", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("122334", $iso8583->getBitData(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION));
        $this->assertSame("|12|234", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION));
        $this->assertSame("0530", $iso8583->getBitData(ISO8583::FD_BIT_13_SALE_DATE));
        $this->assertSame("|050", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_13_SALE_DATE));
        $this->assertSame("2512", $iso8583->getBitData(ISO8583::FD_BIT_14_CARD_EXPIRATION_DATE));
        $this->assertSame("|25|12", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_14_CARD_EXPIRATION_DATE));
        $this->assertSame("2741", $iso8583->getBitData(ISO8583::FD_BIT_18_MERCHANT_CATEGORY_CODE));
        $this->assertSame("|27A", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_18_MERCHANT_CATEGORY_CODE));
        $this->assertSame("0010", $iso8583->getBitData(ISO8583::FD_BIT_22_POS_ENTRY_MODE));
        $this->assertSame("|00|10", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_22_POS_ENTRY_MODE));
        $this->assertSame("0001", $iso8583->getBitData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("|00|01", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("59", $iso8583->getBitData(ISO8583::FD_BIT_25_POS_CONDITION_CODE));
        $this->assertSame("Y", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_25_POS_CONDITION_CODE));
        $this->assertSame("010", $iso8583->getBitData(ISO8583::FD_BIT_31_ACQUIRER_REFERENCE_DATA));
        $this->assertSame("|010", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_31_ACQUIRER_REFERENCE_DATA));
        $this->assertSame("12345678abcd", $iso8583->getBitData(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA));
        $this->assertSame("12345678abcd", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA));
        $this->assertSame("01543323", $iso8583->getBitData(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("01543323", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("000445723054992", $iso8583->getBitData(ISO8583::FD_BIT_42_MERCHANT_ID));
        $this->assertSame("000445723054992", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_42_MERCHANT_ID));
        $this->assertSame("0840", $iso8583->getBitData(ISO8583::FD_BIT_49_TRANSACTION_CURRENCY_CODE));
        $this->assertSame("|08|40", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_49_TRANSACTION_CURRENCY_CODE));
        $this->assertSame("09V8P 3H8  ", $iso8583->getBitData(ISO8583::FD_BIT_59_MERCHANT_ZIP));
        $this->assertSame("|09V8P 3H8  ", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_59_MERCHANT_ZIP));
        $this->assertSame("01", $iso8583->getBitData(ISO8583::FD_BIT_60_ADDITIONAL_POS_INFO));
        $this->assertSame("|01", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_60_ADDITIONAL_POS_INFO));
        $this->assertSame("0301", $iso8583->getBitData(ISO8583::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE));
        $this->assertSame("|03|01", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_70_NETWORK_MANAGEMENT_INFO_CODE));

        $this->assertSame([
            "tableLength" => 7,
            "data" => "1 999",
            "encodedData" => "1 999",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_49_CARD_CODE_VALUE));

        $this->assertSame([
            "tableLength" => 4,
            "data" => "07",
            "encodedData" => "07",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_60_ADDITIONAL_POS_INFO));

        $this->assertSame([
            "tableLength" => 2,
            "data" => "",
            "encodedData" => "",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_VI_COMPLIANCE));

        $this->assertSame([
            "tableLength" => 5,
            "data" => "1  ",
            "encodedData" => "1  ",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_55_MERCHANT_ADVICE_CODE));

        $this->assertSame([
            "tableLength" => 42,
            "data" => "01001306004EATX07009faketoken10006fakeid",
            "encodedData" => "01001306004EATX07009faketoken10006fakeid",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_SP_TRANS_ARMOR));

        $this->assertSame([
            "tableLength" => 48,
            "data" => "Y                     000000000000000000000000",
            "encodedData" => "Y                     000000000000000000000000",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA));

        $this->assertSame([
            "tableLength" => 24,
            "data" => "070000000000000000000000000000000000000000",
            "encodedData" => "07|00|00|00|00|00|00|00|00|00|00|00|00|00|00|00|00|00|00|00|00",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_SK_AMEX_SAFE_KEY));

    }

    /**
     * @throws Exception
     */
    public function testExtendedEchoResponse()
    {
        $response = '|08|10|208|01|00|02|C0|00|02|99|00|00|124V|12|234|050|00|010001543323000445723054992|00|16APPROVAL|20|20|20|20|20|20|20|20';
        $iso8583 = ISO8583::decodeEncodedISO($response);
        $this->assertInstanceOf(ISO8583::class, $iso8583);
        $this->assertSame("0810", $iso8583->getMTI());
        $this->assertSame("|08|10", $iso8583->getEncodedMTI());
        $this->assertSame('2038010002C00002', $iso8583->getBitmap());
        $this->assertSame('|208|01|00|02|C0|00|02', $iso8583->getEncodedBitmap());
        $this->assertSame("990000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("|99|00|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("123456", $iso8583->getBitData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("|124V", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("122334", $iso8583->getBitData(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION));
        $this->assertSame("|12|234", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_12_TIME_LOCAL_TRANSMISSION));
        $this->assertSame("0530", $iso8583->getBitData(ISO8583::FD_BIT_13_SALE_DATE));
        $this->assertSame("|050", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_13_SALE_DATE));
        $this->assertSame("0001", $iso8583->getBitData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("|00|01", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("00", $iso8583->getBitData(ISO8583::FD_BIT_39_RESPONSE_CODE));
        $this->assertSame("00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_39_RESPONSE_CODE));
        $this->assertSame("01543323", $iso8583->getBitData(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("01543323", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("000445723054992", $iso8583->getBitData(ISO8583::FD_BIT_42_MERCHANT_ID));
        $this->assertSame("000445723054992", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_42_MERCHANT_ID));
        $this->assertSame("0016APPROVAL        ", $iso8583->getBitData(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
        $this->assertSame("|00|16APPROVAL        ", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
    }

    /**
     * @throws Exception
     */
    public function testPreAuthResponse1()
    {
        $resultPayload = "|01|102|20|01|80|0E|80|00|02|00|00|00|00|00|00|00P|00|06|02|00|24|18|00|01|98|00|01Y000000000198OK98940001543323|00u|00H14N018153043564243|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X";
        $iso8583 = ISO8583::decodeEncodedISO($resultPayload);
        $this->assertInstanceOf(ISO8583::class, $iso8583);
        $this->assertSame("0110", $iso8583->getMTI());
        $this->assertSame("|01|10", $iso8583->getEncodedMTI());
        $this->assertSame("322001800E800002", $iso8583->getBitmap());
        $this->assertSame("2|20|01|80|0E|80|00|02", $iso8583->getEncodedBitmap());
        $this->assertSame("000000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("|00|00|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("000000005000", $iso8583->getBitData(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION));
        $this->assertSame("|00|00|00|00P|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION));
        $this->assertSame("0602002418", $iso8583->getBitData(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME));
        $this->assertSame("|06|02|00|24|18", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME));
        $this->assertSame("000198", $iso8583->getBitData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("|00|01|98", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("0001", $iso8583->getBitData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("|00|01", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("59", $iso8583->getBitData(ISO8583::FD_BIT_25_POS_CONDITION_CODE));
        $this->assertSame("Y", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_25_POS_CONDITION_CODE));
        $this->assertSame("000000000198", $iso8583->getBitData(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA));
        $this->assertSame("000000000198", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA));
        $this->assertSame("OK9894", $iso8583->getBitData(ISO8583::FD_BIT_38_AUTHORIZATION_IDENTIFICATION_RESPONSE));
        $this->assertSame("OK9894", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_38_AUTHORIZATION_IDENTIFICATION_RESPONSE));
        $this->assertSame("00", $iso8583->getBitData(ISO8583::FD_BIT_39_RESPONSE_CODE));
        $this->assertSame("00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_39_RESPONSE_CODE));
        $this->assertSame("01543323", $iso8583->getBitData(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("01543323", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame([
            "tableLength" => 48,
            "data" => "N018153043564243      000000000000000000000000",
            "encodedData" => "N018153043564243|20|20|20|20|20|20000000000000000000000000",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA));
        $this->assertSame([
            "tableLength" => 18,
            "data" => "APPROVAL        ",
            "encodedData" => "APPROVAL|20|20|20|20|20|20|20|20",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_22_HOST_RESPONSE));
        $this->assertSame([
            "tableLength" => 3,
            "data" => "X",
            "encodedData" => "X",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_49_CARD_CODE_VALUE));
        $this->assertSame("0075004814N018153043564243      000000000000000000000000001822APPROVAL        000349X", $iso8583->getBitData(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
        $this->assertSame("|00u|00H14N018153043564243|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
    }

    /**
     * @throws Exception
     */
    public function testAVSResponse()
    {
        $resultPayload = '|01|102|20|01|80|0E|90|00|02|00|00|00|00|00|00|00P|00|06|05|17AP|00|02|07|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $iso8583 = ISO8583::decodeEncodedISO($resultPayload);
        $this->assertInstanceOf(ISO8583::class, $iso8583);
        $this->assertSame("0110", $iso8583->getMTI());
        $this->assertSame("|01|10", $iso8583->getEncodedMTI());

        //'2|20|01|80|0E|90|00|02|00|00|00|00|00|00|00P|00|06|05|17AP|00|02|07|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("2|20|01|80|0E|90|00|02", $iso8583->getEncodedBitmap());
        $this->assertSame("322001800E900002", $iso8583->getBitmap());

        //'|00|00|00|00|00|00|00P|00|06|05|17AP|00|02|07|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("000000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame("|00|00|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_3_PROCESSING_CODE));

        //'|00|00|00|00P|00|06|05|17AP|00|02|07|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("000000005000", $iso8583->getBitData(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION));
        $this->assertSame("|00|00|00|00P|00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_4_AMOUNT_OF_TRANSACTION));

        //'|06|05|17AP|00|02|07|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("0605174150", $iso8583->getBitData(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME));
        $this->assertSame("|06|05|17AP", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_7_TRANSMISSION_DATETIME));

        //'|00|02|07|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("000207", $iso8583->getBitData(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));
        $this->assertSame("|00|02|07", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_11_SYSTEM_TRACE_DEBIT_REG_RECEIPT_NUMBER));

        //'|00|01Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("0001", $iso8583->getBitData(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));
        $this->assertSame("|00|01", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_24_NETWORK_INTERNATIONAL_ID));

        //Y76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("59", $iso8583->getBitData(ISO8583::FD_BIT_25_POS_CONDITION_CODE));
        $this->assertSame("Y", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_25_POS_CONDITION_CODE));

        //76e8970a9109OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("76e8970a9109", $iso8583->getBitData(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA));
        $this->assertSame("76e8970a9109", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_37_RETRIEVAL_REFERENCE_DATA));

        //OK31290001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("OK3129", $iso8583->getBitData(ISO8583::FD_BIT_38_AUTHORIZATION_IDENTIFICATION_RESPONSE));
        $this->assertSame("OK3129", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_38_AUTHORIZATION_IDENTIFICATION_RESPONSE));

        //0001543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("00", $iso8583->getBitData(ISO8583::FD_BIT_39_RESPONSE_CODE));
        $this->assertSame("00", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_39_RESPONSE_CODE));

        //01543323|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("01543323", $iso8583->getBitData(ISO8583::FD_BIT_41_TERMINAL_ID));
        $this->assertSame("01543323", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_41_TERMINAL_ID));

        //|00|01N|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X';
        $this->assertSame("0001N", $iso8583->getBitData(ISO8583::FD_BIT_44_ADDITIONAL_RESPONSE_DATA));
        $this->assertSame("|00|01N", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_44_ADDITIONAL_RESPONSE_DATA));

        $this->assertSame([
            "tableLength" => 48,
            "data" => "N018156120010306      000000000000000000000000",
            "encodedData" => "N018156120010306|20|20|20|20|20|20000000000000000000000000",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA));
        $this->assertSame([
            "tableLength" => 18,
            "data" => "APPROVAL        ",
            "encodedData" => "APPROVAL|20|20|20|20|20|20|20|20",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_22_HOST_RESPONSE));
        $this->assertSame([
            "tableLength" => 3,
            "data" => "X",
            "encodedData" => "X",
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_49_CARD_CODE_VALUE));
        $this->assertSame("0075004814N018156120010306      000000000000000000000000001822APPROVAL        000349X", $iso8583->getBitData(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
        $this->assertSame("|00u|00H14N018156120010306|20|20|20|20|20|20000000000000000000000000|00|1822APPROVAL|20|20|20|20|20|20|20|20|00|0349X", $iso8583->getBitDataEncoded(ISO8583::FD_BIT_63_FD_PRIVATE_USE));
     }

    /**
     * @throws Exception
     */
    public function testGetBit43AlternativeMerchantName()
    {
        $this->assertSame("SCOTT WANG                    4681 BOULDERWOOD DR      VICTORIA            BC   V8Y 2P8  124               ", ISO8583::getBit43AlternativeMerchantName("Scott Wang", "4681 Boulderwood DR", "Victoria", "BC", "V8Y 2P8", "CA"));
    }

    /**
     * @expectedExceptionMessage merchantName data is invalid. The size of the string must be between 1 and 30
     * @expectedException Exception
     * @throws Exception
     */
    public function testBadGetBit43AlternativeMerchantName()
    {
        $this->assertSame("SCOTT WANG                    4681 BOULDERWOOD DR      VICTORIA            BC   V8Y 2P8  124               ", ISO8583::getBit43AlternativeMerchantName("", "4681 Boulderwood DR", "Victoria", "BC", "V8Y 2P8", "CA"));
    }

    /**
     * @expectedExceptionMessage Invalid state for CA. [BA]
     * @expectedException Exception
     * @throws Exception
     */
    public function testBad1GetBit43AlternativeMerchantName()
    {
        $this->assertSame("SCOTT WANG                    4681 BOULDERWOOD DR      VICTORIA            BA   V8Y 2P8  124               ", ISO8583::getBit43AlternativeMerchantName("Scott Wang", "4681 Boulderwood DR", "Victoria", "BA", "V8Y 2P8", "CA"));
    }

    /**
     * @expectedExceptionMessage Invalid state for US. [BA]
     * @expectedException Exception
     * @throws Exception
     */
    public function testBad2GetBit43AlternativeMerchantName()
    {
        $this->assertSame("SCOTT WANG                    4681 BOULDERWOOD DR      VICTORIA            BA   V8Y 2P8  124               ", ISO8583::getBit43AlternativeMerchantName("Scott Wang", "4681 Boulderwood DR", "Victoria", "BA", "V8Y 2P8", "US"));
    }

    /**
     * @throws Exception
     */
    public function testGetgetBit48MessageDataForAddressVerification()
    {
        $this->assertSame("9912345    123 Main street     ", ISO8583::getBit48MessageDataForAddressVerification("12345", "123 Main street"));
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Cardholder Zip is required.
     */
    public function testBad1GetgetBit48MessageDataForAddressVerification()
    {
        $this->assertSame("9912345    123 Main street     ", ISO8583::getBit48MessageDataForAddressVerification("", "123 Main street"));
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Cardholder Zip cannot be longer than 9 characters.
     */
    public function testBad2GetgetBit48MessageDataForAddressVerification()
    {
        $this->assertSame("9912345    123 Main street     ", ISO8583::getBit48MessageDataForAddressVerification("12345678910", "123 Main street"));
    }

    /**
     * @throws Exception
     * @expectedException Exception
     * @expectedExceptionMessage Cardholder street cannot be longer than 20 characters.
     */
    public function testBad3GetgetBit48MessageDataForAddressVerification()
    {
        $this->assertSame("9912345    123 Main street     ", ISO8583::getBit48MessageDataForAddressVerification("123456789", "123 Main street       a"));
    }

    /**
     * testBit31DummyData
     * @throws Exception
     */
    public function testBit31DummyData()
    {
        $iso8583 = new ISO8583();
        $iso8583->setMTI(ISO8583::FD_MTI_NETWORK_MANAGEMENT_REQUEST);
        $iso8583->setData(ISO8583::FD_BIT_31_ACQUIRER_REFERENCE_DATA, "1");
        $this->assertSame("|08|00|00|00|00|02|00|00|00|00|011", $iso8583->getEncodedISO());
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid data definition
     * @throws Exception
     */
    public function testBadDataDefinition()
    {
        ISO8583::getDataValue("aa", ["size" => "2"]);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid data format
     * @throws Exception
     */
    public function testBad2DataDefinition()
    {
        ISO8583::getDataValue("aa", ["size" => 2, "type" => ISO8583::ISO8583_DATA_TYPE_BCD]);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid data size
     * @throws Exception
     */
    public function testBad3DataDefinition()
    {
        ISO8583::getDataValue("123456", ["size" => 2, "type" => ISO8583::ISO8583_DATA_TYPE_BCD]);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid data size
     * @throws Exception
     */
    public function testBad4DataDefinition()
    {
        ISO8583::getDataValue("123456", ["size" => 2, "type" => ISO8583::ISO8583_DATA_TYPE_ALPHA_NUMERIC]);
    }

    /**
     * @expectedExceptionMessage Unknown format
     * @expectedException Exception
     * @throws Exception
     */
    public function testBadDataFromEncodedISO()
    {
        ISO8583::getDataFromEncodedISO("aaaaa", 4, "unknown");
    }

    /**
     * decodeBit63TableData
     * @throws Exception
     */
    public function testDecodeBit63TableData()
    {
        $encodedISO = "|01|102|20|01|80|0A|90|00|02|00|00|00|00|00|00|00|21|00|06|11|23X|16|00|04|16|00|01Y0beabcc54e955101543323|00|01Y|01|24|00H14N018162866911043NA|20|20|20|20000000000000000000000000|00|1822DECLINED|20|20|20|20|20|20|20|20|00|0349M|00|06VICR|20|20|009SPXZ003HZY07016710262787543002610003002";
        /** @var $iso8583 ISO8583 */
        $iso8583 = ISO8583::decodeEncodedISO($encodedISO);
        $this->assertInstanceOf(ISO8583::class, $iso8583);

        $this->assertSame("0110", $iso8583->getMTI());
        $this->assertSame("|01|10", $iso8583->getEncodedMTI());

        $this->assertSame("000000", $iso8583->getBitData(ISO8583::FD_BIT_3_PROCESSING_CODE));
        $this->assertSame([
            "tableLength" => 39,
            "data" => "XZ003HZY07016710262787543002610003002",
            "encodedData" => "XZ003HZY07016710262787543002610003002"
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_SP_TRANS_ARMOR));
        $this->assertSame([
            [
                "tag" => "XZ",
                "tagLength" => "003",
                "tagData" => "HZY"
            ],
            [
                "tag" => "07",
                "tagLength" => "016",
                "tagData" => "7102627875430026",
                "tagContext" => "Token"
            ],
            [
                "tag" => "10",
                "tagLength" => "003",
                "tagData" => "002",
                "tagContext" => "Provider Id"
            ]
        ], ISO8583::decodeBit63TableSPTransArmorToken("XZ003HZY07016710262787543002610003002"));


        $this->assertSame([
            "tableLength" => 6,
            "data" => "CR  ",
            "encodedData" => "CR|20|20"
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_VI_COMPLIANCE));

        $this->assertSame([
            "tableLength" => 48,
            "data" => "N018162866911043NA    000000000000000000000000",
            "encodedData" => "N018162866911043NA|20|20|20|20000000000000000000000000"
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_14_ADDITIONAL_CARD_DATA));
        $this->assertSame([
            "aci" => "N",
            "tranID" => "018162866911043",
            "validationCode" => "NA  ",
            "marketIndicator" => " ",
            "rps" => " ",
            "firstAuthorizedAmount" => "000000000000",
            "totalAuthorizedAmount" => "000000000000",
            "aci-context" => "Not a custom payment service transaction",
        ], ISO8583::decodeBit63Table14AdditionalVisaData("N018162866911043NA|20|20|20|20000000000000000000000000"));

        $this->assertSame([
            "tableLength" => 18,
            "data" => "DECLINED        ",
            "encodedData" => "DECLINED|20|20|20|20|20|20|20|20"
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_22_HOST_RESPONSE));

        $this->assertSame([
            "tableLength" => 3,
            "data" => "M",
            "encodedData" => "M"
        ], $iso8583->getBitDataTableByTableId(ISO8583::FD_BIT_63_FD_PRIVATE_USE, ISO8583::FD_BIT_63_TABLE_49_CARD_CODE_VALUE));

        $this->assertSame([
            "responseValue" => "M",
            "responseValue-context" => "CVV2/CVC2/CID Match"
        ], ISO8583::decodeBit63Table49CardCodeValue("M"));
    }
}