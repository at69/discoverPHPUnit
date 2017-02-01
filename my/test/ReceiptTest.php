<?php
namespace TDD\Test;

require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;
use TDD\Receipt;

class ReceiptTest extends TestCase
{
	public function setUp()
	{
		$this->Formatter = $this->getMockBuilder('TDD\Formatter')
								->setMethods(['currencyAmount'])
								->getMock();
		$this->Formatter->expects($this->any())
			->method('currencyAmount')
			->with($this->anything())
			->will($this->returnArgument(0));

		$this->Receipt = new Receipt($this->Formatter);
	}

	public function tearDown()
	{
		unset($this->Receipt);
	}

	/**
	 * Ch01
	 */
//	public function testSubtotal()
//	{
//		$input = [0, 2, 5, 8];
//		$output = $this->Receipt->subtotal($input);
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

	public function testSubtotalWithDummyCoupon()
	{
		$input = [0, 2, 5, 8];
		$coupon = null;
		$output = $this->Receipt->subtotal($input, $coupon);

		$this->assertEquals(
			15,
			$output,
			'Should equal 15'
		);
	}

	public function testSubtotalWithCoupon()
	{
		$input = [0, 2, 5, 8];
		$coupon = 0.20;
		$output = $this->Receipt->subtotal($input, $coupon);

		$this->assertEquals(
			12,
			$output,
			'Should equal 12'
		);
	}

	public function testPostTaxTotalWithStub()
	{
		$Receipt = $this->getMockBuilder('TDD\Receipt')
			->setMethods(['tax', 'subtotal'])
			->getMock();

		$Receipt->method('subtotal')
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
		                ->setMethods(['tax', 'subtotal'])
						->setConstructorArgs([$this->Formatter])
		                ->getMock();

		$Receipt->expects($this->once())
				->method('subtotal')
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
	 * @dataProvider provideSubtotal
	 * @param array $items
	 * @param int|float $expected
	 */
	public function testSubtotalWithDummyCouponAndDataProvider($items, $expected)
	{
		$coupon = null;
		$output = $this->Receipt->subtotal($items, $coupon);

		$this->assertEquals(
			$expected,
			$output,
			"Should equal 15 {$expected}"
		);
	}

	public function provideSubtotal()
	{
		return [
			'ints totaling 16' => [[1, 2, 5, 8], 16],
			[[-1, 2, 5, 8], 14],
			[[1, 2, 8], 11],
		];
	}

	public function testSubtotalException()
	{
		$input = [0, 2, 5, 8];
		$coupon = 1.20;

		$this->expectException('BadMethodCallException');
		$this->Receipt->subtotal($input, $coupon);
	}
}