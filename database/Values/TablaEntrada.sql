USE mydb;
SHOW TABLES;

INSERT INTO entrada (
  idEntrada,
  fechaEntrada,
  idProveedores
)
VALUES
(1, '2025-10-20', 1);

 -- Ver los datos insertados
SELECT * FROM entrada;