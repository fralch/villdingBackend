-- Subtipos para 'Residencial' (ID = 1)
INSERT INTO project_subtypes (project_type_id, name, created_at, updated_at) VALUES
(1, 'Vivienda unifamiliar', NOW(), NOW()),
(1, 'Vivienda multifamiliar', NOW(), NOW()),
(1, 'Urbanizaciones', NOW(), NOW());

-- Subtipos para 'Comercial' (ID = 2)
INSERT INTO project_subtypes (project_type_id, name, created_at, updated_at) VALUES
(2, 'Oficinas', NOW(), NOW()),
(2, 'Centros comerciales', NOW(), NOW()),
(2, 'Restaurantes y bares', NOW(), NOW()),
(2, 'Hoteles', NOW(), NOW());

-- Subtipos para 'Industrial' (ID = 3)
INSERT INTO project_subtypes (project_type_id, name, created_at, updated_at) VALUES
(3, 'Fábricas y plantas de producción', NOW(), NOW()),
(3, 'Almacenes y centros de distribución', NOW(), NOW()),
(3, 'Instalaciones de energía (solar, eólica, etc.)', NOW(), NOW());

-- Subtipos para 'Infraestructura' (ID = 4)
INSERT INTO project_subtypes (project_type_id, name, created_at, updated_at) VALUES
(4, 'Carreteras y puentes', NOW(), NOW()),
(4, 'Aeropuertos', NOW(), NOW()),
(4, 'Puertos y muelles', NOW(), NOW()),
(4, 'Estaciones de tren y metro', NOW(), NOW());

-- Subtipos para 'Institucional' (ID = 5)
INSERT INTO project_subtypes (project_type_id, name, created_at, updated_at) VALUES
(5, 'Escuelas y universidades', NOW(), NOW()),
(5, 'Hospitales y centros de salud', NOW(), NOW()),
(5, 'Edificios gubernamentales', NOW(), NOW()),
(5, 'Instalaciones deportivas y recreativas', NOW(), NOW());

-- Tipos sin subtipos
-- 'Renovación y remodelación' (ID = 6) y 'Proyectos de paisajismo y diseño urbano' (ID = 7)
-- No necesitan subtipos, pero los registros ya existen en project_types.
