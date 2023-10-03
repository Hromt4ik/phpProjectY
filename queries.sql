INSERT INTO Category (Id, NameCategory, SymbolCode)
VALUES (1, 'Доски и лыжи', 'boards'),
       (2, 'Крепления', 'attachment'),
       (3, 'Ботинки', 'boots'),
       (4, 'Одежда ', 'clothing'),
       (5, 'Инструменты ', 'tools'),
       (6, 'Разное ', 'other');

INSERT INTO User(Id, Email, NameUser, PasswordUser, ContactInfo)
VALUES (1, 'Emaillo@gmail.com', 'Главный Юзер', 'root', '880005553535'),
       (2, 'gigagan@gmail.com', 'Второй пользователь', 'root', '89226635572');

INSERT INTO Lot(Id, NameLot, Detail, Image, StartPrise, DateEnd, StepBet, AuthorId, CategoryId)
VALUES (1, '2014 Rossignol District Snowboard',
        'very nice',
        'img/lot-1.jpg',
        10999, '2023-09-30', 2, 1, 1),
       (2, 'DC Ply Mens 2016/2017 Snowboard',
        'very nice2',
        'img/lot-2.jpg',
        159999, '2023-09-10', 5, 1, 1),
       (3, 'Крепления Union Contact Pro 2015 года размер L/XL',
        'very nice3',
        'img/lot-3.jpg',
        8000, '2023-09-09', 10, 2, 2),
       (4, 'Ботинки для сноуборда DC Mutiny Charocal',
        'very nice4',
        'img/lot-4.jpg',
        10999, '2023-09-20', 20, 1, 3),
       (5, 'Куртка для сноуборда DC Mutiny Charocal',
        'very nice5',
        'img/lot-5.jpg',
        7500, '2023-09-25', 20, 2, 4),
       (6, 'Маска Oakley Canopy',
        'very nice6',
        'img/lot-6.jpg',
        5400, '2023-09-25', 1, 1, 6);

INSERT INTO Bet(Id, Sum, UserId, LotId)
VALUES (1, 20000, 2, 1),
       (2, 25555, 1, 5),
       (3, 30000, 1, 5);

#1) получить список всех категорий;
SELECT *
FROM `Category`;
# 2) получить cписок лотов, которые еще не истекли отсортированных по дате публикации,
# от новых к старым. Каждый лот должен включать название, стартовую цену, ссылку на изображение,
# название категории и дату окончания торгов
SELECT `NameLot`, `StartPrise`, `Image`, Category.NameCategory, `DateEnd`
FROM `Lot`
         INNER JOIN Category ON Lot.CategoryId = Category.Id
WHERE `DateEnd` > NOW()
ORDER BY `DateCreate` DESC;
# 3) показать информацию о лоте по его ID. Вместо id категории должно
#    выводиться  название категории, к которой принадлежит лот из таблицы категорий;
SELECT `NameLot`,
       `Detail`,
       `DateCreate`,
       `StartPrise`,
       `Image`,
       Category.NameCategory,
       `DateEnd`,
       `StepBet`
FROM `Lot`
         INNER JOIN Category ON Lot.CategoryId = Category.Id
WHERE Lot.Id = 2;

# 4) обновить название лота по его идентификатору;
UPDATE `Lot`
SET NameLot = 'change name lot request 4'
WHERE Id = 2;

# 5) получить список ставок для лота по его идентификатору с сортировкой по дате.
# Список должен содержать дату и время размещения ставки, цену,
# по которой пользователь готов приобрести лот, название лота и имя пользователя,
# сделавшего ставку
SELECT Bet.DateCreate, `Sum`, Lot.NameLot, User.NameUser
FROM `Bet`
         INNER JOIN `User` ON Bet.UserId = User.Id
         INNER JOIN `Lot` ON Bet.LotId = Lot.Id
WHERE Lot.Id = 5
ORDER BY Bet.DateCreate DESC;
