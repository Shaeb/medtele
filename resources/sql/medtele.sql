create table User(
	userId int not null auto_increment primary key,
	username varchar( 50 ) not null unique,
	password varchar( 25 ) not null,
	userLevel int not null default 0,
	isLoggedIn int not null default 0, 
	lastLogin datetime,
	created datetime,
	ipaddress char(16),
	sessionId varchar( 50 ),
	constraint userlevel_foreignkey foreign key( userLevel ) references UserLevel( userLevelId ) on delete cascade on update cascade
);

create table UserLevel (
	userLevelId int not null auto_increment primary key,
	description varchar(25)
);
, in ipaddress, in sessionId
delimiter //
create procedure sp_login ( inout username varchar, in password varchar, out userLevel int ) 
begin
	select u.username into username, u.userLevel from User u where u.username = username and u.password = password;
end //
delimiter ;

delimiter //
create procedure sp_users()
begin
	select username from User;
end //
delimiter ;