<?php

return [
    'startMessage'                => "Вы на главной странице бота для отслеживания состояния Вашего счета в валюте.\nЭтот бот запоминает сколько и по какой цене вы купили валюту, и когда у монобанка изменяется курс валют он пересчитает сколько у вас денег по новому курсу и оповестит вас. Что бы вы сами не проверяли постоянно курс и не тратили на это время.",
    'notAuth'                     => "Вы не авторизованы, это частный бот, Вам не доступны его функции в данный момент.\nВозможно когда-то бот будет публичным.",
    'pong'                        => 'Pong',
    'environment'                 => "Окружение: :env",
    'environmentChanged'          => "Окружение изменилось на: :env",
    'commandNotFound'             => 'Команда не найдена',
    'occurredError'               => 'Произошла ошибка, пожалуйста повторите еще раз',
    'internalError'               => 'Произошла внутренняя ошибка, пожалуйста повторите запрос через несколько минут',
    'ok'                          => 'Хорошо',
    'success'                     => 'Операция выполнена успешно',
    'currencyNotSupported'        => "Эта валюта не поддерживается.\nВыберите пожалуйста валюту из списка.",
    'chooseCurrencyBuy'           => 'Какую валюту вы хотите купить?',
    'chooseCurrencySell'          => 'Какую валюту вы хотите продать?',
    'chooseCurrency'              => 'Выберите валюту',
    'buySum'                      => "На какую сумму в гривне вы хотите купить валюту?\nВведите только цифры. Для разделителя дроби используйте точку.",
    'buyMessage'                  => ":sumUahFormat₴ = *:uahToCurrency* :currency\n-----\nКурс валюты на сейчас: *:currencyRate*\n\nЭто приемлемый для Вас курс, или вы зададите свой, по которому вы покупали валюту?",
    'buySuccessMessage'           => 'Операция успешно сохранена',
    'changeRateMessage'           => "Хорошо. Введите Ваш курс валюты.\nВведите только цифры. Для разделителя дроби используйте точку.",
    'sellSum'                     => "На вашем счету есть :currencySum :currency\nКакую сумму вы хотите продать?\n\nВведите только цифры. Для разделителя дроби используйте точку.",
    'sellEmptySum'                => "По этой валюте нет операций\nПродажа невозможна",
    'moreThanHave'                => "Это больше, чем у вас есть\nТакую сумму продать нельзя",
    'sellConfirm'                 => "Подтвердите продажу :sum :currency",
    'sellSuccessMessage'          => 'Операция успешно сохранена',
    'numberMustBeGreaterThanZero' => 'Число должно быть больше нуля',
    'userBalanceSum'              => "Ваши счета:\n\n:balance-----\nСуммарно в гривне: :uahSum",
    'userBalanceSumItem'          => "`:currencyNameUpper:\n   Трата:  :uah₴ (:avg₴)\n   Сумма:  :currency\n   Цена:   :newUah₴\n   Выгода: :diff₴ (:percentProfit%)`",
    'userBalanceSumTotal'         => "`\n-----\nСумма:\n   Трата:  :uah₴\n   Цена:   :newUah₴\n   Выгода: :diff₴ (:percentProfit%)`",
    'userBalanceEmpty'            => "На Ваших счетах нет средств",
    'delimiter'                   => "\n-----\n",
    'userReportRate'              => "\n\n:rateChange",
    'userReportAccount'           => "\n-----\nОтчет по вашим счетам:\n\n:accountChange",
    'userReport'                  => "Изменился курс валют.",
    'statisticsCurrencyReport'    => "Курс :currencyUpper за последний месяц:\n\n:ratesResponse\n-----\nПокупка:\n\n`Min: :buyMin`\n`Max: :buyMax`\n-----\nПродажа:\n\n`Min: :sellMin`\n`Max: :sellMax`",
];
