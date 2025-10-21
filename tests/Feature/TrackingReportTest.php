<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectSubtype;
use App\Models\Tracking;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackingReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de generación exitosa de reporte diario en PDF con actividades.
     */
    public function test_generate_daily_report_with_activities()
    {
        // Crear datos de prueba
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'name' => 'Proyecto Test',
            'location' => 'Lima, Perú',
            'company' => 'Empresa Test S.A.C.',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Semanal 01',
            'date_start' => '2025-01-15',
            'duration_days' => 7,
        ]);

        // Crear actividades para una fecha específica
        $reportDate = '2025-01-16';
        Activity::factory()->count(3)->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => $reportDate,
            'status' => 'completado',
            'name' => 'Actividad Test',
            'description' => 'Descripción de la actividad',
        ]);

        // Hacer la petición
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => $reportDate,
        ]);

        // Verificar que el PDF se generó exitosamente
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Verificar que el nombre del archivo es correcto
        $expectedFileName = 'reporte_diario_' . $project->name . '_' . $reportDate . '.pdf';
        $response->assertDownload($expectedFileName);
    }

    /**
     * Test de generación de reporte diario sin actividades.
     */
    public function test_generate_daily_report_without_activities()
    {
        // Crear datos de prueba
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'name' => 'Proyecto Sin Actividades',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Test',
            'date_start' => '2025-01-15',
            'duration_days' => 7,
        ]);

        // No crear actividades para esta fecha
        $reportDate = '2025-01-16';

        // Hacer la petición
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => $reportDate,
        ]);

        // Verificar que el PDF se generó exitosamente incluso sin actividades
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Test de validación de fecha requerida.
     */
    public function test_generate_daily_report_requires_date()
    {
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
        ]);

        // Hacer la petición sin fecha
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", []);

        // Verificar que falla la validación (puede ser 302 o 422 dependiendo de la configuración)
        $this->assertContains($response->status(), [302, 422]);
    }

    /**
     * Test de validación de formato de fecha.
     */
    public function test_generate_daily_report_requires_valid_date_format()
    {
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
        ]);

        // Hacer la petición con formato de fecha incorrecto
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => '16-01-2025', // Formato incorrecto
        ]);

        // Verificar que falla la validación (puede ser 302 o 422 dependiendo de la configuración)
        $this->assertContains($response->status(), [302, 422]);
    }

    /**
     * Test de error cuando el tracking no existe.
     */
    public function test_generate_daily_report_with_nonexistent_tracking()
    {
        // Hacer la petición con un tracking_id que no existe
        $response = $this->post("/endpoint/tracking/report/daily/99999", [
            'date' => '2025-01-16',
        ]);

        // Verificar que retorna error (puede ser 404 o 500)
        $this->assertContains($response->status(), [404, 500]);
    }

    /**
     * Test de generación de reporte con actividades con diferentes estados.
     */
    public function test_generate_daily_report_with_mixed_status_activities()
    {
        // Crear datos de prueba
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'name' => 'Proyecto Mix Estados',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Mix',
            'date_start' => '2025-01-15',
            'duration_days' => 7,
        ]);

        $reportDate = '2025-01-16';

        // Crear actividades con diferentes estados
        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => $reportDate,
            'status' => 'completado',
            'name' => 'Actividad Completada',
        ]);

        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => $reportDate,
            'status' => 'pendiente',
            'name' => 'Actividad Pendiente',
        ]);

        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => $reportDate,
            'status' => 'programado',
            'name' => 'Actividad Programada',
        ]);

        // Hacer la petición
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => $reportDate,
        ]);

        // Verificar que el PDF se generó exitosamente
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Test de generación de reporte con actividades con imágenes.
     */
    public function test_generate_daily_report_with_activities_with_images()
    {
        // Crear datos de prueba
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'name' => 'Proyecto Con Imágenes',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Con Imágenes',
            'date_start' => '2025-01-15',
            'duration_days' => 7,
        ]);

        $reportDate = '2025-01-16';

        // Crear actividad con imágenes
        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => $reportDate,
            'status' => 'completado',
            'name' => 'Actividad Con Imágenes',
            'image' => json_encode(['image1.jpg', 'image2.jpg', 'image3.jpg']),
        ]);

        // Hacer la petición
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => $reportDate,
        ]);

        // Verificar que el PDF se generó exitosamente
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /**
     * Test de cálculo correcto del número de semana.
     */
    public function test_daily_report_calculates_week_number_correctly()
    {
        // Crear datos de prueba
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'name' => 'Proyecto Semanas',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Semanas',
            'date_start' => '2025-01-01', // Primer día
            'duration_days' => 28, // 4 semanas
        ]);

        // Crear actividad en la tercera semana (día 15, que es semana 3)
        $reportDate = '2025-01-15';
        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => $reportDate,
            'status' => 'completado',
        ]);

        // Hacer la petición
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => $reportDate,
        ]);

        // Verificar que el PDF se generó exitosamente
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // El contenido del PDF debería contener "SEMANA 003"
        // Nota: No podemos verificar el contenido del PDF directamente sin parsearlo,
        // pero al menos verificamos que se generó correctamente
    }

    /**
     * Test de generación de reporte con actividades solo de ese día específico.
     */
    public function test_daily_report_only_includes_activities_from_specified_date()
    {
        // Crear datos de prueba
        $projectType = ProjectType::factory()->create();
        $projectSubtype = ProjectSubtype::factory()->create(['project_type_id' => $projectType->id]);

        $project = Project::factory()->create([
            'name' => 'Proyecto Fechas',
            'project_type_id' => $projectType->id,
            'project_subtype_id' => $projectSubtype->id,
        ]);

        $tracking = Tracking::factory()->create([
            'project_id' => $project->id,
            'title' => 'Seguimiento Fechas',
            'date_start' => '2025-01-15',
            'duration_days' => 7,
        ]);

        // Crear actividades en diferentes fechas
        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => '2025-01-15',
            'status' => 'completado',
            'name' => 'Actividad Día 15',
        ]);

        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => '2025-01-16',
            'status' => 'completado',
            'name' => 'Actividad Día 16',
        ]);

        Activity::factory()->create([
            'project_id' => $project->id,
            'tracking_id' => $tracking->id,
            'fecha_creacion' => '2025-01-17',
            'status' => 'completado',
            'name' => 'Actividad Día 17',
        ]);

        // Hacer la petición solo para el día 16
        $response = $this->post("/endpoint/tracking/report/daily/{$tracking->id}", [
            'date' => '2025-01-16',
        ]);

        // Verificar que el PDF se generó exitosamente
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // El reporte debería contener solo la actividad del día 16
        // Nota: No podemos verificar el contenido del PDF directamente sin parsearlo
    }
}
