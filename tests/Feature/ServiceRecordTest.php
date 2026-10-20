<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    private Equipment $equipment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->equipment = Equipment::factory()->create(['purchased_at' => '2022-01-01']);
    }

    private function url(): string
    {
        return "/equipment/{$this->equipment->id}/service-records";
    }

    public function test_service_record_can_be_added_from_detail(): void
    {
        $this->post($this->url(), [
            'description' => 'Výměna ložisek',
            'serviced_at' => '2024-03-10',
            'cost' => '1499.90',
        ])->assertRedirect("/equipment/{$this->equipment->id}");

        $record = $this->equipment->serviceRecords()->first();
        $this->assertSame('Výměna ložisek', $record->description);
        $this->assertSame('1499.90', $record->cost);
        $this->assertSame(1499.90, $this->equipment->totalServiceCost());
    }

    public function test_cost_accepts_czech_decimal_comma_and_zero(): void
    {
        $this->post($this->url(), ['description' => 'A', 'serviced_at' => '2024-01-01', 'cost' => '250,5'])
            ->assertSessionHasNoErrors();
        $this->post($this->url(), ['description' => 'B', 'serviced_at' => '2024-01-02', 'cost' => '0'])
            ->assertSessionHasNoErrors();

        $this->assertSame(250.5, $this->equipment->totalServiceCost());
    }

    public function test_cost_must_be_non_negative_decimal(): void
    {
        foreach (['-1', '-0.01', 'abc', '12.345', ''] as $bad) {
            $this->post($this->url(), ['description' => 'Oprava', 'serviced_at' => '2024-01-01', 'cost' => $bad])
                ->assertSessionHasErrors('cost');
        }
        $this->assertSame(0, ServiceRecord::count());
    }

    public function test_service_date_must_be_valid(): void
    {
        foreach (['2024-02-31', '10/03/2024', 'zítra', '2021-12-31', now()->addDay()->format('Y-m-d')] as $bad) {
            $this->post($this->url(), ['description' => 'Oprava', 'serviced_at' => $bad, 'cost' => '100'])
                ->assertSessionHasErrors('serviced_at');
        }
        $this->assertSame(0, ServiceRecord::count());
    }

    public function test_service_record_can_be_deleted_only_through_its_equipment(): void
    {
        $record = ServiceRecord::factory()->create(['equipment_id' => $this->equipment->id]);
        $other = Equipment::factory()->create();

        $this->delete("/equipment/{$other->id}/service-records/{$record->id}")->assertNotFound();
        $this->delete("/equipment/{$this->equipment->id}/service-records/{$record->id}")
            ->assertRedirect("/equipment/{$this->equipment->id}");

        $this->assertSame(0, ServiceRecord::count());
    }

    public function test_service_history_can_be_exported_to_csv(): void
    {
        ServiceRecord::factory()->create([
            'equipment_id' => $this->equipment->id,
            'description' => 'Kalibrace',
            'serviced_at' => '2024-06-01',
            'cost' => 300,
        ]);

        $response = $this->get("/equipment/{$this->equipment->id}/export");
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Kalibrace', $csv);
        $this->assertStringContainsString('2024-06-01', $csv);
        $this->assertStringContainsString('300,00', $csv);
        $this->assertStringContainsString($this->equipment->inventory_number, $csv);
    }
}
