-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 16 2026 г., 17:27
-- Версия сервера: 9.1.0
-- Версия PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `fashion_archive`
--

-- --------------------------------------------------------

--
-- Структура таблицы `garments`
--

DROP TABLE IF EXISTS `garments`;
CREATE TABLE IF NOT EXISTS `garments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `author` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `material` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cut` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `season` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `collection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'draft',
  `image_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `model_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `cover_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `garments`
--

INSERT INTO `garments` (`id`, `name`, `description`, `author`, `department`, `material`, `color`, `cut`, `season`, `collection`, `image_path`, `model_path`, `created_at`, `cover_path`) VALUES
(1, 'Черно-серое блестящее платье', 'Осенне-зимний ансамбль с укороченной серебристой накидкой-болеро с пышными рукавами, отделанной черным кружевом и бисером, которая надевается поверх длинного прямого черного платья с вертикальной кружевной планкой по центру.', '', 'Кафедра дизайна костюма', 'Парча, кружево, шёлк', 'Серебристый, чёрный', 'Приталенный', 'Осень', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af2de11e825d2.94006759.jpg,/fashion-archive/uploads/img_6af2de11e897a7.05514551.jpg,/fashion-archive/uploads/img_6af2de11e8ef91.39051973.jpg,/fashion-archive/uploads/img_6af2de11e94d13.76704081.jpg,/fashion-archive/uploads/img_6af2de11e9b643.53244187.jpg,/fashion-archive/uploads/img_6af2de11ea03e5.35794884.jpg,/fashion-archive/uploads/img_6af2de11ea6696.16648097.jpg,/fashion-archive/uploads/img_6af2de11eae659.66908347.jpg,/fashion-archive/uploads/img_6af2de11eb3249.89533905.jpg,/fashion-archive/uploads/img_6af2de11eb8db6.31074442.jpg', '/fashion-archive/assets/models/model_6af35de7df05f4.02338373.obj', '2026-11-06 10:25:28', '/fashion-archive/uploads/cover_6af2de11e77077.39489266.jpg'),
(2, 'Черно-красное платье', 'Зимний комплект из ярко-красного укороченного жакета с застежкой на фигурные петли и широкими рукавами с черными меховыми манжетами, который носится с прямой черной юбкой, украшенной кружевной кокеткой и меховой отделкой по подолу, что создает стилизацию под традиционный кафтан.', '', 'Кафедра дизайна костюма', 'Плотное сукно, кружево, искусственный мех', 'Алый, чёрный', 'Прямой', 'Зима', 'Русская-народная 2026', '/fashion-archive/uploads/img_6aed94c41bd312.09338747.jpg,/fashion-archive/uploads/img_6aed94c41c6d44.75473822.jpg,/fashion-archive/uploads/img_6aed94c41cff45.85085417.jpg,/fashion-archive/uploads/img_6aed94c41de333.28889205.jpg,/fashion-archive/uploads/img_6aed94c41e6871.20286052.jpg,/fashion-archive/uploads/img_6aed94c41ee171.45483756.jpg,/fashion-archive/uploads/img_6aed94c41f4f85.31093764.jpg', '/fashion-archive/assets/models/model_6aed94c41fb8f6.91466881.obj', '2026-11-06 11:37:26', '/fashion-archive/uploads/cover_6aed98cc78ebe0.21388949.jpg'),
(4, 'Бордо', 'Сложный многослойный демисезонный образ с бордовым кружевным топом и объемными оливковыми шортами, дополненными асимметричной накидкой с этническим орнаментом и бахромой, а также воротником-стойкой с вышивкой.', '', 'Кафедра дизайна костюма', 'Плотный трикотаж, гипюр/кружево, фактурная ткань, вышивка и бахрома', 'Бордовый (марсала), оливково-зеленый, с вкраплениями золота.', 'Многослойный', 'Осень', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af2e2661b5f35.22022964.jpg,/fashion-archive/uploads/img_6af2e2661bf728.85795846.jpg,/fashion-archive/uploads/img_6af2e2661c5010.50695537.jpg,/fashion-archive/uploads/img_6af2e2661ca0f8.84856787.jpg,/fashion-archive/uploads/img_6af2e2661d0285.22848260.jpg,/fashion-archive/uploads/img_6af2e2661d5795.76854824.jpg', '/fashion-archive/assets/models/model_6af2d416483a11.73631199.glb', '2026-11-10 11:21:42', '/fashion-archive/uploads/cover_6af2e2661a6376.34004689.jpg'),
(5, 'Желто-синее длинное платье', 'Длинное платье в пол для осени или весны, выполненное из плотной узорчатой ткани, напоминающей парчу или гобелен, с сине-золотистым орнаментом; крой включает высокий кружевной воротник-стойку, длинные рукава, широкий золотой пояс и отрезную юбку со сборками, что создает исторический дворцовый силуэт.', '', 'Кафедра дизайна костюма', 'Парча, кружево, тесьма', 'Голубой, жёлтый, с вкраплениями золота', 'Приталенный', 'Осень', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af2e230848ae9.80110736.jpg,/fashion-archive/uploads/img_6af2e230852210.21122437.jpg,/fashion-archive/uploads/img_6af2e23085cd36.66664235.jpg,/fashion-archive/uploads/img_6af2e230870d23.11337575.jpg,/fashion-archive/uploads/img_6af2e23087e414.87409175.jpg,/fashion-archive/uploads/img_6af2e230886ea2.13359484.jpg,/fashion-archive/uploads/img_6af2e23088d117.01343682.jpg,/fashion-archive/uploads/img_6af2e230892bf9.37054727.jpg', '/fashion-archive/assets/models/model_6af2d8501cfb47.97339443.obj', '2026-11-10 11:39:44', '/fashion-archive/uploads/cover_6af2e230839701.25067853.jpg'),
(6, 'Бело-красное бахатистое платье', 'Бордовое бархатное платье для сценического образа осенью или зимой, украшенное золотой вышивкой с павлинами, бисером и кружевом; крой с отрезной талией, пышной юбкой в пол, объемным верхом-болеро и кружевным воротником-стойкой, что формирует царственный фольклорный образ.', '', 'Кафедра дизайна костюма', 'Бордовый бархат, гипюр/кружево, бисер', 'Бордовый (марсала), белый, золотой', 'Отрезная талия, пышная юбка в пол', 'Осень', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af2de50760342.33419624.jpg,/fashion-archive/uploads/img_6af2de50768966.43367269.jpg,/fashion-archive/uploads/img_6af2de50770470.43697897.jpg,/fashion-archive/uploads/img_6af2de507756c8.33773475.jpg,/fashion-archive/uploads/img_6af2de5077a0e7.24489899.jpg,/fashion-archive/uploads/img_6af2de5077fd02.54341802.jpg,/fashion-archive/uploads/img_6af2de50784df4.11542221.jpg,/fashion-archive/uploads/img_6af2de50789d66.70206119.jpg,/fashion-archive/uploads/img_6af2de5078ec15.16154224.jpg,/fashion-archive/uploads/img_6af2de507933a3.82247096.jpg', '/fashion-archive/assets/models/model_6af2de8109f0d6.50881932.obj', '2026-11-10 12:05:20', '/fashion-archive/uploads/cover_6af2de507542b0.98703316.jpg'),
(7, 'Золотое с рюшами платье', 'Торжественное платье для помещений в осенне-весенний период из золотой парчи с кружевом и атласными лентами; крой с завышенной талией (ампир), пышной юбкой с рюшами и длинными кружевными рукавами, что создает дворцовый исторический стиль.', '', 'Кафедра дизайна костюма', 'Золотая парча, кружево, атласные ленты', 'Золотой, бежевый, кремовый', 'Завышенная талия (ампир), пышная юбка с рюшами', 'Осень', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af2deda2157a3.81030296.jpg,/fashion-archive/uploads/img_6af2deda21b7f1.70994479.jpg,/fashion-archive/uploads/img_6af2deda223e39.95474137.jpg,/fashion-archive/uploads/img_6af2deda229bf8.56566500.jpg,/fashion-archive/uploads/img_6af2deda22ef08.28309577.jpg,/fashion-archive/uploads/img_6af2deda233de3.28337866.jpg,/fashion-archive/uploads/img_6af2deda2386d6.78297503.jpg', '/fashion-archive/assets/models/model_6af2deda23e2d8.78597629.obj', '2026-11-10 12:07:38', '/fashion-archive/uploads/cover_6af2deda20bdf0.45842497.jpg'),
(8, 'Черное пальто с зелеными узорами', 'Чёрный удлиненный кафтан для весны или осени из плотной костюмной ткани с изумрудной вышивкой пайетками и золотой тесьмой; крой прямой, с акцентом на центральную планку и рукава с вертикальной вышивкой, что отсылает к восточным или сказочным мотивам.', '', 'Кафедра дизайна костюма', 'Плотная костюмная ткань, пайетки/аппликация, золотая тесьма', 'Черный, изумрудный, золотой', 'Прямой удлинённый', 'Осень', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af2e2e834c738.92521877.jpg,/fashion-archive/uploads/img_6af2e2e8353fe2.98766184.jpg,/fashion-archive/uploads/img_6af2e2e835b6d5.33194850.jpg,/fashion-archive/uploads/img_6af2e2e8361a31.37438940.jpg,/fashion-archive/uploads/img_6af2e2e83694d3.07885826.jpg,/fashion-archive/uploads/img_6af2e2e836ffc8.34499981.jpg,/fashion-archive/uploads/img_6af2e2e8376e52.47125626.jpg,/fashion-archive/uploads/img_6af2e2e837ddf9.85928452.jpg,/fashion-archive/uploads/img_6af2e2e83885e4.23410459.jpg', '/fashion-archive/assets/models/model_6af35323a49e32.74832111.obj', '2026-11-10 12:24:56', '/fashion-archive/uploads/cover_6af2e2e8344f67.99858429.jpg'),
(9, 'Белое пушистое платье', 'Длинное белое пальто-накидка для демисезона или зимы из плотной пальтовой ткани с начесом, с искусственным мехом и кружевом на груди; силуэт трапециевидный, в пол, с широкими рукавами и воротником-хомутом, что придает образу сказочный вид «Снежной королевы».', '', 'Кафедра дизайна костюма', 'Букле, искусственный мех, кружево', 'Молочно-белый', 'Трапеция, воротник-хомут', 'Зима', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af35ca7ad20a0.87493555.jpg,/fashion-archive/uploads/img_6af35ca7ad9900.67004780.jpg,/fashion-archive/uploads/img_6af35ca7adfd26.51212644.jpg,/fashion-archive/uploads/img_6af35ca7ae6013.19411639.jpg,/fashion-archive/uploads/img_6af35ca7aebd78.63087957.jpg,/fashion-archive/uploads/img_6af35ca7af1912.09556376.jpg,/fashion-archive/uploads/img_6af35ca7af6fa1.74369978.jpg', '/fashion-archive/assets/models/model_6af356011afe81.99695693.glb', '2026-11-10 20:25:21', '/fashion-archive/uploads/cover_6af35e3a93aa67.87632310.jpg'),
(10, 'Черно-белый костюм с цветочками', 'Черный утепленный кафтан для зимы или поздней осени, выполненный из плотного сукна с отделкой из искусственного белого меха на воротнике и манжетах, украшенный золотисто-желтой цветочной вышивкой и черным кружевом по подолу; крой прямой, с застежкой на фигурные петли, что создает образ в стиле русского боярского кафтана.', '', 'Кафедра дизайна костюма', 'Сукно, искусственный мех, кружево', 'Чёрный, белый, золотой', 'Прямой', 'Зима', 'Русская-народная 2026', '/fashion-archive/uploads/img_6af35c80352ce6.76247952.jpg,/fashion-archive/uploads/img_6af35c8035b853.35284431.jpg,/fashion-archive/uploads/img_6af35c803624c1.47440615.jpg,/fashion-archive/uploads/img_6af35c80367b93.69914088.jpg,/fashion-archive/uploads/img_6af35c8036fa80.02757146.jpg,/fashion-archive/uploads/img_6af35c803750f5.29263842.jpg,/fashion-archive/uploads/img_6af35c8037a2e5.55567349.jpg', '/fashion-archive/assets/models/model_6af35861150d60.60801418.glb', '2026-11-10 20:40:44', '/fashion-archive/uploads/cover_6af35e4631ee52.24409447.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'student',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$5Q2jXWq1B/9wB3E5D7jXWeM5YQ3G4hK6N7mP8rS9tU0vW1xY2zA3B', 'teacher'),
(2, 'admin1', '$2y$10$8Iw3ByElCjYWeTb0fNHdYuJ0ZrDIT9SCsUIhtWxy3AoLIju7EEYae', 'teacher'),
(3, 'stud', '$2y$10$LYMSBTkf11TLPuBzqlswrOdqEbx6n/D2ahr5eBoRrwsAu/KdyFkkK', 'student');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
