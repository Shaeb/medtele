create table EmployeeTitles (
	employeeTitleId int not null auto_increment primary key,
	description varchar(50)
) engine=INNODB;

create table UserLevels (
	userLevelId int not null auto_increment primary key,
	description varchar(25)
) engine=INNODB;

create table Users(
	userId int not null auto_increment primary key,
	username varchar( 50 ) not null unique,
	password varchar( 25 ) not null,
	userLevel int not null default 0,
	isLoggedIn int not null default 0, 
	lastLogin datetime,
	created datetime,
	ipaddress char(16),
	sessionId varchar( 50 ),
	constraint users_userlevel_foreignkey foreign key( userLevel ) references UserLevels( userLevelId ) on delete cascade on update cascade
) engine=INNODB;

create table Employees (
	employeeId int not null auto_increment primary key,
	userId int not null,
	employeeTitleId int not null,
	constraint Employees_userId_foreignkey foreign key( userId ) references Users(userId) on delete cascade on update cascade,
	constraint Employees_employeeTitleId_foreignkey foreign key( employeeTitleId ) references EmployeeTitles(employeeTitleId) on delete cascade on update cascade
) engine=INNODB;

create table AssignedEmployeeTitles (
	id int not null auto_increment primary key,
	employeeId int not null,
	employeeTitleId int not null,
	dateAssigned datetime,
	assignedBy int not null,
	constraint AssignedEmployeeTitles_employeeId_foreignkey foreign key( employeeId ) references Employees(employeeId) on delete cascade on update cascade,
	constraint AssignedEmployeeTitles_employeeTitleId_foreignkey foreign key( employeeTitleId ) references EmployeeTitles(employeeTitleId) on delete cascade on update cascade,
	constraint AssignedEmployeeTitles_assignedBy_foreignkey foreign key( assignedBy ) references Employees(employeeId) on delete cascade on update cascade
) engine=INNODB;

create table Departments (
	departmentId int not null auto_increment primary key,
	name varchar(50) not null,
	manager int not null,
	created datetime,
	constraint Departments_manager_foreignkey foreign key( manager ) references Employees(employeeId) on delete cascade on update cascade
) engine=INNODB;

create table EmployeeDepartmentAssignments (
	id int not null auto_increment primary key,
	employeeId int not null,
	departmentId int not null,
	dateAssigned datetime,
	assignedBy int not null,
	constraint EmployeeDepartmentAssignments_employeeId_foreignkey foreign key( employeeId ) references Employees(employeeId) on delete cascade on update cascade,
	constraint EmployeeDepartmentAssignments_departmentId_foreignkey foreign key( departmentId ) references Departments(departmentId) on delete cascade on update cascade,
	constraint EmployeeDepartmentAssignments_assignedBy_foreignkey foreign key( assignedBy ) references Employees(employeeId) on delete cascade on update cascade
) engine=INNODB;

#all times are recorded as military hours: 8am = 800, 11pm = 23
# for this table, the logic for overnight shifts is as such
# if endingTime < beginningTime the shift is overnight: beginningTime = 1845 endingTime = 645
create table ShiftTypes(
	shiftTypeId int not null auto_increment primary key,
	description varchar(50),
	beginningTime int not null,
	endingTime int not null
) engine=INNODB;

# active means this is the current (or pending) shift.  this will help us get an historical perspective if shifts are being frequently changed
create table Shifts(
	shiftId int not null auto_increment primary key,
	employeeId int not null,
	shiftTypeId int not null,
	scheduledDate datetime,
	scheduledOn datetime,
	scheduledBy int not null,
	isApproved int not null default 0,
	approvedBy int not null,
	approvedDate datetime,
	active int not null default 1,
	constraint Shifts_employeeId_foreignkey foreign key( employeeId ) references Employees(employeeId) on delete cascade on update cascade,
	constraint Shifts_shiftTypeId_foreignkey foreign key( shiftTypeId ) references ShiftTypes(shiftTypeId) on delete cascade on update cascade,
	constraint Shifts_scheduledBy_foreignkey foreign key( scheduledBy ) references Employees(employeeId) on delete cascade on update cascade,
	constraint Shifts_approvedBy_foreignkey foreign key( approvedBy ) references Employees(employeeId) on delete cascade on update cascade
) engine=INNODB;

create table ScaffoldingTables(
	id int not null auto_increment primary key,
	tableName varchar(255) not null
) engine=INNODB;

create table ScaffoldingFields(
	id int not null auto_increment primary key,
	fieldName varchar(255) not null,
	tableId int not null,
	constraint ScaffoldingFields_tableId_foreignkey foreign key( tableId ) references ScaffoldingTables(id) on delete cascade on update cascade	
) engine=INNODB;

insert into ScaffoldingTables(tableName) values('ShiftTypes');
insert into ScaffoldingTables(tableName) values('EmployeeTitles');
insert into ScaffoldingTables(tableName) values('UserLevels');