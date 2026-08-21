<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RevenueChartTest extends TestCase
{
    use RefreshDatabase;

    private const TZ = 'Asia/Manila';

    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        config(['store.timezone' => self::TZ]);
    }

    /** Wall-clock date in the store's timezone, $daysAgo days back. */
    private function localDate(int $daysAgo = 0): string
    {
        return Carbon::now(self::TZ)->subDays($daysAgo)->format('Y-m-d');
    }

    /** Create a completed order placed at a given Manila wall-clock time. */
    private function order(string $manilaTime, array $attributes = []): Order
    {
        return Order::create(array_merge([
            'order_number'   => 'MB-TEST-' . ++$this->sequence,
            'subtotal'       => 100,
            'shipping_fee'   => 0,
            'discount_total' => 0,
            'grand_total'    => 100,
            'status'         => 'completed',
            'payment_status' => 'paid',
            'placed_at'      => Carbon::parse($manilaTime, self::TZ)->utc(),
        ], $attributes));
    }

    private function dashboard()
    {
        return $this->actingAs(User::factory()->create(['role' => 'admin']))
                    ->get(route('admin.dashboard'))
                    ->assertOk();
    }

    private function chart(): Collection
    {
        return $this->dashboard()->viewData('revenueChart');
    }

    private function today(): array
    {
        return $this->chart()->last();
    }

    public function test_window_is_always_seven_consecutive_days_ending_today(): void
    {
        $chart = $this->chart();

        $this->assertCount(7, $chart, 'the window must be exactly 7 days, not 6 or 8');

        $expected = collect(range(6, 0))
            ->map(fn ($ago) => Carbon::now(self::TZ)->subDays($ago)->format('M d'))
            ->all();

        $this->assertSame($expected, $chart->pluck('date')->all());
    }

    public function test_days_without_orders_are_still_present_and_zeroed(): void
    {
        $this->order($this->localDate() . ' 10:00');

        $chart = $this->chart();

        // Only today earned anything; the other six days must still be emitted
        // so the x-axis keeps all seven labels.
        $this->assertSame(6, $chart->where('revenue', 0.0)->count());
        $this->assertSame(100.0, (float) $chart->last()['revenue']);
    }

    public function test_late_night_order_buckets_on_the_local_day_not_the_utc_day(): void
    {
        // 00:30 Manila today is 16:30 UTC yesterday. Reading the raw UTC column
        // would drop this onto the previous bar.
        $this->order($this->localDate() . ' 00:30');

        $chart = $this->chart();

        $this->assertSame(100.0, (float) $chart->last()['revenue'], 'order landed on the wrong day');
        $this->assertSame(0.0, (float) $chart[5]['revenue'], 'yesterday must stay empty');
    }

    public function test_end_of_day_order_stays_on_today(): void
    {
        $this->order($this->localDate() . ' 23:59');

        $this->assertSame(100.0, (float) $this->today()['revenue']);
    }

    public function test_only_completed_and_delivered_orders_count(): void
    {
        $at = $this->localDate() . ' 09:00';

        foreach (['pending_payment', 'confirmed', 'processing', 'shipped', 'cancelled', 'return_requested', 'refunded'] as $status) {
            $this->order($at, ['status' => $status]);
        }

        $this->assertSame(0.0, (float) $this->today()['revenue']);

        $this->order($at, ['status' => 'delivered']);
        $this->order($at, ['status' => 'completed']);

        $this->assertSame(200.0, (float) $this->today()['revenue']);
    }

    public function test_delivered_order_that_was_refunded_is_excluded(): void
    {
        $this->order($this->localDate() . ' 09:00', [
            'status'         => 'delivered',
            'payment_status' => 'refunded',
        ]);

        $this->assertSame(0.0, (float) $this->today()['revenue']);
    }

    public function test_stack_splits_into_product_sales_and_shipping_fees(): void
    {
        $this->order($this->localDate() . ' 09:00', [
            'subtotal'     => 500,
            'shipping_fee' => 89,
            'grand_total'  => 589,
        ]);

        $today = $this->today();

        $this->assertSame(500.0, (float) $today['product_revenue']);
        $this->assertSame(89.0, (float) $today['shipping_revenue']);
        $this->assertSame(589.0, (float) $today['revenue']);
    }

    public function test_product_discount_is_netted_off_product_sales(): void
    {
        // 500 goods + 89 shipping - 50 coupon = 539 actually charged.
        $this->order($this->localDate() . ' 09:00', [
            'subtotal'       => 500,
            'shipping_fee'   => 89,
            'discount_total' => 50,
            'grand_total'    => 539,
        ]);

        $today = $this->today();

        $this->assertSame(450.0, (float) $today['product_revenue']);
        $this->assertSame(89.0, (float) $today['shipping_revenue']);
        $this->assertSame(539.0, (float) $today['revenue'], 'stack height must equal grand_total');
    }

    public function test_free_shipping_discount_is_netted_off_shipping(): void
    {
        // A free_shipping coupon is stored as a discount equal to the fee.
        $this->order($this->localDate() . ' 09:00', [
            'subtotal'       => 500,
            'shipping_fee'   => 89,
            'discount_total' => 89,
            'grand_total'    => 500,
        ]);

        $today = $this->today();

        $this->assertSame(500.0, (float) $today['product_revenue']);
        $this->assertSame(0.0, (float) $today['shipping_revenue']);
        $this->assertSame(500.0, (float) $today['revenue']);
    }

    public function test_order_count_is_reported_per_day(): void
    {
        $at = $this->localDate() . ' 09:00';
        $this->order($at);
        $this->order($at);

        $this->assertSame(2, $this->today()['orders']);
    }

    public function test_orders_outside_the_window_are_ignored(): void
    {
        $this->order($this->localDate(7) . ' 23:00');
        $this->order($this->localDate(30) . ' 12:00');

        $this->assertSame(0.0, (float) $this->chart()->sum('revenue'));
    }

    public function test_oldest_day_in_the_window_is_included(): void
    {
        $this->order($this->localDate(6) . ' 00:01');

        $chart = $this->chart();

        $this->assertSame(100.0, (float) $chart->first()['revenue']);
        $this->assertSame(100.0, (float) $chart->sum('revenue'));
    }

    public function test_order_without_placed_at_falls_back_to_created_at(): void
    {
        $order = $this->order($this->localDate() . ' 09:00');

        Order::whereKey($order->getKey())->update([
            'placed_at'  => null,
            'created_at' => Carbon::parse($this->localDate() . ' 09:00', self::TZ)->utc(),
        ]);

        $this->assertSame(100.0, (float) $this->today()['revenue']);
    }

    public function test_today_kpi_matches_the_final_bar(): void
    {
        $this->order($this->localDate() . ' 09:00', [
            'subtotal' => 500, 'shipping_fee' => 89, 'discount_total' => 50, 'grand_total' => 539,
        ]);

        $response = $this->dashboard();

        $this->assertSame(
            (float) $response->viewData('revenueChart')->last()['revenue'],
            (float) $response->viewData('todayRevenue')
        );
    }

    public function test_empty_window_renders_the_empty_state(): void
    {
        $response = $this->dashboard();

        $this->assertSame(0.0, (float) $response->viewData('revenueTotal'));
        $response->assertSee('No revenue in this period');
    }

    public function test_chart_renders_when_the_window_has_revenue(): void
    {
        $this->order($this->localDate() . ' 09:00');

        $this->dashboard()
             ->assertSee('revenue-chart')
             ->assertDontSee('No revenue in this period');
    }
}
