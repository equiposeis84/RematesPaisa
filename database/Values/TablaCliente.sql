USE mydb;
SHOW TABLES;
INSERT INTO cliente (
	idCliente,
	tipoDocumentoCliente,
	nombreCliente,
	apellidoCliente,
	direccionCliente,
	telefonoCliente,
	emailCliente
)
VALUES
('1002456789', 'CC', 'Camila', 'Torres', 'Carrera 12 #45-23', '3102546789', 'camila.torres@gmail.com'),
('1034865921', 'CC', 'Andres', 'Gomez', 'Calle 8 #15-12', '3205896325', 'andresgomez@hotmail.com'),
('1034692352', 'CC', 'María', 'Fernández', 'Calle 32 #21-53', '3210025649', 'mariaf@gmail.com'),
('1067432893', 'CC', 'Juan', 'Pérez', 'Calle 10 #20-30', '3216549870', 'juanperez@gmail.com'),
('1089345621', 'CC', 'Valentina', 'Morales', 'Avenida 3 #20-45', '3008541236', 'valentina.morales@yahoo.com'),
('2843298452', 'CC', 'Gonzalo', 'Rodriguez', 'Tranversal 14a #42-21', '3216549870', 'gonzalor@gmail.com'),
('1054826931', 'CC', 'Laura', 'Ramirez', 'Carrera 15 #18-22', '3207458963', 'laura.ramirez@gmail.com'),
('1078952146', 'CC', 'Felipe', 'Mendoza', 'Calle 24 #9-31', '3115489623', 'felipe.mendoza@hotmail.com'),
('1096345721', 'CC', 'Paula', 'Ortega', 'Avenida 6 #44-10', '3128956470', 'paula.ortega@yahoo.com'),
('1045689321', 'CC', 'Santiago', 'Cruz', 'Transversal 22 #17-50', '3007845123', 'santiago.cruz@gmail.com');

-- Ver los datos insertados
SELECT * FROM cliente;