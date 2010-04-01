create table User(
	userId int not null auto_increment primary key,
	username varchar( 50 ) not null unique,
	password varchar( 25 ) not null,
	userLevel int not null default 0,
	lastLogin datetime
);

create table Medication(
	medicationId int not null auto_increment primary key,
	genericName varchar(250),
	tradeName varchar(250),
	commonDose int,
	minimumSafeDose int,
	maximumSafeDose int,
	route int
);

create table Patient (
	patientId int not null auto_increment primary key,
	firstName varchar(50),
	lastName varchar(50),
	accountNumber int,
	admissionDate datetime,
	roomNumber int,
	unit int,
	unitName varchar(15)
);

create table MedicationList (
	medicationListId int not null auto_increment primary key,
	patientId int not null,
	medicationId int not null,
	active int not null default 0,
	userId int,
	confirmed int,
	confirmedOn datetime,
	constraint constraint_MedicationList_Patient_PatientId_fk foreign key( patientId ) references Patient(patientId)
		on delete cascade on update cascade,
	constraint constraint_MedicationList_Medication_MedicationId_fk foreign key( medicationId ) references Medication(medicationId)
		on delete cascade on update cascade,
	constraint constraint_MedicationList_User_UserId_fk foreign key( userId ) references User(userId)
		on delete cascade on update cascade
);

insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 10, 1, 1, 1 );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 20, 1, 1, 1 );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 30, 1, 1, 1 );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 40, 1, 1, 1 );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 50, 0, 1, 1 );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 60, 1, 0, null );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 70, 1, 0, null );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 80, 0, 0, null );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 90, 1, 0, null );
insert into MedicationList( patientId, medicationId, active, confirmed, userId ) values( 1, 100, 1, 0, null );

create table Orders (
	ordersId int not null auto_increment primary key,
	ordersType int,
	ordersDescription varchar(255),
	patientId int not null,
	executed datetime,
	constraint constraint_Orders_Patient_PatientId_fk foreign key( patientId ) references 
	Patient( PatientId ) on delete cascade on update cascade	
);

create table LabResult (
	labResultId int not null auto_increment primary key,
	labResultType int,
	labResultDescription varchar(255),
	patientId int not null,
	executed datetime,
	constraint constraint_Labresult_Patient_PatientId_fk foreign key( patientId ) references 
	Patient( PatientId ) on delete cascade on update cascade	
);

create table DiagnosticStudy (
	diagnosticStudyId int not null auto_increment primary key,
	diagnosticStudyType int,
	diagnosticStudyDescription varchar(255),
	patientId int not null,
	executed datetime,
	constraint constraint_DiagnosticStudy_Patient_PatientId_fk foreign key( patientId ) references 
	Patient( PatientId ) on delete cascade on update cascade	
);

create table VitalSigns (
	vitalSignsId int not null auto_increment primary key,
	systolicBloodPressure int,
	diastolicBloodPressure int,
	pulseRate int,
	respiratoryRate int,
	temperature float,
	oxygenSaturation int,
	painLevel int,
	patientId int,
	constraint constraint_VitalSigns_Patient_PatientId_fk foreign key( patientId ) references 
	Patient( PatientId ) on delete cascade on update cascade
);

create table AdministeredMedications (
	administrationId int not null auto_increment primary key,
	administeredBy int not null,
	administeredTo int not null,
	administeredMedication int not null,
	administrationTime datetime,
	constraint constrain_AdministeredMedications_administeredBy_UserId foreign key( administeredBy ) references
	User( userId ) on delete cascade on update cascade,
	constraint constrain_administeredMedication_administeredTo_Patient foreign key( administeredTo ) references
	Patient( patientId ) on delete cascade on update cascade,
	constraint constrain_administeredMedication_medicationId foreign key( administeredMedication ) references
	MedicationList( medicationListId ) on delete cascade on update cascade
);

insert into Patient(firstName,lastName,roomNumber,unitName) values('Nathan','Bardgett',1,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('MiMi','Chan',2,'ED');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Erimi','Kendrick',3,'ED');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Nathan','Bardgett',4,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Glenda','Bardgett',5,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Charles','Bardgett',6,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Michelle','Bardgett',7,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Nannette','Fernandez',8,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Neal','Fernandez',9,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Ellie','Fernandez',8,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Claire','Fernandez',9,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Hobbes','Bardgett',4,'MSU');
insert into Patient(firstName,lastName,roomNumber,unitName) values('Mack','Bardgett',5,'MSU'); 