use solicitudes;

-- 1. Crear tabla de categorías
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT,
    nombre       VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (id_categoria)
);

-- 2. Agregar columna a area (nullable primero para poder poblarla)
ALTER TABLE area ADD COLUMN id_categoria INT NULL;
ALTER TABLE area ADD CONSTRAINT fk_area_categoria
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria);

-- 3. Insertar categorías base
INSERT INTO categoria (nombre) VALUES
    ('Dirección'),
    ('Académico'),
    ('Administrativo'),
    ('Jefaturas');

-- 4. Asignar categorías a las áreas existentes
UPDATE area SET id_categoria = (SELECT id_categoria FROM categoria WHERE nombre = 'Dirección')
    WHERE nombre IN ('Dirección General', 'Dirección Académica', 'Dirección de Vinculación');

UPDATE area SET id_categoria = (SELECT id_categoria FROM categoria WHERE nombre = 'Académico')
    WHERE nombre IN ('Docencia', 'Desarrollo Académico', 'Coordinación de Inglés',
                     'Biblioteca', 'Titulación', 'Psicopedagogía', 'Cultura y Deportes');

UPDATE area SET id_categoria = (SELECT id_categoria FROM categoria WHERE nombre = 'Administrativo')
    WHERE nombre IN ('Recursos Materiales', 'Recursos Financieros', 'Caja',
                     'Planeación', 'Calidad', 'Transparencia', 'Centro de Copiado');

UPDATE area SET id_categoria = (SELECT id_categoria FROM categoria WHERE nombre = 'Jefaturas')
    WHERE nombre IN ('Industrial', 'Innovación Agrícola', 'Informática',
                     'Sistemas Computacionales', 'Gestión Empresarial');

-- 5. Hacer la columna obligatoria ahora que todas las filas tienen valor
ALTER TABLE area MODIFY COLUMN id_categoria INT NOT NULL;

-- Verificación
SELECT c.nombre AS categoria, a.nombre AS area
FROM categoria c
LEFT JOIN area a ON a.id_categoria = c.id_categoria
ORDER BY c.nombre, a.nombre;
