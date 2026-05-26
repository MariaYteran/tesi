-- ============================================================
-- Migration: Multi-Clinica (Multi-Tenant) Architecture
-- ============================================================

-- Step 1: Create clinica table
CREATE TABLE IF NOT EXISTS clinica (
    RIF_clinica VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    email VARCHAR(255),
    logo VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Step 2: Migrate existing clinic data from veterinario to clinica
INSERT IGNORE INTO clinica (RIF_clinica, nombre)
SELECT DISTINCT RIF_clinica, MAX(Nombre_clinic) FROM veterinario GROUP BY RIF_clinica;

-- Step 3: Add RIF_clinica to all tenant tables
ALTER TABLE mascota ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE propietario ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE citas ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE actividad ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE consulta ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE test_rapidos ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE laboratorio ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE vacunas ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE ventas ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE producto ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE proveedor ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE venta ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE detalle_venta ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE clientenormal ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE recepcionista ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;
ALTER TABLE `aux-vet` ADD COLUMN RIF_clinica VARCHAR(20) DEFAULT NULL;

-- Step 4: Backfill existing data with the first clinic's RIF
SET @default_rif = (SELECT RIF_clinica FROM clinica LIMIT 1);
UPDATE mascota SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE propietario SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE citas SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE actividad SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE consulta SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE test_rapidos SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE laboratorio SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE vacunas SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE ventas SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE producto SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE proveedor SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE venta SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE detalle_venta SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE clientenormal SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE recepcionista SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;
UPDATE `aux-vet` SET RIF_clinica = @default_rif WHERE RIF_clinica IS NULL;

-- Step 5: Add rol column to veterinario (admin/vet)
ALTER TABLE veterinario ADD COLUMN rol VARCHAR(20) DEFAULT 'vet';

-- Step 6: Add id_auxiliar to consulta table
ALTER TABLE consulta ADD COLUMN id_auxiliar VARCHAR(20) DEFAULT NULL;

-- Make the first vet of each existing clinic an admin
UPDATE veterinario v
SET v.rol = 'admin'
WHERE v.Id_veterinario = (
    SELECT MIN(v2.Id_veterinario)
    FROM veterinario v2
    WHERE v2.RIF_clinica = v.RIF_clinica
);
