<?php

namespace TDD;

class Formatter
{
	public function currencyAmount($amount)
	{
		$rounded = round($amount, 2);
		return $rounded;
	}
}