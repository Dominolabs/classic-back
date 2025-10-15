INSERT INTO `tbl_pizzeria` (`pizzeria_id`, `image`, `phones`, `email`, `instagram`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
  (1, '6577ce6f873e62a4185835a540b0d758.jpeg', '+380963427612\r\n+380993457865', 'pizzeria1@example.com', 'https://www.instagram.com/explore/tags/pizzeria/?hl=uk', 1, 1, 1551255609, 1551256988),
  (2, '7f1d88711b582bbd51860c6bbc0086f2.jpeg', '+380964443265\r\n+380994323443\r\n+380683217235', 'pizzeria2@example.com', 'https://www.instagram.com/explore/tags/pizzeria/?hl=uk', 1, 2, 1551256444, 1551256661),
  (3, '93d8c1e986ae6b2a8b9ef837ee09d890.jpg', '+380444328934\r\n+380957432343\r\n+380997643768', 'pizzeria3@example.com', 'https://www.instagram.com/explore/tags/pizzeria/?hl=uk', 1, 3, 1551256652, 1551256652);

INSERT INTO `tbl_pizzeria_description` (`pizzeria_id`, `language_id`, `name`, `address`, `schedule`) VALUES
  (1, 1, 'Піцерія 1', 'м. Луцьк, вул. Львівська, 10', 'Пн-Пт 9:00 - 18:00'),
  (1, 2, 'Pizzeria 1', 'Lutsk, Lvivska str., 10', 'Mon-Fri 09:00 - 18:00'),
  (2, 1, 'Піцерія 2', 'м. Луцьк, вул. Дубнівська, 15', 'Пн-Пт 08:00 - 17:00'),
  (2, 2, 'Pizzeria 2', 'Lutsk, Dubnivska str., 15', 'Mon-Fri 08:00 - 17:00'),
  (3, 1, 'Піцерія 3', 'м. Луцьк, просп. Волі, 32', 'Пн-Сб 12:00 - 21:00'),
  (3, 2, 'Pizzeria 3', 'Lutsk, Voly str., 32', 'Mon-Sat 12:00 - 21:00');
