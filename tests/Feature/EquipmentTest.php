<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\ServiceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $override = []): array
    {
        return array_merge([
            'name' => 'Aku vrtačka',
            'inventory_number' => 'INV-100',
            'status' => 'active',
            'purchased_at' => '2023-05-01',
        ], $override);
    }

    public function test_index_lists_equipment_and_filters_by_status(): void
    {
        Equipment::factory()->create(['name' => 'Aktivní pila', 'status' => 'active']);
        Equipment::factory()->create(['name' => 'Bruska v servisu', 'status' => 'service']);
        Equipment::factory()->create(['name' => 'Starý kompresor', 'status' => 'retired']);

        $this->get('/equipment')->assertOk()
            ->assertSee('Aktivní pila')->assertSee('Bruska v servisu')->assertSee('Starý kompresor');

        $this->get('/equipment?status=service')->assertOk()
            ->assertSee('Bruska v servisu')
            ->assertDontSee('Aktivní pila')
            ->assertDontSee('Starý kompresor');

        $this->get('/equipment?status=retired')->assertOk()
            ->assertSee('Starý kompresor')
            ->assertDontSee('Bruska v servisu');
    }

    public function test_invalid_status_filter_is_rejected(): void
    {
        $this->get('/equipment?status=broken')->assertSessionHasErrors('status');
    }

    public function test_equipment_can_be_created(): void
    {
        $response = $this->post('/equipment', $this->validData());

        $equipment = Equipment::firstWhere('inventory_number', 'INV-100');
        $this->assertNotNull($equipment);
        $response->assertRedirect(route('equipment.show', $equipment));
        $this->assertSame('2023-05-01', $equipment->purchased_at->format('Y-m-d'));
    }

    public function test_inventory_number_must_be_unique(): void
    {
        Equipment::factory()->create(['inventory_number' => 'INV-100']);

        $this->post('/equipment', $this->validData())->assertSessionHasErrors('inventory_number');
        // i při jiné velikosti písmen / mezerách
        $this->post('/equipment', $this->validData(['inventory_number' => ' inv-100 ']))
            ->assertSessionHasErrors('inventory_number');
        $this->assertSame(1, Equipment::count());
    }

    public function test_update_keeps_own_inventory_number_but_rejects_foreign_one(): void
    {
        $a = Equipment::factory()->create(['inventory_number' => 'INV-A']);
        Equipment::factory()->create(['inventory_number' => 'INV-B']);

        $this->put("/equipment/{$a->id}", $this->validData(['inventory_number' => 'INV-A', 'name' => 'Nový název']))
            ->assertSessionHasNoErrors();
        $this->assertSame('Nový název', $a->fresh()->name);

        $this->put("/equipment/{$a->id}", $this->validData(['inventory_number' => 'INV-B']))
            ->assertSessionHasErrors('inventory_number');
    }

    public function test_status_must_be_from_allowed_list(): void
    {
        $this->post('/equipment', $this->validData(['status' => 'lost']))->assertSessionHasErrors('status');
        $this->assertSame(0, Equipment::count());
    }

    public function test_purchase_date_must_be_valid(): void
    {
        foreach (['2023-02-30', '01.05.2023', 'včera', '2023-13-01', '1800-01-01', now()->addDay()->format('Y-m-d')] as $bad) {
            $this->post('/equipment', $this->validData(['purchased_at' => $bad]))
                ->assertSessionHasErrors('purchased_at');
        }
        $this->assertSame(0, Equipment::count());
    }

    public function test_equipment_can_be_deleted_with_its_history(): void
    {
        $equipment = Equipment::factory()->create();
        ServiceRecord::factory()->count(2)->create(['equipment_id' => $equipment->id]);

        $this->delete("/equipment/{$equipment->id}")->assertRedirect('/equipment');

        $this->assertSame(0, Equipment::count());
        $this->assertSame(0, ServiceRecord::count());
    }

    public function test_detail_shows_history_and_total_cost(): void
    {
        $equipment = Equipment::factory()->create(['purchased_at' => '2020-01-01']);
        ServiceRecord::factory()->create(['equipment_id' => $equipment->id, 'cost' => 1000.50, 'description' => 'Výměna uhlíků']);
        ServiceRecord::factory()->create(['equipment_id' => $equipment->id, 'cost' => 250.25, 'description' => 'Revize']);
        ServiceRecord::factory()->create(['cost' => 9999]); // jiné zařízení – nesmí se započítat

        $this->assertSame(1250.75, $equipment->totalServiceCost());

        $this->get("/equipment/{$equipment->id}")->assertOk()
            ->assertSee('Výměna uhlíků')
            ->assertSee('Revize')
            ->assertSee('1 250,75 Kč');
    }

    public function test_status_badge_has_color_class_and_text(): void
    {
        $equipment = Equipment::factory()->create(['status' => 'service']);

        $this->get("/equipment/{$equipment->id}")
            ->assertSee('status-service')
            ->assertSee('V servisu');
    }
}
