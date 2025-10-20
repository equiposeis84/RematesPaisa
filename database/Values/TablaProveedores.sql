USE mydb;
SHOW TABLES;

INSERT INTO proveedores (
  idProveedores,
  tipoDocumentoProveedor,
  nombreProveedor,
  telefonoProveedor,
  correoProveedor
)
VALUES
(1, 'CC', 'Postobon S.A.', '3104567890', 'postobon@gmail.com'),
(2, 'CC', 'Grupo Nutresa', '3115678901', 'nutresa@gmail.com'),
(3, 'CC', 'Alpina Productos Alimenticios', '3126789012', 'alpinaalimentos@gmail.com'),
(4, 'CC', 'Juan Valdez Cafe', '3137890123', 'juanvaldez@gmail.com'),
(5, 'CC', 'Bavaria S.A.', '3148901234', 'bavaria@gmail.com'),
(6, 'CC', 'Colombina S.A.', '3159012345', 'colombinaoficial@gmail.com'),
(7, 'CC', 'Ramo S.A.', '3160123456', 'ramoempresa@gmail.com'),
(8, 'CC', 'Roya Prestige S.A', '321468952', 'royalprestige@gmail.com'),
(9, 'CC', 'Yupi', '325489958', 'yupi@gmail.com'),
(10, 'CC', 'Norma', '41455556', 'norma@gmail.com'),
(11, 'CC', 'Peersal', '321568977', 'peersal@gmail.com'),
(12, 'CC', 'Offi-esco', '321654654', 'offiesco@gmail.com');

 -- Ver los datos insertados
SELECT * FROM proveedores;