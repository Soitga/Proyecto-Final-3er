create DATABASE FOODY

use FOODY

rename table ingred_pursha to ingred_purcha

ALTER TABLE factoryadmin MODIFY tel VARCHAR(10);
desc employee

select * from employee

ALTER TABLE employee MODIFY tel VARCHAR(10);


create table factory(
    code VARCHAR(8)PRIMARY key not null,
    name varchar(16) not null,
    tel int null,
    email VARCHAR (25) null UNIQUE,
    streetAddr varchar (15)null,
    numAddr int null,
    colonyAddr varchar(20),
    numberEmp int not null,
    city varchar(3) not null,
    Foreign Key (city) REFERENCES city(code)
)

create table city(
    code VARCHAR(5) PRIMARY KEY,
    name VARCHAR(20) not null UNIQUE
)

create table factoryAdmin(
    num int PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(20) not null,
    middleName VARCHAR(20) not null,
    lastName VARCHAR(20) null,
    tel int null,
    factory varchar(8) not null,
    Foreign Key (factory) REFERENCES factory(code)
)

create table diningRoomManager(
    num int PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(20) not null,
    middleName VARCHAR(20) not null,
    lastName VARCHAR(20) null,
    tel int null,
    diningRoom int not null,
    Foreign Key (diningRoom) REFERENCES diningRoom(num)
)

create table employee(
    num int PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(20) not null,
    middleName VARCHAR(20) not null,
    lastName VARCHAR(20) null,
    tel int null,
    email VARCHAR(30) null UNIQUE,
    factory VARCHAR(8) not null, 
    jobPosition VARCHAR(5) not null,
    Foreign Key (factory) REFERENCES factory(code),
    Foreign Key (jobPosition) REFERENCES jobPosition(code)
) 

create table jobPosition(
    code VARCHAR (5) PRIMARY KEY,
    description VARCHAR (20) UNIQUE
)

create table orderEmp(
    num int PRIMARY KEY AUTO_INCREMENT,
    paymentAmount FLOAT not null,
    dateOrde date not null,
    totalDiscount float not null,
    employee int not null,
    status VARCHAR(5) not null,
    Foreign Key (employee) REFERENCES employee(num),
    Foreign Key (status) REFERENCES status(code)
)

create table ticket(
    num int PRIMARY KEY AUTO_INCREMENT,
    total float not null,
    dateTick date not null,
    reportStatus VARCHAR(20) not null,
    report int not null,
    orderEmp int not null,
    Foreign Key (report) REFERENCES report(num),
    Foreign Key (orderEmp) REFERENCES orderEmp(num)
)

create table report(
    num int PRIMARY KEY AUTO_INCREMENT,
    totalSales int not null,
    dateStart  date not null,
    endDate date not null,
    productionDate date not null
)

create table status(
    code VARCHAR(5) PRIMARY KEY,
    description VARCHAR(10) not null
)

create table category(
    code VARCHAR(5) PRIMARY KEY,
    name VARCHAR(20)
)

create table dish(
    code VARCHAR(5) PRIMARY KEY,
    name VARCHAR(50) not null,
    description VARCHAR(50) not null,
    price float not null,
    discountPercentage float not null,
    category VARCHAR(5) not null,
    menu VARCHAR(5) not null,
    Foreign Key (category) REFERENCES category(code),
    Foreign Key (menu) REFERENCES menu(code)
)

create table ingredients(
    num int PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR (20) not NULL,
    experitionDate date NULL
)


create table purchaseOrder(
    num int PRIMARY KEY AUTO_INCREMENT,
    amountPayment FLOAT not NULL,
    datePurs date not null,
    diningroommanager int not null,
    supplier VARCHAR(7) not null,
    Foreign Key (diningroommanager) REFERENCES diningroommanager(num),
    Foreign Key (supplier) REFERENCES supplier(code)
)

create table supplier(
    code VARCHAR(7) PRIMARY KEY,
    name VARCHAR(25) not NULL,
    tel INT null,
    email VARCHAR (30) NULL UNIQUE
)

create table diningRoom(
    num int PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) not NULL,
    ubication VARCHAR(30) not null,
    factory VARCHAR(8) not null,
    Foreign Key (factory) REFERENCES factory(code)
)

create table menu(
    code VARCHAR (5) PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    description VARCHAR (60) NOT NULL
)

create table dish_ingred(
    dish VARCHAR(5),
    ingredients int,
    numberIngred int not null,
    PRIMARY KEY(dish, ingredients),
    FOREIGN KEY (dish) references dish(code),
    FOREIGN KEY (ingredients) references ingredients(num)
)

create table ingred_pursha(
    ingredients int not null,
    purchaseOrder int not null,
    numberIngred int NOT NULL,
    PRIMARY KEY(ingredients,purchaseOrder),
    Foreign Key (purchaseOrder) REFERENCES purchaseOrder(num),
    Foreign Key (ingredients) REFERENCES ingredients(num)
)

create table dining_menu(
    diningRoom int not null,
    menu VARCHAR(5) not null,
    PRIMARY KEY(menu, diningRoom)
)

create table ord_dish(
    numberDishes int NOT NULL,
    amount int NOT NULL,
    dish VARCHAR(5) NOT NULL,
    orderemp int NOT NULL,
    PRIMARY KEY(dish,orderemp),
    Foreign Key (dish) REFERENCES dish(code),
    Foreign Key (orderemp) REFERENCES orderemp(num)
)

create table menu_type(
    num int PRIMARY KEY AUTO_INCREMENT,
    description VARCHAR(10) not null,
    start_time TIME not null,
    end_time TIME not null
)

/*agrego la fk de menu_type a menu*/
ALTER TABLE menu
ADD COLUMN type_menu int not null

ALTER TABLE menu
CHANGE COLUMN type_menu menu_type int not null

ALTER TABLE menu
ADD CONSTRAINT menu_type
FOREIGN KEY (menu_type) REFERENCES menu_type(num);


INSERT INTO factory (code, name, tel, email, streetAddr, numAddr, colonyAddr, numberEmp, city) VALUES
('FERROS', 'Ferros Inc', 6642588677, 'ferrostijuana@gmail.com', 'Brisa', 2, 'a',20,'TIJ'),
('ACEROS', 'Aceros del Norte', 6641589814, 'acerosnorte@gmail.com', 'Reforma', 5,'b', 20, 'ENS'),
('ALUMEX', 'Alumex Corp', 6643584872, 'aluminiosmexico@gmail.com','Insurgentes', 15,'c',20, 'TIJ'),
('MADERB', 'Maderas Benitez', 6641651487, 'madebenitez@gmail.com', 'Las Rosas', 14,'d', 20, 'ROS'),
('TEXTIL', 'Textiles SA', 6644585759, 'textilesssa@gmail.com','Alameda', 12 ,'e',20, 'MEX'),
('PLASTI', 'Plastica Mex', 6646585478, 'plasticosmex123@gmail.com','Blvd. Diaz Ordaz', 10 ,'f',20,'TIJ'),
('ELECTNO', 'Electro Nova', 6641243257, 'electronova142@gmail.com', 'Castaños', 54,'g',20, 'TIJ'),
('MUEBLES', 'Muebles Elvia', 6631478799, 'muebleselvia@gmail.com', 'Colinas', 72,'h', 20, 'ENS'),
('DULMEX', 'Dulmex Paletas', 6648498742, 'dulmexpaletas@gmail.com','Monterrey', 45,'i', 20, 'ROS'),
('CMTNOR', 'CMT Norte', 6634228879, 'CMTNORTE@gmail.com', 'Jamaica', 54, 'j', 20, 'MEX');

insert into city(code, name) VALUES
("TIJ","Tijuana"),
("MEX","Mexicali"),
("ENS","Ensenada"),
("ROS","Rosarito");

INSERT INTO factoryAdmin(num, firstName, middleName, lastName, tel, factory) VALUES
(1, 'Javier', 'Diaz', 'Molina', '6645748912', 'FERROS'),
(2, 'Ernesto', 'Mendoza', 'Cardenas', '6647419988', 'ACEROS'),
(3, 'Marta', 'Figueroa', 'Castro', '6641587441', 'ALUMEX'),
(4, 'Alejandra', 'Alvarez', 'Cruz', '6641575863', 'MADERB'),
(5, 'Fernando', 'Ortega', 'Salinas', '6642368941', 'TEXTIL'),
(6, 'Carlos', 'Vasquez', 'Beltran', '6632225413', 'PLASTI'),
(7, 'Armando', 'Nuñes', 'Murillo', '6647894152', 'ELECTNO'),
(8, 'Rigoberta', 'Osuna', 'Valencia', '6649871420', 'MUEBLES'),
(9, 'Genaro', 'Brito', 'Canales', '6640211470', 'DULMEX'),
(10, 'Josefa', 'Hernandez', 'Ochoa', '6637991481', 'CMTNOR');

INSERT INTO employee(num, firstName, middleName, lastName, tel, email, factory, jobposition)
VALUES
(1,'Santiago','García','Roman', '6644216897', 'santiago.García@gmail.com', 'DULMEX', 'OPRD'),
(2,'Luis','Rodríguez','Sorte', '6647116666', 'luis.Rodríguez@gmail.com', 'FERROS', 'OPRC'),
(3,'Marta','Martínez','Mendoza', '6641178531', 'marta.Martínez@gmail.com', 'FERROS', 'INGN'),
(4,'Ángel','López','Chávez', '6648715471', 'ángel.López@gmail.com', 'FERROS', 'SCRT'),
(5,'Carmen','González','Aguilar', '6645781245', 'carmen.González@gmail.com', 'FERROS', 'GRNT'),
(6,'Hernán','Pérez','Rojas', '6641252549', 'hernán.Pérez@gmail.com', 'FERROS', 'ASTN'),
(7,'Antonia','Sánchez','Medina', '6644786655', 'antonia.Sánchez@gmail.com', 'FERROS', 'SPVR'),
(8,'Rodrigo','Ramírez','Delgado', '6648871478', 'rodrigo.Ramírez@gmail.com', 'FERROS', 'INTN'),
(9,'Norma','Torres','Soto', '6634785211', 'norma.Torres@gmail.com', 'FERROS', 'OPRD'),
(10,'Eduardo','Flores','Vega', '6642317743', 'eduardo.Flores@gmail.com', 'FERROS', 'OPRC'),
(11,'Fabiana','Rivera','Silva', '6641784785', 'fabiana.Rivera@gmail.com', 'DULMEX', 'INGN'),
(12,'Sebastián','Gómez','Salazar', '6443321476', 'sebastián.Gómez@gmail.com', 'DULMEX', 'SCRT'),
(13,'Maribel','Díaz','León', '6641478793', 'maribel.Díaz@gmail.com', 'ACEROS', 'GRNT'),
(14,'Iván','Vázquez','Cruz', '6635556871', 'iván.Vázquez@gmail.com', 'ACEROS', 'ASTN'),
(15,'Paloma','Romero','Navarro', '6645718745', 'paloma.Romero@gmail.com', 'ACEROS', 'SPVR'),
(16,'Rafael','Herrera','Fuentes', '6634578125', 'rafael.Herrera@gmail.com', 'ACEROS', 'INTN'),
(17,'Juana','Castro','Paredes', '6647486541', 'juana.Castro@gmail.com', 'ACEROS', 'OPRD'),
(18,'Mateo','Domínguez','Ramos', '6644419873', 'mateo.Domínguez@gmail.com', 'ACEROS', 'OPRC'),
(19,'Claudia','Morales','Valencia', '6641234777', 'claudia.Morales@gmail.com', 'ACEROS', 'INGN'),
(20,'Tomás','Ortiz','Acosta', '6648796551', 'tomás.Ortiz@gmail.com', 'ACEROS', 'SCRT'),
(21,'Fátima','Ruiz','Molina', '6647483214', 'fátima.Ruiz@gmail.com', 'ACEROS', 'GRNT'),
(22,'Jorge','Jiménez','Orozco', '6648798899', 'jorge.Jiménez@gmail.com', 'DULMEX', 'ASTN'),
(23,'Pilar','Mendoza','Ponce', '6641717781', 'pilar.Mendoza@gmail.com', 'DULMEX', 'SPVR'),
(24,'Damián','Chávez','Serrano', '6647779983', 'damián.Chávez@gmail.com', 'ALUMEX', 'INTN'),
(25,'Vanessa','Aguilar','Miranda', '6648752214', 'vanessa.Aguilar@gmail.com', 'ALUMEX', 'OPRD'),
(26,'Lucas','Rojas','Rivas', '6641475856', 'lucas.Rojas@gmail.com', 'ALUMEX', 'OPRC'),
(27,'Cristina','Medina','Cárdenas', '6647481444', 'cristina.Medina@gmail.com', 'ALUMEX', 'INGN'),
(28,'Joaquín','Delgado','Esquivel', '6641119874', 'joaquín.Delgado@gmail.com', 'ALUMEX', 'SCRT'),
(29,'Selene','Soto','Barrios', '6648987412', 'selene.Soto@gmail.com', 'ALUMEX', 'GRNT'),
(30,'Hugo','Vega','Reyes', '6648749886', 'hugo.Vega@gmail.com', 'ALUMEX', 'ASTN'),
(31,'Jimena','Silva','Palacios', '6647881236', 'jimena.Silva@gmail.com', 'ALUMEX', 'SPVR'),
(32,'Bruno','Salazar','Bautista', '6647772556', 'bruno.Salazar@gmail.com', 'ALUMEX', 'INTN'),
(33,'Regina','León','Zamora', '6641444786', 'regina.León@gmail.com', 'DULMEX', 'OPRD'),
(34,'Esteban','Cruz','Castañeda', '6648918745', 'esteban.Cruz@gmail.com', 'DULMEX', 'OPRC'),
(35,'Almudena','Navarro','Luna', '6648452132', 'almudena.Navarro@gmail.com', 'MADERB', 'INGN'),
(36,'César','Fuentes','Villanueva', '6644477851', 'césar.Fuentes@gmail.com', 'MADERB', 'SCRT'),
(37,'Gloria','Paredes','Montes', '6647844712', 'gloria.Paredes@gmail.com', 'MADERB', 'GRNT'),
(38,'Alexis','Ramos','Lara', '6644857892', 'alexis.Ramos@gmail.com', 'MADERB', 'ASTN'),
(39,'Sabrina','Valencia','Montero', '6647844444', 'sabrina.Valencia@gmail.com', 'MADERB', 'SPVR'),
(40,'Guillermo','Acosta','Mora', '6448882241', 'guillermo.Acosta@gmail.com', 'MADERB', 'INTN'),
(41,'Aitana','Sandoval','Olivares', '6641471147', 'aitana.Sandoval@gmail.com', 'MADERB', 'OPRD'),
(42,'Lorenzo','Peña','Salinas', '6647884561', 'lorenzo.Peña@gmail.com', 'MADERB', 'OPRC'),
(43,'Melisa','Escobar','Cordero', '6645556841', 'melisa.Escobar@gmail.com', 'MADERB', 'INGN'),
(44,'Álvaro','Carrillo','Núñez', '6648816937', 'álvaro.Carrillo@gmail.com', 'DULMEX', 'SCRT'),
(45,'Rosario','Márquez','Aguirre', '6641427411', 'rosario.Márquez@gmail.com', 'CMTNOR', 'GRNT'),
(46,'Enrique','Valdez','Peralta', '6637485487', 'enrique.Valdez@gmail.com', 'TEXTIL', 'ASTN'),
(47,'Daniela','Vargas','Santiago', '6634216741', 'daniela.Vargas@gmail.com', 'TEXTIL', 'SPVR'),
(48,'Samuel','Molina','Coronado', '6648448745', 'samuel.Molina@gmail.com', 'TEXTIL', 'INTN'),
(49,'Sonia','Orozco','Trujillo', '6631484718', 'sonia.Orozco@gmail.com', 'TEXTIL', 'OPRD'),
(50,'Marcelo','Ponce','Figueroa', '6634711123', 'marcelo.Ponce@gmail.com', 'TEXTIL', 'OPRC'),
(51,'Rosaura','Serrano','Padilla', '6647311124', 'rosaura.Serrano@gmail.com', 'TEXTIL', 'INGN'),
(52,'Julia','Miranda','Quiroz', '6635474785', 'julia.Miranda@gmail.com', 'TEXTIL', 'SCRT'),
(53,'Mireya','Rivas','Barrera', '6639879999', 'mireya.Rivas@gmail.com', 'TEXTIL', 'GRNT'),
(54,'Alicia','Cárdenas','Espinoza', '6649891578', 'alicia.Cárdenas@gmail.com', 'TEXTIL','ASTN')

INSERT INTO employee(num, firstName, middleName, lastName, tel, email, factory, jobposition)
VALUES
(55, 'Yolanda', 'Esquivel', 'Meza', '6648791451', 'yolanda.Esquivel@gmail.com', 'CMTNOR', 'SPVR'),
(56, 'Magdalena', 'Barrios', 'Gutiérrez', '6634154711', 'magdalena.Barrios@gmail.com', 'CMTNOR', 'INTN'),
(57, 'Belén', 'Reyes', 'Maldonado', '6645541785', 'belén.Reyes@gmail.com', 'PLASTI', 'OPRD'),
(58, 'Norma', 'Palacios', 'Naranjo', '6648799994', 'norma.Palacios@gmail.com', 'PLASTI', 'OPRC'),
(59, 'Elsa', 'Bautista', 'Ochoa', '6634857412', 'elsa.Bautista@gmail.com', 'PLASTI', 'INGN'),
(60, 'Marisol', 'Zamora', 'Olmedo', '6647679983', 'marisol.Zamora@gmail.com', 'PLASTI', 'SCRT'),
(61, 'Julieta', 'Castañeda', 'Escamilla', '6648752414', 'julieta.Castañeda@gmail.com', 'PLASTI', 'GRNT'),
(62, 'Estela', 'Luna', 'Domínguez', '6641175856', 'estela.Luna@gmail.com', 'PLASTI', 'ASTN'),
(63, 'Rafael', 'Villanueva', 'Marín', '6647481444', 'rafael.Villanueva@gmail.com', 'PLASTI', 'SPVR'),
(64, 'Juana', 'Montes', 'Velasco', '6641009874', 'juana.Montes@gmail.com', 'PLASTI', 'INTN'),
(65, 'Mateo', 'Lara', 'Morales', '6648987412', 'mateo.Lara@gmail.com', 'PLASTI', 'OPRD'),
(66, 'Claudia', 'Montero', 'Morales', '6648009006', 'claudia.Montero@gmail.com', 'CMTNOR', 'OPRC'),
(67, 'Tomás', 'Cabrera', 'Tejada', '6647881236', 'tomás.Cabrera@gmail.com', 'CMTNOR', 'INGN'),
(68, 'Fátima', 'Valencia', 'Juárez', '6637102556', 'fátima.Valencia@gmail.com', 'ELECTNO', 'SCRT'),
(69, 'Jorge', 'Campos', 'Fernández', '6631444786', 'jorge.Campos@gmail.com', 'ELECTNO', 'GRNT'),
(70, 'Pilar', 'Carrasco', 'Cerezo', '6638918745', 'pilar.Carrasco@gmail.com', 'ELECTNO', 'ASTN'),
(71, 'Damián', 'Suárez', 'Carrillo', '6638452132', 'damián.Suárez@gmail.com', 'ELECTNO', 'SPVR'),
(72, 'Vanessa', 'Moya', 'Méndez', '6644477851', 'vanessa.Moya@gmail.com', 'ELECTNO', 'INTN'),
(73, 'Lucas', 'Bravo', 'Mendoza', '6647844712', 'lucas.Bravo@gmail.com', 'ELECTNO', 'OPRD'),
(74, 'Cristina', 'Velázquez', 'Romero', '6644857892', 'cristina.Velázquez@gmail.com', 'ELECTNO', 'OPRC'),
(75, 'Joaquín', 'Mora', 'Santiago', '6637844422', 'joaquín.Mora@gmail.com', 'ELECTNO', 'INGN'),
(76, 'Selene', 'Olivares', 'Coronado', '6645814712', 'selene.Olivares@gmail.com', 'ELECTNO', 'OPRD'),
(77, 'Hugo', 'Salinas', 'Trujillo', '6631472541', 'hugo.Salinas@gmail.com', 'CMTNOR', 'OPRC'),
(78, 'Jimena', 'Cordero', 'Figueroa', '6337898591', 'jimena.Cordero@gmail.com', 'CMTNOR', 'INGN'),
(79, 'Bruno', 'Núñez', 'Padilla', '6647889881', 'bruno.Núñez@gmail.com', 'MUEBLES', 'SCRT'),
(80, 'Regina', 'Aguirre', 'Quiroz', '6641756891', 'regina.Aguirre@gmail.com', 'MUEBLES', 'GRNT'),
(81, 'Esteban', 'Peralta', 'Barrera', '6637758473', 'esteban.Peralta@gmail.com', 'MUEBLES', 'ASTN'),
(82, 'Almudena', 'Santiago', 'Espinoza', '6647899998', 'almudena.Santiago@gmail.com', 'MUEBLES', 'SPVR'),
(83, 'César', 'Coronado', 'Meza', '6648855412', 'césar.Coronado@gmail.com', 'MUEBLES', 'INTN'),
(84, 'Gloria', 'Trujillo', 'Gutiérrez', '6648527784', 'gloria.Trujillo@gmail.com', 'MUEBLES', 'OPRD'),
(85, 'Alexis', 'Figueroa', 'Maldonado', '6637482142', 'alexis.Figueroa@gmail.com', 'MUEBLES', 'OPRC'),
(86, 'Sabrina', 'Padilla', 'Naranjo', '6645447481', 'sabrina.Padilla@gmail.com', 'MUEBLES', 'INGN'),
(87, 'Guillermo', 'Quiroz', 'Ochoa', '6647719712', 'guillermo.Quiroz@gmail.com', 'MUEBLES', 'SCRT'),
(88, 'Aitana', 'Barrera', 'Olmedo', '6641476555', 'aitana.Barrera@gmail.com', 'DULMEX', 'GRNT'),
(89, 'Lorenzo', 'Espinoza', 'Acosta', '6645749855', 'lorenzo.Espinoza@gmail.com', 'DULMEX', 'ASTN'),
(90, 'Melisa', 'Meza', 'Sandoval', '6648578974', 'melisa.Meza@gmail.com', 'DULMEX', 'SPVR'),
(91, 'Álvaro', 'Gutiérrez', 'Peña', '6647854101', 'álvaro.Gutiérrez@gmail.com', 'DULMEX', 'INTN'),
(92, 'Rosario', 'Maldonado', 'Escobar', '6640078802', 'rosario.Maldonado@gmail.com', 'DULMEX', 'OPRD'),
(93, 'Enrique', 'Naranjo', 'Carrillo', '6641102145', 'enrique.Naranjo@gmail.com', 'DULMEX', 'OPRC'),
(94, 'Daniela', 'Ochoa', 'Márquez', '6637845855', 'daniela.Ochoa@gmail.com', 'DULMEX', 'INGN'),
(95, 'Samuel', 'Olmedo', 'Valdez', '6645746118', 'samuel.Olmedo@gmail.com', 'DULMEX', 'OPRD'),
(96, 'Sonia', 'Escamilla', 'Vargas', '6641148807', 'sonia.Escamilla@gmail.com', 'DULMEX', 'OPRC'),
(97, 'Marcelo', 'Domínguez', 'Molina', '6649892555', 'marcelo.Domínguez@gmail.com', 'DULMEX', 'OPRD'),
(98, 'América', 'Marín', 'Orozco', '6644789899', 'america.Marín@gmail.com', 'DULMEX', 'OPRC'),
(99, 'Román', 'Velasco', 'Ponce', '6642221121', 'roman.Velasco@gmail.com', 'CMTNOR', 'INGN'),
(100, 'Juan', 'Morales', 'Serrano', '6642217789', 'juan.Morales@gmail.com', 'CMTNOR', 'SCRT'),
(101, 'Marcos', 'Morales', 'Miranda', '6647891110', 'marcos.Morales@gmail.com', 'CMTNOR', 'GRNT'),
(102, 'Daniel', 'Tejada', 'Rivas', '6630024101', 'daniel.Tejada@gmail.com', 'CMTNOR', 'ASTN'),
(103, 'Pedro', 'Juárez', 'Cárdenas', '6631875154', 'pedro.Juárez@gmail.com', 'CMTNOR', 'SPVR'),
(104, 'José', 'Fernández', 'Bermúdez', '6637100999', 'jose.Fernández@gmail.com', 'CMTNOR', 'INTN'),
(105, 'Armando', 'Rincón', 'Guerrero', '6643124411', 'armando.Rincón@gmail.com', 'CMTNOR', 'OPRD'),
(106, 'Rosendo', 'Galindo', 'Cervantes', '6634658503', 'rosendo.Galindo@gmail.com', 'CMTNOR', 'OPRC'),
(107, 'Ismael', 'Rivas', 'Martínez', '6647888654', 'ismael.Rivas@gmail.com', 'CMTNOR', 'INGN'),
(108, 'Fernando', 'Carmona', 'Torres', '6645498745', 'fernando.Carmona@gmail.com', 'CMTNOR', 'SCRT'),
(109, 'Gabriela', 'Salinas', 'Castillo', '6647256631', 'gabriela.Salinas@gmail.com', 'CMTNOR', 'GRNT'),
(110, 'Octavio', 'Mendoza', 'Vidal', '6648642515', 'octavio.Mendoza@gmail.com', 'CMTNOR', 'ASTN'),
(111, 'Silvia', 'Hernández', 'Fuentes', '6649562243', 'silvia.Hernández@gmail.com', 'CMTNOR', 'SPVR'),
(112, 'María', 'Núñez', 'Delgado', '6645554123', 'maría.Núñez@gmail.com', 'CMTNOR', 'INTN'),
(113, 'Diego', 'Flores', 'Hernández', '6648845472', 'diego.Flores@gmail.com', 'CMTNOR', 'OPRD'),
(114, 'Sergio', 'Duarte', 'Pérez', '6648884413', 'sergio.Duarte@gmail.com', 'CMTNOR', 'OPRC'),
(115, 'Susana', 'Pérez', 'Armenta', '6647564418', 'susana.Pérez@gmail.com', 'CMTNOR', 'INGN'),
(156,'Emiliano','Reyes','Cortes','6645711142','emiliano.Reyes@gmail.com','TEXTIL','OPRD'),
(157,'Maximiliano','Palacios','Ramirez','6641749861','maxim.Palacios@gmail.com','TEXTIL','OPRC'),
(158,'Rodrigo','Bautista', 'Bautista','6645174781','rodrigo.Bautista@gmail.com','TEXTIL','INGN'),
(159,'Alfredo','Zamora','Bautista','6649871475','alfredo.Zamora@gmail.com','TEXTIL','SCRT'),
(160,'Lorenzo','Castañeda','Bautista','6645212132','lorenzo.Castañeda@gmail.com','TEXTIL','GRNT'),
(161,'Ismael','Luna','Escobar','6641544783','ismael.Luna@gmail.com','TEXTIL','ASTN'),
(162,'Jonás','Villanueva','Carrillo','6648774718','jonás.Villanueva@gmail.com','TEXTIL','SPVR'),
(163,'Federico','Montes','Márquez','6641235471','federico.Montes@gmail.com','TEXTIL','INTN'),
(164,'Sebastián','Lara','Valdez','6641758444','sebastián.Lara@gmail.com','TEXTIL','OPRD'),
(165,'Ignacio','Montero','Vargas','6641124423','ignacio.Montero@gmail.com','PLASTI','OPRC'),
(166,'Tomás','Cabrera','Molina','6644174111','tomás.molina@gmail.com','PLASTI','OPRD'),
(167,'Joshua','Benitez','Carrillo','6638588882','joshua.Benitez@gmail.com','PLASTI','OPRC'),
(168,'Daniel','Macías','Márquez','6638579993','daniel.Macías@gmail.com','PLASTI','INGN'),
(169,'Osmar','Zepeda','Valdez','6648481114','osmar.Zepeda@gmail.com','PLASTI','SCRT'),
(170,'Emer','Flores','Vargas','6648997888','emer.Flores@gmail.com','PLASTI','GRNT'),
(171,'Elver','Brito','Benitez','6634781243','elver.Brito@gmail.com','PLASTI','ASTN'),
(172,'Gamaliel','Gomez','Macías','6631484472','gamaliel.Gomez@gmail.com','PLASTI','SPVR'),
(173,'Sebastián','Gutierrez','Zepeda','6641172223','sebastián.Gutierrez@gmail.com','PLASTI','INTN'),
(174,'Armando','Guerrero','Flores','6647891249','armando.Guerrero@gmail.com','PLASTI','OPRD'),
(175,'Rosaura','Lagunes','Brito','6634177771','rosaura.Lagunes@gmail.com','PLASTI','OPRC'),
(176,'Pedro','Martinez','Gomez','6648589999','pedro.Martinez@gmail.com','ELECTNO','INGN'),
(177,'Penelope','Colorado','Gutierrez','6647778571','penelope.Colorado@gmail.com','ELECTNO','SCRT'),
(178,'Marcos','Cortes','Guerrero','6647877878','marcos.Cortes@gmail.com','ELECTNO','GRNT'),
(179,'Genaro','Ramirez','Lagunes','6637458888','genaro.Ramirez@gmail.com','ELECTNO','ASTN'),
(180,'Ron','Zepeda','Martinez','6637414111','ron.Zepeda@gmail.com','ELECTNO','SPVR'),
(181,'Emely','Guerrero','Colorado','6649889988','emely.Guerrero@gmail.com','ELECTNO','INTN'),
(182,'Emilio','Lagunes','Montes','6634771257','emilio.Lagunes@gmail.com','ELECTNO','OPRD'),
(183,'Enzo','Martinez','Lara','6337485551','enzo.Martinez@gmail.com','ELECTNO','OPRC'),
(184,'Roman','Colorado','Montero','6641444789','roman.Colorado@gmail.com','ELECTNO','INGN'),
(185,'Gonzalo','Cortes','Cabrera','6648777444','gonzalo.Cortes@gmail.com','ELECTNO','OPRD'),
(186,'Tomás','Ramirez','Valencia','6648887141','tomás.Ramirez@gmail.com','ELECTNO','OPRC'),
(187,'Óscar','García','Campos','6643211123','óscar.García@gmail.com','MUEBLES','INGN'),
(188,'Mario','Rodríguez','Carrasco','6641245553','mario.Rodríguez@gmail.com','MUEBLES','SCRT'),
(189,'Santiago','Martínez','Suárez','6647844586','santiago.Martínez@gmail.com','MUEBLES','GRNT'),
(190,'Hernán','López','Moya','6639741112','hernán.López@gmail.com','MUEBLES','ASTN'),
(191,'Nicolás','González','Bravo','634784116','nicolás.González@gmail.com','MUEBLES','SPVR'),
(192,'Álvaro','Pérez','Velázquez','6648899917','álvaro.Pérez@gmail.com','MUEBLES','INTN'),
(193,'Genesis','Sánchez','Valdez','6645775711','genesis.Sánchez@gmail.com','MUEBLES','OPRD'),
(194,'Elena','Ramírez','Vargas','6648811112','elena.Ramírez@gmail.com','MUEBLES','OPRC'),
(195,'Gabriela','Torres','Molina','6634871477','gabriela.Torres@gmail.com','MUEBLES','INGN'),
(196,'Paula','Flores','Carrillo','6647484422','paula.Flores@gmail.com','MUEBLES','SCRT'),
(197,'Fernanda','Rivera','Márquez','6639997777','fernanda.Rivera@gmail.com','MUEBLES','GRNT'),
(198,'Carolina','Gómez','Valdez','6642141415','carolina.Gómez@gmail.com','CMTNOR','ASTN'),
(199,'Patricio','Gutiérrez','Vargas','6643164749','patricio.Gutiérrez@gmail.com','CMTNOR','SPVR'),
(200,'Verónica','Zepeda','Bautista','6337811547','verónica.Zepeda@gmail.com','DULMEX','INTN')

INSERT INTO employee(firstName, middleName, lastName, tel, email, factory, jobposition)
VALUES
('Miguel','Cerezo','Barrios','6647874758','miguel.Cerezo@gmail.com','CMTNOR','OPRD'),
('Luis','Carrillo','Reyes','6630022451','luis.Carrillo@gmail.com','CMTNOR','OPRC'),
('Carlos','Mendez','Palacios','6633332032','carlos.Mendez@gmail.com','CMTNOR','INGN'),
('Alejandro','Mendoza','Bautista','6645544041','alejandro.Mendoza@gmail.com','CMTNOR','SCRT'),
('Francisco','Romero','Zamora','6634044400','francisco.Romero@gmail.com','CMTNOR','GRNT'),
('Javier','Santiago','Castañeda','6634144546','javier.Santiago@gmail.com','FERROS','ASTN'),
('Antonio','Coronado','Luna','6634755222','antonio.Coronado@gmail.com','FERROS','SPVR'),
('Roberto','Trujillo','Villanueva','6644044477','roberto.Trujillo@gmail.com','FERROS','INTN'),
('Diego','Figueroa','Montes','6648884710','diego.Figueroa@gmail.com','FERROS','OPRD'),
('Fernando','Padilla','Lara','6647857412','fernando.Padilla@gmail.com','FERROS','OPRC'),
('Eduardo','Quiroz','Montero','6644774555','eduardo.Quiroz@gmail.com','FERROS','INGN'),
('Andrés','Barrera','Cabrera','6648879862','andrés.Barrera@gmail.com','FERROS','OPRD'),
('Manuel','Espinoza','Benitez','6647856555','manuel.Espinoza@gmail.com','FERROS','OPRC'),
('Rafael','Meza','Macías','6641488814','rafael.Meza@gmail.com','FERROS','OPRD'),
('Ricardo','Gutiérrez','Zepeda','6647854711','ricardo.Gutiérrez@gmail.com','FERROS','OPRC'),
('Sergio','Maldonado','Flores','6641744555','sergio.Maldonado@gmail.com','FERROS','INGN'),
('Martín','Naranjo','Brito','6642144556','martín.Naranjo@gmail.com','ACEROS','SCRT'),
('Pablo','Ochoa','Gomez','6647879874','pablo.Ochoa@gmail.com','ACEROS','GRNT'),
('David', 'Olmedo', 'Gutierrez', '6646515858', 'david.Olmedo@gmail.com', 'ACEROS', 'ASTN'),
('Adrián', 'Acosta', 'Soto', '6649875455', 'adrián.Acosta@gmail.com', 'ACEROS', 'SPVR'),
('Gabriel', 'Sandoval', 'Aguirre', '6645851478', 'gabriel.Sandoval@gmail.com', 'ACEROS', 'INTN'),
('Hugo', 'Peña', 'Trujillo', '6647894712', 'hugo.Peña@gmail.com', 'ACEROS', 'OPRD'),
('Cristian', 'Escobar', 'Meza', '6644575888', 'cristian.Escobar@gmail.com', 'ACEROS', 'OPRC'),
('Esteban', 'Carrillo', 'Gutiérrez', '6647845456', 'esteban.Carrillo@gmail.com', 'ACEROS', 'INGN'),
('Joaquín', 'Márquez', 'Maldonado', '6648888544', 'joaquín.Márquez@gmail.com', 'ACEROS', 'SCRT'),
('Ramiro', 'Valdez', 'Naranjo', '6647848599', 'ramiro.Valdez@gmail.com', 'ACEROS', 'GRNT'),
('Vicente', 'Vargas', 'Ochoa', '6647778651', 'vicente.Vargas@gmail.com', 'ACEROS', 'ASTN'),
('Julián', 'Molina', 'Olmedo', '6648748555', 'julián.Molina@gmail.com', 'ALUMEX', 'SPVR'),
('Álvaro', 'Orozco', 'Acosta', '6648455749', 'álvaro.Orozco@gmail.com', 'ALUMEX', 'INTN'),
('Jesús', 'Ponce', 'Sandoval', '6648887454', 'jesús.Ponce@gmail.com', 'ALUMEX', 'OPRD'),
('Raúl', 'Serrano', 'Peña', '6647845888', 'raúl.Serrano@gmail.com', 'ALUMEX', 'OPRC'),
('Gonzalo', 'Miranda', 'Escobar', '6648749144', 'gonzalo.Miranda@gmail.com', 'ALUMEX', 'INGN'),
('Tomás', 'Rivas', 'Carrillo', '6649877411', 'tomás.Rivas@gmail.com', 'ALUMEX', 'OPRD'),
('Óscar', 'Cárdenas', 'Márquez', '6639877777', 'óscar.Cárdenas@gmail.com', 'ALUMEX', 'OPRC'),
('Mario', 'Esquivel', 'Valdez', '6638749981', 'mario.Esquivel@gmail.com', 'ALUMEX', 'INGN'),
('Santiago', 'Barrios', 'Vargas', '6637749874', 'santiago.Barrios@gmail.com', 'ALUMEX', 'SCRT'),
('Hernán', 'Reyes', 'Molina', '6648974555', 'hernán.Reyes@gmail.com', 'ALUMEX', 'GRNT'),
('Nicolás', 'Benitez', 'Carrillo', '6648748511', 'nicolás.Benitez@gmail.com', 'ALUMEX', 'ASTN'),
('Álvaro', 'Carrillo', 'Márquez', '6648119888', 'álvaro.Carrillo@gmail.com', 'MADERB', 'SPVR'),
('Iván', 'Márquez', 'Valdez', '6641119814', 'iván.Márquez@gmail.com', 'MADERB', 'INTN'),
('Simón', 'Valdez', 'Vargas', '6641472223', 'simón.Valdez@gmail.com', 'MADERB', 'OPRD'),
('Felipe', 'Vargas', 'Molina', '6648472232', 'felipe.Vargas@gmail.com', 'MADERB', 'OPRC')

/*TABLA DE AYUDA PARA ALMACENAR LAS CONTRASEÑAS*/
CREATE table users(
    num INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    hash_password VARCHAR(255) NOT NULL,
    rol ENUM('employee', 'dinningRoomManager', 'generalManager')
)

/*INSERT A UN USUARIO*/
INSERT INTO users(email, hash_password, rol)
VALUES ('miguel.Cerezo@gmail.com', 'contRAdePRUEBA0205', 'employee');

INSERT INTO users(email, hash_password, rol)
VALUES ('armando.Rincón@gmail.com', 'contRAdePRUEBA111', 'employee');


select * from users

/*INSERT en la tabla de jobposition*/
LOAD DATA LOCAL INFILE 'C:/Users/User/Desktop/UTT/BaseDeDatos/ImporteTablas/tablaJobposition.csv'
INTO TABLE jobposition
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"' 
LINES TERMINATED BY '\n' 
IGNORE 1 LINES 
(code,description);

select * from employee

insert into diningRoom(name, ubication, factory)
VALUES
('Comiditas', 'area de comida', 'FERROS')

insert into diningroommanager(firstName, middleName, lastName, tel, groodininm)
values
('Julio', 'Camejo', 'Ortega', 6641870158, 1 )

insert into diningroommanager(firstName, middleName, lastName, tel, diningroom)
VALUES
('Juan', 'Martinez', 'Garcia', 6641871282, 2 )

insert into supplier(code, name, tel, email)
values('carne', 'Carniceria los pepes', 6641644789, 'carniceria.lospepes@gmail.com')

insert into purchaseOrder(amountPayment, datePurs, diningroommanager, supplier)
values(500, '2024-10-02', 1, 'carne' )

insert into purchaseorder(amountPayment, datePurs, diningroommanager, supplier)
values(420, '2024-10-02', 3, 'carne')


ALTER TABLE purchaseOrder
ADD CONSTRAINT diningroommanager
FOREIGN KEY (diningroommanager) REFERENCES diningroommanager(num);

/* --- SELECTS GENERALES*/

/*consulta de prueba*/

select d.firstName, p.amountPayment
from purchaseorder as p
INNER join diningroommanager as d on p.diningroommanager = d.num
where d.num = 1

INSERT INTO diningroommanager()
