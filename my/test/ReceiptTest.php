<?php
namespace TDD\Test;

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;
use TDD\Receipt;

class ReceiptTest extends TestCase
{
	public function setUp()
	{
		$this->Receipt = new Receipt();
	}

	public function tearDown()
	{
		unset($this->Receipt);
	}

	/**
	 * Ch01
	 */
//	public function testTotal()
//	{
//		$input = [0, 2, 5, 8];
//		$output = $this->Receipt->total($input);
//
//		$this->assertEquals(
//			15,
//			$output,
//			'Should equal 15'
//		);
//	}

	public function testTax()
	{
		$inputAmount = 10.00;
		$inputTax = 0.10;

		$output = $this->Receipt->tax($inputAmount, $inputTax);
		$this->assertEquals(
			1.00,
			$output,
			'The tax calculation should equal 1.00'
		);
	}

	public function testTotalWithDummyCoupon()
	{
		$input = [0, 2, 5, 8];
		$coupon = null;
		$output = $this->Receipt->total($input, $coupon);

		$this->assertEquals(
			15,
			$output,
			'Should equal 15'
		);
	}

	public function testTotalWithCoupon()
	{
		$input = [0, 2, 5, 8];
		$coupon = 0.20;
		$output = $this->Receipt->total($input, $coupon);

		$this->assertEquals(
			12,
			$output,
			'Should equal 12'
		);
	}

	public function testPostTaxTotalWithStub()
	{
		$Receipt = $this->getMockBuilder('TDD\Receipt')
			->setMethods(['tax', 'total'])
			->getMock();

		$Receipt->method('total')
			->will($this->returnValue(10.00));

		$Receipt->method('tax')
			->will($this->returnValue(1.00));

		$result = $Receipt->postTaxTotal([1, 2, 5, 8], 0.20, null);

		$this->assertEquals(11.00, $result);
	}

	public function testPostTaxTotalWithMock()
	{
		$items = [1, 2, 5, 8];
		$tax = 0.20;
		$coupon = null;

		$Receipt = $this->getMockBuilder('TDD\Receipt')
		                ->setMethods(['tax', 'total'])
		                ->getMock();

		$Receipt->expects($this->once())
				->method('total')
				->with($items, $coupon)
		        ->will($this->returnValue(10.00));

		$Receipt->expects($this->once())
				->method('tax')
				->with(10.00, $tax) //if 15.00 and tax it will fail because tax did not receive 10.00 as first param but 15.00
		        ->will($this->returnValue(1.00));

		$result = $Receipt->postTaxTotal([1, 2, 5, 8], 0.20, null);

		$this->assertEquals(11.00, $result);
	}

	/**
	 * @dataProvider provideTotal
	 * @param array $items
	 * @param int|float $expected
	 */
	public function testTotalWithDummyCouponAndDataProvider($items, $expected)
	{
		$coupon = null;
		$output = $this->Receipt->total($items, $coupon);

		$this->assertEquals(
			$expected,
			$output,
			"Should equal 15 {$expected}"
		);
	}

	public function provideTotal()
	{
		return [
			'ints totaling 16' => [[1, 2, 5, 8], 16],
			[[-1, 2, 5, 8], 14],
			[[1, 2, 8], 11],
		];
	}

	public function testTotalException()
	{
		$input = [0, 2, 5, 8];
		$coupon = 1.20;

		$this->expectException('BadMethodCallException');
		$this->Receipt->total($input, $coupon);
	}

	/**
	 * @dataProvider provideCurrencyAmount
	 * @param $input
	 * @param $expected
	 * @param $msg
	 */
	public function testCurrencyAmount($input, $expected, $msg)
	{
		$this->assertSame(
			$expected,
			$this->Receipt->currencyAmount($input),
			$msg
		);
	}

	public function provideCurrencyAmount()
	{
		return [
			[1, 1.00, '1 should be transformed into 1.00'],
			[1.1, 1.10, '1.1 should be transformed into 1.10'],
			[1.11, 1.11, '1.11 should stay as 1.11'],
			[1.111, 1.11, '1.111 should be transformed into 1.11'],
		];
	}
}