create table Category(
    Id integer PRIMARY KEY auto_increment,
    NameCategory varchar(100) unique NOT NULL,
    SymbolCode varchar(20) unique NOT NULL


);

create table User(
    Id integer primary key auto_increment,
    DateRegistration timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Email varchar(200) unique NOT NULL,
    NameUser varchar(75) NOT NULL,
    PasswordUser varchar(300) NOT NULL,
    ContactInfo varchar(100) NOT NULL

);

create table Lot(
    Id integer primary key auto_increment,
    DateCreate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    NameLot varchar(75) NOT NULL,
    Detail varchar(500) NOT NULL ,
    Image varchar(200) NOT NULL ,
    StartPrise integer NOT NULL,
    DateEnd date NOT NULL,
    StepBet integer NOT NULL,

    AuthorId integer NOT NULL,
    WinerId integer,
    CategoryId integer NOT NULL,

    FOREIGN KEY (AuthorId) REFERENCES User(Id) ON DELETE CASCADE,
    FOREIGN KEY (WinerId) REFERENCES User(Id) ON DELETE CASCADE,
    FOREIGN KEY (CategoryId) REFERENCES Category(Id) ON DELETE CASCADE



);

create table Bet(
    Id integer primary key auto_increment,
    DateCreate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Sum integer NOT NULL,

    UserId integer NOT NULL,
    LotId integer NOT NULL,

    FOREIGN KEY (UserId) REFERENCES  User(Id) ON DELETE CASCADE,
    FOREIGN KEY (LotId) REFERENCES  Lot(id) ON DELETE CASCADE
);


ALTER TABLE Lot
    ADD FULLTEXT(NameLot,Detail);