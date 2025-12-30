<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Tracking;
use App\Models\Activity;
use App\Models\ProjectType;
use App\Models\ProjectSubtype;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackingDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;
    protected $tracking;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuario de prueba
        $this->user = User::factory()->create();

        // Crear tipo y subtipo de proyecto
        $projectType = ProjectType::create([
            'name' => 'Test Type',
            'description' => 'Test Description'
        ]);

        $projectSubtype = ProjectSubtype::create([
            'name' => 'Test Subtype',
            'description' => 'Test Subtype Description',
            'project_type_id' => $projectType->id
        ]);

        // Crear proyecto de prueba
        $this->project = Project::create([
            'name' => 'Test Project',
            'description' => 'Test Project Description',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
            'location' => 'Test Location',
            'company' => 'Test Company',
            'code' => 'TEST-001',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active'
        ]);

        // Crear tracking de prueba
        $this->tracking = Tracking::create([
            'project_id' => $this->project->id,
            'title' => 'Test Tracking',
            'description' => 'Test Tracking Description',
            'date_start' => now(),
            'duration_days' => 7,
            'status' => true
        ]);
    }

    /** @test */
    public function test_tracking_can_be_soft_deleted()
    {
        // Verificar que el tracking existe y no está eliminado
        $this->assertDatabaseHas('trackings', [
            'id' => $this->tracking->id,
            'deleted_at' => null
        ]);

        // Hacer soft delete del tracking
        $response = $this->postJson("/endpoint/tracking/delete/{$this->tracking->id}");

        // Verificar respuesta
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tracking eliminado exitosamente (soft delete).'
                 ]);

        // Verificar que el tracking fue soft deleted
        $this->assertSoftDeleted('trackings', [
            'id' => $this->tracking->id
        ]);

        // Verificar que el tracking no aparece en consultas normales
        $this->assertNull(Tracking::find($this->tracking->id));

        // Verificar que el tracking aparece con withTrashed()
        $this->assertNotNull(Tracking::withTrashed()->find($this->tracking->id));
    }

    /** @test */
    public function test_tracking_can_be_soft_deleted_with_custom_timestamp()
    {
        $customTimestamp = now()->subDays(2);

        // Hacer soft delete con timestamp personalizado
        $response = $this->postJson("/endpoint/tracking/delete/{$this->tracking->id}", [
            'deleted_at' => $customTimestamp
        ]);

        $response->assertStatus(200);

        // Verificar que se usó el timestamp personalizado
        $tracking = Tracking::withTrashed()->find($this->tracking->id);
        $this->assertNotNull($tracking->deleted_at);
        $this->assertEquals(
            $customTimestamp->format('Y-m-d H:i:s'),
            $tracking->deleted_at->format('Y-m-d H:i:s')
        );
    }

    /** @test */
    public function test_soft_deleted_tracking_can_be_restored()
    {
        // Soft delete del tracking
        $this->tracking->delete();

        // Verificar que está soft deleted
        $this->assertSoftDeleted('trackings', [
            'id' => $this->tracking->id
        ]);

        // Restaurar el tracking
        $response = $this->postJson("/endpoint/tracking/restore/{$this->tracking->id}");

        // Verificar respuesta
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tracking restaurado exitosamente.'
                 ]);

        // Verificar que el tracking fue restaurado
        $this->assertDatabaseHas('trackings', [
            'id' => $this->tracking->id,
            'deleted_at' => null
        ]);

        // Verificar que el tracking aparece en consultas normales
        $this->assertNotNull(Tracking::find($this->tracking->id));
    }

    /** @test */
    public function test_tracking_can_be_force_deleted_when_not_soft_deleted()
    {
        // Verificar que el tracking existe
        $trackingId = $this->tracking->id;
        $this->assertDatabaseHas('trackings', [
            'id' => $trackingId
        ]);

        // Force delete del tracking (sin hacer soft delete primero)
        $response = $this->deleteJson("/endpoint/tracking/force-delete/{$trackingId}");

        // Verificar respuesta
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tracking eliminado permanentemente.'
                 ]);

        // Verificar que el tracking fue eliminado permanentemente
        $this->assertDatabaseMissing('trackings', [
            'id' => $trackingId
        ]);

        // Verificar que el tracking no aparece ni siquiera con withTrashed()
        $this->assertNull(Tracking::withTrashed()->find($trackingId));
    }

    /** @test */
    public function test_soft_deleted_tracking_can_be_force_deleted()
    {
        // Primero hacer soft delete
        $trackingId = $this->tracking->id;
        $this->tracking->delete();

        // Verificar que está soft deleted
        $this->assertSoftDeleted('trackings', [
            'id' => $trackingId
        ]);

        // Force delete del tracking
        $response = $this->deleteJson("/endpoint/tracking/force-delete/{$trackingId}");

        // Verificar respuesta
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tracking eliminado permanentemente.'
                 ]);

        // Verificar que el tracking fue eliminado permanentemente
        $this->assertDatabaseMissing('trackings', [
            'id' => $trackingId
        ]);

        // Verificar que el tracking no aparece ni siquiera con withTrashed()
        $this->assertNull(Tracking::withTrashed()->find($trackingId));
    }

    /** @test */
    public function test_soft_delete_returns_404_for_nonexistent_tracking()
    {
        $nonexistentId = 99999;

        // Intentar soft delete de un tracking que no existe
        $response = $this->postJson("/endpoint/tracking/delete/{$nonexistentId}");

        // Verificar que devuelve error
        $response->assertStatus(500);
    }

    /** @test */
    public function test_force_delete_returns_404_for_nonexistent_tracking()
    {
        $nonexistentId = 99999;

        // Intentar force delete de un tracking que no existe
        $response = $this->deleteJson("/endpoint/tracking/force-delete/{$nonexistentId}");

        // Verificar que devuelve error
        $response->assertStatus(500);
    }

    /** @test */
    public function test_restore_returns_404_for_nonexistent_tracking()
    {
        $nonexistentId = 99999;

        // Intentar restaurar un tracking que no existe
        $response = $this->postJson("/endpoint/tracking/restore/{$nonexistentId}");

        // Verificar que devuelve error
        $response->assertStatus(500);
    }

    /** @test */
    public function test_restore_returns_error_for_non_deleted_tracking()
    {
        // Intentar restaurar un tracking que no está eliminado
        $response = $this->postJson("/endpoint/tracking/restore/{$this->tracking->id}");

        // Verificar que devuelve error (el tracking no está en la papelera)
        $response->assertStatus(500);
    }

    /** @test */
    public function test_tracking_appears_in_with_trashed_endpoint_after_soft_delete()
    {
        // Soft delete del tracking
        $this->tracking->delete();

        // Verificar que aparece en el endpoint with-trashed
        $response = $this->getJson('/endpoint/trackings/with-trashed');

        $response->assertStatus(200);

        // Buscar el tracking en la respuesta
        $trackings = $response->json();
        $found = collect($trackings)->firstWhere('id', $this->tracking->id);

        $this->assertNotNull($found);
        $this->assertNotNull($found['deleted_at']);
    }

    /** @test */
    public function test_tracking_appears_in_only_trashed_endpoint_after_soft_delete()
    {
        // Soft delete del tracking
        $this->tracking->delete();

        // Verificar que aparece en el endpoint only-trashed
        $response = $this->getJson('/endpoint/trackings/only-trashed');

        $response->assertStatus(200);

        // Buscar el tracking en la respuesta
        $trackings = $response->json();
        $found = collect($trackings)->firstWhere('id', $this->tracking->id);

        $this->assertNotNull($found);
        $this->assertNotNull($found['deleted_at']);
    }

    /** @test */
    public function test_tracking_does_not_appear_in_normal_endpoint_after_soft_delete()
    {
        // Soft delete del tracking
        $this->tracking->delete();

        // Verificar que NO aparece en el endpoint normal
        $response = $this->getJson('/endpoint/trackings');

        $response->assertStatus(200);

        // Buscar el tracking en la respuesta
        $trackings = $response->json();
        $found = collect($trackings)->firstWhere('id', $this->tracking->id);

        $this->assertNull($found);
    }

    /** @test */
    public function test_soft_delete_preserves_activities()
    {
        // Crear actividad para el tracking
        $activity = Activity::factory()->create([
            'project_id' => $this->project->id,
            'tracking_id' => $this->tracking->id,
            'name' => 'Test Activity'
        ]);

        // Verificar que la actividad existe
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'deleted_at' => null
        ]);

        // Hacer soft delete del tracking
        $response = $this->postJson("/endpoint/tracking/delete/{$this->tracking->id}");

        $response->assertStatus(200);

        // Verificar que el tracking fue soft deleted
        $this->assertSoftDeleted('trackings', [
            'id' => $this->tracking->id
        ]);

        // Verificar que la actividad NO fue soft deleted (ahora debe persistir)
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'deleted_at' => null
        ]);
    }
}
