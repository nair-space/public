<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trade>
 */
class TradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entry = $this->faker->randomFloat(2, 20000, 60000);
        $status = $this->faker->randomElement(['open', 'closed']);
        $exit = $status === 'closed' ? ($this->faker->boolean(70) ? $entry * 1.05 : $entry * 0.95) : null;
        $pnl = $exit ? ($exit - $entry) * 0.1 : 0;

        return [
            'pair' => $this->faker->randomElement(['BTC/USDT', 'ETH/USDT', 'SOL/USDT', 'XRP/USDT']),
            'type' => $this->faker->randomElement(['buy', 'sell']),
            'entry_price' => $entry,
            'exit_price' => $exit,
            'size' => 0.1,
            'pnl' => $pnl,
            'status' => $status,
            'notes' => $this->faker->sentence(),
            'trade_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
