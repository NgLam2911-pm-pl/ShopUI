<?php
declare(strict_types=1);

namespace NgLam2911\ShopUI\forms;

use Generator;
use SOFe\AwaitGenerator\Await;

abstract readonly class AsyncForm{

	public final function send(): void{
		Await::g2c($this->asyncSend());
	}

	protected abstract function asyncSend(): Generator;
}