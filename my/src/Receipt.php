<?php

namespace TDD;

use \BadMethodCallException;

/**
 * Class Receipt
 * @package TDD
 */
class Receipt
{
	public function __construct($formatter)
	{
		$this->Formatter = $formatter;
	}

	/**
	 * @param array $items
	 * @param $coupon
	 *
	 * @return number
	 */
	public function subtotal(array $items = [], $coupon)
	{
		if($coupon > 1.00)
		{
			throw new BadMethodCallException('Coupon must be less than or equal to 1.00');
		}

		$sum = array_sum($items);

		if(!(is_null($coupon)))
		{
			$sum = $sum - ($sum * $coupon);
		}

		return $sum;
	}

	/**
	 * @param $amount
	 * @param $tax
	 *
	 * @return mixed
	 */
	public function tax($amount, $tax)
	{
		$taxedAmount = $this->Formatter->currencyAmount(($amount * $tax));
		return $taxedAmount;
	}

	/**
	 * @param $items
	 * @param $tax
	 * @param $coupon
	 *
	 * @return number
	 */
	public function postTaxTotal($items, $tax, $coupon)
	{
		$subtotal = $this->subtotal($items, $coupon);

		$postTaxTotal = $subtotal + $this->tax($subtotal, $tax);

		return $postTaxTotal;
	}
}
